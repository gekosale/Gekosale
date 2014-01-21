<?php

namespace Gekosale\Tools;
use \Gekosale\App as App;

require_once (ROOTPATH . 'tools' . DS . 'bootstrap.php');

class Plugin extends \Gekosale\Tools\Tool
{

    const DEFAULT_NAMESPACE = 'Gekosale';

    const DEFAULT_MODE = 'Frontend';

    /**
     * Application logic goes here
     */
    public function run ()
    {
        $name = ucfirst(strtolower($this->getParam('name', true)));
        $mode = ucfirst(strtolower($this->getParam('mode', false, self::DEFAULT_MODE)));
        $ns = ucfirst(strtolower($this->getParam('ns', false, self::DEFAULT_NAMESPACE)));
        
        $pluginBasePath = ROOTPATH . DS . 'plugin';
        $destDir = $pluginBasePath . DS . $ns . DS . $mode . DS . strtolower($name);
        
        $tplValues = array(
            'PLUGIN_NAME' => $name,
            'PLUGIN_SLUG' => strtolower($name),
            'PLUGIN_MODE' => $mode,
            'PLUGIN_NAMESPACE' => $ns
        );
        
        if ($this->rcopy(ROOTPATH . DS . 'tools' . DS . 'data' . DS . 'plugin_template', $destDir, $tplValues) < 0)
            throw new \Exception(sprintf('Destination directory: %s already exists! Exiting.', $destDir));
    }
}