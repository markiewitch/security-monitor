<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class OrmPackagesRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function fetchAll(int $page = 1, int $perPage = 20)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Package::class, 'p')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->orderBy('p.name', 'ASC');

        return $qb->getQuery()->execute();
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
