<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core 
 * @subpackage  Gekosale\Core\Resolver
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Resolver;

use Gekosale\Core\Resolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Datagrid extends Resolver implements ResolverInterface
{

    public function create ($class)
    {
        if (! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Datagrid "%s" does not exist.', $class));
        }
        
        $datagrid = new $class();
        if ($datagrid instanceof ContainerAwareInterface) {
            $datagrid->setContainer($this->container);
        }
        
        $datagrid->initDatagrid();
        
        return $datagrid;
    }
}
