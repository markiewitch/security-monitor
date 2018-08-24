<?php

declare(strict_types=1);

namespace App\Listener;

use App\Dto\LockfilePackage;
use App\Entity\Package;
use App\Entity\PackageReference;
use App\Event\ProjectLockfileDownloaded;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PackageVersionsPersister implements EventSubscriberInterface
{
    private $logger;

    private $em;

    private $projects;

    public function __construct(LoggerInterface $logger, RegistryInterface $em, ProjectsRepository $projects)
    {
        $this->logger   = $logger;
        $this->em       = $em;
        $this->projects = $projects;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProjectLockfileDownloaded::NAME => 'onLockfileDownload',
        ];
    }

    public function onLockfileDownload(ProjectLockfileDownloaded $event)
    {
        $this->logger->info("event received, ", ['event' => $event]);

        $em = $this->em->getEntityManager();

        $packages   = $em->getRepository(Package::class);
        $references = $em->getRepository(PackageReference::class);
        $project    = $event->getProject();

        foreach ($this->readPackages($event->getContents()) as $lockfilePackage) {
            $package = $packages->findOneBy(['name' => $lockfilePackage->getName()]);
            if (!$package instanceof Package) {
                $package = new Package($lockfilePackage->getName());

            }

            $reference = $references->findOneBy(['package' => $package, 'project' => $project]);

            if (!$reference instanceof PackageReference) {
                $reference = new PackageReference($project, $package, $lockfilePackage->getVersion());
            } else {
                $reference->updateLastSeen($lockfilePackage->getVersion());
            }


            try {
                $em->persist($package);
                $em->persist($reference);
            } catch (ORMException $e) {
                $this->logger->warning("Exception while persisting packageReference", ['exception' => $e]);
            }
        }
    }

    /**
     * @param string $lockfile
     * @return \Generator|LockfilePackage[]
     */
    private function readPackages(string $lockfile): \Generator
    {
        $decoded = json_decode($lockfile, true);

        foreach ($decoded['packages'] as $package) {
            yield LockfilePackage::fromLockfileArray($package);
        }
    }
}
