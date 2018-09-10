<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\CreateProject;
use App\Command\Exception\ProjectCreationFailure;
use App\Repository\OrmConnectionsRepository;
use App\Repository\ProjectsRepository;
use App\Service\SecurityChecker;
use App\Service\VulnerabilitiesChartDataProvider;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/projects")
 */
class ProjectsController extends Controller
{
    /**
     * @Route(path="/", name="project_list")
     */
    public function show(Request $request, ProjectsRepository $repository): Response
    {
        $perPage = $request->query->getInt('perPage', 12);
        $page    = $request->query->getInt('page', 1);

        return $this->render(
            "projects/list.html.twig",
            [
                "projects" => $repository->fetchLatest($perPage, $perPage * ($page - 1)),
                "page"     => $page,
            ]);
    }

    /**
     * @Route(path="/{uuid}", name="project_view")
     */
    public function view(string $uuid, ProjectsRepository $projects)
    {
        $uuid    = Uuid::fromString($uuid);
        $project = $projects->find($uuid);

        return $this->render(
            "projects/view.html.twig",
            [
                "project"   => $project,
                "lastCheck" => $project->getLastCheck(),
            ]
        );
    }

    /**
     * @Route(path="/import/{connectionId}", name="import_project")
     */
    public function importFromConnection(
        Request $request,
        int $connectionId,
        OrmConnectionsRepository $connectionsRepository)
    {
        $page         = $request->query->getInt('page', 1);
        $organization = $request->query->get('organization', '');

        $connectionInfo = $connectionsRepository->find($connectionId);

        $available = $connectionInfo->getConnection()
            ->listProjects($organization, $page);

        return $this->render("projects/import.html.twig",
                             ["available" => $available, 'page' => $page, 'connectionId' => $connectionId]);
    }

    /**
     * @Route(path="/check/{projectUuid}", name="project_check")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function check(string $projectUuid, ProjectsRepository $repository, SecurityChecker $checker): Response
    {
        $project = $repository->find(Uuid::fromString($projectUuid));

        $checker->check($project);

        return $this->redirectToRoute('project_list');
    }

    /**
     * @Route(path="/create/{organization}/{name}/{connectionId}", name="project_create", methods={"POST"})
     */
    public function create(
        string $organization,
        string $name,
        int $connectionId,
        MessageBusInterface $commandBus): Response
    {
        try {
            $commandBus->dispatch(new CreateProject($connectionId, $organization, $name));
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
