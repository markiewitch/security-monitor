<?php

declare(strict_types=1);

namespace App\Command\Handler;

use App\Command\CreateProject;
use App\Command\Exception\ProjectCreationFailure;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ConnectionsRepository;
use App\Repository\ProjectsRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateProjectHandler
{
    private $connections;
    private $projects;
    private $tokenStorage;

    public function __construct(
        ConnectionsRepository $connections,
        ProjectsRepository $projects,
        TokenStorageInterface $tokenStorage)
    {
        $this->projects     = $projects;
        $this->connections  = $connections;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws ProjectCreationFailure
     */
    public function __invoke(CreateProject $cmd)
    {
        try {
            /** @var User $user */
            $user       = $this->tokenStorage->getToken()->getUser();
            $connection = $this->connections->find($cmd->getConnectionId());

            if (!$connection->isPublic() && !$connection->getCreatedBy()->getId()->equals($user->getId())) {
                throw new ProjectCreationFailure("This private connection is not owned by you!");
            }

            $project = new Project($cmd->getOrganizationName(), $cmd->getProjectName());
            $project->setConnection($connection);
            $this->projects->persist($project);
        } catch (\Throwable $t) {
            throw new ProjectCreationFailure("Project could not be created", 0, $t);
        }
    }
}
