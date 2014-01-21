<?php

namespace Gekosale\Resolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Registry;
use Gekosale\App;

class Form
{

    protected $container;

    protected $mode;

    protected $action;

    protected $namespaces;

    protected $baseController;

    public function __construct (ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->classesMap = $this->container->get('classmapper')->getClassMap();
        $this->baseController = App::getRegistry()->router->getBaseController();
        $this->modeName = App::getRegistry()->router->getModeName();
        $this->path = App::getRegistry()->router->getPath();
        $this->registry = App::getRegistry();
    }

    public function getModel ($index)
    {
        $serviceId = $index . 'Form';
        
        if ($this->container->hasDefinition($serviceId)){
            return $this->container->get($serviceId);
        }
        
        return $this->createModel($index);
    }

    protected function createModel ($index)
    {
        $name = explode('/', $index);
        if (isset($name[1])){
            $controller = $name[0];
            $model = $name[1];
        }
        else{
            $controller = $this->baseController;
            $model = $name[0];
        }
        $modelFile = $this->getFormModelFile($controller, $model);
        if ($modelFile === array()){
            return false;
        }
        
        if (count($modelFile) === 2){
            $file = array_shift($modelFile);
            require_once $file;
        }
        
        require_once current($modelFile);
        
        $class = key($modelFile);
        
        if (in_array($class, $this->classesMap)){
            return;
        }
        
        $classPath = explode(DS, $class);
        $objClassName = end($classPath) . '/' . $model . 'Form';
        if (class_exists($class)){
            try{
                return $this->registry->$objClassName = new $class($this->registry, current($modelFile));
            }
            catch (Exception $e){
                throw new CoreException($e->getMessage());
            }
        }
        else{
            throw new CoreException('Class doesn\'t exists: ' . $class);
        }
    }

    protected function getFormModelFile ($controller, $model)
    {
        $mode = $this->modeName;
        
        $files = array();
        
        foreach ($this->registry->loader->getNamespaces() as $ns){
            $path = $this->path . DS . $ns . DS . $mode . DS . strtolower($model) . DS . 'form' . DS . strtolower($model) . '.php';
            
            if (in_array($path, $this->classesMap)){
                $files['\\' . $ns . '\\' . $model . 'Form'] = $path;
                continue;
            }
            
            $path = $this->path . DS . $ns . DS . $mode . DS . strtolower($controller) . DS . 'form' . DS . strtolower($model) . '.php';
            
            if (in_array($path, $this->classesMap)){
                $files['\\' . $ns . '\\' . $model . 'Form'] = $path;
                continue;
            }
        }
        
        return $files;
    }
}
