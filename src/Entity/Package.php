<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="packages")
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
    private $name;

    /**
     * @var PackageReference[]
     * @ORM\OneToMany(targetEntity="PackageReference", mappedBy="package", fetch="EXTRA_LAZY")
     */
    private $references;

    public function __construct(string $name)
    {
        $this->name       = $name;
        $this->references = new ArrayCollection();
    }

    public function addReference(PackageReference $reference)
    {
        $this->references->add($reference);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PackageReference[]|ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }
}
