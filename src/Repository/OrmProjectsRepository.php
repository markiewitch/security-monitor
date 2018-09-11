<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\UuidInterface;

class OrmProjectsRepository implements ProjectsRepository
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em        = $em;
        $this->paginator = $paginator;
    }

    /**
     * @param int $limit
     * @param int $after
     * @return Project[]
     */
    public function fetchLatest(int $page = 1): PaginationInterface
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from('App:Project', 'p')
            ->addOrderBy('p.lastCheckDate', 'desc')
            ->addOrderBy('p.uuid');

        return $this->paginator->paginate($qb, $page);
    }

    public function persist(Project $project)
    {
        $this->em->persist($project);
        $this->em->flush();
    }

    public function find(UuidInterface $uuid)
    {
        return $this->em->find('App:Project', $uuid);
    }

    public function flush(Project $project)
    {
        $this->em->flush();
    }
}
