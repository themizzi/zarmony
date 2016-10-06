<?php

namespace Dotfiles\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class CompileCommand extends Command
{
    protected function configure()
    {
        $this->setName('compile');
        $this->setDescription('Compile dotfiles.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userConfigPath = getenv('HOME') . '/.zarmony.yaml';
        $yaml = Yaml::parse(file_get_contents($userConfigPath));
        $configPath = $yaml['dotfiles']['path'];

        $configYaml = Yaml::parse(file_get_contents($configPath . '/zarmony.yaml'));
        $packages = isset($configYaml['dotfiles']['packages']) ? $configYaml['dotfiles']['packages'] : [];
        $packagesPath = realpath(dirname(__FILE__) . '/../../../packages');
        $source = '';
        foreach ($packages as $package) {
            $packagePath = $packagesPath . DIRECTORY_SEPARATOR . $package;
            $packageYamlPath =  $packagePath . DIRECTORY_SEPARATOR . 'zarmony.yaml';
            $packageYaml = Yaml::parse(file_get_contents($packageYamlPath));
            $bootstrap = isset($packageYaml['dotfiles']['bootstrap']) ? $packageYaml['dotfiles']['bootstrap'] : 'source.zsh';
            $source .= file_get_contents($packagePath . DIRECTORY_SEPARATOR . $bootstrap);
        }
        $fs = new Filesystem();
        $dotfilesPath = dirname(readlink($userConfigPath));
        $compiledSourcePath = $dotfilesPath . DIRECTORY_SEPARATOR . 'compiled.zsh';
        $fs->dumpFile($compiledSourcePath, $source);
        $output->writeln(sprintf('<info>Wrote compiled source to %s.</info>', $compiledSourcePath));
    }

}