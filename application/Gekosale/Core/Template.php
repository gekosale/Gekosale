<?php

namespace Gekosale\Core;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Loader_String;
use Twig_Filter_Function;
use Twig_Function_Function;
use Twig_Extensions_Extension_Intl;
use Twig_Extension_Optimizer;
use Twig_NodeVisitor_Optimizer;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;

class Template
{

    private static $translations = Array();

    private static $generator = NULL;

    private static $layerData;

    public function __construct ($registry, $mode, $container)
    {
        $this->registry = $registry;
        $this->container = $container;
        $this->parameters = array();
        $this->themePath = $registry->loader->getParam('theme');
        $namespaces = array_reverse(array_unique($registry->loader->getNamespaces()));
        $designPaths = Array();
        
        if ($mode == 1){
            foreach ($namespaces as $namespace){
                if (! is_dir(ROOTPATH . 'design' . DS . 'admin' . DS . ucfirst($namespace))){
                    mkdir(ROOTPATH . 'design' . DS . 'admin' . DS . ucfirst($namespace), 0755);
                }
                $designPaths[] = ROOTPATH . 'design' . DS . 'admin' . DS . ucfirst($namespace) . DS;
                $designPaths[] = ROOTPATH . 'themes' . DS;
            }
        }
        else{
            if (strlen($this->themePath) > 0 && is_dir(ROOTPATH . 'themes' . DS . $this->themePath . DS . 'templates' . DS)){
                $designPaths[] = ROOTPATH . 'themes' . DS . $this->themePath . DS . 'templates' . DS;
            }
            $designPaths[] = ROOTPATH . 'themes' . DS;
            $designPaths[] = ROOTPATH . 'design' . DS . 'frontend';
        }
        
        $this->template = new Twig_Environment(new Twig_Loader_Filesystem($designPaths), array(
            'cache' => ROOTPATH . 'cache' . DS,
            'auto_reload' => true,
            'autoescape' => false
        ));
        
        $this->template->addFilter('priceFormat', new Twig_Filter_Function('Gekosale\Template::priceFormat'));
        $this->template->addFunction('css_layout', new Twig_Function_Function('Gekosale\Template::getLayoutCSS'));
        $this->template->addFunction('css_namespace', new Twig_Function_Function('Gekosale\Template::getNamespaceCSS'));
        $this->template->addFunction('css_asset', new Twig_Function_Function('Gekosale\Template::getCSSAsset'));
        $this->template->addFunction('recommendations', new Twig_Function_Function('Gekosale\Template::getRecommendations'));
        $this->template->addExtension(new Twig_Extensions_Extension_Intl());
        $this->template->addExtension(new TranslationExtension(new Translation()));
        $this->template->addExtension(new RoutingExtension($this->container->get('router')->getGenerator()));
        $this->template->addExtension(new Twig_Extension_Optimizer(Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL));
    }

    public static function getRecommendations ($limit, $view = 0, $slot = '')
    {
        return App::getModel('recommendations')->getBlock($limit, $view, $slot);
    }

    public static function priceFormat ($price, $currency = Array())
    {
        $price = (float)str_replace(',', '.', $price);
        if (NULL === self::$layerData){
            self::$layerData = App::getRegistry()->loader->getCurrentLayer();
        }
        
        if ($price < 0){
            return (self::$layerData['negativepreffix'] . number_format(abs($price), self::$layerData['decimalcount'], self::$layerData['decimalseparator'], self::$layerData['thousandseparator']) . self::$layerData['negativesuffix']);
        }
        return (self::$layerData['positivepreffix'] . number_format($price, self::$layerData['decimalcount'], self::$layerData['decimalseparator'], self::$layerData['thousandseparator']) . self::$layerData['positivesuffix']);
    }

    public static function getLayoutCSS ()
    {
        $viewid = Helper::getViewId();
        $namespaces = array_unique(App::getRegistry()->loader->getNamespaces());
        foreach ($namespaces as $namespace){
            if (file_exists(ROOTPATH . 'design' . DS . '_css_frontend' . DS . $namespace . DS . $viewid . '.css')){
                return DESIGNPATH . '_css_frontend' . '/' . $namespace . '/' . $viewid . '.css';
            }
        }
        return DESIGNPATH . '_css_frontend' . '/Gekosale/' . 'gekosale.css';
    }

    public static function getNamespaceCSS ($css_file, $mode)
    {
        switch ($mode) {
            case 'admin':
                $mode = '_css_panel';
                break;
            case 'frontend':
            default:
                $mode = '_css_frontend';
                break;
        }
        
        $namespace = App::getRegistry()->loader->getCurrentNamespace();
        if (file_exists(ROOTPATH . DS . 'design' . DS . $mode . DS . $namespace . DS . $css_file)){
            return DESIGNPATH . $mode . '/' . $namespace . '/' . $css_file;
        }
        return DESIGNPATH . $mode . '/core/' . $css_file;
    }

    public static function getCSSAsset ($css_file)
    {
        $theme = App::getRegistry()->loader->getParam('theme');
        return App::getURLForAssetDirectory() . $theme . '/assets/' . $css_file;
    }

    public function assign ($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function display ($template)
    {
        return $this->template->display($template, $this->parameters);
    }

    public function fetch ($template)
    {
        return $this->template->render($template, $this->parameters);
    }

    public function parse ($content)
    {
        $twig = new Twig_Environment(new Twig_Loader_String());
        $twig->addFilter('priceFormat', new Twig_Filter_Function('Gekosale\Template::priceFormat'));
        $twig->addFunction('path', new Twig_Function_Function('Gekosale\Template::getPathFromRoute'));
        $twig->addExtension(new TranslationExtension(new Translation()));
        return $twig->render($content, $this->parameters);
    }

    public function render ($parameters = array())
    {
        return $this->template->render(array_merge($this->parameters, $parameters));
    }

    public function setStaticTemplateVariables ()
    {
        if ($this->registry->router->getAdministrativeMode() == 1){
            $this->registry->core->setAdminStoreConfig();
        }
        
        $templateData = App::getModel('templatedata')->getTemplateData();
        
        $Data = Event::dispatch($this, 'template.setStaticTemplateVariables', Array(
            'data' => $templateData
        ));
        
        foreach ($Data as $param => $value){
            $this->assign($param, $value);
        }
        
        $methods = App::getModel('templatedata')->getXajaxMethods();
        
        foreach ($methods as $xajaxMethodName => $xajaxMethodParams){
            $this->registry->xajax->registerFunction(array(
                $xajaxMethodName,
                App::getModel($xajaxMethodParams['model']),
                $xajaxMethodParams['method']
            ));
        }
    }
}