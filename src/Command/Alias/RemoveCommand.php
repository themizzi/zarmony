<?php
namespace Dotfiles\Command\Alias;

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
        $this->setName('alias:remove');
        $this->setDescription('Remove an alias');
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Name of the alias'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Removing alias.');
        /** @var Application $app */
        $app = $this->getApplication();
        $name = $input->getArgument('name');
        if ($app->removeAlias($name)) {
            $io->success(sprintf('Removed alias %s.', $name));
        } else {
            $io->error(sprintf('Alias %s not found', $name));
        }
    }

}