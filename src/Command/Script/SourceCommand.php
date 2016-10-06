<?php
namespace Dotfiles\Command\Script;

use Dotfiles\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SourceCommand extends Command
{
    protected function configure()
    {
        $this->setName('script:source');
        $this->setDescription('Output source for all scripts');
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
        $scripts = $app->getScripts();
        $tags = $input->getOption('tag');
        foreach($scripts as $file => $script) {
            $match = false;
            if (!empty($tags)) {
                $count = count(array_intersect($tags, $script['tags']));
                if ($input->getOption('match-all')) {
                    $match = $count == count($tags);
                } else {
                    $match = (bool)$count;
                }
            }
            if (empty($tags) || $match) {
                if ($script['absolute']) {
                    $path = $file;
                } else {
                    $path = $app->getDotfilesPath().DIRECTORY_SEPARATOR.$file;
                }
                $source = file_get_contents($path);
                if ($source) {
                    $output->write(rtrim($source, PHP_EOL).PHP_EOL);
                }
            }
        }
    }


}