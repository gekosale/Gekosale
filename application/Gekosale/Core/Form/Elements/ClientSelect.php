<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
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

class ClientSelect extends Select
{
	public $datagrid;
	protected $_jsFunction;
	protected $_jsFunctionDetails;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_jsFunction = 'LoadClients_' . $this->_id;
		$this->_jsFunctionDetails = 'LoadClientData_' . $this->_id;
		$this->_attributes['jsfunction'] = 'xajax_' . $this->_jsFunction;
		$this->_attributes['jsfunctiondetails'] = 'xajax_' . $this->_jsFunctionDetails;
		App::getRegistry()->xajax->registerFunction(array(
			$this->_jsFunction,
			$this,
			'loadClients'
		));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsFunctionDetails,
			$this,
			'loadClientDetails'
		));
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('help', 'sHelp'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('jsfunction', 'fLoadClients', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('jsfunctiondetails', 'fLoadClientData', FE::TYPE_FUNCTION),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

	public function loadClients ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function loadClientDetails ($request)
	{
		return App::getModel('order')->getClientDataWithAddresses($request);
	}

	public function getDatagrid ()
	{
		$this->datagrid = App::getModel('datagrid/datagrid');
		$this->initDatagrid($this->datagrid);
		return $this->datagrid;
	}

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('clientdata', Array(
			'idclient' => Array(
				'source' => 'C.idclient'
			),
			'disable' => Array(
				'source' => 'disable'
			),
			'clientorder' => Array(
				'source' => 'SUM(O.globalprice)'
			),
			'firstname' => Array(
				'source' => 'CD.firstname',
				'prepareForAutosuggest' => true,
				'encrypted' => true
			),
			'surname' => Array(
				'source' => 'CD.surname',
				'prepareForAutosuggest' => true,
				'encrypted' => true
			),
			'email' => Array(
				'source' => 'CD.email',
				'encrypted' => true
			),
			'groupname' => Array(
				'source' => 'CGT.name',
				'prepareForSelect' => true
			),
			'phone' => Array(
				'source' => 'CD.phone',
				'encrypted' => true
			),
			'phone2' => Array(
				'source' => 'CD.phone2',
				'encrypted' => true
			),
			'adddate' => Array(
				'source' => 'CD.adddate'
			),
		));
		$datagrid->setFrom('
			client C
			LEFT JOIN clientdata CD ON CD.clientid = C.idclient
			LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=:languageid
			LEFT JOIN orderclientdata OCD ON OCD.clientid = CD.clientid
			LEFT JOIN `order` O ON O.idorder = OCD.orderid
		');
		
		$datagrid->setGroupBy('C.idclient');
		
		$datagrid->setAdditionalWhere('
			IF(:viewid IS NULL,1,C.viewid = :viewid)
		');
	}
}