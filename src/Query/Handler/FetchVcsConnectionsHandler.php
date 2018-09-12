<?php
declare(strict_types=1);

namespace App\Query\Handler;

use App\Query\FetchVcsConnections;
use App\Query\ViewModel\VcsConnectionViewModel;
use Doctrine\DBAL\Connection;

final class FetchVcsConnectionsHandler
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(FetchVcsConnections $query): iterable
    {
        $connections = $this->connection->executeQuery(
            "SELECT *, user_id = :uuid AS is_owned FROM vcs_connections WHERE `public` = 1 OR user_id = :uuid",
            ['uuid' => $query->getUserId(),],
            ['uuid' => 'uuid_binary_ordered_time']);

        return $this->hydrate(
            $connections->fetchAll(\PDO::FETCH_ASSOC),
            $query->getUserId()->toString());
    }

    private function hydrate(array $rows): iterable
    {
        return array_map(function (array $row) {
            return new VcsConnectionViewModel(
                (int)$row['id'],
                $row['driver'],
                $row['host'],
                (bool)$row['public'],
                new \DateTime($row['created_at']),
                (bool)$row['is_owned']
            );
        }, $rows);
    }
}
