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
 * $Revision: 222 $
 * $Author: gekosale $
 * $Date: 2011-06-25 15:20:08 +0200 (So, 25 cze 2011) $
 * $Id: categoriesbox.php 222 2011-06-25 13:20:08Z gekosale $
 */
namespace Gekosale;

class LayeredNavigationBoxModel extends Component\Model
{

	public function getLayeredAttributesForCategory ($id)
	{
		$Data = Array();
		$sql = 'SELECT 
					AP.name AS attributegroupname, 
					AP.idattributeproduct AS attributegroupid,
					APV.name AS attributename, 
					APV.idattributeproductvalue AS attributeid
				FROM productattributeset AS PAS 
				INNER JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset 
				LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue 
				LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct 
				LEFT JOIN product AS P ON PAS.productid = P.idproduct 
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				WHERE PC.categoryid = :id AND P.enable = 1 AND IF(P.trackstock = 1, PAS.stock > 0, 1) AND PAS.status = 1
				ORDER BY APV.name ASC
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['attributegroupid']]['name'] = $rs['attributegroupname'];
			$Data[$rs['attributegroupid']]['attributes'][$rs['attributeid']] = Array(
				'id' => $rs['attributeid'],
				'name' => $rs['attributename']
			);
		}
		foreach ($Data as $key => $val){
			$Data[$key]['attributes'] = $val['attributes'];
		}
		return $Data;
	}

	public function getLayeredAttributesByProductIds ($ids)
	{
		$Data = Array();
		$sql = 'SELECT 
					AP.name AS attributegroupname, 
					AP.idattributeproduct AS attributegroupid,
					APV.name AS attributename, 
					APV.idattributeproductvalue AS attributeid
				FROM productattributeset AS PAS 
				INNER JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset 
				LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue 
				LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct 
				LEFT JOIN product AS P ON PAS.productid = P.idproduct 
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				WHERE FIND_IN_SET(CAST(P.idproduct as CHAR), :ids) AND P.enable = 1 AND PAS.status = 1
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('ids', implode(',', $ids));
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['attributegroupid']]['name'] = $rs['attributegroupname'];
			$Data[$rs['attributegroupid']]['attributes'][$rs['attributeid']] = Array(
				'id' => $rs['attributeid'],
				'name' => $rs['attributename']
			);
		}
		return $Data;
	}

	public function getProductsForAttributes ($categoryid, $attributes, $technicaldata = Array())
	{
		$list = array();
		
		$sql = 'SELECT
					PAS.productid
				FROM productattributeset PAS
				LEFT JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				WHERE FIND_IN_SET(CAST(PAVS.attributeproductvalueid as CHAR), :attributes) AND PAS.status = 1
				GROUP BY PAS.productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributes', implode(',', $attributes));
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['productid'];
		}
		if (count($Data)){
			$list[] = $Data;
		}
		
		foreach ($technicaldata as $attribute){
			$sql = 'SELECT
				   		PTG.productid
					FROM producttechnicaldatagroup PTG
					LEFT JOIN producttechnicaldatagroupattribute PTGA ON PTG.idproducttechnicaldatagroup = PTGA.producttechnicaldatagroupid
					WHERE PTGA.technicaldataattributeid = :id AND PTGA.value = 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $attribute);
			$stmt->execute();
			$Data = Array();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['productid'];
			}
			$list[] = $Data;
		}
		
		if (count($list) > 1){
			return array_values(call_user_func_array('array_intersect', $list));
		}
		else{
			return current($list);
		}
	}

	public function getTechnicalAttributes ($categoryid)
	{
		$Data = Array();
		$sql = "SELECT
					DISTINCT TDAT.name,
				  	PTDGA.technicaldataattributeid,
					TDGT.name AS groupname
				FROM producttechnicaldatagroup PTDG
				INNER JOIN producttechnicaldatagroupattribute PTDGA ON PTDGA.producttechnicaldatagroupid = PTDG.idproducttechnicaldatagroup
				INNER JOIN productcategory PC ON PC.productid = PTDG.productid
				INNER JOIN product P ON PTDG.productid = P.idproduct
				INNER JOIN technicaldataattributetranslation TDAT ON TDAT.technicaldataattributeid = PTDGA.technicaldataattributeid AND TDAT.languageid = :languageid
				INNER JOIN technicaldatagrouptranslation TDGT ON TDGT.technicaldatagroupid = PTDG.technicaldatagroupid AND TDGT.languageid = :languageid
				INNER JOIN technicaldataattribute TDA ON TDA.idtechnicaldataattribute = TDAT.technicaldataattributeid
				WHERE P.enable = 1 AND PC.categoryid = :categoryid AND TDA.type = 5 AND PTDGA.value = 1
				ORDER BY TDAT.name asc
			";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('categoryid', $categoryid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['groupname']][] = Array(
				'id' => $rs['technicaldataattributeid'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getTechnicalAttributesByProductIds ($ids)
	{
		$Data = Array();
		$sql = "SELECT
					DISTINCT TDAT.name,
				  	TDGA.technicaldataattributeid,
					TDGT.name AS groupname
				FROM technicaldataattributetranslation TDAT
				LEFT JOIN technicaldataattribute TDA ON TDA.idtechnicaldataattribute = TDAT.technicaldataattributeid
				LEFT JOIN technicaldatasetgroupattribute TDGA ON TDGA.technicaldataattributeid = TDAT.technicaldataattributeid
				LEFT JOIN technicaldatasetgroup TDSG ON TDGA.technicaldatasetgroupid = TDSG.idtechnicaldatasetgroup
				LEFT JOIN technicaldatagrouptranslation TDGT ON TDGT.technicaldatagroupid = TDSG.technicaldatagroupid AND TDGT.languageid = :languageid
				INNER JOIN producttechnicaldatagroup PTDG ON PTDG.technicaldatagroupid = TDGT.technicaldatagroupid
				INNER JOIN product P ON P.idproduct = PTDG.productid
				WHERE TDAT.languageid = :languageid AND FIND_IN_SET(CAST(P.idproduct as CHAR), :ids) AND P.enable = 1 AND TDA.type = 5
				ORDER BY TDAT.name asc
			";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('ids', implode(',', $ids));
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['groupname']][] = Array(
				'id' => $rs['technicaldataattributeid'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}
}