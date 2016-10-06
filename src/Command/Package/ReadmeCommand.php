<?php

namespace Dotfiles\Command\Package;

use Dotfiles\Package\Repository\GitRepository;
use Github\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadmeCommand extends Command
{
    protected function configure()
    {
        $this->setName('package:readme');
        $this->setDescription('Show the readme for a package.');
        $this->addArgument(
            'name',
            InputArgument::REQUIRED
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = new GitRepository(new Client());
        $readme = $repo->getPackageReadme($input->getArgument('name'));
        if ($readme) {
            $output->write($readme);
        } else {
            $output->write('<error>Invalid Repository</error>');
        }
    }
}