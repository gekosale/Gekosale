<?php

namespace Gekosale\Core\Template\Extension;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Routing extends \Twig_Extension
{

    private $router;

    private $generator;

    public function __construct (Router $router)
    {
        $this->router = $router;
        $this->generator = $router->getGenerator();
    }

    public function getFunctions ()
    {
        return array(
            new \Twig_SimpleFunction('url', array(
                $this,
                'getUrl'
            ), array(
                'is_safe_callback' => array(
                    $this,
                    'isUrlGenerationSafe'
                )
            )),
            new \Twig_SimpleFunction('path', array(
                $this,
                'getPath'
            ), array(
                'is_safe_callback' => array(
                    $this,
                    'isUrlGenerationSafe'
                )
            ))
        );
    }

    public function getPath ($name, $parameters = array(), $relative = false)
    {
        return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getUrl ($name, $parameters = array(), $schemeRelative = false)
    {
        return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function isUrlGenerationSafe (\Twig_Node $argsNode)
    {
        // support named arguments
        $paramsNode = $argsNode->hasNode('parameters') ? $argsNode->getNode('parameters') : ($argsNode->hasNode(1) ? $argsNode->getNode(1) : null);
        
        if (null === $paramsNode || $paramsNode instanceof \Twig_Node_Expression_Array && count($paramsNode) <= 2 && (! $paramsNode->hasNode(1) || $paramsNode->getNode(1) instanceof \Twig_Node_Expression_Constant)) {
            return array(
                'html'
            );
        }
        
        return array();
    }

    public function getName ()
    {
        return 'routing';
    }
}
