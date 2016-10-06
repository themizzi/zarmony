<?php
namespace Dotfiles\Command\Alias;

use Dotfiles\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends Command
{
    public function configure()
    {
        $this->setName('alias:list');
        $this->setDescription('List aliases');
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
        $io = new SymfonyStyle($input, $output);
        $io->title('Listing all aliases');
        $aliases = $app->getAliases();
        $tags = $input->getOption('tag');
        $rows = [];
        foreach ($aliases as $name => $alias) {
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
                $rows[] = [
                    $name,
                    $alias['replacement'],
                    $alias['description'],
                    implode(',', $alias['tags']),
                    $alias['priority']
                ];
            }
        }
        $io->table(
            [
                'Name',
                'Replacement',
                'Description',
                'Tags',
                'Priority'
            ],
            $rows
        );
    }

}