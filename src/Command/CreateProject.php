<?php
declare(strict_types=1);

namespace App\Command;

class CreateProject
{
    private $connectionId;
    private $organization;
    private $name;

    public function __construct(int $connectionId, string $organization, string $name)
    {
        $this->organization = $organization;
        $this->connectionId = $connectionId;
        $this->name         = $name;
    }

    public function getConnectionId(): int
    {
        return $this->connectionId;
    }

    public function getOrganizationName(): string
    {
        return $this->organization;
    }

    public function getProjectName(): string
    {
        return $this->name;
    }
}
