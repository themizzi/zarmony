<?php

namespace Dotfiles\Command\Package;

use Dotfiles\Application;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    /** @var SymfonyStyle */
    protected $io;

    /** @var  ProgressBar */
    protected $pb;

    protected function configure()
    {
        $this->setName('package:install')
            ->setDescription('Install a dotfiles package')
            ->setDefinition(
                [
                    new InputArgument(
                        'package',
                        InputArgument::REQUIRED,
                        'Package to install.'
                    ),
                ]
            )
            ->setHelp('Installs a dotfiles package');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        $package = $input->getArgument('package');
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Installing Package.');
        $file = uniqid().'.zip';
        $this->downloadPackage($package, $file);
        $this->extractFile($package, $file);
        $app->addPackage($package);
    }

    protected function downloadPackage($name, $file)
    {
        /** @var Application $app */
        $app = $this->getApplication();

        $client = new Client();
        $client->request(
            'GET',
            sprintf('https://api.github.com/repos/%s/zipball', $name),
            [
                'sink'     => $app->getDotfilesPath().DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$file,
                'on_headers' => function(ResponseInterface $response) {
                    $this->pb = $this->io->createProgressBar($response->getHeaderLine('Content-Length'));
                },
                'progress' => function (
                    $downloadTotal,
                    $downloadedBytes,
                    $uploadTotal,
                    $uploadedBytes
                ) {
                    if ($downloadTotal > 0) {
                        $this->pb->setProgress($downloadedBytes);
                    }
                },
            ]
        );
        $this->pb->finish();
        $this->io->writeln('');
    }

    public function extractFile($package, $file)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        $archive = new \ZipArchive();
        $archive->open($app->getDotfilesPath().DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$file);
        $path = $app->getDotfilesPath().DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0700, true);
        }
        $extratedPath = $archive->getNameIndex(0);
        $archive->extractTo($path);
        $archive->close();
        $fs = new Filesystem(new Local($app->getDotfilesPath()));
        $fs->delete('tmp'.DIRECTORY_SEPARATOR.$file);
        $fs->deleteDir('packages'.DIRECTORY_SEPARATOR.$package);
        $fs->rename('tmp'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$extratedPath, 'packages'.DIRECTORY_SEPARATOR.$package);
        $fs->deleteDir('tmp'.DIRECTORY_SEPARATOR.$package);
    }
}