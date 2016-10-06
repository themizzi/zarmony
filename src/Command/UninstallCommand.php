<?php

namespace Dotfiles\Command;

use Dotfiles\Application;
use Dotfiles\Uninstaller;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class UninstallCommand extends Command
{
    protected function configure()
    {
        $app = $this->getApplication();
        $this->setName('uninstall');
        $this->setDescription('Uninstall dotfiles');
        $this->setHelp('Uninstalls dotfiles from your system');
        $this->addArgument(
            'zshrc-backup-file',
            InputArgument::OPTIONAL,
            'Backup file to restore',
            getenv('HOME').DIRECTORY_SEPARATOR.'.zshrc.bak'
        );
        $this->addOption(
            'no-restore',
            null,
            InputOption::VALUE_NONE,
            'Do not restore backup file'
        );
        $this->addOption(
            'preserve-zshrc-backup',
            null,
            InputOption::VALUE_NONE,
            'Preserve the zshrc backup file'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        $zshrcBackupFile = $input->getOption('no-restore') ? null
            : $input->getArgument('zshrc-backup-file');
        $uninstaller = new Uninstaller(
            new SymfonyStyle($input, $output),
            new Filesystem(),
            $app->getHomeZshrcFile(),
            $zshrcBackupFile,
            $input->getOption('preserve-zshrc-backup')
        );
        $uninstaller->uninstall();
    }
}