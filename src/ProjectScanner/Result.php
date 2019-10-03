<?php

declare(strict_types=1);

namespace App\ProjectScanner;

class Result
{
    /** @var array */
    private $vulnerabilities;

    public function __construct(array $vulnerabilities = [])
    {
        $this->vulnerabilities = $vulnerabilities;
    }

    public function getVulnerabilities(): array
    {
        return $this->vulnerabilities;
    }

    public function hasVulnerabilities(): bool
    {
        return count($this->vulnerabilities) > 0;
    }
}
