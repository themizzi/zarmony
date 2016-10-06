<?php

namespace Dotfiles;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Application extends ConsoleApplication
{
    const ZSHRC_FILENAME = '.zshrc';

    protected $binPath;
    protected $packages;
    protected $config;
    protected $aliases;
    protected $scripts;
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
        parent::__construct('Dotfiles', '0.1.0');
    }


    protected function getDefaultCommands()
    {
        return array_merge(
            parent::getDefaultCommands(),
            [
                new Command\InstallCommand(),
                new Command\UninstallCommand(),
                new Command\SourceCommand(),
                new Command\Package\InstallCommand(),
                new Command\Package\UninstallCommand(),
                new Command\Package\ListCommand(),
                new Command\CompileCommand(),
                new Command\Alias\ListCommand(),
                new Command\Alias\AddCommand(),
                new Command\Alias\SourceCommand(),
                new Command\Script\AddCommand(),
                new Command\Script\ListCommand(),
                new Command\Script\SourceCommand(),
                new Command\Alias\RemoveCommand(),
                new Command\Script\RemoveCommand(),
                new Command\Package\ReadmeCommand(),
                new Command\Package\DownloadCommand()
            ]
        );
    }

    public function getDotfilesPath()
    {
        $userPath = getenv('HOME');
        $zshrcFile = $userPath.DIRECTORY_SEPARATOR.'.zshrc';

        return dirname(readlink($zshrcFile));
    }

    public function getBinPath()
    {
        return $this->binPath;
    }

    public function setBinPath($path)
    {
        $this->binPath = $path;
    }

    public function getHomeZshrcFile()
    {
        return getenv('HOME').DIRECTORY_SEPARATOR.'.zshrc';
    }

    public function getConfig()
    {
        if (!isset($this->config)) {
            $yamlFile = $this->getConfigFile();
            $this->config = Yaml::parse(file_get_contents($yamlFile));
        }

        return $this->config;
    }

    public function getPackages()
    {
        if (!isset($this->packages)) {
            $this->packages = isset($config['dotfiles']['packages'])
                ? $config['dotfiles']['packages'] : [];
        }

        return $this->getPackages();
    }

    public function getAliases()
    {
        if (!isset($this->aliases)) {
            $config = $this->getConfig();
            $this->aliases = isset($config['dotfiles']['aliases'])
                ? $config['dotfiles']['aliases'] : [];
            uasort(
                $this->aliases,
                function ($a, $b) {
                    return $a['priority'] - $b['priority'];
                }
            );
        }

        return $this->aliases;
    }

    public function getScripts()
    {
        if (!isset($this->scripts)) {
            $config = $this->getConfig();
            $this->scripts = isset($config['dotfiles']['scripts'])
                ? $config['dotfiles']['scripts'] : [];
            uasort(
                $this->scripts,
                function ($a, $b) {
                    return $a['priority'] - $b['priority'];
                }
            );
        }

        return $this->scripts;
    }

    public function addAlias(
        $name,
        $replacement,
        $description,
        $tags = [],
        $priority = 0
    ) {
        $aliases = $this->getAliases();
        if (array_key_exists($name, $aliases)) {
            return false;
        } else {
            $aliases[$name] = [
                'replacement' => $replacement,
                'description' => $description,
                'tags'        => $tags,
                'priority'    => $priority,
            ];
            $config = $this->getConfig();
            $config['dotfiles']['aliases'] = $aliases;
            $this->saveConfig($config);

            return true;
        }
    }

    public function removeAlias($name)
    {
        $aliases = $this->getAliases();
        if (array_key_exists($name, $aliases)) {
            unset($aliases[$name]);
            $config = $this->getConfig();
            $config['dotfiles']['aliases'] = $aliases;
            $this->saveConfig($config);
            return false;
        } else {
            return true;
        }
    }

    public function addScript(
        $file,
        $description,
        $tags = [],
        $priority = 0,
        $absolute = false
    ) {
        $scripts = $this->getScripts();
        if (array_key_exists($file, $scripts)) {
            return false;
        } else {
            $scripts[$file] = [
                'description' => $description,
                'tags'        => $tags,
                'priority'    => $priority,
                'absolute'    => $absolute,
            ];
            $config = $this->getConfig();
            $config['dotfiles']['scripts'] = $scripts;
            $this->saveConfig($config);

            return true;
        }
    }

    public function removeScript($file)
    {
        $scripts = $this->getScripts();
        if (array_key_exists($file, $scripts)) {
            unset($scripts[$file]);
            $config = $this->getConfig();
            $config['dotfiles']['scripts'] = $scripts;
            $this->saveConfig($config);
            return true;
        } else {
            return false;
        }
    }

    public function saveConfig(array $config)
    {
        $this->fs->dumpFile(
            $this->getConfigFile(),
            Yaml::dump($config)
        );
    }

    public function getConfigFile()
    {
        return $this->getDotfilesPath().DIRECTORY_SEPARATOR.'zarmony.yaml';
    }

    public function addPackage($package)
    {
        $config = $this->getConfig();
        $config['dotfiles']['packages'][] = $package;
        $this->saveConfig($config);
    }
}