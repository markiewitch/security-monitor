<?php
declare(strict_types=1);

namespace App\Query;

use Ramsey\Uuid\UuidInterface;

final class FetchVcsConnections
{
    private $userId;

    private function __construct(UuidInterface $userId)
    {
        $this->userId = $userId;
    }

    public static function forUser(UuidInterface $userId)
    {
        return new self($userId);
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }
}
