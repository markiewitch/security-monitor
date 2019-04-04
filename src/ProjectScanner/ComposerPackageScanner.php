<?php

declare(strict_types=1);

namespace App\ProjectScanner;

use App\Entity\Project;
use App\Event\PHPLockfileDownloaded;
use SensioLabs\Security\SecurityChecker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ComposerPackageScanner implements ProjectScanner
{
    private $events;

    private $checker;

    public function __construct(EventDispatcherInterface $events, SecurityChecker $checker)
    {
        $this->events = $events;
        $this->checker = $checker;
    }

    public function scan(Project $project): Result
    {
        $filename = tempnam(sys_get_temp_dir(), 'security-monitor-composer');
        $lockfile = $project->getFile('composer.lock');
        file_put_contents($filename, $lockfile);

        $this->events->dispatch(PHPLockfileDownloaded::NAME, new PHPLockfileDownloaded($project, $lockfile));

        $vulnerabilities = $this->checker->check($filename);
        unlink($filename);

        if (count($vulnerabilities) === 0) {
            return new Result();
        }
        return new Result(json_decode((string)$vulnerabilities, true));
    }
}
