<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
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
            new Console\Command\Admin\Add(),
            new Console\Command\Admin\Reset(),
            new Console\Command\Migrate\Add(),
            new Console\Command\Migrate\Up(),
            new Console\Command\Routes\Dump()
        ));
    }
}