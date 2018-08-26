<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Check;
use App\Entity\Project;
use App\Event\ProjectLockfileDownloaded;
use App\Repository\ProjectsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SecurityChecker
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

    public function check(Project $project)
    {
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
