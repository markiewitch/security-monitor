<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid_binary_ordered_time")
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
}
