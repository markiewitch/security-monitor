<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="checks")
 */
class Check
{
    /**
     * @var int
     * @ORM\Column(type="integer", unique=true)
     * @GeneratedValue()
     * @ORM\Id()
     */
    private $id;
    /**
     * @var Uuid
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="checks")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="uuid")
     */
    private $project;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;
    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $vulnerabilities;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vulnerabilitiesCount;
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $wasSuccessful;

    private function __construct(Project $project)
    {
        $this->createdAt = new \DateTime();
        $this->project   = $project;
    }

    public static function create(Project $project)
    {
        $check = new self($project);
//        $project->addCheck($check);

        return $check;
    }

    public function markAsFinishedSuccessfully()
    {
        $this->wasSuccessful        = true;
        $this->vulnerabilities      = [];
        $this->vulnerabilitiesCount = 0;
        $this->finishedAt           = new \DateTime();
    }

    public function finishWithVulnerabilities(array $vulnerabilities)
    {
        $this->wasSuccessful        = false;
        $this->vulnerabilities      = $vulnerabilities;
        $this->vulnerabilitiesCount = count($vulnerabilities);
        $this->finishedAt           = new \DateTime();
    }

    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    public function wasSuccessful()
    {
        return $this->wasSuccessful;
    }

    public function getVulnerabilities()
    {
        return $this->vulnerabilities;
    }
}
