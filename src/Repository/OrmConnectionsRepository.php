<?php


namespace App\Repository;

use App\Entity\VcsConnectionInfo;
use Doctrine\ORM\EntityManagerInterface;

class OrmConnectionsRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $limit
     * @param int $after
     * @return VcsConnectionInfo[]
     */
    public function fetchLatest(int $limit = 10, int $after = 0): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from('App:VcsConnectionInfo', 'c')
            ->setMaxResults($limit)
            ->setFirstResult($after);

        return $qb->getQuery()->getResult();
    }

    public function find(int $id): VcsConnectionInfo
    {
        return $this->em->find(VcsConnectionInfo::class, $id);
    }

    public function persist(VcsConnectionInfo $connectionInfo)
    {
        $this->em->persist($connectionInfo);
        $this->em->flush();
    }
}
