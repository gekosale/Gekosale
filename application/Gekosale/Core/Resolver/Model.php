<?php

namespace Gekosale\Core\Resolver;
use Gekosale\Core\Resolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Model extends Resolver
{

    const COMPONENT_FOLDER = 'Component';

    public function getModel ($id)
    {
        foreach ($this->getNamespaces() as $namespace){
            $className = $this->getClassName($namespace, $id);
            if (class_exists($className)){
                $component = new $className();
                if ($component instanceof ContainerAwareInterface){
                    $component->setContainer($this->container);
                }
            }
        }
        
        if (! isset($component)){
            throw new \InvalidArgumentException(sprintf('Model "%s" does not exist.', $id));
        }
        
        return $component;
    }

    protected function getClassName ($namespace, $id)
    {
        return sprintf('%s\%s\%s', $namespace, self::COMPONENT_FOLDER, $id);
    }
}
