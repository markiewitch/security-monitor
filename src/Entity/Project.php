<?php

declare(strict_types=1);

namespace App\Entity;

use App\Vcs\GithubConnection;
use App\Vcs\GitlabConnection;
use App\Vcs\VcsConnectionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\GeneratedValue;
use Github\Client as GithubClient;
use Gitlab\Client as GitlabClient;
use Ramsey\Uuid\UuidInterface;

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
    /**
     * @var VcsConnectionInfo
     * @ORM\ManyToOne(targetEntity="App\Entity\VcsConnectionInfo")
     * @ORM\JoinColumn(name="connection_id", referencedColumnName="id", nullable=false)
     */
    private $connection;

    /**
     * @var PackageReference[]
     * @ORM\OneToMany(targetEntity="PackageReference", mappedBy="project")
     */
    private $packages;

    public function __construct(string $organization, string $name)
    {
        $this->organization = $organization;
        $this->name         = $name;
        $this->checks       = new ArrayCollection();
        $this->packages     = new ArrayCollection();
    }

    public function getConnection(): VcsConnectionInterface
    {
        if ($this->connection->getDriver() === "github") {
            return new GithubConnection($this->connection, new GithubClient());
        } elseif ($this->connection->getDriver() === "gitlab") {
            return new GitlabConnection($this->connection, new GitlabClient());
        } else {
            throw new \RuntimeException("Unkown driver");
        }
    }

    public function setConnection(VcsConnectionInfo $connection)
    {
        $this->connection = $connection;
    }

    public function getUuid(): UuidInterface
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

}
