<?php
declare(strict_types=1);

namespace App\Dto;

class VcsProjectInfo
{
    private $organization;
    private $name;
    private $connection;

    public function __construct(string $organization, string $name, int $connection)
    {
        $this->organization = $organization;
        $this->name         = $name;
        $this->connection   = $connection;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }

    public function getConnectionId(): int
    {
        return $this->connection;
    }
}
