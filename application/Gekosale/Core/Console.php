<?php

/**
 *
 * WellCommerce
 *
 * @copyright   Copyright (c) 2012-2013 WellCommerce
 * @author      Adam Piotrowski, apiotrowski@wellcommerce.pl
 */
namespace Gekosale\Core;
use Gekosale\Core\App;
use Symfony\Component\Console\Application;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Console extends Application
{

    public function __construct ()
    {
        parent::__construct('Welcome to WellCommerce CLI Tool', '1.0');
        
        $this->addCommands(array(
            new Console\Command\Cache\Clear(),
            new Console\Command\Admin\Add(),
            new Console\Command\Admin\Reset(),
            new Console\Command\Propel\Build(),
            new Console\Command\Propel\Diff(),
            new Console\Command\Propel\Reverse(),
            new Console\Command\Propel\Migration(),
            new Console\Command\Migrate\Add(),
            new Console\Command\Migrate\Up(),
            new Console\Command\Tests\Run()
        ));
    }
}