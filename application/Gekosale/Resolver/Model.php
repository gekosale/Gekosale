<?php

namespace Gekosale\Resolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Registry;
use Gekosale\App;

class Model
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

    public function createModel ($index)
    {
        $name = explode('/', $index);
        $mode = null;
        
        if (isset($name[2])){
            $mode = $name[0];
            $controller = $name[1];
            $model = $name[2];
        }
        else{
            if (isset($name[1])){
                $controller = $name[0];
                $model = $name[1];
            }
            else{
                $controller = $this->baseController;
                $model = $name[0];
            }
        }
        
        $modelFile = $this->getModelFile($controller, $model, $mode);
        
        if ($modelFile === array()){
            return false;
        }
        
        if (count($modelFile) === 2){
            $file = array_shift($modelFile);
            require_once $file;
        }
        
        include_once current($modelFile);
        
        $class = key($modelFile);
        
        if (in_array($class, $this->classesMap)){
            return;
        }
        
        $classNames = explode(DS, $class);
        
        $objClassName = end($classNames) . '/' . $model . 'Model';
        
        if (class_exists($class)){
            try{
                $modelObject = new $class($this->registry, current($modelFile));
                return $this->registry->$objClassName = $modelObject;
            }
            // allegro
            catch (\SoapFault $e){
                throw new \Exception($e->getMessage());
            }
            catch (Exception $e){
                throw new CoreException($e->getMessage());
            }
        }
        else{
            throw new CoreException('Class doesn\'t exists: ' . $class);
        }
    }

    protected function getModelFile ($controller, $model, $mode = null)
    {
        if (! $mode){
            $mode = $this->modeName;
        }
        
        $modes = array(
            $mode,
            $mode = 'Super'
        );
        
        $files = array();
        
        foreach (App::getRegistry()->loader->getNamespaces() as $ns){
            foreach ($modes as $mode){
                $path = $this->path . DS . $ns . DS . $mode . DS . strtolower($model) . DS . 'model' . DS . strtolower($model) . '.php';
                
                if (in_array($path, $this->classesMap)){
                    $files['\\' . $ns . '\\' . $model . 'Model'] = $path;
                    break;
                }
                
                $path = $this->path . DS . $ns . DS . $mode . DS . strtolower($controller) . DS . 'model' . DS . strtolower($model) . '.php';
                
                if (in_array($path, $this->classesMap)){
                    $files['\\' . $ns . '\\' . $model . 'Model'] = $path;
                    break;
                }
            }
        }
        
        return $files;
    }
}
