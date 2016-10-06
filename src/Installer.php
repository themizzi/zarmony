<?php

namespace Dotfiles;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Installer
{
    protected $input;
    protected $output;
    protected $fs;
    protected $binPath;
    protected $additionalPaths;
    protected $installationPath;
    protected $homeZshrcFile;
    protected $zshrcBackupFile;
    protected $overwriteZshrcBackup;
    protected $io;

    public function __construct(
        SymfonyStyle $io,
        Filesystem $fs,
        $binPath,
        $additionalPaths,
        $installationPath,
        $homeZshrcFile,
        $zshrcBackupFile,
        $overwriteZshrcBackup = false
    ) {
        $this->io = $io;
        $this->fs = $fs;
        $this->binPath = $binPath;
        $this->additionalPaths = $additionalPaths ?: array();
        $this->installationPath = $installationPath;
        $this->homeZshrcFile = $homeZshrcFile;
        $this->zshrcBackupFile = $zshrcBackupFile;
        $this->overwriteZshrcBackup = $overwriteZshrcBackup;
    }

    public function validateInstallation()
    {
        if ($this->fs->exists($this->installationPath)) {
            $this->io->note(
                sprintf(
                    'Cannot install. Installation path %s already exists.',
                    $this->installationPath
                )
            );

            return false;
        } elseif (!empty($this->zshrcBackupFile) && !$this->overwriteZshrcBackup
            && $this->fs->exists($this->zshrcBackupFile)
        ) {
            $this->io->note(
                sprintf(
                    'Cannot install. Zshrc backup file %s already exists.'
                )
            );

            return false;
        } else {
            return true;
        }
    }

    public function install()
    {
        $this->io->title('Installing Dotfiles.');
        if ($this->validateInstallation()) {
            $this->createInstallationPath();
            $this->createConfigurationFile();
            $this->createZshrcFile();
            if ($this->zshrcBackupFile) {
                $this->backupZshrcFile();
            }
            $this->linkZshrcFile();
            $this->io->success('Installation successful.');
        } else {
            $this->io->error('Installation failed.');
        }
    }

    public function createInstallationPath()
    {
        $this->io->text(
            sprintf(
                'Creating installation path %s.',
                $this->installationPath
            )
        );
        $this->fs->mkdir($this->installationPath, 0700);
    }

    public function createConfigurationFile()
    {
        $configurationFile = $this->installationPath.DIRECTORY_SEPARATOR
            .'zarmony.yaml';
        $this->io->text(
            sprintf('Creating configuration file %s.', $configurationFile)
        );
        $this->fs->dumpFile(
            $configurationFile,
            Yaml::dump(['dotfiles' => []])
        );
    }

    public function createZshrcFile()
    {
        $zshrcFile = $this->getZshrcFile();
        $this->io->text(sprintf('Creating zshrc file %s.', $zshrcFile));
        $paths = array_merge([$this->binPath], $this->additionalPaths);
        $zshrc = 'export PATH='.implode(':', $paths).':'.'$PATH'.PHP_EOL;
        $zshrc .= 'dotfiles source | source /dev/stdin'.PHP_EOL;
        $this->fs->dumpFile($zshrcFile, $zshrc);
    }

    public function backupZshrcFile()
    {
        $existingZshrcFile = $this->homeZshrcFile;
        $this->io->text(
            sprintf(
                'Backing up zshrc %s to %s.',
                $existingZshrcFile,
                $this->zshrcBackupFile
            )
        );
        $this->fs->rename($existingZshrcFile, $this->zshrcBackupFile);
    }

    public function linkZshrcFile()
    {
        $zshrcFileLink = $this->homeZshrcFile;
        $zshrcFile = $this->getZshrcFile();
        $this->io->text(
            sprintf('Linking zshrc %s to %s.', $zshrcFile, $zshrcFileLink)
        );
        $this->fs->symlink($zshrcFile, $zshrcFileLink);
    }



    protected function getZshrcFile()
    {
        return $this->installationPath.DIRECTORY_SEPARATOR.'zshrc';
    }
}