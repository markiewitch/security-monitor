<?php

declare(strict_types=1);

namespace App\Repository;


use App\Entity\Project;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

interface ProjectsRepository
{
    /**
     * @param int $limit
     * @param int $after
     * @return Project[]
     */
    public function fetchLatest(int $limit = 10, int $after = 0): array;

    public function persist(Project $project);

    /**
     * @param UuidInterface $uuid
     * @return Project
     */
    public function find(UuidInterface $uuid);

    public function flush(Project $project);
}
