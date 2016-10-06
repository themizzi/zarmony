<?php

namespace Dotfiles\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Dotfiles\Application;

class SourceCommand extends Command
{
    protected function configure()
    {
        $this->setName('source');
        $this->setDescription('Source the dotfiles');
        $this->addOption('no-compile', null, InputOption::VALUE_NONE, 'Ignore compiled source');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        if (!$input->getOption('no-compile')) {
            /** @var Application $app */
            $app = $this->getApplication();
            $dotfilesPath = $app->getDotfilesPath();
            $compiledSourcePath = $dotfilesPath . DIRECTORY_SEPARATOR . 'compiled.zsh';
            $fs = new Filesystem();
            if ($fs->exists($compiledSourcePath)) {
                $output->write(file_get_contents($compiledSourcePath));
                return;
            }
        }

        $userConfigPath = getenv('HOME') . '/.zarmony.yaml';
        $yaml = Yaml::parse(file_get_contents($userConfigPath));
        $configYaml = Yaml::parse(file_get_contents($app->getDotfilesPath() . '/zarmony.yaml'));
        $packages = isset($configYaml['dotfiles']['packages']) ? $configYaml['dotfiles']['packages'] : [];
        $packagesPath = $app->getDotfilesPath().'/packages';
        $source = '';
        foreach ($packages as $package) {
            $packagePath = $packagesPath . DIRECTORY_SEPARATOR . $package;
            $packageYamlPath =  $packagePath . DIRECTORY_SEPARATOR . 'zarmony.yaml';
            $packageYaml = Yaml::parse(file_get_contents($packageYamlPath));
            $scripts = $packageYaml['dotfiles']['scripts'];
            foreach ($scripts as $script) {
                $source .= rtrim(file_get_contents($packagePath . DIRECTORY_SEPARATOR . $script), PHP_EOL) . PHP_EOL;
            }
        }
        $output->write($source);

        /** @var \Dotfiles\Command\Script\SourceCommand $command */
        $command = $this->getApplication()->find('script:source');
        $returnCode = $command->run(new ArrayInput([]), $output);

        $command = $this->getApplication()->find('alias:source');
        $returnCode = $command->run(new ArrayInput([]), $output);
    }

}