<?php
namespace Dotfiles\Package;

interface RepositoryInterface
{
    public function getPackages();
    public function searchPackages($search);
    public function getPackage($name);
    public function downloadPackage($name);
    public function getPackageReadme($name);
    public function isPackageValid($name);
}