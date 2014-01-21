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
            'ean' => Array(
                'source' => 'P.ean'
            ),
            'barcode' => Array(
                'source' => 'P.barcode'
            ),
            'delivelercode' => Array(
                'source' => 'P.delivelercode'
            ),
            'stock' => Array(
                'source' => 'P.stock'
            ),
            'weight' => Array(
                'source' => 'P.weight'
            ),
            'adddate' => Array(
                'source' => 'P.adddate'
            ),
            'editdate' => Array(
                'source' => 'P.adddate'
            ),
            'url' => Array(
                'source' => 'PT.seo',
                'processFunction' => Array(
                    $this,
                    'getURL'
                )
            ),
            'producer' => Array(
                'source' => 'P.producerid',
                'processFunction' => Array(
                    App::getModel('webapi/producers'),
                    'getProducer'
                )
            ),
            'prices' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'getProductPrices'
                )
            ),
            'translation' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'getProductTranslation'
                )
            ),
            'photos' => Array(
                'source' => 'Photo.photoid',
                'processFunction' => Array(
                    App::getModel('webapi'),
                    'getPhotos'
                )
            ),
            'attributes' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'getAttributesForProductById'
                )
            ),
            'categories' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'productCategory'
                )
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
        
        $dataset->setGroupBy('
			P.idproduct
		');
        
        $dataset->setAdditionalWhere('
			IF(:id > 0, P.idproduct = :id, 1)
		');
    }

    public function getProductDataset ()
    {
        return $this->getDataset()->getDatasetRecords();
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
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = $rs['id'];
        }
        return $Data;
    }

    public function getProductPrices ($idproduct)
    {
        $sql = 'SELECT
					C.currencysymbol,
					V.value AS vatvalue,
					P.sellprice * CR.exchangerate AS pricenetto,
					P.sellprice * (1 + (V.value / 100)) * CR.exchangerate AS price,
					P.buyprice * CR.exchangerate AS buypricenetto,
					P.buyprice * (1 + (V.value / 100)) * CR.exchangerate AS buyprice,
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * CR.exchangerate, NULL) AS discountpricenetto,
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
					CR.exchangerate
				FROM product P
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN currency C ON C.idcurrency = P.sellcurrencyid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				WHERE P.idproduct = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $idproduct);
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $Data = Array();
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $Data = Array(
                    'pricenetto' => $this->parsePrice($rs['pricenetto']),
                    'price' => $this->parsePrice($rs['price']),
                    'buypricenetto' => $this->parsePrice($rs['buypricenetto']),
                    'buyprice' => $this->parsePrice($rs['buyprice']),
                    'discountpricenetto' => $this->parsePrice($rs['discountpricenetto']),
                    'discountprice' => $this->parsePrice($rs['discountprice']),
                    'vatvalue' => $this->parsePrice($rs['vatvalue']),
                    'currencysymbol' => $rs['currencysymbol'],
                    'exchangerate' => $rs['exchangerate']
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        return $Data;
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

    public function parsePrice ($price)
    {
        return number_format($price, 2, '.', '');
    }

    public function getURL ($seo)
    {
        return $this->registry->router->generate('frontend.productcart', true, Array(
            'param' => $seo
        ));
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
                    'attributename' => $rs['attributename'],
                    'attributegroupname' => $rs['attributegroupname'],
                    'attributepricenetto' => $this->parsePrice($rs['attributeprice']),
                    'attributeprice' => $this->parsePrice($rs['price'])
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        return $Data;
    }

    public function addProduct ($Data)
    {
        if (isset($Data['vat'])){
            $vatid = $this->getVAT($Data['vat']);
        }
        
        $sql = 'INSERT INTO product SET
					producerid 			= (SELECT idproducer FROM producer WHERE idproducer = :producerid),
					stock 				= :stock, 
					trackstock 			= :trackstock, 
					enable 				= :enable, 
					weight 				= :weight,
					width 				= :width,
					height 				= :height,
					deepth 				= :deepth,
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
					promotionend 		= :promotionend';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('producerid', (int) $Data['producerid']);
        $stmt->bindValue('stock', $Data['stock']);
        $stmt->bindValue('trackstock', $Data['trackstock']);
        $stmt->bindValue('enable', $Data['enable']);
        $stmt->bindValue('weight', $Data['weight']);
        $stmt->bindValue('width', $Data['width']);
        $stmt->bindValue('height', $Data['height']);
        $stmt->bindValue('deepth', $Data['deepth']);
        $stmt->bindValue('vatid', $vatid);
        $stmt->bindValue('ean', $Data['ean']);
        $stmt->bindValue('delivelercode', $Data['delivercode']);
        $stmt->bindValue('buyprice', $Data['buyprice']);
        $stmt->bindValue('sellprice', $Data['sellprice']);
        $stmt->bindValue('currency', $Data['currency']);
        $stmt->bindValue('setid', NULL);
        $stmt->bindValue('promotion', $Data['promotion']);
        $stmt->bindValue('discountprice', $Data['discountprice']);
        $stmt->bindValue('promotionstart', $Data['promotionstart']);
        $stmt->bindValue('promotionend', $Data['promotionend']);
        $stmt->execute();
        
        $idproduct = Db::getInstance()->lastInsertId();
        
        foreach ($Data['translation'] as $key => $val){
            $sql = 'INSERT INTO producttranslation (productid, name, shortdescription,longdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:productid, :name, :shortdescription,:longdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $idproduct);
            $stmt->bindValue('name', $val['name']);
            $stmt->bindValue('shortdescription', $val['shortdescription']);
            $stmt->bindValue('description', $val['description']);
            $stmt->bindValue('longdescription', $val['longdescription']);
            $stmt->bindValue('languageid', $key);
            $stmt->bindValue('seo', $val['seo']);
            $stmt->bindValue('keyword_title', $val['keyword_title']);
            $stmt->bindValue('keyword', $val['keyword']);
            $stmt->bindValue('keyword_description', $val['keyword_description']);
            $stmt->execute();
        }
        
        foreach ($Data['categories'] as $category){
            $sql = 'INSERT INTO productcategory (productid, categoryid)
						VALUES (:productid, :categoryid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $idproduct);
            $stmt->bindValue('categoryid', $category);
            $stmt->execute();
        }
        
        foreach ($Data['statuses'] as $status){
            $sql = 'INSERT INTO productstatuses (productid, productstatusid)
					VALUES (:productid, :productstatusid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $idproduct);
            $stmt->bindValue('productstatusid', $status);
            $stmt->execute();
        }
        
        $variantids = Array();
        
        //         if (! empty($Data['attributesets'])){
        //             $attributegroupnameid = App::getModel('firmes/subiekt')->addEmptyGroup('Import API');
        //             foreach ($Data['attributesets'] as $variants){
        //                 $atributeproductvalues = Array();
        //                 foreach ($variants['attributes'] as $attributeproduct){
        //                     $attributeproductid = App::getModel('firmes/subiekt')->addNewAttributeProduct($attributeproduct['attributegroupname']);
        //                     App::getModel('firmes/subiekt')->addAttributeToGroup($attributeproductid, $attributegroupnameid);
        //                     $atributeproductvalues[] = App::getModel('firmes/subiekt')->addAttributeValues($attributeproduct['attributename'], $attributeproductid);
        //                 }
        

        //                 $productattributesetid = App::getModel('firmes/subiekt')->addVariant($idproduct, $variants, $atributeproductvalues, $attributegroupnameid);
        //                 $variantids[] = Array(
        //                     'externalid' => $variants['id'],
        //                     'id' => $productattributesetid
        //                 );
        //             }
        //         }
        

        return Array(
            'id' => $idproduct,
            'success' => true,
            'attributes' => $variantids
        );
    }

    public function updateProduct ($Data)
    {
        if (isset($Data['vat'])){
            $vatid = $this->getVAT($Data['vat']);
        }
        
        $idproduct = (int) $Data['id'];
        
        $sql = 'UPDATE product SET
					producerid 			= (SELECT idproducer FROM producer WHERE idproducer = :producerid),
					stock 				= :stock, 
					trackstock 			= :trackstock, 
					enable 				= :enable, 
					weight 				= :weight,
					width 				= :width,
					height 				= :height,
					deepth 				= :deepth,
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
					promotionend 		= :promotionend
        		WHERE idproduct = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $idproduct);
        $stmt->bindValue('producerid', (int) $Data['producerid']);
        $stmt->bindValue('stock', $Data['stock']);
        $stmt->bindValue('trackstock', $Data['trackstock']);
        $stmt->bindValue('enable', $Data['enable']);
        $stmt->bindValue('weight', $Data['weight']);
        $stmt->bindValue('width', $Data['width']);
        $stmt->bindValue('height', $Data['height']);
        $stmt->bindValue('deepth', $Data['deepth']);
        $stmt->bindValue('vatid', $vatid);
        $stmt->bindValue('ean', $Data['ean']);
        $stmt->bindValue('delivelercode', $Data['delivercode']);
        $stmt->bindValue('buyprice', $Data['buyprice']);
        $stmt->bindValue('sellprice', $Data['sellprice']);
        $stmt->bindValue('currency', $Data['currency']);
        $stmt->bindValue('setid', NULL);
        $stmt->bindValue('promotion', $Data['promotion']);
        $stmt->bindValue('discountprice', $Data['discountprice']);
        $stmt->bindValue('promotionstart', $Data['promotionstart']);
        $stmt->bindValue('promotionend', $Data['promotionend']);
        $stmt->execute();
        
        DbTracker::deleteRows('producttranslation', 'productid', $idproduct);
        
        foreach ($Data['translation'] as $key => $val){
            $sql = 'INSERT INTO producttranslation (productid, name, shortdescription,longdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:productid, :name, :shortdescription,:longdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $idproduct);
            $stmt->bindValue('name', $val['name']);
            $stmt->bindValue('shortdescription', $val['shortdescription']);
            $stmt->bindValue('description', $val['description']);
            $stmt->bindValue('longdescription', $val['longdescription']);
            $stmt->bindValue('languageid', $key);
            $stmt->bindValue('seo', $val['seo']);
            $stmt->bindValue('keyword_title', $val['keywordtitle']);
            $stmt->bindValue('keyword', $val['keyword']);
            $stmt->bindValue('keyword_description', $val['keyworddescription']);
            $stmt->execute();
        }
        
        DbTracker::deleteRows('productcategory', 'productid', $idproduct);
        
        foreach ($Data['categories'] as $category){
            $sql = 'INSERT INTO productcategory (productid, categoryid)
					VALUES (:productid, :categoryid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $idproduct);
            $stmt->bindValue('categoryid', $category);
            $stmt->execute();
        }
        
        DbTracker::deleteRows('productstatuses', 'productid', $idproduct);
        
        foreach ($Data['statuses'] as $status){
            $sql = 'INSERT INTO productstatuses (productid, productstatusid)
					VALUES (:productid, :productstatusid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $idproduct);
            $stmt->bindValue('productstatusid', $status);
            $stmt->execute();
        }
        
        $variantids = Array();
        
        //         if (! empty($Data['attributesets'])){
        //             $attributegroupnameid = App::getModel('firmes/subiekt')->addEmptyGroup('Import API');
        //             foreach ($Data['attributesets'] as $variants){
        //                 $atributeproductvalues = Array();
        //                 foreach ($variants['attributes'] as $attributeproduct){
        //                     $attributeproductid = App::getModel('firmes/subiekt')->addNewAttributeProduct($attributeproduct['attributegroupname']);
        //                     App::getModel('firmes/subiekt')->addAttributeToGroup($attributeproductid, $attributegroupnameid);
        //                     $atributeproductvalues[] = App::getModel('firmes/subiekt')->addAttributeValues($attributeproduct['attributename'], $attributeproductid);
        //                 }
        

        //                 $productattributesetid = App::getModel('firmes/subiekt')->addVariant($idproduct, $variants, $atributeproductvalues, $attributegroupnameid);
        //                 $variantids[] = Array(
        //                     'externalid' => $variants['id'],
        //                     'id' => $productattributesetid
        //                 );
        //             }
        //         }
        

        return Array(
            'id' => $idproduct,
            'success' => true,
            'attributes' => $variantids
        );
    }

    public function clearSeoUTF ($name)
    {
        $seo = Core::clearUTF(trim($name));
        $seo = preg_replace('/[^A-Za-z0-9\-\s\s+]/', '', $seo);
        $seo = Core::clearSeoUTF($seo);
        return str_replace('/', '', strtolower($seo));
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