<?php
namespace Dotfiles\Command\Alias;

use Dotfiles\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SourceCommand extends Command
{
    protected function configure()
    {
        $this->setName('alias:source');
        $this->setDescription('Output source for all aliases');
        $this->addOption(
            'tag',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Tags to list'
        );
        $this->addOption(
            'match-all',
            null,
            InputOption::VALUE_NONE,
            'Match all tag instead of any tags'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        $aliases = $app->getAliases();
        $tags = $input->getOption('tag');
        foreach($aliases as $name => $alias) {
            $match = false;
            if (!empty($tags)) {
                $count = count(array_intersect($tags, $alias['tags']));
                if ($input->getOption('match-all')) {
                    $match = $count == count($tags);
                } else {
                    $match = (bool)$count;
                }
            }
            if (empty($tags) || $match) {
                $output->writeln(
                    sprintf(
                        'alias %s=%s',
                        $name,
                        escapeshellarg($alias['replacement'])
                    )
                );
            }
        }
    }
}