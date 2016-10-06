<?php

namespace Dotfiles\Command\Package;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class UninstallCommand extends Command
{
    protected function configure()
    {
        $this->setName('package:uninstall')
            ->setDescription('Uninstall a dotfiles package')
            ->setDefinition(
                [
                    new InputArgument(
                        'package',
                        InputArgument::REQUIRED,
                        'Package to uninstall.'
                    )
                ]
            )
            ->setHelp('Uninstalls a dotfiles package');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $package = $input->getArgument('package');
        $fs = new Filesystem();
        $userConfigYaml = Yaml::parse(
            file_get_contents(getenv('HOME').'/.zarmony.yaml')
        );
        $configPath = $userConfigYaml['dotfiles']['path'].'/zarmony.yaml';
        $packages = isset($userConfigYaml['dotfiles']['packages'])
            ? $userConfigYaml['dotfiles']['packages'] : [];
        $key = array_search($package, $packages);
        if ($key === false) {
            $output->writeln(
                sprintf(
                    '<error>Package %s is not installed</error>',
                    $package
                )
            );

            return 1;
        }
        $output->writeln(sprintf('<comment>Uninstalling package %s.</comment>', $package));
        unset($packages[$key]);
        $userConfigYaml['dotfiles']['packages'] = $packages;
        $output->writeln(
            sprintf(
                '<comment>Writing new config to %s.</comment>',
                $configPath
            )
        );
        $fs->dumpFile($configPath, Yaml::dump($userConfigYaml));
    }
}