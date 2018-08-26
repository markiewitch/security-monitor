<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\UuidInterface;

class VulnerabilitiesChartDataProvider
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function prepareData(UuidInterface $projectId)
    {
        $sql = "select MAX(vulnerabilities_count) as vulnerabilities, finished_at from checks c WHERE c.project_id = :project_id AND c.finished_at > DATE_SUB(NOW(), INTERVAL 31 DAY) GROUP BY DAY(c.finished_at)";

        //todo we could fetch the json with CVEs and count them instead

        $checks = $this->connection->executeQuery(
            $sql,
            ['project_id' => $projectId],
            ['project_id' => 'uuid_binary_ordered_time'])->fetchAll();

        $data = [];
        foreach ($this->hydrate($checks) as $day) {
            $data[] = $day;
        }

        return $data;
    }

    private function hydrate(array $rows)
    {
        foreach ($rows as $row) {
            yield [
                't' => (new \DateTime($row['finished_at']))->format("Y-m-d"),
                'y' => $row['vulnerabilities'],
            ];
        }
    }
}
