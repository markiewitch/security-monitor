<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="projects")
 */
class Project
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Column(type="uuid")
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

    public function __construct(string $organization, string $name)
    {
        $this->uuid = Uuid::uuid4();
        $this->organization = $organization;
        $this->name = $name;
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

    public function onCheckWasRun(object $event)
    {
        //todo
    }
}
