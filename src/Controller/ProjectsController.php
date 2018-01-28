<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Check;
use App\Entity\Project;
use App\Repository\ProjectsRepository;
use Github\Client;
use Ramsey\Uuid\Uuid;
use SensioLabs\Security\SecurityChecker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route(path="/import", name="project_import")
     */
    public function import(Client $client): Response
    {
        $available = $client->organization()->repositories("gogcom");

        return $this->render("projects/import.html.twig",
            ["available" => $available]);
    }

    /**
     * @Route(path="/check/{uuid}", name="project_check")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function check(Client $client, string $uuid, ProjectsRepository $repository): Response
    {
        $project = $repository->find(Uuid::fromString($uuid));
        $check = Check::create($project);
        $filename = tempnam(sys_get_temp_dir(), "security-monitor");
        $lockfile = base64_decode(
            $client->repositories()->contents()->show($project->getOrganization(), $project->getName(),
                "composer.lock")["content"]
        );
        file_put_contents($filename, $lockfile);
        $checker = new SecurityChecker();
        $results = $checker->check($filename);
        if(count($results) === 0) {
            $check->markAsFinishedSuccessfully();
        }
        $repository->flush($project);
        return $this->render("index/index.html.twig",
            ["filename" => $filename, "results" => $results]);
    }

    /**
     * @Route(path="/create/{organization}/{name}", name="project_create", methods={"POST"})
     */
    public function create(string $organization, string $name, ProjectsRepository $repository): Response
    {
        try {
            $project = new Project($organization, $name);
            $repository->persist($project);
        } catch (\Throwable $t) {
            return JsonResponse::create(['error' => $t->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return JsonResponse::create(['uuid' => $project->getUuid()]);
    }
}
