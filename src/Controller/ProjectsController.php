<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectsRepository;
use Github\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/projects")
 */
class ProjectsController extends Controller
{
    /**
     * @Route(path="/", name="project_list")
     */
    public function show(ProjectsRepository $repository)
    {
        return $this->render("projects/list.html.twig",
            ["projects" => $repository->fetchLatest()]);
    }

    /**
     * @Route(path="/import", name="project_import")
     */
    public function import(Client $client)
    {
        $available = $client->organization()->repositories("gogcom");

        return $this->render("projects/import.html.twig",
            ["available" => $available]);
    }

    /**
     * @Route(path="/create/{organization}/{name}", name="project_create", methods={"POST"})
     */
    public function create(string $organization, string $name, ProjectsRepository $repository)
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
