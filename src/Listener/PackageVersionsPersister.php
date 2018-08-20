<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\ProjectLockfileDownloaded;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\VarDumper\VarDumper;

class PackageVersionsPersister implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onLockfileDownload(ProjectLockfileDownloaded $event)
    {
        VarDumper::dump($event);
        $this->logger->info("event received, ", ['event' => $event]);
    }

    public static function getSubscribedEvents()
    {
        return [
            ProjectLockfileDownloaded::NAME => 'onLockfileDownload',
        ];
    }
}
