<?php


namespace App\Vcs;

use App\Dto\VcsProjectInfo;

interface VcsConnectionInterface
{
    /**
     * @param string $organization
     * @param string|null $project
     * @param int $page
     * @param int $perPage
     * @return VcsProjectInfo[]|array
     */
    public function listProjects(?string $organization = null, ?string $project = null, int $page = 1, int $perPage = 20): array;

    public function fetchLockfile(string $organization, string $project);
}
