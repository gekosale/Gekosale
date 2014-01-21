<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace FormEngine;

abstract class Condition
{

    protected $_jsConditionName;

    protected $_argument;

    public function __construct ($argument)
    {
        $classPath = explode('\\', get_class($this));
        $this->_jsConditionName = 'GFormCondition.' . strtoupper(end($classPath));
        $this->_argument = $argument;
    }

    public function Render_JS ()
    {
        if ($this->_argument instanceof Condition){
            return "new GFormCondition({$this->_jsConditionName}, {$this->_argument->Render_JS()})";
        }
        if (is_array($this->_argument) and isset($this->_argument[0]) and ($this->_argument[0] instanceof Condition)){
            $parts = Array();
            foreach ($this->_argument as $part){
                $parts[] = $part->Render_JS();
            }
            $argument = '[' . implode(', ', $parts) . ']';
        }
        else{
            $argument = json_encode($this->_argument);
        }
        return "new GFormCondition({$this->_jsConditionName}, {$argument})";
    }

    public function Evaluate ($value)
    {
        return true;
    }
}
