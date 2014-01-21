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
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: searchresults.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale;

class SearchResultsModel extends Component\Model\Dataset
{

	public function initDataset ($dataset)
	{
		$clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();

		if (! empty($clientGroupId)){

			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'P.idproduct'
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
				'onstock' => Array(
					'source' => 'IF(P.trackstock = 1, IF(P.stock > 0, 1, 0), 1)'
				),
				'seo' => Array(
					'source' => 'PT.seo'
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
						$this,
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
				)
			));

			$dataset->setFrom('
				product P
				LEFT JOIN productcategory PC ON P.idproduct = PC.productid
				INNER JOIN category C ON PC.categoryid = C.idcategory AND C.enable = 1
				INNER JOIN viewcategory VC ON PC.categoryid = VC.categoryid AND VC.viewid = :viewid
				LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid = :clientgroupid
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
				LEFT JOIN productnew PN ON P.idproduct = PN.productid
				LEFT JOIN producerview PV ON PV.producerid = P.producerid
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
				LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				LEFT JOIN productattributeset PAS ON P.idproduct = PAS.productid
				LEFT JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
				LEFT JOIN producttechnicaldatagroup PTDG ON PTDG.productid = P.idproduct
				LEFT JOIN producttechnicaldatagroupattribute PTDGA ON PTDG.idproducttechnicaldatagroup = PTDGA.producttechnicaldatagroupid
			');
		}
		else{

			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'P.idproduct'
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
				'onstock' => Array(
					'source' => 'IF(P.trackstock = 1, IF(P.stock > 0, 1, 0), 1)'
				),
				'seo' => Array(
					'source' => 'PT.seo'
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
						$this,
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
				)
			));

			$dataset->setFrom('
				product P
				LEFT JOIN productcategory PC ON P.idproduct = PC.productid
				INNER JOIN category C ON PC.categoryid = C.idcategory AND C.enable = 1
				INNER JOIN viewcategory VC ON PC.categoryid = VC.categoryid AND VC.viewid = :viewid
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
				LEFT JOIN productnew PN ON P.idproduct = PN.productid
				LEFT JOIN producerview PV ON PV.producerid = P.producerid
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
				LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
				LEFT JOIN productattributeset PAS ON P.idproduct = PAS.productid
				LEFT JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
				LEFT JOIN producttechnicaldatagroup PTDG ON PTDG.productid = P.idproduct
				LEFT JOIN producttechnicaldatagroupattribute PTDGA ON PTDG.idproducttechnicaldatagroup = PTDGA.producttechnicaldatagroupid
			');
		}

		$dataset->setAdditionalWhere('
			(
				LOWER(P.ean) LIKE :name OR
				LOWER(P.delivelercode) LIKE :name OR
				LOWER(PT.name) LIKE :name OR
				LOWER(PT.shortdescription) LIKE :name OR
				LOWER(PT.description) LIKE :name OR
				LOWER(PT.keyword) LIKE :name OR
				LOWER(APV.name) LIKE :name OR
				LOWER(PTDGA.value) LIKE :name
			) AND
			P.enable = 1 AND
			IF(P.producerid IS NOT NULL, PV.viewid = :viewid, 1)
		');

		$dataset->setGroupBy('
			P.idproduct
		');

		$dataset->setSQLParams(Array(
			'name' => ''
		));
	}

	public function getProductDataset ()
	{
		return $this->getDataset()->getDatasetRecords();
	}

	public function getImagePath ($id)
	{
		return App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallestImageById($id));
	}

	public function addPhrase ($name)
	{
		try{
			if ($name != NULL){
				$result = $this->checkInsertingMostSearch($name);
				if ($result == NULL){
					$this->addPhraseAboutMostSearch($name);
				}
				else{
					$this->updatePhraseAboutMostSearch($result['idmostsearch'], $result['textcount']);
				}
			}
		}
		catch (Exception $fe){
			throw new FrontendException($e->getMessage());
		}
	}

	public function checkInsertingMostSearch ($phrase)
	{
		$Data = Array();
		$sql = "SELECT MS.idmostsearch, MS.textcount
					FROM mostsearch MS
					WHERE MS.name= :phrase";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('phrase', $phrase);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'idmostsearch' => $rs['idmostsearch'],
					'textcount' => $rs['textcount']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return $Data;
	}

	public function addPhraseAboutMostSearch ($name, $counter = 0)
	{
		$sql = 'INSERT INTO mostsearch (name, viewid)
					VALUES (:name, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('viewid', Helper::getViewId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function updatePhraseAboutMostSearch ($id, $counter = 0)
	{
		$counter = $counter + 1;
		$sql = 'UPDATE mostsearch MS SET MS.textcount = :counter
					WHERE MS.idmostsearch = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('counter', $counter);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
	}
}
