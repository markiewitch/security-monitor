<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\CheckForVulnerabilities;
use App\Command\CreateProject;
use App\Command\Exception\ProjectCreationFailure;
use App\Entity\User;
use App\Repository\OrmConnectionsRepository;
use App\Repository\ProjectsRepository;
use App\Service\System;
use App\Service\VulnerabilitiesChartDataProvider;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route(path="/projects")
 */
class ProjectsController extends Controller
{
    private $system;

    public function __construct(System $system)
    {
        $this->system = $system;
    }

    /**
     * @Route(path="/", name="project_list")
     */
    public function show(Request $request, ProjectsRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render(
            "projects/list.html.twig",
            [
                "projects" => $repository->fetchLatest($page),
            ]);
    }

    /**
     * @Route(path="/{uuid}", name="project_view")
     */
    public function view(string $uuid, ProjectsRepository $projects, TokenStorageInterface $tokenStorage)
    {
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $uuid = Uuid::fromString($uuid);
        $project = $projects->find($uuid);

        if (!$project->getConnectionInfo()->isPublic() && !$project->getConnectionInfo()->getCreatedBy()->getId()->equals($user->getId())) {
            throw new AccessDeniedHttpException("You don't have access to this project");
        }

        return $this->render(
            'projects/view.html.twig',
            [
                'project' => $project,
                'lastCheck' => $project->getLastCheck(),
            ]
        );

    }

    /**
     * @Route(path="/import/{connectionId}", name="import_project")
     */
    public function importFromConnection(
        Request $request,
        int $connectionId,
        OrmConnectionsRepository $connectionsRepository,
        TokenStorageInterface $tokenStorage)
    {
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $page = $request->query->getInt('page', 1);
        $organization = $request->query->get('organization');
        $project = $request->query->get('project');

        $connectionInfo = $connectionsRepository->find($connectionId);

        if (!$connectionInfo->isPublic() && !$connectionInfo->getCreatedBy()->getId()->equals($user->getId())) {
            throw new AccessDeniedHttpException("This connection isn't owned by you!");
        }

        $available = $connectionInfo->getConnection()
            ->listProjects($organization, $project, $page);

        return $this->render("projects/import.html.twig",
            ['available' => $available, 'page' => $page, 'connectionId' => $connectionId]);
    }

    /**
     * @Route(path="/check/{projectUuid}", name="project_check")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function check(
        string $projectUuid,
        LoggerInterface $logger): Response
    {
        try {
            $this->system->command(new CheckForVulnerabilities($projectUuid));
        } catch (\Throwable $t) {
            $logger->error("Couldn't check project $projectUuid for vulnerable packages", ['exception' => $t]);
        }
return new Response("");
//        return $this->redirectToRoute('project_list');
    }

    /**
     * @Route(path="/create/{organization}/{name}/{connectionId}", name="project_create", methods={"POST"})
     */
    public function create(
        string $organization,
        string $name,
        int $connectionId): Response
    {
        try {
            $this->system->command(new CreateProject($connectionId, $organization, $name));
        } catch (ProjectCreationFailure $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route(path="/{uuid}/chart.json", name="project_chart")
     */
    public function drawChart(string $uuid, VulnerabilitiesChartDataProvider $chartDataProvider)
    {
        $uuid = Uuid::fromString($uuid);

        $data = $chartDataProvider->prepareData($uuid);

        return new JsonResponse($data);
    }
}
