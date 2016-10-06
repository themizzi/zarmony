<?php
namespace Dotfiles\Command\Script;

use Dotfiles\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveCommand extends Command
{
    public function configure()
    {
        $this->setName('script:remove');
        $this->setDescription('Remove an script');
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Script file'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Removing script.');
        /** @var Application $app */
        $app = $this->getApplication();
        $file = $input->getArgument('file');
        if ($app->removeScript($file)) {
            $io->success(sprintf('Removed script %s.', $file));
        } else {
            $io->error(sprintf('Script %s not found.', $file));
        }
    }

}