<?php

namespace Gekosale\Core\Resolver;
use Gekosale\Core\Resolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Form extends Resolver
{

    const COMPONENT_FOLDER = 'Component';

    public function getForm ($id)
    {
        foreach ($this->getNamespaces() as $namespace){
            $className = $this->getClassName($namespace, $id);
            if (class_exists($className)){
                $component = new $className($this->container);
            }
        }
        
        if (! isset($component)){
            throw new \InvalidArgumentException(sprintf('Component "%s" does not exist.', $id));
        }
        
        return $component;
    }

    protected function getClassName ($namespace, $id)
    {
        return sprintf('%s\%s\%s', $namespace, self::COMPONENT_FOLDER, $id);
    }
}
