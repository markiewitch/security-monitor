<?php
declare(strict_types=1);

namespace App\Dto;

class LockfilePackage
{
    private $version;
    private $name;

    public function __construct($name, $version)
    {
        $this->version = $version;
        $this->name    = $name;
    }

    public static function fromLockfileArray(array $raw)
    {
        return new self($raw['name'], $raw['version']);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
