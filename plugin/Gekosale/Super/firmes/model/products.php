<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: product.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale;

class ProductsModel extends Component\Model\Dataset
{

	public function initDataset ($dataset)
	{
		$dataset->setTableData(Array(
			'id' => Array(
				'source' => 'P.idproduct'
			),
			'adddate' => Array(
				'source' => 'P.adddate'
			),
			'editdate' => Array(
				'source' => 'P.adddate'
			),
			'name' => Array(
				'source' => 'PT.name'
			),
			'url' => Array(
				'source' => 'PT.seo',
				'processFunction' => Array(
					$this,
					'getURL'
				)
			),
			'producername' => Array(
				'source' => 'PRT.name'
			),
			'producerid' => Array(
				'source' => 'PRT.producerid'
			),
			'ean' => Array(
				'source' => 'P.ean'
			),
			'barcode' => Array(
				'source' => 'P.barcode'
			),
			'weight' => Array(
				'source' => 'P.weight'
			),
			'delivelercode' => Array(
				'source' => 'P.delivelercode'
			),
			'shortdescription' => Array(
				'source' => 'PT.shortdescription'
			),
			'description' => Array(
				'source' => 'PT.description'
			),
			'seo' => Array(
				'source' => 'PT.seo'
			),
			'stock' => Array(
				'source' => 'P.stock'
			),
			'currency' => Array(
				'source' => 'CUR.currencysymbol'
			),
			'pricenetto' => Array(
				'source' => 'P.sellprice * CR.exchangerate',
				'processFunction' => Array(
					$this,
					'parsePrice'
				)
			),
			'price' => Array(
				'source' => 'P.sellprice * (1 + (V.value / 100)) * CR.exchangerate',
				'processFunction' => Array(
					$this,
					'parsePrice'
				)
			),
			'buypricenetto' => Array(
				'source' => 'ROUND(P.buyprice * CR.exchangerate, 2)',
				'processFunction' => Array(
					$this,
					'parsePrice'
				)
			),
			'buyprice' => Array(
				'source' => 'ROUND((P.buyprice + (P.buyprice * V.`value`)/100) * CR.exchangerate, 2)',
				'processFunction' => Array(
					$this,
					'parsePrice'
				)
			),
			'discountpricenetto' => Array(
				'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * CR.exchangerate, NULL)',
				'processFunction' => Array(
					$this,
					'parsePrice'
				)
			),
			'discountprice' => Array(
				'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL)',
				'processFunction' => Array(
					$this,
					'parsePrice'
				)
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
			'attributes' => Array(
				'source' => 'P.idproduct',
				'processFunction' => Array(
					$this,
					'getAttributesForProductById'
				)
			),
			'categorypath' => Array(
				'source' => '(SELECT
							    GROUP_CONCAT(SUBSTRING(CT.name, 1) ORDER BY C.order DESC SEPARATOR \' / \')
							FROM categorytranslation CT
							LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
							WHERE C.categoryid = PC.categoryid AND CT.languageid = :languageid)'
			),
			'categoryid' => Array(
				'source' => 'PC.categoryid'
			),
			'viewid' => Array(
				'source' => '0'
			),
			'vat' => Array(
				'source' => 'V.value'
			)
		));
		
		$dataset->setFrom('
			productcategory PC
			LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
			LEFT JOIN product P ON PC.productid= P.idproduct
			LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
			LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
			LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
			LEFT JOIN productnew PN ON P.idproduct = PN.productid
			LEFT JOIN vat V ON P.vatid= V.idvat
			LEFT JOIN productreview PREV ON PREV.productid = P.idproduct
			LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
			LEFT JOIN currency CUR ON CUR.idcurrency = P.sellcurrencyid
			LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = (SELECT idcurrency FROM currency WHERE currencysymbol = :currencysymbol)
		');
		
		$dataset->setAdditionalWhere('
			P.adddate > :filterdate
		');
		
		$dataset->setGroupBy('
			P.idproduct
		');
	}

	public function parsePrice ($price)
	{
		return number_format($price, 2);
	}

	public function getURL ($seo)
	{
		return $this->registry->router->generate('frontend.productcart', true, Array(
			'param' => $seo
		));
	}

	public function getProductDataset ()
	{
		return $this->getDataset()->getDatasetRecords();
	}

	public function getAttributesForProductById ($id)
	{
		$sql = "SELECT 
					P.idproduct as id, 
					V.value AS vat,
					PAS.stock, 
					PAS.idproductattributeset, 
					PAS.`value`,
					PAS.symbol AS ean,
					PAS.symbol AS barcode,
					PAS.adddate AS adddate,
					IF(PAS.weight IS NULL, P.weight, PAS.weight) AS weight,
					PAVS.idproductattributevalueset, 
					PAVS.productattributesetid AS attributesgroup,
					APV.name AS attributename, 
					APV.idattributeproductvalue AS attributeid,
					AP.name AS attributegroupname, 
					AP.idattributeproduct AS attributegroupid, 
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), PAS.discountprice, PAS.attributeprice) * CR.exchangerate AS attributeprice, 
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), PAS.discountprice, PAS.attributeprice) * (1 + (V.value / 100)) * CR.exchangerate AS price 
	            FROM productattributeset AS PAS
				LEFT JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
				LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct 
				LEFT JOIN product AS P ON PAS.productid = P.idproduct
				LEFT JOIN `vat` V ON P.vatid = V.idvat 
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				WHERE PAS.productid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('currencyto', 28);
		$Data = Array();
		$stmt->execute();
		try{
			while ($rs = $stmt->fetch()){
				$price = 0;
				$priceWithoutVat = 0;
				$attrId = $rs['idproductattributeset'];
				
				$Data[] = Array(
					'id' => $rs['id'],
					'stock' => $rs['stock'],
					'ean' => $rs['ean'],
					'vat' => $rs['vat'],
					'barcode' => $rs['barcode'],
					'weight' => $rs['weight'],
					'adddate' => $rs['adddate'],
					'idproductattributeset' => $rs['idproductattributeset'],
					'idproductattributevalueset' => $rs['idproductattributevalueset'],
					'attributesgroup' => $rs['attributesgroup'],
					'attributename' => $rs['attributename'],
					'attributeid' => $rs['attributeid'],
					'attributegroupname' => $rs['attributegroupname'],
					'attributegroupid' => $rs['attributegroupid'],
					'attributepricenetto' => $rs['attributeprice'],
					'attributeprice' => $rs['price'],
					'value' => $rs['value']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return $Data;
	}

	public function addUpdateProduct ($Data)
	{
		$producerid = 0;
		
		if (isset($Data['producerid']) && $Data['producerid'] > 0){
			$producerid = $this->getProducer($Data['producerid'], base64_decode($Data['producername']));
		}
		if (isset($Data['vat'])){
			$vatid = $this->getVAT($Data['vat']);
		}
		
		$sql = "SELECT 
					idproduct as id 
				FROM product 
				WHERE firmesid = :firmesid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firmesid', $Data['id']);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$idproduct = $rs['id'];
			
			if (isset($Data['name']) || isset($Data['shortdescription']) || isset($Data['description'])){
				$sql = 'UPDATE producttranslation SET ';
				if (isset($Data['name'])){
					$sql .= 'name = :name,';
				}
				if (isset($Data['shortdescription'])){
					$sql .= 'shortdescription = :shortdescription,';
				}
				if (isset($Data['description'])){
					$sql .= 'description = :description,';
				}
				$sql .= 'longdescription = longdescription WHERE productid = :productid AND languageid = :languageid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $idproduct);
				if (isset($Data['name'])){
					$stmt->bindValue('name', base64_decode($Data['name']));
				}
				if (isset($Data['shortdescription'])){
					$stmt->bindValue('shortdescription', base64_decode($Data['shortdescription']));
				}
				if (isset($Data['description'])){
					$stmt->bindValue('description', base64_decode($Data['description']));
				}
				$stmt->bindValue('languageid', Helper::getLanguageId());
				$stmt->execute();
			}
		}
		else{
			$sql = 'INSERT INTO product SET
						producerid 			= :producerid,
						stock 				= :stock, 
						trackstock 			= :trackstock, 
						enable 				= :enable, 
						weight 				= :weight,
						width 				= :width,
						height 				= :height,
						deepth 				= :deepth,
						unit 				= :unit,
						vatid 				= :vatid, 
						ean					= :ean, 
						delivelercode		= :delivelercode, 
						buyprice			= :buyprice,
						sellprice			= :sellprice, 
						buycurrencyid 		= (SELECT idcurrency FROM currency WHERE currencysymbol = :currency),
						sellcurrencyid 		= (SELECT idcurrency FROM currency WHERE currencysymbol = :currency),
						technicaldatasetid	= :setid,
						promotion 			= :promotion,
						discountprice 		= :discountprice,
						promotionstart		= :promotionstart,
						promotionend 		= :promotionend,
						firmesid 			= :firmesid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', ($producerid > 0) ? $producerid : NULL);
			$stmt->bindValue('stock', $Data['stock']);
			$stmt->bindValue('trackstock', 1);
			$stmt->bindValue('enable', 1);
			$stmt->bindValue('weight', $Data['weight']);
			$stmt->bindValue('width', NULL);
			$stmt->bindValue('height', NULL);
			$stmt->bindValue('deepth', NULL);
			$stmt->bindValue('unit', NULL);
			$stmt->bindValue('vatid', $vatid);
			$stmt->bindValue('ean', $Data['ean']);
			$stmt->bindValue('delivelercode', $Data['delivercode']);
			$stmt->bindValue('buyprice', 0);
			$stmt->bindValue('sellprice', $Data['pricenetto']);
			$stmt->bindValue('currency', 'PLN');
			$stmt->bindValue('setid', NULL);
			$stmt->bindValue('promotion', 0);
			$stmt->bindValue('discountprice', 0);
			$stmt->bindValue('promotionstart', NULL);
			$stmt->bindValue('promotionend', NULL);
			$stmt->bindValue('firmesid', $Data['id']);
			$stmt->execute();
			
			$idproduct = Db::getInstance()->lastInsertId();
			
			$sql = 'INSERT INTO producttranslation (productid, name, shortdescription,longdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:productid, :name, :shortdescription,:longdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $idproduct);
			$stmt->bindValue('name', base64_decode($Data['name']));
			$stmt->bindValue('shortdescription', base64_decode($Data['shortdescription']));
			$stmt->bindValue('description', base64_decode($Data['description']));
			$stmt->bindValue('longdescription', '');
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->bindValue('seo', strtolower($this->clearSeoUTF(base64_decode($Data['name']))));
			$stmt->bindValue('keyword_title', '');
			$stmt->bindValue('keyword', '');
			$stmt->bindValue('keyword_description', '');
			$stmt->execute();
			
			if (isset($Data['categories']) && ! empty($Data['categories'])){
				$this->addUpdateCategory($Data['categories'], $idproduct);
			}
		}
		
		$variantids = Array();
		
		if (! empty($Data['attributesets'])){
			$attributegroupnameid = App::getModel('firmes/subiekt')->addEmptyGroup('Subiekt');
			foreach ($Data['attributesets'] as $variants){
				$atributeproductvalues = Array();
				foreach ($variants['attributes'] as $attributeproduct){
					$attributeproductid = App::getModel('firmes/subiekt')->addNewAttributeProduct($attributeproduct['attributegroupname']);
					App::getModel('firmes/subiekt')->addAttributeToGroup($attributeproductid, $attributegroupnameid);
					$atributeproductvalues[] = App::getModel('firmes/subiekt')->addAttributeValues($attributeproduct['attributename'], $attributeproductid);
				}
				
				$productattributesetid = App::getModel('firmes/subiekt')->addVariant($idproduct, $variants, $atributeproductvalues, $attributegroupnameid);
				$variantids[] = Array(
					'externalid' => $variants['id'],
					'id' => $productattributesetid
				);
			}
		}
		
		return Array(
			'id' => $idproduct,
			'attributesets' => $variantids
		);
	}

	public function clearSeoUTF ($name)
	{
		$seo = Core::clearUTF(trim($name));
		$seo = preg_replace('/[^A-Za-z0-9\-\s\s+]/', '', $seo);
		$seo = Core::clearSeoUTF($seo);
		return str_replace('/', '', strtolower($seo));
	}

	public function addUpdateCategory ($Categories, $id)
	{
		$productCategories = Array();
		foreach ($Categories as $category){
			$name = base64_decode($category['name']);
			$sql = 'SELECT
						C.idcategory AS id
					FROM category C
					WHERE C.firmesid = :firmesid AND C.firmesparentid = :firmesparentid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('firmesid', $category['categoryid']);
			$stmt->bindValue('firmesparentid', $category['parentid']);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				if ($category['showproduct'] == 1){
					$productCategories[] = $rs['id'];
				}
			}
			else{
				$sql = 'INSERT INTO category SET
						photoid = :photoid,
						categoryid  = :categoryid,
						firmesid = :firmesid,
						firmesparentid = :firmesparentid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('categoryid', NULL);
				$stmt->bindValue('firmesid', $category['categoryid']);
				$stmt->bindValue('firmesparentid', $category['parentid']);
				$stmt->bindValue('photoid', NULL);
				$stmt->execute();
				
				$categoryid = Db::getInstance()->lastInsertId();
				
				$sql = 'INSERT INTO categorytranslation (
						categoryid,
						name,
						seo,
						languageid
					)
					VALUES
					(
						:categoryid,
						:name,
						:seo,
						:languageid
					)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('categoryid', $categoryid);
				$stmt->bindValue('name', $name);
				$stmt->bindValue('seo', strtolower($this->clearSeoUTF($name)));
				$stmt->bindValue('languageid', Helper::getLanguageId());
				$stmt->execute();
				
				$sql = 'INSERT INTO viewcategory (categoryid,viewid)
						VALUES (:categoryid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('categoryid', $categoryid);
				$stmt->bindValue('viewid', Helper::getViewId());
				$stmt->execute();
				
				if ($category['showproduct'] == 1){
					$productCategories[] = $categoryid;
				}
			}
		}
		
		foreach ($productCategories as $productCategory){
			$sql = 'INSERT INTO productcategory (productid, categoryid)
					VALUES (:productid, :categoryid)
					ON DUPLICATE KEY UPDATE productid = :productid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('categoryid', $productCategory);
			$stmt->execute();
		}
	}

	public function getVAT ($value)
	{
		$value = number_format(str_replace(',', '.', $value), 2);
		$sql = "SELECT 
					idvat AS vatid
				FROM vat
				WHERE value = :value
				";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('value', $value);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['vatid'];
		}
		else{
			$sql = 'INSERT INTO `vat` (value) VALUES (:value)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('value', $value);
			$stmt->execute();
			
			$vatid = Db::getInstance()->lastInsertId();
			
			$sql = 'INSERT INTO vattranslation SET
						vatid = :vatid,
						name = :name, 
						languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('vatid', $vatid);
			$stmt->bindValue('name', 'VAT ' . $value);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->execute();
			return $vatid;
		}
	}

