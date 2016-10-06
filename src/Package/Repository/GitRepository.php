<?php
namespace Dotfiles\Package\Repository;

use Dotfiles\Package\RepositoryInterface;
use Github\Client;

class GitRepository implements RepositoryInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPackages()
    {
        // TODO: Implement getPackages() method.
    }

    public function searchPackages($search)
    {
        // TODO: Implement searchPackages() method.
    }

    public function getPackage($name)
    {

    }

    public function getPackageReadme($name)
    {
        if ($this->isPackageValid($name)) {
            $parts = explode('/', $name);
            $result = $this->client->repo($name)->readme($parts[0], $parts[1]);
            $readme = base64_decode($result['content']);
            return $readme;
        } else {
            return false;
        }
    }

    public function downloadPackage($name)
    {
        if ($this->isPackageValid($name)) {
            $parts = explode('/', $name);
            $archive = $this->client->repo($name)->archives()->zipball($parts[0], $parts[1]);
            return $archive;
        } else {
            return false;
        }
    }

    public function isPackageValid($name)
    {
        $parts = explode('/', $name);
        if (count($parts) != 2) {
            return false;
        } else {
            return $this->client->repo($name)->contents()->exists($parts[0], $parts[1], 'zarmony.yaml');
        }
    }
}