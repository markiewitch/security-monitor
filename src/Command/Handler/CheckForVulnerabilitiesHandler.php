<?php
declare(strict_types=1);

namespace App\Command\Handler;

use App\Command\CheckForVulnerabilities;
use App\Entity\Check;
use App\Event\PHPLockfileDownloaded;
use App\ProjectScanner\ComposerPackageScanner;
use App\ProjectScanner\NodePackageScanner;
use App\ProjectScanner\ProjectScanner;
use App\Repository\ProjectsRepository;
use Psr\Log\LoggerInterface;
use SensioLabs\Security\SecurityChecker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckForVulnerabilitiesHandler
{
    /** @var ProjectsRepository */
    private $projects;
    private $checker;
    private $events;
    private $logger;

    public function __construct(ProjectsRepository $projects, EventDispatcherInterface $events, LoggerInterface $logger)
    {
        $this->checker = new SecurityChecker();
        $this->projects = $projects;
        $this->events = $events;
        $this->logger = $logger;
    }

    public function __invoke(CheckForVulnerabilities $cmd)
    {
        $project = $this->projects->find($cmd->getProjectId());
        $check = Check::create($project);
        $issues = [];

        foreach ($this->scanners() as $scanner) {
            $result = $scanner->scan($project);
            foreach ($result->getVulnerabilities() as $package => $vulnerability) {
                $issues[$package] = $vulnerability;
            }
        }

        if (count($issues) > 0) {
            $check->finishWithVulnerabilities($issues);
        } else {
            $check->markAsFinishedSuccessfully();
        }

        $project->addCheck($check);
        $this->projects->flush($project);
    }

    /**
     * @return \Generator|ProjectScanner[]
     */
    private function scanners(): \Generator
    {
        yield new ComposerPackageScanner($this->events, $this->checker);
        yield new NodePackageScanner($this->logger);
    }
}
