<?php
namespace Dotfiles;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class Uninstaller
{
    protected $io;
    protected $fs;
    protected $homeZshrcFile;
    protected $zshrcBackupFile;
    protected $preserveZshrcBackupFile;

    public function __construct(
        SymfonyStyle $io,
        Filesystem $fs,
        $homeZshrcFile,
        $zshrcBackupFile,
        $preserveZshrcBackupFile = false
    ) {
        $this->io = $io;
        $this->fs = $fs;
        $this->homeZshrcFile = $homeZshrcFile;
        $this->zshrcBackupFile = $zshrcBackupFile;
        $this->preserveZshrcBackupFile = $preserveZshrcBackupFile;
    }

    public function uninstall()
    {
        $this->io->title('Uninstalling Dotfiles.');
        if ($this->validateUninstallion()) {
            $this->unlinkZshrcFile();
            if ($this->zshrcBackupFile) {
                $this->restoreZshrcBackupFile();
            }
            $this->io->success('Uninstallation successful.');
        } else {
            $this->io->error('Uninstallation failed.');
        }
    }

    protected function validateUninstallion()
    {
        if (!is_link($this->homeZshrcFile)) {
            $this->io->note(
                sprintf(
                    'Cannot uninstall. %s is not a link.',
                    $this->homeZshrcFile
                )
            );

            return false;
        } elseif ($this->zshrcBackupFile
            && !$this->fs->exists(
                $this->zshrcBackupFile
            )
        ) {
            $this->io->note(
                sprintf(
                    'Backup zshrc file %s does not exist.',
                    $this->zshrcBackupFile
                )
            );

            return false;
        } else {
            return true;
        }
    }

    protected function unlinkZshrcFile()
    {
        $this->io->text(sprintf('Unlinking zshrc %s.', $this->homeZshrcFile));
        $this->fs->remove($this->homeZshrcFile);
    }

    protected function restoreZshrcBackupFile()
    {
        if ($this->preserveZshrcBackupFile) {
            $this->io->text(sprintf('Restoring backup zsrhc %s and preserving.', $this->zshrcBackupFile));
            $this->fs->copy($this->zshrcBackupFile, $this->homeZshrcFile);
        } else {
            $this->io->text(sprintf('Restoring backup zsrhc %s.', $this->zshrcBackupFile));
            $this->fs->rename($this->zshrcBackupFile, $this->homeZshrcFile);
        }
    }
}