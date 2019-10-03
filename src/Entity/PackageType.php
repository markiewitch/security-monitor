<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class PackageType
{
    private const NPM = 'NPM';
    private const COMPOSER = 'COMPOSER';

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function NPM(): self
    {
        return new static(self::NPM);
    }

    public static function COMPOSER(): self
    {
        return new static (self::COMPOSER);
    }
}
