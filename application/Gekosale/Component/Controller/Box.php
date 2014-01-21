<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: boxcontroller.class.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Component\Controller;
use Gekosale\App;

abstract class Box extends \Gekosale\Component\Controller
{

    protected $_boxId;

    protected $_heading = '';

    protected $_scheme = '';

    protected $_style = '';

    protected $_jsVariables;

    protected $_boxAttributes;

    protected $_boxParams;

    public function __construct ($registry, $box = Array())
    {
        parent::__construct($registry, App::getContainer());
        $this->registry = $registry;
        $this->layer = $registry->loader->getCurrentLayer();
        $this->_boxId = $box['id'];
        $this->_heading = isset($box['params']['heading']) ? $box['params']['heading'] : array();
        $this->_jsVariables = isset($box['params']['js']) ? $box['params']['js'] : array();
        $this->_boxAttributes = isset($box['params']['css']) ? $box['params']['css'] : array();
    }

    public function boxVisible ()
    {
        return true;
    }

    public function getBoxHeading ()
    {
        return $this->_heading;
    }

    public function getBoxClassname ()
    {
        $className = Array(
            'layout-box'
        );
        $className[] = $this->getBoxTypeClassname();
        if (isset($this->_jsVariables['bNoHeader']) && (int) $this->_jsVariables['bNoHeader']){
            $className[] = 'layout-box-option-header-false';
        }
        else{
            $className[] = 'layout-box-option-header-true';
        }
        if (isset($this->_jsVariables['iDefaultSpan']) && (int) $this->_jsVariables['iDefaultSpan']){
            $className[] = 'layout-box-option-stretch-' . $this->_jsVariables['iDefaultSpan'];
        }
        return implode(' ', $className);
    }

    public function getBoxTypeClassname ()
    {
        $class = explode('\\', get_class($this));
        $className = strtolower(end($class));
        $className = substr($className, 0, strlen($className) - strlen('BoxController'));
        $className = strtolower(preg_replace("/([A-Z])/", "-$1", $className));
        $className = trim($className, '-');
        return 'layout-box-type-' . $className;
    }

    public function getBoxContents ($action)
    {
        if (! is_callable(Array(
            $this,
            $action
        ))){
            $action = 'index';
        }
        
        $this->registry->template->assign('box', Array(
            'id' => 'layout-box-' . $this->_boxId,
            'editableid' => $this->layer['pageschemeid'] . ',' . $this->_boxId,
            'schemeClass' => $this->getBoxClassname(),
            'heading' => $this->getBoxHeading(),
            'style' => $this->_style
        ));
        
        return call_user_func(Array(
            $this,
            $action
        ));
        
        return $this->registry->template->fetch($this->loadTemplate($action . '.tpl'));
    }
}