<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class OrmPackagesRepository
{
    private $em;
    private $paginator;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em        = $em;
        $this->paginator = $paginator;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return \Knp\Component\Pager\Pagination\PaginationInterface|Package[]
     */
    public function fetchAll(int $page = 1, int $perPage = 20)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Package::class, 'p')
            ->orderBy('p.name', 'ASC');

        return $this->paginator->paginate($qb, $page, $perPage);
    }

    public function findOne(int $id): Package
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Package::class, 'p')
            ->where('p.id = :id');

        $package = $qb->getQuery()->execute(['id' => $id]);

        if (count($package) === 1) {
            return $package[0];
        }

        throw new \Exception('package not found');
    }
}
