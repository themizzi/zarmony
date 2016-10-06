<?php

use Dotfiles\Package\RepositoryInterface;

class Package
{
    protected $name;
    protected $description;
    protected $author;
    protected $version;
    protected $repository;

    public function __construct(
        $name,
        $description,
        $author,
        $version,
        RepositoryInterface $repository
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->author = $author;
        $this->version = $version;
        $this->repository = $repository;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function download()
    {
        return $this->repository->downloadPackage($this->name);
    }
}