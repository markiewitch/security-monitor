<?php
declare(strict_types=1);

namespace App\Dto;

class VcsProjectInfo
{
    private $organization;
    private $name;

    public function __construct(string $organization, string $name)
    {
        $this->organization = $organization;
        $this->name         = $name;
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
}
