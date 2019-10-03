<?php

declare(strict_types=1);

namespace App\ProjectScanner;

use App\Entity\Project;

interface ProjectScanner
{
    public function scan(Project $project): Result;
}
