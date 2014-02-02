<?php

namespace Gekosale\Core\Resolver;

use Gekosale\Core\Resolver;

class Resolver extends Resolver implements ResolverInterface
{

    public function create ($class)
    {
        if (! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Resolver "%s" does not exist.', $class));
        }
        
        $form = new $class($this->container);
        
        return $form;
    }
}
