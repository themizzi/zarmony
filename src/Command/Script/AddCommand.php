<?php
namespace Dotfiles\Command\Script;

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
        $this->setName('script:add');
        $this->setDescription('Add a new script');
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'File of the script'
        );
        $this->addArgument(
            'description',
            InputArgument::OPTIONAL,
            'Description of the alias'
        );
        $this->addArgument(
            'priority',
            InputArgument::OPTIONAL,
            'Priority to load the script',
            0
        );
        $this->addOption(
            'tag',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Alias tags'
        );
        $this->addOption(
            'absolute',
            null,
            InputOption::VALUE_NONE,
            'Path is absolute'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Adding new script.');
        /** @var Application $app */
        $app = $this->getApplication();
        $file = $input->getArgument('file');
        if ($app->addScript(
            $file,
            $input->getArgument('description'),
            $input->getOption('tag'),
            $input->getArgument('priority'),
            $input->getOption('absolute')
        )
        ) {
            $io->success(sprintf('Added new script %s.', $file));
        } else {
            $io->error(sprintf('Script %s already exists.', $file));
        };

    }

}