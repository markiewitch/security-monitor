<?php

declare(strict_types=1);

namespace App\ProjectScanner;

use App\Entity\Project;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\VarDumper\VarDumper;

class NodePackageScanner implements ProjectScanner
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function scan(Project $project): Result
    {
        $this->logger->info("Began node scanning of {$project->getName()}");
        try {
            $json = $project->getFile('package.json');
            $lockfile = $project->getFile('package-lock.json');
            $dir = $this->getTempDir($project->getOrganization() . '_' . $project->getName());

            file_put_contents($dir . DIRECTORY_SEPARATOR . 'package.json', $json);
            file_put_contents($dir . DIRECTORY_SEPARATOR . 'package-lock.json', $lockfile);

            $audit = new Process(['npm', 'audit', '--json'], $dir, null, null, 20.0);
            $audit->start();
            $audit->wait();

            return new Result(
                $this->extractReport($audit->getOutput())
            );
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return new Result();
            // todo probably no npm packages, but could be nicer
        }
    }

    private function getTempDir($projectPrefix): string
    {
        $systemTempDir = sys_get_temp_dir();
        $projectTempDir = $projectPrefix . bin2hex(random_bytes(4));

        $path = $systemTempDir . DIRECTORY_SEPARATOR . $projectTempDir;
        if (!mkdir($path) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Temp dir "%s" for npm scan was not created', $path));
        }

        return $path;
    }

    private function extractReport(string $output): array
    {
        $data = json_decode($output, true);

        $issues = [];
        foreach ($data['advisories'] as $advisory) {
            $issues[$advisory['module_name']] = [
                'version' => $advisory['vulnerable_versions'],
                'advisories' => [
                    ['link' => $advisory['url'], 'title' => $advisory['title']],
                ],
            ];
        }

        return $issues;
    }
}
