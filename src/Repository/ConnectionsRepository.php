<?php

namespace App\Repository;

use App\Entity\VcsConnectionInfo;
use Doctrine\ORM\EntityManagerInterface;

interface ConnectionsRepository
{
        /**
     * @param int $limit
     * @param int $after
     * @return VcsConnectionInfo[]
     */
    public function fetchLatest(int $limit = 10, int $after = 0): array;

    public function find(int $id): VcsConnectionInfo;

    public function persist(VcsConnectionInfo $connectionInfo);
}
