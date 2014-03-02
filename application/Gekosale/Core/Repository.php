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

use Closure;

/**
 * Class Repository
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Repository extends Component
{
    /**
     * Wraps callback function into DB transaction
     *
     * @param callable $callback
     *
     * @return mixed
     */
    final protected function transaction(Closure $callback)
    {
        return $this->container->get('database_manager')->getConnection()->transaction($callback);
    }
}