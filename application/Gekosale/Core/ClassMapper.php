<?php

namespace Gekosale\Core;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Core\App;

class ClassMapper extends ContainerAware
{

    protected $classMap = Array();

    protected $classMapFile = 'classesmap.reg';

    protected $classMapPath;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct (ContainerInterface $container = NULL)
    {
        $this->container = $container;
        
        $this->classMapPath = ROOTPATH . 'serialization' . DS . $this->classMapFile;
        
        $this->classMap = @ file_get_contents($this->classMapPath);
        
        $this->classMap = unserialize($this->classMap);
    }

    public function getClassMap ()
    {
        return $this->classMap;
    }

    public function getClassMapFile ($id)
    {
        if (isset($this->classMap[$id])){
            return $this->classMap[$id];
        }
        return NULL;
    }
    
}