<?php

namespace Dotfiles\Command;

use Dotfiles\Application;
use Dotfiles\Installer;
use SebastianBergmann\CodeCoverage\Node\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class InstallCommand extends Command
{
    protected $fs;

    public function __construct($name = null)
    {
        $this->fs = new Filesystem();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install dotfiles');
        $this->addArgument(
            'path',
            InputArgument::OPTIONAL,
            'Path to install dotfiles into',
            getenv('HOME').DIRECTORY_SEPARATOR.'.dotfiles'
        );
        $this->addArgument(
            'zshrc-backup-file',
            InputArgument::OPTIONAL,
            'Path to save zshrc backup to',
            getenv('HOME').DIRECTORY_SEPARATOR.'.zshrc.bak'
        );
        $this->addOption(
            'no-backup',
            null,
            InputOption::VALUE_NONE,
            'Do not backup existing .zshrc file'
        );
        $this->addOption(
            'overwrite-zshrc-backup',
            null,
            InputOption::VALUE_NONE,
            'Overwrite an existing .zshrc backup file'
        );
        $this->addOption(
            'additional-path',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Additional paths to add to .zshrc on startup'
        );
        $this->setHelp('Configures your system to use dotfiles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        $installationPath = rtrim(
            $input->getArgument('path'),
            DIRECTORY_SEPARATOR
        );
        $zshrcBackupFile = $input->getOption('no-backup')
            ? null
            : rtrim(
                $input->getArgument('zshrc-backup-file'),
                DIRECTORY_SEPARATOR
            );
        $installer = new Installer(
            new SymfonyStyle($input, $output),
            new Filesystem(),
            $app->getBinPath(),
            $input->getOption('additional-path'),
            $installationPath,
            $app->getHomeZshrcFile(),
            $zshrcBackupFile,
            $input->getOption('overwrite-zshrc-backup')
        );
        $installer->install();
    }

    protected function getZshrcBackupPath(InputInterface $input)
    {
        return $input->getOption('zshrc-backup-file') ?: '.zshrc.bak';
    }
}