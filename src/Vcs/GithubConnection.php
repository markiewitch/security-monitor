<?php


namespace App\Vcs;

use App\Dto\VcsProjectInfo;
use App\Entity\VcsConnectionInfo;
use Github\Client;

class GithubConnection implements VcsConnectionInterface
{
    private $client;

    public function __construct(VcsConnectionInfo $connectionInfo, Client $client)
    {
        $client->authenticate($connectionInfo->getToken(), null, Client::AUTH_HTTP_TOKEN);
        $this->client = $client;
    }

    public function listProjects(string $organization = '', int $page = 1): array
    {
        if ($organization != '') {
            $projects = $this->client->organization()->repositories($organization);
        } else {
            $projects = $this->client->repositories()->all();
        }

        return array_map(function (array $githubProject) {
            return new VcsProjectInfo($githubProject['owner']['login'], $githubProject['name']);
        }, $projects);
    }

    public function fetchLockfile(string $organization, string $project)
    {
        return base64_decode($this->client->repositories()->contents()->show($organization, $project,
            "composer.lock")["content"]);
        throw new \RuntimeException("not implemented");
    }
}