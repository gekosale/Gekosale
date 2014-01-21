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


	protected function _PrepareAttributes_JS() {
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('chunks', 'iChunks'),
			$this->_FormatAttribute_JS('load', 'fLoadRecords', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('process', 'fProcessRecords', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('success', 'fSuccessRecords', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('preventSubmit', 'bPreventSubmit', FE::TYPE_BOOLEAN),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}
	
}
