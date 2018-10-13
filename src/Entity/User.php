<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 * @method UuidInterface getId()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary_ordered_time", name="id")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     */
    protected $id;

    /**
     * @ORM\Column(name="sensio_id", type="string", length=255, nullable=true)
     */
    protected $sensioId;

    /**
     * @ORM\Column(name="sensio_access_token", type="string", length=255, nullable=true)
     */
    protected $sensioAccessToken;
    /**
     * @ORM\Column(name="github_id", type="string", length=255, nullable=true)
     */
    protected $githubId;

    /**
     * @ORM\Column(name="github_access_token", type="string", length=255, nullable=true)
     */
    protected $githubAccessToken;

    /**
     * @var VcsConnectionInfo[]
     * @ORM\OneToMany(targetEntity="App\Entity\VcsConnectionInfo", mappedBy="createdBy")
     */
    private $connections;


    public function __construct()
    {
        parent::__construct();
    }


    public function setSensioId(string $id)
    {
        $this->sensioId = $id;
    }

    public function setSensioAccessToken(string $token)
    {
        $this->sensioAccessToken = $token;
    }
    
    public function setGithubId(int $id)
    {
        $this->githubId = $id;
    }

    public function setGithubAccessToken(string $token)
    {
        $this->githubAccessToken = $token;
    }
}
