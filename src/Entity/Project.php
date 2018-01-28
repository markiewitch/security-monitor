<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\GeneratedValue;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="projects")
 */
class Project
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     * @ORM\Id()
     */
    private $uuid;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $organization;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $wasLastCheckSuccessful;
    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastCheckDate;
    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Check", mappedBy="project", cascade={"persist"})
     */
    private $checks;

    public function __construct(string $organization, string $name)
    {
        $this->organization = $organization;
        $this->name = $name;
        $this->checks = new ArrayCollection();
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrganization(): string
    {
        return $this->organization;
    }

    /**
     * @return Check[]
     */
    public function getChecks()
    {
        return $this->checks->toArray();
    }

    public function addCheck(Check $check)
    {
        $this->checks->add($check);
    }

    public function onCheckWasRun(object $event)
    {
        //todo
    }
}
