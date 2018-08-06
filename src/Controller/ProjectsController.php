<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\VcsProjectInfo;
use App\Entity\Check;
use App\Entity\Project;
use App\Repository\OrmConnectionsRepository;
use App\Repository\ProjectsRepository;
use App\Vcs\GithubConnection;
use App\Vcs\GitlabConnection;
use Github\Client;
use Github\Client as GithubClient;
use Gitlab\Client as GitlabClient;
use Ramsey\Uuid\Uuid;
use SensioLabs\Security\SecurityChecker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/projects")
 */
class ProjectsController extends Controller
{
    /**
     * @Route(path="/", name="project_list")
     */
    public function show(ProjectsRepository $repository): Response
    {
        return $this->render("projects/list.html.twig",
            ["projects" => $repository->fetchLatest()]);
    }

    /**
     * @Route(path="/import/{connectionId}", name="import_project")
     */
    public function importFromConnection(Request $request, int $connectionId, OrmConnectionsRepository $connectionsRepository)
    {
        $page = $request->query->getInt('page', 1);

        $connectionInfo = $connectionsRepository->find($connectionId);
        if ($connectionInfo->getDriver() === "github") {
            $driver = new GithubConnection($connectionInfo, new GithubClient());
        } elseif ($connectionInfo->getDriver() === "gitlab") {
            $driver = new GitlabConnection($connectionInfo, new GitlabClient());
        } else {
            throw new \RuntimeException("Unkown driver");
        }

        /** @var VcsProjectInfo[] $available */
        $available = $driver->listProjects('', $page);
        return $this->render("projects/import.html.twig",
            ["available" => $available]);
    }

    /**
     * @Route(path="/check/{projectUuid}", name="project_check")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function check(string $projectUuid, ProjectsRepository $repository): Response
    {
        $project = $repository->find(Uuid::fromString($projectUuid));
        $client = $project->getConnection();
        $check = Check::create($project);
        $filename = tempnam(sys_get_temp_dir(), "security-monitor");
        $lockfile = $client->fetchLockfile($project->getOrganization(), $project->getName());
        file_put_contents($filename, $lockfile);
        $checker = new SecurityChecker();
        $results = $checker->check($filename);
        unlink($filename);
        if (count($results) === 0) {
            $check->markAsFinishedSuccessfully();
        }
        $repository->flush($project);
        return $this->render("index/index.html.twig",
            ["filename" => $filename, "results" => $results]);
    }

    /**
     * @Route(path="/create/{organization}/{name}/{connectionId}", name="project_create", methods={"POST"})
     */
    public function create(string $organization, string $name, int $connectionId, ProjectsRepository $repository, OrmConnectionsRepository $connections): Response
    {
        try {
            $connection = $connections->find($connectionId);
            $project = new Project($organization, $name);
            $project->setConnection($connection);
            $repository->persist($project);
        } catch (\Throwable $t) {
            return JsonResponse::create(['error' => $t->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return JsonResponse::create(['uuid' => $project->getUuid()]);
    }
}
