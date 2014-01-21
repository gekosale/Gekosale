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
 * $Id: mostviewed.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class MostViewedModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('product', Array(
			'id' => Array(
				'source' => 'P.idproduct'
			),
			'name' => Array(
				'source' => 'PT.name'
			),
			'qty' => Array(
				'source' => 'P.viewed'
			),
			'categoryname' => Array(
				'source' => 'CT.name'
			),
			'categoryid' => Array(
				'source' => 'PC.categoryid',
				'prepareForTree' => true,
				'first_level' => App::getModel('product')->getCategories()
			),
			'ancestorcategoryid' => Array(
				'source' => 'CP.ancestorcategoryid'
			),
			'categoriesname' => Array(
				'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
				'filter' => 'having'
			)
		));
		$datagrid->setFrom('
			product P
			LEFT JOIN producttranslation PT ON P.idproduct = PT.productid  
			LEFT JOIN productcategory PC ON PC.productid = PT.productid
			LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
		');
		$datagrid->setGroupBy('
			P.idproduct
		');
		
		$datagrid->setAdditionalWhere('
			VC.viewid IN (' . Helper::getViewIdsAsString() . ')
		');
	
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getMostViewedForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function deleteMostViewed ()
	{
		$sql = "UPDATE product SET viewed = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}

}