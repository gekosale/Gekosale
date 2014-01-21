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
 * $Revision: 6 $
 * $Author: gekosale $
 * $Date: 2011-03-27 21:01:27 +0200 (N, 27 mar 2011) $
 * $Id: productsincategorybox.php 6 2011-03-27 19:01:27Z gekosale $
 */
namespace Gekosale;

class ProducerListBoxModel extends Component\Model\Dataset
{

	public function initDataset ($dataset)
	{
		$clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();
		
		if (! empty($clientGroupId)){
			
			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'P.idproduct'
				),
				'adddate' => Array(
					'source' => 'P.adddate'
				),
				'name' => Array(
					'source' => 'PT.name'
				),
				'ean' => Array(
					'source' => 'P.ean'
				),
				'delivelercode' => Array(
					'source' => 'P.delivelercode'
				),
				'shortdescription' => Array(
					'source' => 'PT.shortdescription'
				),
				'seo' => Array(
					'source' => 'PT.seo'
				),
				'onstock' => Array(
					'source' => 'IF(P.trackstock = 1, IF(P.stock > 0, 1, 0), 1)'
				),
				'pricenetto' => Array(
					'source' => 'IF(PGP.groupprice = 1, 
								 	PGP.sellprice, 
								 	P.sellprice
								 ) * CR.exchangerate'
				),
				'price' => Array(
					'source' => 'IF(PGP.groupprice = 1, 
									PGP.sellprice, 
									P.sellprice
								 ) * (1 + (V.value / 100)) * CR.exchangerate'
				),
				'buypricenetto' => Array(
					'source' => 'P.buyprice * CR.exchangerate'
				),
				'buyprice' => Array(
					'source' => 'P.buyprice * (1 + (V.value / 100)) * CR.exchangerate'
				),
				'discountpricenetto' => Array(
					'source' => 'IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
								 	PGP.discountprice,
								 	IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, NULL)
								 ) * CR.exchangerate'
				),
				'discountprice' => Array(
					'source' => 'IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
								 	PGP.discountprice,
								 	IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, NULL)
								 ) * (1 + (V.value / 100)) * CR.exchangerate'
				),
				'photo' => Array(
					'source' => 'Photo.photoid',
					'processFunction' => Array(
						App::getModel('product'),
						'getImagePath'
					)
				),
				'opinions' => Array(
					'source' => 'COUNT(DISTINCT PREV.idproductreview)'
				),
				'rating' => Array(
					'source' => 'IF(CEILING(AVG(PRANGE.value)) IS NULL, 0, CEILING(AVG(PRANGE.value)))'
				),
				'new' => Array(
					'source' => 'IF(PN.active = 1 AND (PN.startdate IS NULL OR PN.startdate <= CURDATE()) AND (PN.enddate IS NULL OR PN.enddate >= CURDATE()), 1, 0)'
				),
				'dateto' => Array(
					'source' => 'IF(PGP.promotionend IS NOT NULL, PGP.promotionend, IF(P.promotionend IS NOT NULL, P.promotionend, NULL))'
				),
				'statuses' => Array(
					'source' => 'P.idproduct',
					'processFunction' => Array(
						App::getModel('product'),
						'getProductStatuses'
					)
				)
			));
			
			$dataset->setFrom('
				productcategory PC
				LEFT JOIN category C ON PC.categoryid= C.idcategory
				LEFT JOIN viewcategory VC ON C.idcategory= VC.categoryid AND VC.viewid = :viewid
				LEFT JOIN product P ON PC.productid = P.idproduct
				LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid = :clientgroupid
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
				LEFT JOIN productnew PN ON P.idproduct = PN.productid
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
				LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
			');
		}
		else{
			
			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'P.idproduct'
				),
				'adddate' => Array(
					'source' => 'P.adddate'
				),
				'name' => Array(
					'source' => 'PT.name'
				),
				'ean' => Array(
					'source' => 'P.ean'
				),
				'delivelercode' => Array(
					'source' => 'P.delivelercode'
				),
				'shortdescription' => Array(
					'source' => 'PT.shortdescription'
				),
				'seo' => Array(
					'source' => 'PT.seo'
				),
				'onstock' => Array(
					'source' => 'IF(P.trackstock = 1, IF(P.stock > 0, 1, 0), 1)'
				),
				'pricenetto' => Array(
					'source' => 'P.sellprice * CR.exchangerate'
				),
				'price' => Array(
					'source' => 'P.sellprice * (1 + (V.value / 100)) * CR.exchangerate'
				),
				'buypricenetto' => Array(
					'source' => 'ROUND(P.buyprice * CR.exchangerate, 2)'
				),
				'buyprice' => Array(
					'source' => 'ROUND((P.buyprice + (P.buyprice * V.`value`)/100) * CR.exchangerate, 2)'
				),
				'discountpricenetto' => Array(
					'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * CR.exchangerate, NULL)'
				),
				'discountprice' => Array(
					'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL)'
				),
				'photo' => Array(
					'source' => 'Photo.photoid',
					'processFunction' => Array(
						App::getModel('product'),
						'getImagePath'
					)
				),
				'opinions' => Array(
					'source' => 'COUNT(DISTINCT PREV.idproductreview)'
				),
				'rating' => Array(
					'source' => 'IF(CEILING(AVG(PRANGE.value)) IS NULL, 0, CEILING(AVG(PRANGE.value)))'
				),
				'new' => Array(
					'source' => 'IF(PN.active = 1 AND (PN.startdate IS NULL OR PN.startdate <= CURDATE()) AND (PN.enddate IS NULL OR PN.enddate >= CURDATE()), 1, 0)'
				),
				'dateto' => Array(
					'source' => 'IF(P.promotionend IS NOT NULL, P.promotionend, NULL)'
				),
				'statuses' => Array(
					'source' => 'P.idproduct',
					'processFunction' => Array(
						App::getModel('product'),
						'getProductStatuses'
					)
				)
			));
			
			$dataset->setFrom('
				productcategory PC
				LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid AND VC.viewid = :viewid
				LEFT JOIN product P ON PC.productid= P.idproduct
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
				LEFT JOIN productnew PN ON P.idproduct = PN.productid
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
				LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
			');
		}
		
		$dataset->setAdditionalWhere('
			P.producerid = :producer AND
			IF(:collection > 0, P.collectionid = :collection, 1) AND
			ROUND((P.sellprice + (P.sellprice * V.`value`)/100), 2) BETWEEN IF(:pricefrom > 0, :pricefrom, 0) AND IF( :priceto > 0, :priceto, 999999) AND
			P.enable = 1 AND 
			VC.viewid  = :viewid
		');
		
		$dataset->setGroupBy('
			P.idproduct 
		');
		
		$dataset->setSQLParams(Array(
			'producer' => 0,
			'collection' => 0,
			'pricefrom' => 0,
			'priceto' => 0
		));
	}

	public function getProductDataset ()
	{
		return $this->getDataset()->getDatasetRecords();
	}

	public function getProducerBySeo ($seo)
	{
		$sql = "SELECT
					P.photoid,
					PT.producerid,
					PT.name,
					PT.seo,
					PT.description,
					PT.keyword_title,
					PT.keyword,
					PT.keyword_description
				FROM producertranslation PT
				LEFT JOIN producer P ON P.idproducer = PT.producerid
				WHERE PT.seo =:seo AND PT.languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('seo', $seo);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['producerid'],
				'name' => $rs['name'],
				'description' => $rs['description'],
				'seo' => $rs['seo'],
				'photo' => $this->getImagePath($rs['photoid']),
				'keyword_title' => ($rs['keyword_title'] == NULL || $rs['keyword_title'] == '') ? $rs['name'] : $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description']
			);
		}
		return $Data;
	}

	public function getImagePath ($id)
	{
		if ($id > 0){
			return App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($id));
		}
	}
}