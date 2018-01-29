<?php


namespace App\Vcs;

use App\Dto\VcsProjectInfo;

interface VcsConnectionInterface
{
    /**
     * @param string $organization
     * @return VcsProjectInfo[]|array
     */
    public function listProjects(string $organization = ''): array;

    public function fetchLockfile(string $organization, string $project);
}
