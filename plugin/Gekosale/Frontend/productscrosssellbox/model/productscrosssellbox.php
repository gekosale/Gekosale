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
 * $Revision: 616 $
 * $Author: gekosale $
 * $Date: 2011-12-05 10:13:27 +0100 (Pn, 05 gru 2011) $
 * $Id: productscrosssellbox.php 616 2011-12-05 09:13:27Z gekosale $
 */
namespace Gekosale;

class ProductsCrosssellBoxModel extends Component\Model\Dataset
{

	public function initDataset ($dataset)
	{
		$clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();
		
		if (! empty($clientGroupId)){
			
			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'UP.relatedproductid'
				),
				'hierarchy' => Array(
					'source' => 'UP.hierarchy'
				),
				'adddate' => Array(
					'source' => 'P.adddate'
				),
				'name' => Array(
					'source' => 'PT.name',
					'sortable' => true
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
				'categoryname' => Array(
					'source' => 'CT.name'
				),
				'categoryseo' => Array(
					'source' => 'CT.seo'
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
				crosssell UP
	           	LEFT JOIN product P ON  P.idproduct = UP.relatedproductid
	           	LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid = :clientgroupid
	           	LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
	           	LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
	           	LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN vat V ON V.idvat = P.vatid
	           	LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto = 1
	           	LEFT JOIN productnew PN ON P.idproduct = PN.productid
	           	LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
				LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
			');
		}
		else{
			
			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'UP.relatedproductid'
				),
				'adddate' => Array(
					'source' => 'P.adddate'
				),
				'hierarchy' => Array(
					'source' => 'UP.hierarchy'
				),
				'name' => Array(
					'source' => 'PT.name',
					'sortable' => true
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
				'categoryname' => Array(
					'source' => 'CT.name'
				),
				'categoryseo' => Array(
					'source' => 'CT.seo'
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
				crosssell UP
	           	LEFT JOIN product P ON  P.idproduct = UP.relatedproductid
	           	LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
            	LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
            	LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN vat V ON V.idvat = P.vatid
            	LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto = 1
            	LEFT JOIN productnew PN ON P.idproduct = PN.productid
            	LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
				LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
			');
		}
		if ($this->registry->router->getCurrentController() == 'cart'){
			$dataset->setAdditionalWhere('
				FIND_IN_SET(CAST(UP.productid as CHAR), :ids) AND
				VC.viewid = :viewid AND
				P.enable = 1
			');
			
			$dataset->setSQLParams(Array(
				'ids' => App::getModel('cart')->getProductIds()
			));
		}
		elseif ($this->registry->router->getCurrentController() == 'productcart'){
			$dataset->setAdditionalWhere('
				UP.productid = (SELECT productid FROM producttranslation WHERE seo = :seo LIMIT 1) AND 
				VC.viewid = :viewid AND
				P.enable = 1
			');
			
			$dataset->setSQLParams(Array(
				'seo' => $this->registry->core->getParam()
			));
		}
		else{
			$dataset->setAdditionalWhere('
				VC.viewid = :viewid AND
				P.enable = 1
			');
		}
		
		$dataset->setGroupBy('
			UP.relatedproductid
		');
	}

	public function getProductDataset ()
	{
		return $this->getDataset()->getDatasetRecords();
	}
}