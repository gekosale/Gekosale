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
use Doctrine\Common\Annotations\AnnotationReader;

class Model extends Resolver implements ResolverInterface
{

    public function create ($class)
    {
        if (! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Model "%s" does not exist.', $class));
        }
        
        $model = new $class();
        
        $annotationReader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($class);
        $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
        
        if ($model instanceof ContainerAwareInterface) {
            $model->setContainer($this->container);
        }
        
        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof \Gekosale\Core\Datagrid) {
                $datagrid = new $annotation->model();
                if ($datagrid instanceof ContainerAwareInterface) {
                    $datagrid->setContainer($this->container);
                }
                echo $annotation->alias;
                $model->$annotation->alias = $datagrid;
            }
        }
        
        return $model;
    }
}
