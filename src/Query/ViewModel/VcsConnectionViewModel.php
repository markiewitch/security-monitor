<?php
declare(strict_types=1);

namespace App\Query\ViewModel;

use DateTime;

class VcsConnectionViewModel
{
    /** @var int */
    private $id;
    /** @var string */
    private $driver;
    /** @var string */
    private $host;
    /** @var bool */
    private $public;
    /** @var \DateTime */
    private $createdAt;
    /** @var bool */
    private $createdByCurrentUser;

    public function __construct(
        int $id,
        string $driver,
        string $host,
        bool $public,
        DateTime $createdAt,
        bool $createdByCurrentUser)
    {
        $this->id                   = $id;
        $this->driver               = $driver;
        $this->host                 = $host;
        $this->public               = $public;
        $this->createdAt            = $createdAt;
        $this->createdByCurrentUser = $createdByCurrentUser;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function isCreatedByCurrentUser(): bool
    {
        return $this->createdByCurrentUser;
    }


}
