<?php

declare(strict_types=1);

namespace App\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CheckForVulnerabilities
{
    private $projectUuid;

    public function __construct(string $projectUuid)
    {
        $this->projectUuid = Uuid::fromString($projectUuid);
    }

    public function getProjectId(): UuidInterface
    {
        return $this->projectUuid;
    }
}
