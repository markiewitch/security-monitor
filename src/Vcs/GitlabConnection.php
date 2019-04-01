<?php


namespace App\Vcs;

use App\Dto\VcsProjectInfo;
use App\Entity\VcsConnectionInfo;
use Gitlab\Client;

class GitlabConnection implements VcsConnectionInterface
{
    private $client;
    private $info;

    public function __construct(VcsConnectionInfo $connectionInfo, Client $client)
    {
        $this->info = $connectionInfo;
        $client->authenticate($connectionInfo->getToken(), Client::AUTH_HTTP_TOKEN);
        $client->setUrl($connectionInfo->getHost());
        $this->client = $client;
    }

    public function listProjects(?string $organization = null, ?string $project = null, int $page = 1, int $perPage = 20): array
    {
        $params = ['per_page' => $perPage, 'page' => $page];

        if ($project !== null) {
            $params += ['search' => $project];
        }

        if ($organization !== null) {
            $projects = $this->client->groups()->projects($organization, $params);
        } else {
            $projects = $this->client->projects()->all($params);
        }

        return array_map(function (array $gitlabProject) {
            return new VcsProjectInfo(
                $gitlabProject['namespace']['name'],
                $gitlabProject['name'],
                $this->info->getId());
        }, $projects);
    }

    public function fetchLockfile(string $organization, string $project)
    {
        $repositories = $this->client->projects()->all(['search' => "$project"]);
        $repository = array_values(array_filter($repositories,
            function (array $repository) use ($organization, $project) {
                return $repository['path_with_namespace'] === "$organization/$project";
            }))[0];
        $repositoryId = $repository['id'];
        //        VarDumper::dump($repository);
        //        throw new \RuntimeException("not implemented");
        return $this->client->repositoryFiles()->getRawFile($repositoryId, 'composer.lock', 'master');
        //        return base64_decode($this->client->repositoryFiles()->getRawFile();
    }
}
