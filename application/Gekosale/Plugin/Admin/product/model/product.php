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
 * $Revision: 622 $
 * $Author: gekosale $
 * $Date: 2012-01-20 20:42:34 +0100 (Pt, 20 sty 2012) $
 * $Id: product.php 622 2012-01-20 19:42:34Z gekosale $
 */
namespace Gekosale\Plugin;

class ProductModel extends Component\Model\Datagrid
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	public function getNamesForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getAttributeCombinationsForProduct ($productId, $clientGroupId = 0, $currencyid = 0)
	{
		if ($currencyid == 0){
			$currencyid = App::getContainer()->get('session')->getActiveCurrencyId();
		}
		$Data = Array();
		if ($clientGroupId > 0){
			$sql = '
				SELECT
					A.idproductattributeset AS id,
					A.`value`,
					A.stock AS qty,
					A.symbol,
					A.status,
					A.weight,
					A.availablityid,
					A.photoid,
					A.suffixtypeid AS prefix_id,
					GROUP_CONCAT(SUBSTRING(CONCAT(\' \', C.name), 1)) AS name,
					IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
						CASE A.suffixtypeid
                           	WHEN 1 THEN PGP.discountprice * (A.value / 100)
                            WHEN 2 THEN PGP.discountprice + A.value
                            WHEN 3 THEN PGP.discountprice - A.value
                          	WHEN 4 THEN A.`value`
                        END,
						IF(PGP.groupprice IS NULL AND D.promotion = 1 AND IF(D.promotionstart IS NOT NULL, D.promotionstart <= CURDATE(), 1) AND IF(D.promotionend IS NOT NULL, D.promotionend >= CURDATE(), 1),
							A.discountprice,
							IF(PGP.sellprice IS NOT NULL,
								CASE A.suffixtypeid
		                           	WHEN 1 THEN PGP.sellprice * (A.value / 100)
		                            WHEN 2 THEN PGP.sellprice + A.value
		                            WHEN 3 THEN PGP.sellprice - A.value
		                          	WHEN 4 THEN A.`value`
	                           	END,
								A.attributeprice
							)
						)
					) * CR.exchangerate AS price,
					(
						SELECT (
							ROUND(price + (price * V.`value` / 100), 2)
						)
					) AS price_gross
				FROM
					productattributeset A
					LEFT JOIN productattributevalueset B ON A.idproductattributeset = B.productattributesetid
					LEFT JOIN attributeproductvalue C ON B.attributeproductvalueid = C.idattributeproductvalue
					LEFT JOIN product D ON A.productid = D.idproduct
					LEFT JOIN productgroupprice PGP ON PGP.productid = D.idproduct AND PGP.clientgroupid = :clientgroupid
					LEFT JOIN currencyrates CR ON CR.currencyfrom = D.sellcurrencyid AND CR.currencyto = :currencyto
					LEFT JOIN suffixtype E ON A.suffixtypeid = E.idsuffixtype
					LEFT JOIN vat V ON V.idvat = D.vatid
				WHERE
					A.productid = :productid
				GROUP BY
					A.idproductattributeset
			';
		}
		else{
			$sql = '
				SELECT
					A.idproductattributeset AS id,
					A.`value`,
					A.stock AS qty,
					A.symbol,
					A.status,
					A.weight,
					A.availablityid,
					A.photoid,
					A.suffixtypeid AS prefix_id,
					GROUP_CONCAT(SUBSTRING(CONCAT(\' \', C.name), 1)) AS name,
					IF(D.promotion = 1 AND IF(D.promotionstart IS NOT NULL, D.promotionstart <= CURDATE(), 1) AND IF(D.promotionend IS NOT NULL, D.promotionend >= CURDATE(), 1), A.discountprice, A.attributeprice) * CR.exchangerate AS price,
					(
						SELECT (
							ROUND(price + (price * V.`value` / 100), 2)
						)
					) AS price_gross
				FROM
					productattributeset A
					LEFT JOIN productattributevalueset B ON A.idproductattributeset = B.productattributesetid
					LEFT JOIN attributeproductvalue C ON B.attributeproductvalueid = C.idattributeproductvalue
					LEFT JOIN product D ON A.productid = D.idproduct
					LEFT JOIN productgroupprice PGP ON PGP.productid = D.idproduct AND PGP.clientgroupid = :clientgroupid
					LEFT JOIN currencyrates CR ON CR.currencyfrom = D.sellcurrencyid AND CR.currencyto = :currencyto
					LEFT JOIN suffixtype E ON A.suffixtypeid = E.idsuffixtype
					LEFT JOIN vat V ON V.idvat = D.vatid
				WHERE
					A.productid = :productid
				GROUP BY
					A.idproductattributeset
			';
		}
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productId);
		$stmt->bindValue('clientgroupid', $clientGroupId);
		$stmt->bindValue('currencyto', $currencyid);
		$stmt->execute();
		$Data = $stmt->fetchAll();
		foreach ($Data as $key => $value){
			$sql = '
					SELECT
						B.attributeproductid AS attribute,
						A.attributeproductvalueid AS value,
						B.name AS name
					FROM
						productattributevalueset A
						LEFT JOIN attributeproductvalue B ON A.attributeproductvalueid = B.idattributeproductvalue
					WHERE
						A.productattributesetid = :productattributesetid
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productattributesetid', $value['id']);
			$stmt->execute();
			$Data[$key]['attributes'] = Array();
			while ($rs = $stmt->fetch()){
				$Data[$key]['attributes'][] = Array(
					'id' => $rs['attribute'],
					'value_id' => $rs['value'],
					'name' => $rs['name']
				);
			}
		}
		return $Data;
	}

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('product', Array(
			'idproduct' => Array(
				'source' => 'P.idproduct'
			),
			'name' => Array(
				'source' => 'PT.name',
				'prepareForAutosuggest' => true
			),
			'seo' => Array(
				'source' => 'PT.seo',
				'processFunction' => Array(
					$this,
					'getProductSeo'
				)
			),
			'delivelercode' => Array(
				'source' => 'P.delivelercode'
			),
			'hierarchy' => Array(
				'source' => 'P.hierarchy'
			),
			'ean' => Array(
				'source' => 'P.ean'
			),
			'categoryname' => Array(
				'source' => 'CT.name'
			),
			'categoryid' => Array(
				'source' => 'PC.categoryid',
				'prepareForTree' => true,
				'first_level' => $this->getCategories()
			),
			'ancestorcategoryid' => Array(
				'source' => 'CP.ancestorcategoryid'
			),
			'categoriesname' => Array(
				'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
				'filter' => 'having'
			),
			'sellprice' => Array(
				'source' => 'P.sellprice'
			),
			'sellprice_gross' => Array(
				'source' => 'ROUND(P.sellprice * (1 + V.value / 100), 2)'
			),
			'barcode' => Array(
				'source' => 'P.barcode',
				'prepareForAutosuggest' => true
			),
			'buyprice' => Array(
				'source' => 'P.buyprice'
			),
			'buyprice_gross' => Array(
				'source' => 'ROUND(P.buyprice * (1 + V.value / 100), 2)'
			),
			'producer' => Array(
				'source' => 'PRT.name',
				'prepareForSelect' => true
			),
			'deliverer' => Array(
				'source' => 'DT.name',
				'prepareForSelect' => true
			),
			'status' => Array(
				'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', PS.name), 1) SEPARATOR \'<br />\')',
				'filter' => 'having'
			),
			'vat' => Array(
				'source' => 'CONCAT(V.value, \'%\')',
				'prepareForSelect' => true
			),
			'stock' => Array(
				'source' => 'P.stock'
			),
			'enable' => Array(
				'source' => 'P.enable'
			),
			'weight' => Array(
				'source' => 'P.weight'
			),
			'adddate' => Array(
				'source' => 'P.adddate'
			),
			'thumb' => Array(
				'source' => 'PP.photoid',
				'processFunction' => Array(
					$this,
					'getThumbPathForId'
				)
			),
			'attributes' => Array(
				'source' => 'PAS.idproductattributeset'
			),
			'trackstock' => Array(
				'source' => 'P.trackstock'
			),
			'disableatstockenabled' => Array(
				'source' => 'P.disableatstockenabled'
			)
		));

		$datagrid->setFrom('
			product P
			LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = P.idproduct
			LEFT JOIN productphoto PP ON PP.productid = P.idproduct AND PP.mainphoto = 1
			LEFT JOIN productstatuses PSS ON PSS.productid = P.idproduct
			LEFT JOIN productstatus PS ON PS.idproductstatus = PSS.productstatusid
			LEFT JOIN productattributeset PAS ON PAS.productid = P.idproduct
			LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
			LEFT JOIN productdeliverer PD ON PD.productid = P.idproduct
			LEFT JOIN deliverertranslation DT ON PD.delivererid = DT.delivererid AND DT.languageid = :languageid
			LEFT JOIN vat V ON P.vatid = V.idvat
		');

		$datagrid->setGroupBy('
			P.idproduct
		');

		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
				IF(PC.categoryid IS NOT NULL, VC.viewid IN (' . Helper::getViewIdsAsString() . '), 1)
			');
		}
	}

	public function getProductSeo ($seo)
	{
		return App::getURLAdress() . Seo::getSeo('productcart') . '/' . $seo;
	}

	public function getThumbPathForId ($id)
	{
		if ($id > 1){
			try{
				$image = App::getModel('gallery')->getSmallImageById($id);
			}
			catch (Exception $e){
				$image = Array(
					'path' => ''
				);
			}
			return $image['path'];
		}
		else{
			return '';
		}
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getProductForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteProduct ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteProduct'
		), $this->getName());
	}

	public function doAJAXChangeProductStatus ($id, $datagrid, $status)
	{
		$ids = (is_array($id)) ? $id : (array) $id;

		$sql = 'UPDATE product SET status = :status
				WHERE idproduct IN (' . implode(',', $ids) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		if ($status > 0){
			$stmt->bindValue('status', $status);
		}
		else{
			$stmt->bindValue('status', NULL);
		}
		$stmt->execute();
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function setProductEnable ($datagrid, $id, $enable)
	{
		$sql = "UPDATE product SET enable = :enable
				WHERE idproduct = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('enable', $enable);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function deleteProduct ($id)
	{
		$ids = (is_array($id)) ? $id : (array) $id;

		$recordsNotDeleted = Array();
		foreach ($ids as $productid){
			$sql = "SELECT COUNT(productid) as total FROM `orderproduct` WHERE productid = :id";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $productid);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs['total'] == 0){
				DbTracker::deleteRows('product', 'idproduct', $productid);
			}
			else{
				$recordsNotDeleted[] = $productid;
			}
		}

		App::getModel('category')->flushCache();

		if (count($recordsNotDeleted) > 0){
			return Array(
				'error' => $this->trans('ERR_BIND_PRODUCT_ORDER') . ': ' . implode(',', $recordsNotDeleted)
			);
		}
		else{
			return true;
		}
	}

	public function getProductAndAttributesById ($id, $duplicate = false)
	{
		try{
			$Data = $this->getProductView($id, $duplicate);
			if (empty($Data)){
				App::redirect(__ADMINPANE__ . '/product');
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	public function getProductView ($id, $duplicate)
	{
		$sql = "SELECT
  					P.idproduct AS id,
  					P.stock AS standardstock,
  					P.trackstock,
  					P.availablityid,
  					P.enable,
  					P.ean,
  					P.delivelercode,
  					P.weight,
  					P.packagesize,
  					P.width,
  					P.height,
  					P.deepth,
  					P.unit,
  					P.buyprice,
  					P.sellprice,
  					P.buycurrencyid,
  					P.sellcurrencyid,
  					Photo.photoid AS mainphotoid,
  					V.`value` AS vatvalue,
  					P.vatid,
  					ROUND((P.sellprice*V.`value`/100)+P.sellprice, 2) AS sellpricewithvatvalue,
  					ROUND((P.sellprice*V.`value`/100), 2) AS vatvalueofsellprice,
  					IF(P.producerid IS NOT NULL, P.producerid, 0) as producerid,
  					IF(P.collectionid IS NOT NULL, P.collectionid, 0) as collectionid,
  					PT.seo,
  					P.technicaldatasetid,
  					P.promotion,
  					P.discountprice,
  					P.promotionstart,
  					P.promotionend,
					P.disableatstock,
					P.disableatstockenabled
  				FROM product P
  				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
    			LEFT JOIN `vat` V ON V.idvat = P.vatid
    			LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto = 1
  				WHERE P.idproduct = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'standardstock' => $rs['standardstock'],
					'trackstock' => $rs['trackstock'],
					'availablityid' => $rs['availablityid'],
					'enable' => $rs['enable'],
					'ean' => $rs['ean'],
					'delivelercode' => $rs['delivelercode'],
					'weight' => $rs['weight'],
					'packagesize' => $rs['packagesize'],
					'width' => $rs['width'],
					'height' => $rs['height'],
					'deepth' => $rs['deepth'],
					'unit' => $rs['unit'],
					'buyprice' => $rs['buyprice'],
					'sellprice' => $rs['sellprice'],
					'sellpricewithvatvalue' => $rs['sellpricewithvatvalue'],
					'vatvalueofsellprice' => $rs['vatvalueofsellprice'],
					'vatvalue' => $rs['vatvalue'],
					'id' => $rs['id'],
					'buycurrencyid' => $rs['buycurrencyid'],
					'sellcurrencyid' => $rs['sellcurrencyid'],
					'mainphotoid' => $rs['mainphotoid'],
					'vatid' => $rs['vatid'],
					'producerid' => $rs['producerid'],
					'collectionid' => $rs['collectionid'],
					'delivererid' => $this->getProductDeliverer($rs['id']),
					'category' => $this->productCategoryIds($rs['id']),
					'variants' => $this->getSuffixForProductById($rs['id'], $duplicate),
					'photo' => $this->productPhotoIds($rs['id']),
					'file' => $this->productFileIds($rs['id']),
					'technicaldatasetid' => $rs['technicaldatasetid'],
					'language' => $this->getProductTranslation($rs['id']),
					'productnew' => $this->getProductNew($rs['id']),
					'promotion' => $rs['promotion'],
					'discountprice' => $rs['discountprice'],
					'promotionstart' => $rs['promotionstart'],
					'promotionend' => $rs['promotionend'],
					'productstatuses' => $this->getProductStatuses($rs['id']),
					'disableatstock' => $rs['disableatstock'],
					'disableatstockenabled' => $rs['disableatstockenabled']
				);
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	public function getProductStatuses ($id)
	{
		$sql = 'SELECT productstatusid FROM productstatuses WHERE productid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['productstatusid'];
		}
		return $Data;
	}

	public function checkDuplicateNames ($name, $seo, $languageid, $counter)
	{
		$valid = false;

		while (true){
			$counter ++;

			$sql = "SELECT
						name,
						seo
					FROM producttranslation
					WHERE languageid = :languageid AND name = :name AND seo = :seo";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', $languageid);
			$stmt->bindValue('name', $name . ' (' . $counter . ')');
			$stmt->bindValue('seo', $seo . '-' . $counter);
			$stmt->execute();
			$rs = $stmt->fetch();
			if (! $rs){
				return Array(
					'name' => $name . ' (' . $counter . ')',
					'seo' => $seo . '-' . $counter
				);
				break;
			}
		}
	}

	public function getProductTranslation ($id)
	{
		$sql = "SELECT * FROM producttranslation WHERE productid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'shortdescription' => $rs['shortdescription'],
				'description' => $rs['description'],
				'longdescription' => $rs['longdescription'],
				'seo' => $rs['seo'],
				'keywordtitle' => $rs['keyword_title'],
				'keyworddescription' => $rs['keyword_description'],
				'keyword' => $rs['keyword']
			);
		}
		return $Data;
	}

	public function getSuffixForProductById ($id, $duplicate)
	{
		$sql = 'SELECT
					PAS.suffixtypeid as suffix,
					PAS.`value` as modifier,
					PAS.stock,
					PAS.symbol,
					PAS.weight,
					PAS.idproductattributeset,
					PAS.status,
					PAS.availablityid,
					PAS.photoid,
					PAS.attributegroupnameid,
					COUNT(orderid) AS total
			    FROM productattributeset AS PAS
			    LEFT JOIN orderproduct OP ON OP.productattributesetid = PAS.idproductattributeset
			    WHERE PAS.productid=:id
			    GROUP BY PAS.idproductattributeset';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		$i = 0;
		while ($rs = $stmt->fetch()){
			if ($duplicate == true){
				$deletable = ((int) $rs['total'] > 0) ? 0 : 1;

				if ($i === 0) {
					App::getContainer()->get('session')->setActiveSetId($rs['attributegroupnameid']);
				}
			}
			else{
				$deletable = 1;
			}
			$Data[] = Array(
				'idvariant' => ($duplicate == false) ? $rs['idproductattributeset'] : 'new-' . $i,
				'suffix' => $rs['suffix'],
				'modifier' => $rs['modifier'],
				'stock' => $rs['stock'],
				'symbol' => $rs['symbol'],
				'weight' => $rs['weight'],
				'availablity' => $rs['availablityid'],
				'photo' => $rs['photoid'],
				'attributes' => $this->getAttributesForProductById($rs['idproductattributeset']),
				'deletable' => $deletable,
				'status' => $rs['status']
			);
			$i ++;
		}
		return $Data;
	}

	public function getAttributesForProductById ($attrId)
	{
		$sql = "SELECT idattributeproduct as idattributegroup,
				      idattributeproductvalue as  idattribut
				    FROM productattributeset AS PAS
				      LEFT JOIN productattributevalueset AS PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				      LEFT JOIN attributeproductvalue AS APV ON APV.idattributeproductvalue = PAVS.attributeproductvalueid
				      LEFT JOIN attributeproduct AS AP ON AP.idattributeproduct = APV.attributeproductid
				    WHERE productattributesetid=:attrId";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attrId', $attrId);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['idattributegroup']] = $rs['idattribut'];
		}
		return $Data;
	}

	public function productPhoto ($id)
	{
		$sql = 'SELECT photoid AS id FROM productphoto WHERE productid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function productSelectedPhotos ($id)
	{
		$sql = 'SELECT
					F.name,
					F.idfile
				FROM productphoto PP
				LEFT JOIN file F ON PP.photoid = F.idfile
				WHERE PP.productid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['idfile'],
				'name' => $rs['name'],
				'thumb' => $this->getThumbPathForId($rs['idfile'])
			);
		}
		return $Data;
	}

	public function productFile ($id)
	{
		$sql = 'SELECT fileid AS id FROM productfile WHERE productid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function productPhotoIds ($id)
	{
		$Data = $this->productPhoto($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function productFileIds ($id)
	{
		$Data = $this->productFile($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function getProductNew ($id)
	{
		$sql = "SELECT
					startdate as startnew,
					enddate as endnew,
					active as newactive
				FROM productnew
				WHERE productid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'startnew' => $rs['startnew'],
				'endnew' => $rs['endnew'],
				'newactive' => $rs['newactive']
			);
		}

		if (empty($Data)){
			$Data = Array(
				'startnew' => '',
				'endnew' => '',
				'newactive' => '0'
			);
		}
		return $Data;
	}

	public function getProductDeliverer ($id)
	{
		$sql = "SELECT delivererid
					FROM productdeliverer
					WHERE productid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			return $rs['delivererid'];
		}
		return 0;
	}

	public function productProducer ($id)
	{
		$sql = 'SELECT producerid AS id FROM product WHERE idproduct = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function productProducerIds ($id)
	{
		$Data = $this->productProducer($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function productCategory ($id)
	{
		$sql = 'SELECT
					categoryid AS id
				FROM productcategory
				WHERE productid=:id
				GROUP BY categoryid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function productCategoryIds ($id)
	{
		$Data = $this->productCategory($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function productUpdateAll ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->productUpdate($Data, $id);
			$this->productPhotoUpdate($Data, $id);
			$this->productFileUpdate($Data, $id);
			App::getModel('technicaldata')->SaveValuesForProduct($id, $Data['technical_data']);
			if (isset($Data['variants']) && isset($Data['variants']['set']) && $Data['variants']['set'] != ''){
				$this->updateAttributesProduct($Data, $id);
			}
			$this->updateProductNew($Data, $id);
			$this->updateProductStatuses($Data, $id);
			$this->updateProductGroupPrices($Data, $id);
			$this->updateProductAttributesetPricesAll();
			if (! empty($Data['upsell'])){
				$upsell['products'] = $Data['upsell'];
				App::getModel('upsell')->editRelated($upsell, $id);
			}
			if (! empty($Data['similar'])){
				$similar['products'] = $Data['similar'];
				App::getModel('similarproduct')->editRelated($similar, $id);
			}
			if (! empty($Data['crosssell'])){
				$crosssell['products'] = $Data['crosssell'];
				App::getModel('crosssell')->editRelated($crosssell, $id);
			}

			$this->syncStock();

			Event::notify($this, 'admin.product.model.save', Array(
				'id' => $id,
				'data' => $Data
			));
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_EDIT'), 3002, $e->getMessage());
		}
		Db::getInstance()->commit();
		App::getModel('category')->flushCache();
	}

	protected function productFileUpdate ($Data, $idproduct)
	{
		if (isset($Data['file']['unmodified']) && $Data['file']['unmodified']){
			return;
		}

		DbTracker::deleteRows('productfile', 'productid', $idproduct);

		try{
			$this->addFileProduct($Data['file'], $idproduct);
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function productPhotoUpdate ($Data, $id)
	{
		if (isset($Data['photo']['unmodified']) && $Data['photo']['unmodified']){
			return;
		}

		DbTracker::deleteRows('productphoto', 'productid', $id);

		if (isset($Data['photo']['main'])){
			$mainphoto = $Data['photo']['main'];
			foreach ($Data['photo'] as $key => $photo){
				if (! is_array($photo) && is_int($key) && ($photo > 0)){
					$sql = 'INSERT INTO productphoto (productid, mainphoto, photoid)
								VALUES (:productid, :mainphoto, :photoid)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('productid', $id);
					$stmt->bindValue('mainphoto', ($photo == $mainphoto) ? 1 : 0);
					$stmt->bindValue('photoid', $photo);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_PRODUCT_PHOTO_UPDATE'), 112, $e->getMessage());
					}
				}
			}
		}
	}

	protected function updateProductNew ($Data, $id)
	{
		DbTracker::deleteRows('productnew', 'productid', $id);

		if ($Data['newactive'] == 1){
			$sql = 'INSERT INTO productnew (productid, startdate, enddate, active)
					VALUES (:productid, :startdate, :enddate, :active)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			if ($Data['endnew'] == '' || $Data['endnew'] == '0000-00-00 00:00:00'){
				$stmt->bindValue('enddate', NULL);
			}
			else{
				$stmt->bindValue('enddate', $Data['endnew']);
			}
			if ($Data['startnew'] == '' || $Data['startnew'] == '0000-00-00 00:00:00'){
				$stmt->bindValue('startdate', NULL);
			}
			else{
				$stmt->bindValue('startdate', $Data['startnew']);
			}
			$stmt->bindValue('active', $Data['newactive']);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_NEW_ADD'), 112, $e->getMessage());
			}
		}
	}

	protected function updateProductStatuses ($Data, $id)
	{
		DbTracker::deleteRows('productstatuses', 'productid', $id);

		if (isset($Data['productstatuses']) && ! empty($Data['productstatuses'])){
			foreach ($Data['productstatuses'] as $status){
				$sql = 'INSERT INTO productstatuses (productid, productstatusid)
						VALUES (:productid, :productstatusid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $id);
				$stmt->bindValue('productstatusid', $status);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_NEW_ADD'), 112, $e->getMessage());
				}
			}
		}
	}

	public function productUpdate ($Data, $id)
	{
		$this->updateProductcategory($Data['category'], $id);
		$sql = 'UPDATE product SET
					producerid=:producerid,
					collectionid=:collectionid,
					stock=:stock,
					enable=:enable,
					trackstock=:trackstock,
					availablityid=:availablityid,
					weight=:weight,
					packagesize=:packagesize,
					width=:width,
					height=:height,
					deepth=:deepth,
					unit=:unit,
					ean=:ean,
					delivelercode=:delivelercode,
					buyprice=:buyprice,
					sellprice=:sellprice,
					buycurrencyid=:buycurrencyid,
					sellcurrencyid=:sellcurrencyid,
					vatid=:vatid,
					technicaldatasetid=:setid,
					promotion = :promotion,
					discountprice = :discountprice,
					promotionstart = :promotionstart,
					promotionend = :promotionend,
					disableatstock = :disableatstock,
					disableatstockenabled = :disableatstockenabled
				WHERE idproduct = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('stock', $Data['stock']);
		$stmt->bindValue('trackstock', ((int) $Data['trackstock'] == 1) ? 1 : 0);
		if ($Data['availablityid'] > 0){
			$stmt->bindValue('availablityid', $Data['availablityid']);
		}
		else{
			$stmt->bindValue('availablityid', NULL);
		}
		$stmt->bindValue('weight', $Data['weight']);
		$stmt->bindValue('packagesize', $Data['packagesize']);
		if ($Data['width'] != ''){
			$stmt->bindValue('width', abs($Data['width']));
		}
		else{
			$stmt->bindValue('width', NULL);
		}
		if ($Data['height'] != ''){
			$stmt->bindValue('height', abs($Data['height']));
		}
		else{
			$stmt->bindValue('height', NULL);
		}
		if ($Data['deepth'] != ''){
			$stmt->bindValue('deepth', abs($Data['deepth']));
		}
		else{
			$stmt->bindValue('deepth', NULL);
		}
		if ($Data['unit'] > 0){
			$stmt->bindValue('unit', $Data['unit']);
		}
		else{
			$stmt->bindValue('unit', NULL);
		}
		$stmt->bindValue('ean', $Data['ean']);
		$stmt->bindValue('delivelercode', $Data['delivelercode']);
		if ($Data['producerid'] > 0){
			$stmt->bindValue('producerid', $Data['producerid']);
		}
		else{
			$stmt->bindValue('producerid', NULL);
		}
		if ($Data['producerid'] > 0 && $Data['collectionid'] > 0){
			$stmt->bindValue('collectionid', $Data['collectionid']);
		}
		else{
			$stmt->bindValue('collectionid', NULL);
		}
		if (isset($Data['enable']) && $Data['enable'] == 1){
			$stmt->bindValue('enable', $Data['enable']);
		}
		else{
			$stmt->bindValue('enable', 0);
		}
		if (isset($Data['promotion']) && $Data['promotion'] == 1){
			$stmt->bindValue('promotion', $Data['promotion']);
			$stmt->bindValue('discountprice', $Data['discountprice']);
			if ($Data['promotionstart'] != ''){
				$stmt->bindValue('promotionstart', $Data['promotionstart']);
			}
			else{
				$stmt->bindValue('promotionstart', NULL);
			}
			if ($Data['promotionend'] != ''){
				$stmt->bindValue('promotionend', $Data['promotionend']);
			}
			else{
				$stmt->bindValue('promotionend', NULL);
			}
		}
		else{
			$stmt->bindValue('promotion', 0);
			$stmt->bindValue('discountprice', 0);
			$stmt->bindValue('promotionstart', NULL);
			$stmt->bindValue('promotionend', NULL);
		}

		$stmt->bindValue('disableatstock', $Data['disableatstock']);
		$stmt->bindValue('disableatstockenabled', ($Data['disableatstockenabled'] == 1) ? 1 : 0);
		$stmt->bindValue('vatid', ((int) $Data['vatid'] > 0) ? $Data['vatid'] : App::getModel('view')->getDefaultVatId());
		$stmt->bindValue('buyprice', $Data['buyprice']);
		$stmt->bindValue('sellprice', $Data['sellprice']);
		$stmt->bindValue('buycurrencyid', $Data['buycurrencyid']);
		$stmt->bindValue('sellcurrencyid', $Data['sellcurrencyid']);
		$stmt->bindValue('setid', ($Data['technical_data']['set'] > 0) ? $Data['technical_data']['set'] : NULL);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
		}

		DbTracker::deleteRows('producttranslation', 'productid', $id);

		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO producttranslation (productid, name, shortdescription,longdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:productid, :name, :shortdescription,:longdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('shortdescription', $Data['shortdescription'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('longdescription', $Data['longdescription'][$key]);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('seo', Core::clearSeoUTF($Data['seo'][$key]));
			$stmt->bindValue('keyword_title', $Data['keywordtitle'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyworddescription'][$key]);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}
		}

		DbTracker::deleteRows('productdeliverer', 'productid', $id);

		if ($Data['delivererid'] > 0){
			$sql = 'INSERT INTO productdeliverer (productid, delivererid)
						VALUES (:productid, :delivererid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('delivererid', $Data['delivererid']);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_PRODUCT_ADD'), 11, $e->getMessage());
			}
		}

		return true;
	}

	public function updateProductcategory ($array, $id)
	{
		DbTracker::deleteRows('productcategory', 'productid', $id);

		if (! is_array($array))
			return;
		foreach ($array as $value){
			$sql = 'INSERT INTO productcategory (productid, categoryid)
						VALUES (:productid, :categoryid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('categoryid', $value);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
			}
		}
	}

	public function updateAttributesProduct ($Data, $productid)
	{
		$this->deleteAttributesProductValueSet($productid);
		if (isset($Data['variants']) && is_array($Data['variants'])){
			$this->deleteAttributesProductSet($productid, array_keys($Data['variants']));
			$this->addAttributesProducts($Data['variants'], $productid);
		}
		$this->updateProductAttributesetPricesAll();
	}

	public function addAttributesProducts ($variant, $newProductId)
	{
		if (empty($variant)){
			return;
		}
		foreach ($variant as $key => $attributegroup){
			if (is_array($attributegroup)){
				if (substr($key, 0, 3) == 'new'){
					$sql = 'INSERT INTO productattributeset (
							productid,
							stock,
							symbol,
							status,
							weight,
							suffixtypeid,
							value,
							attributegroupnameid,
							availablityid,
							photoid
						)
						VALUES
						(
							:productid,
							:stock,
							:symbol,
							:status,
							:weight,
							:suffixtypeid,
							:value,
							:attributegroupnameid,
							:availablityid,
							:photoid
						)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('productid', $newProductId);
					$stmt->bindValue('stock', $attributegroup['stock']);
					$stmt->bindValue('suffixtypeid', $attributegroup['suffix']);
					$stmt->bindValue('value', $attributegroup['modifier']);
					$stmt->bindValue('symbol', $attributegroup['symbol']);
					$stmt->bindValue('status', $attributegroup['status']);
					$stmt->bindValue('weight', $attributegroup['weight']);
					$stmt->bindValue('availablityid', ((int) $attributegroup['availablity'] > 0) ? $attributegroup['availablity'] : NULL);
					$stmt->bindValue('photoid', ((int) $attributegroup['photo'] > 0) ? $attributegroup['photo'] : NULL);
					$stmt->bindValue('attributegroupnameid', isset($variant['set']) ? $variant['set'] : App::getContainer()->get('session')->getActiveSetId());
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_PRODUCT_ATTRIBUTES_ADD'), 112, $e->getMessage());
					}
					$contener = Db::getInstance()->lastInsertId();
					if (is_array($attributegroup['attributes'])){
						$this->getProductVariant($attributegroup['attributes'], $contener);
					}
				}
				else{
					$sql = 'UPDATE productattributeset SET
								stock = :stock,
								symbol = :symbol,
								status = :status,
								weight = :weight,
								suffixtypeid = :suffixtypeid,
								value = :value,
								attributegroupnameid = :attributegroupnameid,
								availablityid = :availablityid,
								photoid = :photoid
							WHERE productid = :productid AND idproductattributeset = :id';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('productid', $newProductId);
					$stmt->bindValue('id', $key);
					$stmt->bindValue('stock', $attributegroup['stock']);
					$stmt->bindValue('suffixtypeid', $attributegroup['suffix']);
					$stmt->bindValue('value', $attributegroup['modifier']);
					$stmt->bindValue('symbol', $attributegroup['symbol']);
					$stmt->bindValue('status', $attributegroup['status']);
					$stmt->bindValue('weight', $attributegroup['weight']);
					$stmt->bindValue('attributegroupnameid', $variant['set']);
					$stmt->bindValue('availablityid', ((int) $attributegroup['availablity'] > 0) ? $attributegroup['availablity'] : NULL);
					$stmt->bindValue('photoid', ((int) $attributegroup['photo'] > 0) ? $attributegroup['photo'] : NULL);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_PRODUCT_ATTRIBUTES_ADD'), 112, $e->getMessage());
					}
					if (isset($attributegroup['attributes']) && is_array($attributegroup['attributes'])){
						$this->getProductVariant($attributegroup['attributes'], $key);
					}
				}
			}
		}

		if (App::getContainer()->get('session')->getActiveSetId()) {
			App::getContainer()->get('session')->setActiveSetId(NULL);
		}
	}

	protected function deleteAttributesProductSet ($productid, $variants)
	{
		$imp = Array();
		foreach ($variants as $variant){
			$imp[] = "'" . $variant . "'";
		}
		$sql = 'DELETE FROM productattributeset WHERE productid = :productid AND idproductattributeset NOT IN(' . implode(',', $imp) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_ATTRIBUTE_DELETE'), 112, $e->getMessage());
		}
	}

	protected function deleteAttributesProductValueSet ($productid)
	{
		$sql = 'SELECT idproductattributeset FROM productattributeset WHERE productid = :productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['idproductattributeset'];
		}
		foreach ($Data as $attributesetid){
			DbTracker::deleteRows('productattributevalueset', 'productattributesetid', $attributesetid);
		}
	}

	public function addNewProduct ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newProductId = $this->newProduct($Data);
			$this->addProductTranslation($Data, $newProductId);
			$this->addPhotoProduct($Data['photo'], $newProductId);
			$this->addFileProduct($Data['file'], $newProductId);
			if ($Data['category'] > 0){
				$this->addProductToCategoryGroup($Data['category'], $newProductId);
			}
			if (! empty($Data['variants'])){
				$this->addAttributesProducts($Data['variants'], $newProductId);
			}
			if (! empty($Data['upsell'])){
				$upsell['products'] = $Data['upsell'];
				App::getModel('upsell')->editRelated($upsell, $newProductId);
			}
			if (! empty($Data['similar'])){
				$similar['products'] = $Data['similar'];
				App::getModel('similarproduct')->editRelated($similar, $newProductId);
			}
			if (! empty($Data['crosssell'])){
				$crosssell['products'] = $Data['crosssell'];
				App::getModel('crosssell')->editRelated($crosssell, $newProductId);
			}
			$this->updateProductNew($Data, $newProductId);
			$this->updateProductStatuses($Data, $newProductId);
			$this->updateProductGroupPrices($Data, $newProductId);
			$this->updateProductAttributesetPricesAll();
			App::getModel('technicaldata')->SaveValuesForProduct($newProductId, $Data['technical_data']);

			$this->syncStock();

			Event::notify($this, 'admin.product.model.save', Array(
				'id' => $newProductId,
				'data' => $Data
			));
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_ADD'), 112, $e->getMessage());
		}

		Db::getInstance()->commit();

		App::getModel('category')->flushCache();

		return true;
	}

	public function newProduct ($Data)
	{
		$sql = 'INSERT INTO product SET
					producerid =:producerid,
					collectionid =:collectionid,
					stock=:stock,
					trackstock=:trackstock,
					availablityid=:availablityid,
					enable=:enable,
					weight=:weight,
					packagesize=:packagesize,
					width=:width,
					height=:height,
					deepth=:deepth,
					unit=:unit,
					vatid=:vatid,
					ean=:ean,
					delivelercode=:delivelercode,
					buyprice=:buyprice,
					sellprice=:sellprice,
					buycurrencyid = :buycurrencyid,
					sellcurrencyid = :sellcurrencyid,
					technicaldatasetid=:setid,
					promotion = :promotion,
					discountprice = :discountprice,
					promotionstart = :promotionstart,
					promotionend = :promotionend,
					disableatstock = :disableatstock,
					disableatstockenabled = :disableatstockenabled';
		$stmt = Db::getInstance()->prepare($sql);
		if ($Data['producerid'] > 0){
			$stmt->bindValue('producerid', $Data['producerid']);
		}
		else{
			$stmt->bindValue('producerid', NULL);
		}
		if ($Data['producerid'] > 0 && $Data['collectionid'] > 0){
			$stmt->bindValue('collectionid', $Data['collectionid']);
		}
		else{
			$stmt->bindValue('collectionid', NULL);
		}
		if (isset($Data['enable']) && $Data['enable'] == 1){
			$stmt->bindValue('enable', $Data['enable']);
		}
		else{
			$stmt->bindValue('enable', 0);
		}
		$stmt->bindValue('stock', $Data['stock']);
		$stmt->bindValue('trackstock', ($Data['trackstock'] == 1) ? 1 : 0);
		if ($Data['availablityid'] > 0){
			$stmt->bindValue('availablityid', $Data['availablityid']);
		}
		else{
			$stmt->bindValue('availablityid', NULL);
		}
		$stmt->bindValue('weight', $Data['weight']);
		$stmt->bindValue('packagesize', $Data['packagesize']);
		if ($Data['width'] != ''){
			$stmt->bindValue('width', abs($Data['width']));
		}
		else{
			$stmt->bindValue('width', NULL);
		}
		if ($Data['height'] != ''){
			$stmt->bindValue('height', abs($Data['height']));
		}
		else{
			$stmt->bindValue('height', NULL);
		}
		if ($Data['deepth'] != ''){
			$stmt->bindValue('deepth', abs($Data['deepth']));
		}
		else{
			$stmt->bindValue('deepth', NULL);
		}
		if ($Data['unit'] > 0){
			$stmt->bindValue('unit', $Data['unit']);
		}
		else{
			$stmt->bindValue('unit', NULL);
		}
		$stmt->bindValue('ean', $Data['ean']);
		$stmt->bindValue('delivelercode', $Data['delivelercode']);
		$stmt->bindValue('buyprice', $Data['buyprice']);
		$stmt->bindValue('vatid', ((int) $Data['vatid'] > 0) ? $Data['vatid'] : App::getModel('view')->getDefaultVatId());
		$stmt->bindValue('sellprice', $Data['sellprice']);
		$stmt->bindValue('buycurrencyid', $Data['buycurrencyid']);
		$stmt->bindValue('sellcurrencyid', $Data['sellcurrencyid']);
		$stmt->bindValue('disableatstock', $Data['disableatstock']);
		$stmt->bindValue('disableatstockenabled', ($Data['disableatstockenabled'] == 1) ? 1 : 0);

		$stmt->bindValue('setid', ($Data['technical_data']['set'] ? $Data['technical_data']['set'] : NULL));
		if (isset($Data['promotion']) && $Data['promotion'] == 1){
			$stmt->bindValue('promotion', $Data['promotion']);
			$stmt->bindValue('discountprice', $Data['discountprice']);
			if ($Data['promotionstart'] != ''){
				$stmt->bindValue('promotionstart', $Data['promotionstart']);
			}
			else{
				$stmt->bindValue('promotionstart', NULL);
			}
			if ($Data['promotionend'] != ''){
				$stmt->bindValue('promotionend', $Data['promotionend']);
			}
			else{
				$stmt->bindValue('promotionend', NULL);
			}
		}
		else{
			$stmt->bindValue('promotion', 0);
			$stmt->bindValue('discountprice', 0);
			$stmt->bindValue('promotionstart', NULL);
			$stmt->bindValue('promotionend', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_ADD'), 112, $e->getMessage());
		}
		$id = Db::getInstance()->lastInsertId();

		if ($Data['delivererid'] > 0){
			$sql = 'INSERT INTO productdeliverer (productid, delivererid)
						VALUES (:productid, :delivererid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('delivererid', $Data['delivererid']);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_PRODUCT_ADD'), 11, $e->getMessage());
			}
		}

		return $id;
	}

	public function addProductTranslation ($Data, $productid)
	{
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO producttranslation (productid, name, shortdescription,longdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
						VALUES (:productid, :name, :shortdescription,:longdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $productid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('shortdescription', $Data['shortdescription'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('longdescription', $Data['longdescription'][$key]);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('seo', Core::clearSeoUTF($Data['seo'][$key]));
			$stmt->bindValue('keyword_title', $Data['keywordtitle'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyworddescription'][$key]);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_TRANSLATION_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function addPhotoProduct ($array, $productId)
	{
		if ($array['unmodified'] == 0 && isset($array['main'])){
			$mainphoto = $array['main'];
			foreach ($array as $key => $photo){
				if (! is_array($photo) && is_int($key)){
					$sql = 'INSERT INTO productphoto (productid, mainphoto, photoid)
								VALUES (:productid, :mainphoto, :photoid)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('productid', $productId);
					$stmt->bindValue('mainphoto', ($photo == $mainphoto) ? 1 : 0);
					$stmt->bindValue('photoid', $photo);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_PRODUCT_PHOTO_ADD'), 112, $e->getMessage());
					}
				}
			}
		}
		else{
			$photos = $this->productPhotoIds($this->registry->core->getParam());
			foreach ($photos as $key => $val){
				$sql = 'INSERT INTO productphoto (productid, mainphoto, photoid)
							VALUES (:productid, :mainphoto, :photoid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $productId);
				$stmt->bindValue('mainphoto', 1);
				$stmt->bindValue('photoid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_PHOTO_ADD'), 112, $e->getMessage());
				}
			}
		}
	}

	public function addFileProduct ($Data, $productId)
	{
		foreach ($Data as $key => $file){
			if (is_int($key) && $file != 0){
				$sql = 'INSERT INTO productfile (productid, fileid)
							VALUES (:productid, :fileid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $productId);
				$stmt->bindValue('fileid', $file);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_FILE_ADD'), 112, $e->getMessage());
				}
			}
		}
	}

	public function addProductToCategoryGroup ($array, $ProductId)
	{
		foreach ($array as $value){
			$sql = 'INSERT INTO productcategory (productid, categoryid)
						VALUES (:productid, :categoryid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $ProductId);
			$stmt->bindValue('categoryid', $value);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function addProductNew ($Data, $productId)
	{
		if ($Data['newactive'] == 1){
			$sql = 'INSERT INTO productnew (productid, startdate, enddate)
					VALUES (:productid, :startdate, :enddate)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $productId);
			$stmt->bindValue('startdate', $Data['startnew']);
			$stmt->bindValue('enddate', $Data['endnew']);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_NEW_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function getProductVariant ($attributes, $contener)
	{
		foreach ($attributes as $key => $variant){
			$sql = 'INSERT INTO productattributevalueset (attributeproductvalueid, productattributesetid)
						VALUES (:attributeproductvalueid, :productattributesetid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('attributeproductvalueid', $variant);
			$stmt->bindValue('productattributesetid', $contener);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_VARIANT_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function getProductVariantDetails ($product)
	{
		$id = $product['id'];
		if (($product['variant']) > 0){
			$sql = "SELECT
						PAS.suffixtypeid,
						PAS.value,
						PAS.status,
						P.sellprice,
						V.value as vatvalue,
						PT.name,
						PAS.attributeprice AS variantprice,
						ROUND(PAS.attributeprice * (1 + (V.value / 100)), 2) as variantpricevat
					FROM product P
					LEFT JOIN productattributeset PAS ON PAS.productid = P.idproduct
					LEFT JOIN producttranslation PT ON PT.productid = P.idproduct
					LEFT JOIN vat V ON V.idvat = P.vatid
					WHERE idproduct=:id and idproductattributeset=:attributes";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('attributes', $product['variant']);
			$stmt->execute();
			$rs = $stmt->fetch();
			$Data = Array();
			if ($rs){
				$Data = Array(
					'id' => $product['id'],
					'quantity' => $product['quantity'],
					'variant' => $product['variant'],
					'name' => $rs['name'],
					'sellprice' => ($rs['variantprice'] * $product['quantity']),
					'sellprice_gross' => ($rs['variantpricevat'] * $product['quantity'])
				);
			}
			return $Data;
		}
		else{
			$sql = "SELECT PT.name, P.sellprice,
						ROUND(P.sellprice +(P.sellprice * V.value/100), 2)  as variantpricevat
						FROM product P
						LEFT JOIN producttranslation PT ON PT.productid = P.idproduct
						LEFT JOIN vat V ON V.idvat = P.vatid
						WHERE P.idproduct=:id";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->execute();
			$rs = $stmt->fetch();
			$Data = Array();
			if ($rs){
				$Data = Array(
					'id' => $product['id'],
					'quantity' => $product['quantity'],
					'variant' => $product['variant'],
					'name' => $rs['name'],
					'sellprice' => ($rs['sellprice'] * $product['quantity']),
					'sellprice_gross' => ($rs['variantpricevat'] * $product['quantity'])
				);
			}
			return $Data;
		}
	}

	public function loadCategoryChildren ($request)
	{
		return Array(
			'aoItems' => $this->getCategories($request['parentId'])
		);
	}

	public function getCategories ($parent = 0)
	{
		$categories = App::getModel('category')->getChildCategories($parent);
		usort($categories, Array(
			$this,
			'sortCategories'
		));
		return $categories;
	}

	protected function sortCategories ($a, $b)
	{
		return $a['weight'] - $b['weight'];
	}

	public function doAJAXUpdateProduct ($request)
	{
		$id = $request['id'];
		$oRow = $request['product'];
		$product = $this->getProductView($id, false);
		$vatValue = $product['vatvalue'];

		$sql = 'UPDATE product SET
					enable = :enable,
					producerid = IF(:producer != \'\', (SELECT producerid FROM producertranslation WHERE name = :producer AND languageid = :languageid), NULL),
					ean = :ean,
					stock = :stock,
					sellprice = (:price / (1 + (:vat / 100))) ,
					hierarchy = :hierarchy
				WHERE idproduct = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('ean', $oRow['ean']);
		$stmt->bindValue('stock', abs($oRow['stock']));
		$stmt->bindValue('price', abs($oRow['sellprice_gross']));
		$stmt->bindValue('hierarchy', abs($oRow['hierarchy']));
		$stmt->bindValue('producer', $oRow['producer']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('enable', abs($oRow['enable']));
		$stmt->bindValue('vat', $vatValue);
		$stmt->execute();

		$this->syncStock();

		return Array(
			'attr' => 1
		);
	}

	public function doCartesianProduct ($array)
	{
		$current = array_shift($array);
		if (count($array) > 0){
			$results = array();
			$temp = $this->doCartesianProduct($array);
			foreach ($current as $word){
				foreach ($temp as $value){
					$raw = Array();
					if (is_array($value)){
						$raw[] = $word;
						foreach ($value as $key => $val){
							$raw[] = $val;
						}
						$results[] = $raw;
					}
					else{
						$results[] = array(
							$word,
							$value
						);
					}
				}
			}
			return $results;
		}
		else{
			return $current;
		}
	}

	public function doAJAXCreateCartesianVariants ($request)
	{
		$sql = 'SELECT
				   	APV.idattributeproductvalue,
					APV.attributeproductid,
					APV.name
				FROM attributeproductvalue APV
				INNER JOIN attributegroup AG ON AG.attributeproductid = APV.attributeproductid
				WHERE AG.attributegroupnameid = :set AND APV.idattributeproductvalue IN(' . implode(',', $request['ids']) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('set', $request['setid']);
		$stmt->execute();
		$Attributes = Array();
		while ($rs = $stmt->fetch()){
			$Attributes[$rs['attributeproductid']][] = $rs['idattributeproductvalue'];
			$Values[$rs['idattributeproductvalue']] = Array(
				'sAttributeId' => $rs['attributeproductid'],
				'sValueName' => $rs['name'],
				'sValueId' => $rs['idattributeproductvalue']
			);
		}
		if (count($Attributes) > 1){
			$Cartesian = $this->doCartesianProduct($Attributes);
			foreach ($Cartesian as $k => $combination){
				foreach ($combination as $key => $variant){
					$CombinedAttributes[$k][$key] = $Values[$variant];
				}
			}
		}
		else{
			foreach ($Values as $key => $variant){
				$CombinedAttributes[$key][0] = $variant;
			}
		}
		return $CombinedAttributes;
	}

	public function updateProductAttributesetPricesAll ()
	{
		$sql = 'UPDATE productattributeset, product SET
					productattributeset.attributeprice =
					CASE
						WHEN (productattributeset.suffixtypeid = 1) THEN ROUND(product.sellprice * (productattributeset.value / 100), 4)
						WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(product.sellprice + productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.sellprice - productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(productattributeset.value,4)
					END,
					productattributeset.discountprice =
					IF(product.promotion = 1,
					CASE
						WHEN (productattributeset.suffixtypeid = 1) THEN ROUND(product.discountprice * (productattributeset.value / 100), 4)
						WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(product.discountprice + productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.discountprice - productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(productattributeset.value,4)
					END, NULL)
				WHERE productattributeset.productid = product.idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}

	public function updateProductGroupPrices ($Data, $id)
	{
		$clientGroups = App::getModel('clientgroup/clientgroup')->getClientGroupAll();

		DbTracker::deleteRows('productgroupprice', 'productid', $id);

		$sql = 'INSERT INTO productgroupprice SET
					productid = :productid,
					clientgroupid = :clientgroupid,
					groupprice = :groupprice,
					sellprice = :sellprice,
					promotion = :promotion,
					discountprice = :discountprice,
					promotionstart = :promotionstart,
					promotionend = :promotionend
		';
		foreach ($clientGroups as $group){
			$clientgroupid = $group['id'];

			if ((isset($Data['groupid_' . $clientgroupid]) && $Data['groupid_' . $clientgroupid] == 1) || (isset($Data['promotion_' . $clientgroupid]) && $Data['promotion_' . $clientgroupid] == 1)){
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $id);
				$stmt->bindValue('clientgroupid', $clientgroupid);
				if (isset($Data['groupid_' . $clientgroupid]) && $Data['groupid_' . $clientgroupid] == 1){
					$stmt->bindValue('groupprice', 1);
					$stmt->bindValue('sellprice', $Data['sellprice_' . $clientgroupid]);
				}
				else{
					$stmt->bindValue('groupprice', 0);
					$stmt->bindValue('sellprice', 0);
				}
				if (isset($Data['promotion_' . $clientgroupid]) && $Data['promotion_' . $clientgroupid] == 1){
					$stmt->bindValue('promotion', 1);
					$stmt->bindValue('discountprice', $Data['discountprice_' . $clientgroupid]);
					if ($Data['promotionstart_' . $clientgroupid] != ''){
						$stmt->bindValue('promotionstart', $Data['promotionstart_' . $clientgroupid]);
					}
					else{
						$stmt->bindValue('promotionstart', NULL);
					}
					if ($Data['promotionend_' . $clientgroupid] != ''){
						$stmt->bindValue('promotionend', $Data['promotionend_' . $clientgroupid]);
					}
					else{
						$stmt->bindValue('promotionend', NULL);
					}
				}
				else{
					$stmt->bindValue('promotion', 0);
					$stmt->bindValue('discountprice', 0);
					$stmt->bindValue('promotionstart', NULL);
					$stmt->bindValue('promotionend', NULL);
				}
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_GROUP_PRICE_ADD'), 112, $e->getMessage());
				}
			}
		}
	}

	public function getProductGroupPrice ($id, $flat = true)
	{
		$sql = 'SELECT
				   *
				FROM productgroupprice
				WHERE productid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$clientgroupid = $rs['clientgroupid'];
			if ($flat){
				$Data['groupid_' . $clientgroupid] = $rs['groupprice'];
				$Data['sellprice_' . $clientgroupid] = $rs['sellprice'];
				$Data['promotion_' . $clientgroupid] = $rs['promotion'];
				$Data['discountprice_' . $clientgroupid] = $rs['discountprice'];
				$Data['promotionstart_' . $clientgroupid] = $rs['promotionstart'];
				$Data['promotionend_' . $clientgroupid] = $rs['promotionend'];
			}
			else{
				$Data['field_' . $clientgroupid]['groupid_' . $clientgroupid] = $rs['groupprice'];
				$Data['field_' . $clientgroupid]['sellprice_' . $clientgroupid] = $rs['sellprice'];
				$Data['field_' . $clientgroupid]['promotion_' . $clientgroupid] = $rs['promotion'];
				$Data['field_' . $clientgroupid]['discountprice_' . $clientgroupid] = $rs['discountprice'];
				$Data['field_' . $clientgroupid]['promotionstart_' . $clientgroupid] = $rs['promotionstart'];
				$Data['field_' . $clientgroupid]['promotionend_' . $clientgroupid] = $rs['promotionend'];
			}
		}
		return $Data;
	}

	public function addEmptyProductStatus ($request)
	{
		$id = App::getModel('productstatus')->addNewProductStatus($request);
		$Data = $this->getProductStatuses($this->registry->core->getParam());
		$statuses = App::getModel('productstatus')->getProductstatusAsExchangeOptions();
		$Data[] = $id;
		return Array(
			'id' => $Data,
			'options' => $statuses
		);
	}

	public function syncStock ()
	{
		$sql = 'UPDATE product SET stock = (SELECT IF(SUM(productattributeset.stock) IS NOT NULL, SUM(productattributeset.stock), product.stock) FROM productattributeset WHERE productattributeset.productid = product.idproduct)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		$sql = 'UPDATE product SET enable = IF(stock > disableatstock, 1, 0) WHERE disableatstockenabled = 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}
}