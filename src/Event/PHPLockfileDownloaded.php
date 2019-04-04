<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Project;
use Symfony\Component\EventDispatcher\Event;

class PHPLockfileDownloaded extends Event
{
    public const NAME = 'project_lockfile_downloaded';

    /** @var Project */
    private $project;

    /** @var string */
    private $contents;

    public function __construct(Project $project, string $contents)
    {
        $this->project  = $project;
        $this->contents = $contents;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }


}
