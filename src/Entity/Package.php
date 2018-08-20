<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="packages", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique_package_name", columns={"vendor", "name"})
 * })
 */
class Package
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $vendor;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var PackageReference[]
     * @ORM\OneToMany(targetEntity="PackageReference", mappedBy="package")
     */
    private $references;
}
