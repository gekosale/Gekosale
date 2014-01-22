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
namespace Gekosale\Plugin;

class CouponsRegistryModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('view', Array(
			'id' => Array(
				'source' => 'C.idcoupons'
			),
			'coupon' => Array(
				'source' => 'CT.name',
				'prepareForSelect' => true
			),
			'firstname' => Array(
				'source' => 'OCD.firstname',
				'encrypted' => true
			),
			'surname' => Array(
				'source' => 'OCD.surname',
				'encrypted' => true
			),
			'email' => Array(
				'source' => 'OCD.email',
				'encrypted' => true
			),
			'orderid' => Array(
				'source' => 'O.idorder'
			),
			'globalprice' => Array(
				'source' => 'O.globalprice'
			),
			'adddate' => Array(
				'source' => 'O.adddate'
			)
		));
		
		$datagrid->setFrom('
			 coupons C
             INNER JOIN `order` O ON O.couponid = C.idcoupons
             INNER JOIN orderclientdata OCD ON O.idorder = OCD.orderid
             LEFT JOIN couponstranslation CT ON C.idcoupons = CT.couponsid AND CT.languageid = :languageid
		');
		
		$datagrid->setGroupBy('
			O.idorder
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getCouponsRegistryForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteCouponsRegistry ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteCouponsRegistry'
		), $this->getName());
	}

	public function getFirstnameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('firstname', $request, $processFunction);
	}

	public function getSurnameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('surname', $request, $processFunction);
	}

	public function getEmailForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('email', $request, $processFunction);
	}

	public function deleteCouponsRegistry ($id)
	{
		try{
			$this->registry->dbtracker->load($this->getDirPath());
			return $this->registry->dbtracker->run(Array(
				'idview' => $id
			), $this->getName(), 'deleteCouponsRegistry');
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}
}