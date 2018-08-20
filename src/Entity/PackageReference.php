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
//    /**
//     * @var
//     * @ORM\Id()
//     */
//    private $projectId;

    /**
     * @var Project
     * @ORM\OneToOne(targetEntity="Project", mappedBy="packages")
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
}
