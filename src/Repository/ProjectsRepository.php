<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Ramsey\Uuid\UuidInterface;

interface ProjectsRepository
{
    /**
     * @return PaginationInterface|Project[]
     */
    public function fetchLatest(int $page = 1): PaginationInterface;

    public function persist(Project $project);

    /**
     * @param UuidInterface $uuid
     * @return Project
     */
    public function find(UuidInterface $uuid);

    public function flush(Project $project);
}
