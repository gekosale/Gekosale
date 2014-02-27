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

namespace FormEngine\Elements;
use Gekosale\App as App;
use FormEngine\FE as FE;

class ProgressIndicator extends Field {
	
	public $datagrid;
	
	protected static $_filesLoadHandlerSet = false;
	protected $_jsFunction;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->_attributes['load'] = App::getRegistry()->xajaxInterface->registerFunction(array('ProgressIndicator_OnLoad_' . $this->_id, $this->_attributes['load'][0], $this->_attributes['load'][1]));
		$this->_attributes['process'] = App::getRegistry()->xajaxInterface->registerFunction(array('ProgressIndicator_OnProcess_' . $this->_id, $this->_attributes['process'][0], $this->_attributes['process'][1]));
		$this->_attributes['success'] = App::getRegistry()->xajaxInterface->registerFunction(array('ProgressIndicator_OnSuccess_' . $this->_id, $this->_attributes['success'][0], $this->_attributes['success'][1]));
	}


	protected function prepareAttributesJs() {
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('comment', 'sComment'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatAttributeJs('chunks', 'iChunks'),
			$this->formatAttributeJs('load', 'fLoadRecords', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('process', 'fProcessRecords', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('success', 'fSuccessRecords', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('preventSubmit', 'bPreventSubmit', FE::TYPE_BOOLEAN),
			$this->formatRepeatableJs(),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		return $attributes;
	}
	
}
