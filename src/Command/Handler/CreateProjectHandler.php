<?php

declare(strict_types=1);

namespace App\Command\Handler;

use App\Command\CreateProject;
use App\Command\Exception\ProjectCreationFailure;
use App\Entity\Project;
use App\Repository\ConnectionsRepository;
use App\Repository\ProjectsRepository;

class CreateProjectHandler
{
    private $connections;
    private $projects;

    public function __construct(ConnectionsRepository $connections, ProjectsRepository $projects)
    {
        $this->projects    = $projects;
        $this->connections = $connections;
    }

    /**
     * @throws ProjectCreationFailure
     */
    public function __invoke(CreateProject $cmd)
    {
        try {
            $connection = $this->connections->find($cmd->getConnectionId());
            $project    = new Project($cmd->getOrganizationName(), $cmd->getProjectName());
            $project->setConnection($connection);
            $this->projects->persist($project);
        } catch (\Throwable $t) {
            throw new ProjectCreationFailure("Project could not be created", 0, $t);
        }
    }
}
