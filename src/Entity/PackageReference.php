<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="package_references")
 */
class PackageReference
{

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="packages")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="uuid")
     * @ORM\Id()
     */
    private $project;

    /**
     * @var Package
     * @ORM\ManyToOne(targetEntity="Package", inversedBy="references")
     * @ORM\Id()
     */
    private $package;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $firstSeenOn;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $lastSeenOn;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isInstalled;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $lastVersion;

    public function __construct(Project $project, Package $package, string $version)
    {
        $version = preg_replace('/^v/', '', $version);

        $this->project     = $project;
        $this->package     = $package;
        $this->lastVersion = $version;
        $this->firstSeenOn = new \DateTime();
        $this->lastSeenOn  = new \DateTime();
        $this->isInstalled = true;
    }

    public function updateLastSeen(string $version)
    {
        $version = preg_replace('/^v/', '', $version);

        $this->lastVersion = $version;
        $this->lastSeenOn  = new \DateTime();
    }
}
