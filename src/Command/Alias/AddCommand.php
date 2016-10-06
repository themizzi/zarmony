<?php
namespace Dotfiles\Command\Alias;

use Dotfiles\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddCommand extends Command
{
    public function configure()
    {
        $this->setName('alias:add');
        $this->setDescription('Add a new alias');
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Name of the alais'
        );
        $this->addArgument(
            'replacement',
            InputArgument::REQUIRED,
            'Replacement the alias runs'
        );
        $this->addArgument(
            'description',
            InputArgument::OPTIONAL,
            'Description of the alias'
        );
        $this->addOption(
            'tag',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Alias tags'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Adding new alias.');
        /** @var Application $app */
        $app = $this->getApplication();
        $name = $input->getArgument('name');
        $app->addAlias(
            $name,
            $input->getArgument('replacement'),
            $input->getArgument('description'),
            $input->getOption('tag')
        );
        $io->success(sprintf('Added new alias %s.', $name));
    }

}