	public function getProducer ($id, $name)
	{
		if (strlen($name) < 2){
			$name = 'Subiekt GT (bez producenta)';
		}
		$sql = "SELECT 
					idproducer AS id
				FROM producer
				WHERE firmesid = :firmesid
				";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firmesid', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['id'];
		}
		else{
			$sql = 'INSERT INTO producer (photoid, firmesid) VALUES (:photoid, :firmesid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('photoid', NULL);
			$stmt->bindValue('firmesid', $id);
			$stmt->execute();
			
			$producerid = Db::getInstance()->lastInsertId();
			
			$sql = 'INSERT INTO producertranslation (producerid, name, seo, languageid)
					VALUES (:producerid, :name, :seo, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $producerid);
			$stmt->bindValue('name', $name);
			$stmt->bindValue('seo', strtolower($this->clearSeoUTF($name)));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->execute();
			
			$sql = 'INSERT INTO producerview (producerid, viewid)
					VALUES (:producerid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $producerid);
			$stmt->bindValue('viewid', Helper::getViewId());
			$stmt->execute();
			return $producerid;
		}
	}

	public function addProductPhoto ($Data)
	{
		$fileid = 0;
		$filename = md5($Data['id'] . $Data['name']) . '.png';
		
		$sql = "SELECT 
					idproduct as id 
				FROM product 
				WHERE firmesid = :firmesid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firmesid', $Data['productid']);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$idproduct = $rs['id'];
		}
		else{
			return $fileid;
		}
		
		$mainphoto = $Data['main'] == "True" ? 1 : 0;
		
		$sql = "SELECT 
					F.idfile
				FROM file F
				WHERE F.name = :name";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $filename);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$fileid = $rs['idfile'];
		}
		else{
			$sql = "INSERT INTO file (
						name, 
						filetypeid,	
						fileextensionid, 
						visible
					) VALUES (
						:name, 
						2, 
						3, 
						1
			)";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $filename);
			$stmt->execute();
			$fileid = Db::getInstance()->lastInsertId();
		}
		
		header("Content-Type: image/png");
		@unlink(ROOTPATH . 'design' . DS . '_gallery' . DS . '_orginal' . DS . $fileid . '.png');
		$jpg = base64_decode($Data['image']);
		$im = @imagecreatefromstring($jpg);
		if ($im !== FALSE){
			imagepng($im, ROOTPATH . 'design' . DS . '_gallery' . DS . '_orginal' . DS . $fileid . '.png');
			
			$sql = 'INSERT INTO productphoto (productid, mainphoto, photoid)
				VALUES (:productid, :mainphoto, :photoid)
				ON DUPLICATE KEY UPDATE productid = :productid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $idproduct);
			$stmt->bindValue('mainphoto', $mainphoto);
			$stmt->bindValue('photoid', $fileid);
			$stmt->execute();
		}
		return $fileid;
	}
} 