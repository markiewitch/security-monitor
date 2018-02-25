<?php


namespace App\Vcs;

use App\Dto\VcsProjectInfo;
use App\Entity\VcsConnectionInfo;
use Gitlab\Client;

class GitlabConnection implements VcsConnectionInterface
{
    private $client;

    public function __construct(VcsConnectionInfo $connectionInfo, Client $client)
    {
        $client->authenticate($connectionInfo->getToken(), null, Client::AUTH_HTTP_TOKEN);
        $client->setUrl($connectionInfo->getHost());
        $this->client = $client;
    }

    public function listProjects(string $organization = ''): array
    {
        if ($organization != '') {
            $projects = $this->client->projects()->all(['search' => $organization]);
        } else {
            $projects = $this->client->projects()->all();
        }
var_dump($projects);die();
        return array_map(function (array $gitlabProject) {
            return new VcsProjectInfo($gitlabProject['namespace'], $gitlabProject['name']);
        }, $projects);
    }

    public function fetchLockfile(string $organization, string $project)
    {
        throw new \RuntimeException("not implemented");
    }
}
