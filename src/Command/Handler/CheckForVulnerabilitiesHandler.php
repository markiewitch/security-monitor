<?php
declare(strict_types=1);

namespace App\Command\Handler;

use App\Command\CheckForVulnerabilities;
use App\Entity\Check;
use App\Event\ProjectLockfileDownloaded;
use App\Repository\ProjectsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckForVulnerabilitiesHandler
{
    /** @var ProjectsRepository */
    private $projects;
    private $checker;
    private $events;

    public function __construct(ProjectsRepository $projects, EventDispatcherInterface $events)
    {
        $this->checker  = new \SensioLabs\Security\SecurityChecker();
        $this->projects = $projects;
        $this->events   = $events;
    }

    public function __invoke(CheckForVulnerabilities $cmd)
    {
        $project  = $this->projects->find($cmd->getProjectId());
        $client   = $project->getConnection();
        $check    = Check::create($project);
        $filename = tempnam(sys_get_temp_dir(), "security-monitor");
        $lockfile = $client->fetchLockfile($project->getOrganization(), $project->getName());
        file_put_contents($filename, $lockfile);

        $this->events->dispatch(ProjectLockfileDownloaded::NAME, new ProjectLockfileDownloaded($project, $lockfile));

        $vulnerabilities = $this->checker->check($filename);
        unlink($filename);
        if (count($vulnerabilities) === 0) {
            $check->markAsFinishedSuccessfully();
        } else {
            $check->finishWithVulnerabilities($vulnerabilities);
        }

        $project->addCheck($check);
        $this->projects->flush($project);
    }

}
