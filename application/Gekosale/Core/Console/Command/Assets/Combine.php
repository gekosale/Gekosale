<?php

namespace Gekosale\Core\Console\Command\Assets;

use Gekosale\Core\Console\Command\AbstractCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Factory\AssetFactory;
use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;

/**
 * Class Dump
 *
 * @package Gekosale\Core\Console\Command\Routes
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Combine extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('assets:combine');

        $this->setDescription('Dumps routes into one optimized file');

        $this->setHelp(sprintf('%Dumps routes into one optimized file.%s', PHP_EOL, PHP_EOL));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $designPaths   = [];
        $designPaths[] = ROOTPATH . 'design' . DS . 'templates';
        $factory       = new AssetFactory(ROOTPATH . 'design');
        $templates     = ['layout.twig'];
        $am            = new LazyAssetManager($factory);
        $loader        = new \Twig_Loader_Filesystem($designPaths);
        $engine        = new \Twig_Environment($loader, [
            'cache'       => ROOTPATH . 'cache' . DS,
            'auto_reload' => true,
            'autoescape'  => false
        ]);

        $engine->addExtension(new AsseticExtension($factory));
        $am->setLoader('twig', new TwigFormulaLoader($engine));
        foreach ($templates as $template) {
            $resource = new TwigResource($loader, $template);
            $am->addResource($resource, 'twig');
        }
        print_r($am);
        $writer = new AssetWriter(ROOTPATH);
        $writer->writeManagerAssets($am);

        $out = sprintf('%sFinished dumping routes.%s', PHP_EOL, PHP_EOL);

        $output->write($out);
    }
}