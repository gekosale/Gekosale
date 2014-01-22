<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
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
 * $Id: buyalso.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

class BuyAlsoModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('orderproduct', Array(
			'productid' => Array(
				'source' => 'OP.productid'
			),
			'name' => Array(
				'source' => 'OP.name',
				'prepareForAutosuggest' => true
			)
		));
		$datagrid->setFrom('
			orderproduct OP
			LEFT JOIN `order` O ON OP.orderid = O.idorder
			LEFT JOIN product P ON OP.productid = P.idproduct
		');
		
		$datagrid->setGroupBy('
			OP.productid
		');
		
		$datagrid->setAdditionalWhere('
			P.sellprice IS NOT NULL AND O.viewid IN (' . Helper::getViewIdsAsString() . ')
		');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getBuyalsoForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function getAlsoProduct ($id)
	{
		$name = '';
		$sql = "SELECT
					orderid 
				FROM orderproduct 
				WHERE productid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['orderid'];
		}
		
		$Products = Array();
		foreach ($Data as $key => $table){
			$sql = "SELECT 
						productid as idproduct, 
						name,
						SUM(qty) AS qty
					FROM orderproduct 
					WHERE orderid = :orderId AND productid != :id
					GROUP BY productid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderId', $table['orderid']);
			$stmt->bindValue('id', $id);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Products[] = Array(
					'id' => $rs['idproduct'],
					'name' => $rs['name'],
					'qty' => $rs['qty'],
					'link' => $this->registry->router->generate('admin', true, Array(
						'controller' => 'product',
						'action' => 'edit',
						'param' => $rs['idproduct']
					))
				);
			}
		}
		
		return $Products;
	}

	public function alsoChart ($id)
	{
		$Products = $this->getAlsoProduct($id);
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Produkt',
			'pattern' => '',
			'type' => 'string'
		);
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Suma',
			'pattern' => '',
			'type' => 'number'
		);
		
		foreach ($Products as $product){
			$Data['rows'][]['c'] = Array(
				Array(
					'v' => $product['name']
				),
				Array(
					'v' => round($product['qty'], 0)
				)
			);
		}
		
		return json_encode($Data);
	}
}