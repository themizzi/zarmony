<?php

namespace Dotfiles\Command\Package;

use Dotfiles\Package\Repository\GitRepository;
use Github\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class DownloadCommand extends Command
{
    protected function configure()
    {
        $this->setName('package:download');
        $this->setDescription('Download a package.');
        $this->addArgument(
            'name',
            InputArgument::REQUIRED
        );
        $this->addArgument(
            'destination',
            InputArgument::REQUIRED
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = new GitRepository(new Client());
        $archive = $repo->downloadPackage($input->getArgument('name'));
        if ($archive) {
            $fs = new Filesystem();
            $fs->dumpFile($input->getArgument('destination'), $archive);
        } else {
            $output->write('<error>Invalid Repository</error>');
        }
    }
}