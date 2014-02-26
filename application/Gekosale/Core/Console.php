<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core;
use Symfony\Component\Console\Application;

/**
 * Class Console
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Console extends Application
{

    public function __construct ()
    {
        parent::__construct('Welcome to Gekosale CLI Tool', '1.0');
        
        $this->addCommands(array(
            new Console\Command\Routes\Dump()
        ));
    }
}