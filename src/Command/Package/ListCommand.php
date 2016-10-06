<?php

namespace Dotfiles\Command\Package;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ListCommand extends Command
{
    protected function configure()
    {
        $this->setName('package:list')
            ->setDescription('List installed packages')
            ->setHelp('Lists installed dotfiles package');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userConfigYaml = Yaml::parse(file_get_contents(getenv('HOME') . '/.zarmony.yaml'));
        $packages = isset($userConfigYaml['dotfiles']['packages']) ? $userConfigYaml['dotfiles']['packages'] : [];
        $rows = [];
        foreach ($packages as $package) {
            $rows[] = [$package];
        }
        if (count($packages)) {
            $table = new Table($output);
            $table->setHeaders(['Package']);
            $table->setRows($rows);
            $table->render();
        } else {
            $output->writeln('<info>There are no packages installed.</info>');
        }
    }
}