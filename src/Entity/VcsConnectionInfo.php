<?php


namespace App\Entity;

use App\Vcs\GithubConnection;
use App\Vcs\GitlabConnection;
use App\Vcs\VcsConnectionInterface;
use Doctrine\ORM\Mapping as ORM;
use Github\Client as GithubClient;
use Gitlab\Client as GitlabClient;

/**
 * @ORM\Entity()
 * @ORM\Table(name="vcs_connections")
 */
class VcsConnectionInfo
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $driver;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $host;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $token;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     */
    public function setDriver(string $driver): void
    {
        $this->driver = $driver;
    }

    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getConnection(): VcsConnectionInterface
    {
        if ($this->driver === "github") {
            $driver = new GithubConnection($this, new GithubClient());
        } elseif ($this->driver === "gitlab") {
            $driver = new GitlabConnection($this, new GitlabClient());
        } else {
            throw new \RuntimeException("Unkown driver");
        }

        return $driver;
    }
}
