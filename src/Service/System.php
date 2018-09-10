<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Messenger\MessageBusInterface;

class System
{
    private $commandBus;
    private $queryBus;

    public function __construct(MessageBusInterface $commandBus, MessageBusInterface $queryBus)
    {
        $this->commandBus = $commandBus;
        $this->queryBus   = $queryBus;
    }

    /**
     * @param object $message
     * @return mixed
     */
    public function query(object $message)
    {
        return $this->queryBus->dispatch($message);
    }

    public function command(object $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
