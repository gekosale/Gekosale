<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: exchange.php 583 2011-10-28 20:19:07Z gekosale $
 */
namespace Gekosale\Plugin;
use XMLReader;

class ExchangeModel extends Component\Model
{

    protected $lastPeriodId;

    public function importFromFile ($file, $entity)
    {
        switch ($entity) {
            case 1:
                $this->importProducts($file);
                break;
            case 2:
                $this->importCategories($file);
                break;
        }
    }

    public function getCategoryViewsByNames ($views)
    {
        $views = explode(';', $views);
        
        $sql = 'SELECT idview FROM view
				WHERE FIND_IN_SET(CAST(name as CHAR), :views) GROUP BY idview';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('views', implode(',', $views));
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = $rs['idview'];
        }
        return $Data;
    }

    public function getProductProducerByName ($producer)
    {
        $sql = 'SELECT producerid FROM producertranslation
				WHERE name = :producer GROUP BY producerid';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('producer', $producer);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['producerid'];
        }
        else{
            return null;
        }
    }

    public function updateParentCategories ($ParentCategories)
    {
        foreach ($ParentCategories as $key => $val){
            $sql = 'UPDATE category SET categoryid = :categoryid WHERE idcategory = :id';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('id', $val['idcategory']);
            if (! is_null($val['categoryid'])){
                $stmt->bindValue('categoryid', $val['categoryid']);
            }
            else{
                $stmt->bindValue('categoryid', NULL);
            }
            $stmt->execute();
        }
    }

    public function importProducts ($file)
    {
        if (($handle = fopen(ROOTPATH . 'upload' . DS . $file, "r")) === FALSE)
            return;
        while (($cols = fgetcsv($handle, 1000, ";")) !== FALSE){
            if ($cols[0] != 'name'){
                $Data[] = $cols;
            }
        }
        $categories = array_flip($this->getCategoryPath());
        $vatValues = array_flip(App::getModel('vat')->getVATValuesAll());
        $currencies = App::getModel('currencieslist')->getCurrencyIds();
        
        foreach ($Data as $key => $product){
            if (count($product) == 14){
                $name = $product[0];
                $ean = $product[1];
                $delivelercode = $product[2];
                $barcode = $product[3];
                $buyprice = $product[4];
                $buycurrency = (isset($currencies[$product[5]])) ? $currencies[$product[5]] : NULL;
                $sellprice = $product[6];
                $sellcurrency = (isset($currencies[$product[7]])) ? $currencies[$product[7]] : NULL;
                $stock = $product[8];
                $weight = $product[9];
                $vat = (isset($vatValues[$product[10]])) ? $vatValues[$product[10]] : 2;
                $photo = $this->getPhotoByName($product[11]);
                $producer = $this->getProductProducerByName($product[12]);
                $category = (isset($categories[$product[13]])) ? $categories[$product[13]] : NULL;
                
                $sql = 'SELECT 
                            productid
    					FROM producttranslation
    					WHERE name = :name AND languageid = :languageid';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('name', $name);
                $stmt->bindValue('languageid', Helper::getLanguageId());
                $stmt->execute();
                $rs = $stmt->fetch();
                if ($rs){
                    $sql = 'UPDATE product SET
	    					ean				=:ean,
	    					delivelercode	=:delivelercode,
	    					barcode			=:barcode,
							buyprice		=:buyprice,
							buycurrencyid	=:buycurrencyid,
							sellcurrencyid  =:sellcurrencyid,
							sellprice		=:sellprice,
							stock			=:stock,
							weight			=:weight,
							vatid			=:vatid,
							producerid		=:producerid
						WHERE idproduct = :id';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('id', $rs['productid']);
                    $stmt->bindValue('ean', $ean);
                    $stmt->bindValue('delivelercode', $delivelercode);
                    $stmt->bindValue('barcode', $barcode);
                    $stmt->bindValue('buyprice', $buyprice);
                    $stmt->bindValue('sellprice', $sellprice);
                    $stmt->bindValue('buycurrencyid', $buycurrency);
                    $stmt->bindValue('sellcurrencyid', $sellcurrency);
                    $stmt->bindValue('stock', $stock);
                    $stmt->bindValue('weight', $weight);
                    $stmt->bindValue('vatid', $vat);
                    if (! is_null($producer)){
                        $stmt->bindValue('producerid', $producer);
                    }
                    else{
                        $stmt->bindValue('producerid', NULL);
                    }
                    
                    $stmt->execute();
                    
                    $sql = 'UPDATE producttranslation SET
								name = :name,
								seo = :seo
							WHERE productid = :productid AND languageid = :languageid
					';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('productid', $rs['productid']);
                    $stmt->bindValue('name', $name);
                    $stmt->bindValue('seo', strtolower(Core::clearSeoUTF($name)));
                    $stmt->bindValue('languageid', Helper::getLanguageId());
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
                    }
                    
                    $sql = 'DELETE FROM productcategory WHERE productid = :id';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('id', $rs['productid']);
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new Exception($e->getMessage());
                    }
                    
                    if ($category != NULL){
                        $sql = 'INSERT INTO productcategory (productid, categoryid)
						VALUES (:productid, :categoryid)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('productid', $rs['productid']);
                        $stmt->bindValue('categoryid', $category);
                        
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
                        }
                    }
                }
                else{
                    
                    $sql = 'INSERT INTO product SET
	    					ean				=:ean,
	    					delivelercode	=:delivelercode,
	    					barcode			=:barcode,
							buyprice		=:buyprice,
							sellprice		=:sellprice,
							buycurrencyid   =:buycurrencyid,
							sellcurrencyid  =:sellcurrencyid,
							stock			=:stock,
							weight			=:weight,
							vatid			=:vatid,
							producerid		=:producerid
						';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('ean', $ean);
                    $stmt->bindValue('delivelercode', $delivelercode);
                    $stmt->bindValue('barcode', $barcode);
                    $stmt->bindValue('buyprice', $buyprice);
                    $stmt->bindValue('sellprice', $sellprice);
                    $stmt->bindValue('buycurrencyid', $buycurrency);
                    $stmt->bindValue('sellcurrencyid', $sellcurrency);
                    $stmt->bindValue('stock', $stock);
                    $stmt->bindValue('weight', $weight);
                    $stmt->bindValue('vatid', $vat);
                    if (! is_null($producer)){
                        $stmt->bindValue('producerid', $producer);
                    }
                    else{
                        $stmt->bindValue('producerid', NULL);
                    }
                    
                    $stmt->execute();
                    
                    $idproduct = Db::getInstance()->lastInsertId();
                    
                    $sql = 'INSERT INTO producttranslation SET
								productid = :productid,
								name = :name,
								seo = :seo,
								languageid = :languageid
					';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('productid', $idproduct);
                    $stmt->bindValue('name', $name);
                    $stmt->bindValue('seo', strtolower(Core::clearSeoUTF($name)));
                    $stmt->bindValue('languageid', Helper::getLanguageId());
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
                    }
                    
                    if ($category != NULL){
                        $sql = 'INSERT INTO productcategory (productid, categoryid)
						VALUES (:productid, :categoryid)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('productid', $idproduct);
                        $stmt->bindValue('categoryid', $category);
                        
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    public function importCategories ($file)
    {
        $categories = array_flip($this->getCategoryPath());
        
        if (($handle = fopen(ROOTPATH . 'upload' . DS . $file, "r")) === FALSE)
            return;
        while (($cols = fgetcsv($handle, 1000, ";")) !== FALSE){
            if ($cols[0] != 'name'){
                $Data[] = $cols;
            }
        }
        Db::getInstance()->beginTransaction();
        
        foreach ($Data as $key => $category){
            
            $name = $category[0];
            $photo = $category[1];
            $parent = $category[2];
            $views = $this->getCategoryViewsByNames($category[3]);
            
            if ($parent != ''){
                $fullPath = implode('/', array_merge(explode('/', $parent), Array(
                    $name
                )));
            }
            else{
                $fullPath = $name;
            }
            
            $categoryid = (isset($categories[$parent])) ? $categories[$parent] : NULL;
            
            if (isset($categories[$fullPath]) && $idcategory = $categories[$fullPath]){
                
                $sql = 'UPDATE categorytranslation SET
	    					seo	 		= :seo
	    				WHERE
	    					categoryid = :categoryid
	    				AND
	    					languageid = :languageid';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('seo', strtolower(Core::clearSeoUTF($fullPath)));
                $stmt->bindValue('categoryid', $idcategory);
                $stmt->bindValue('languageid', Helper::getLanguageId());
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
                }
                
                $sql = 'UPDATE category SET
			    			photoid 	= :photoid,
			    			categoryid  = :categoryid
			    		WHERE
			    			idcategory = :idcategory';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('idcategory', $idcategory);
                if ($categoryid == NULL){
                    $stmt->bindValue('categoryid', NULL);
                }
                else{
                    $stmt->bindValue('categoryid', $categoryid);
                }
                $stmt->bindValue('photoid', $this->getPhotoByName($photo));
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
                }
                
                $sql = 'DELETE FROM viewcategory WHERE categoryid =:id';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('id', $idcategory);
                $stmt->execute();
                
                foreach ($views as $key => $val){
                    $sql = 'INSERT INTO viewcategory (categoryid,viewid)
										VALUES (:categoryid, :viewid)';
                    $stmt = Db::getInstance()->prepare($sql);
                    
                    $stmt->bindValue('categoryid', $idcategory);
                    $stmt->bindValue('viewid', $val);
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
                    }
                }
            }
            else{
                $sql = 'INSERT INTO category SET
							photoid = :photoid,
							categoryid  = :categoryid';
                $stmt = Db::getInstance()->prepare($sql);
                if ($categoryid == NULL){
                    $stmt->bindValue('categoryid', NULL);
                }
                else{
                    $stmt->bindValue('categoryid', $categoryid);
                }
                $stmt->bindValue('photoid', $this->getPhotoByName($photo));
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
                }
                
                $idcategory = Db::getInstance()->lastInsertId();
                
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
                $stmt->bindValue('categoryid', $idcategory);
                $stmt->bindValue('name', $name);
                $stmt->bindValue('seo', strtolower(Core::clearSeoUTF($fullPath)));
                $stmt->bindValue('languageid', Helper::getLanguageId());
                $stmt->execute();
                
                foreach ($views as $key => $val){
                    $sql = 'INSERT INTO viewcategory (categoryid,viewid)
							VALUES (:categoryid, :viewid)';
                    $stmt = Db::getInstance()->prepare($sql);
                    
                    $stmt->bindValue('categoryid', $idcategory);
                    $stmt->bindValue('viewid', $val);
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
                    }
                }
            }
        }
        
        Db::getInstance()->commit();
        $this->updateParentCategories($ParentCategories);
        App::getModel('category')->getCategoriesPathById();
        App::getModel('seo')->doRefreshSeoCategory();
    }

    public function exportFile ($entity)
    {
        switch ($entity) {
            case 1:
                $this->exportProducts();
                break;
            case 2:
                $this->exportCategories();
                break;
            case 3:
                $this->exportClients();
                break;
            case 4:
                $this->exportOrders();
                break;
        }
    }

    public function getPhotoByName ($name)
    {
        $sql = 'SELECT idfile FROM file WHERE name = :name';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('name', $name);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = 1;
        if ($rs){
            $Data = $rs['idfile'];
        }
        return $Data;
    }

    public function exportCategories ()
    {
        $categories = $this->getCategoryPath();
        $columns = Array();
        
        $sql = "SELECT
    			CT.name,
    			C.categoryid as parent,
    			F.name AS photo,
    			GROUP_CONCAT(DISTINCT V.name ORDER BY V.name ASC SEPARATOR ';') as view
				FROM categorytranslation CT
				LEFT JOIN category C ON C.idcategory = CT.categoryid
				LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
				LEFT JOIN view V ON VC.viewid = V.idview
				LEFT JOIN file F ON F.idfile = C.photoid
				WHERE CT.languageid = :languageid
				GROUP BY
				CT.categoryid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'name' => $rs['name'],
                'photo' => $rs['photo'],
                'parent' => (isset($categories[$rs['parent']])) ? $categories[$rs['parent']] : '',
                'view' => $rs['view']
            );
        }
        $filename = 'categories_' . date('Y_m_d_H_i_s') . '.csv';
        $header = Array();
        if (isset($Data[0])){
            $header = array_keys($Data[0]);
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $fp = fopen("php://output", 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, $header, ";");
        foreach ($Data as $key => $values){
            fputcsv($fp, $values, ";");
        }
        fclose($fp);
        exit();
    }

    public function exportClients ()
    {
        $sql = "SELECT
					AES_DECRYPT(CD.firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(CD.surname, :encryptionkey) AS surname,
					AES_DECRYPT(CD.email, :encryptionkey) AS email,
					CGT.name AS groupname,
					AES_DECRYPT(CD.phone, :encryptionkey) AS phone,
					CD.adddate AS adddate,
					SUM(O.globalprice) AS ordertotal,
					V.name AS shop
				FROM
				client C
				LEFT JOIN clientdata CD ON CD.clientid = C.idclient
				LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=1
				LEFT JOIN orderclientdata OCD ON OCD.clientid = CD.clientid
				LEFT JOIN `order` O ON O.idorder = OCD.orderid
				LEFT JOIN view V ON C.viewid = V.idview
				WHERE C.viewid IN (:views)
				GROUP BY C.idclient ORDER BY idclient ASC";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('views', implode(',', Helper::getViewIds()));
        $stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'firstname' => $rs['firstname'],
                'surname' => $rs['surname'],
                'email' => $rs['email'],
                'phone' => $rs['phone'],
                'groupname' => $rs['groupname'],
                'adddate' => date('Y-m-d', strtotime($rs['adddate'])),
                'ordertotal' => $rs['ordertotal'],
                'shop' => $rs['shop']
            );
        }
        $header = Array();
        $filename = 'clients_' . date('Y_m_d_H_i_s') . '.csv';
        if (isset($Data[0])){
            $header = Array(
                $this->trans('TXT_FIRSTNAME'),
                $this->trans('TXT_SURNAME'),
                $this->trans('TXT_EMAIL'),
                $this->trans('TXT_PHONE'),
                $this->trans('TXT_VIEW_ORDER_CLIENT_GROUP'),
                $this->trans('TXT_REGISTRATION'),
                $this->trans('TXT_SUM_ALL_ORDER'),
                $this->trans('TXT_SHOP')
            );
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $fp = fopen("php://output", 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, $header, ";");
        foreach ($Data as $key => $values){
            fputcsv($fp, $values, ";");
        }
        fclose($fp);
        exit();
    }

    public function exportOrders ()
    {
        $sql = 'SELECT
					O.idorder AS idorder,
					CONCAT(AES_DECRYPT(OC.surname,:encryptionkey)," ",AES_DECRYPT(OC.firstname,:encryptionkey)) AS client,
					CONCAT(O.globalprice," ",O.currencysymbol) AS globalprice,
					O.dispatchmethodprice AS dispatchmethodprice,
					OST.name AS orderstatusname,
					O.dispatchmethodname AS dispatchmethodname,
					O.paymentmethodname AS paymentmethodname,
					O.adddate AS adddate,
					V.name AS shop
				FROM `order` O
				LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = 1
				LEFT JOIN orderclientdata OC ON OC.orderid=O.idorder
				LEFT JOIN view V ON O.viewid = V.idview
				WHERE O.viewid IN (' . Helper::getViewIdsAsString() . ')
				ORDER BY idorder DESC';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'idorder' => $rs['idorder'],
                'client' => $rs['client'],
                'globalprice' => $rs['globalprice'],
                'dispatchmethodprice' => $rs['dispatchmethodprice'],
                'orderstatusname' => $rs['orderstatusname'],
                'dispatchmethodname' => $rs['dispatchmethodname'],
                'paymentmethodname' => $rs['paymentmethodname'],
                'adddate' => date('Y-m-d', strtotime($rs['adddate'])),
                'shop' => $rs['shop']
            );
        }
        $filename = 'orders_' . date('Y_m_d_H_i_s') . '.csv';
        $header = Array(
            $this->trans('TXT_ORDER_NUMER'),
            $this->trans('TXT_CLIENT'),
            $this->trans('TXT_VIEW_ORDER_TOTAL'),
            $this->trans('TXT_DELIVERERPRICE'),
            $this->trans('TXT_ORDER_STATUS'),
            $this->trans('TXT_VIEW_ORDER_DELIVERY_METHOD'),
            $this->trans('TXT_VIEW_ORDER_PAYMENT_METHOD'),
            $this->trans('TXT_VIEW_ORDER_ORDER_DATE'),
            $this->trans('TXT_SHOP')
        );
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $fp = fopen("php://output", 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, $header, ";");
        foreach ($Data as $key => $values){
            fputcsv($fp, $values, ";");
        }
        fclose($fp);
        exit();
    }

    public function getCategoryPath ()
    {
        $sql = 'SELECT
					C.categoryid,
					GROUP_CONCAT(SUBSTRING(CT.name, 1) ORDER BY C.order DESC SEPARATOR \'/\') AS path
				FROM categorytranslation CT
				LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
				WHERE CT.languageid = :languageid
				GROUP BY C.categoryid
				';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[$rs['categoryid']] = $rs['path'];
        }
        return $Data;
    }

    public function exportProducts ()
    {
        $categories = $this->getCategoryPath();
        
        $sql = 'SELECT
        			PT.name AS name,
        			P.ean as ean,
        			P.delivelercode as delivelercode,
        			P.barcode as barcode,
        			ROUND(P.buyprice,2) as buyprice,
        			BUYCUR.currencysymbol AS buycurrency,
        			ROUND(P.sellprice,2) as sellprice,
        			SELLCUR.currencysymbol AS sellcurrency,
        			P.stock as stock,
        			ROUND(P.weight,2) as weight,
        			F.name as photo,
        			ROUND(V.value,2) as vat,
        			PRT.name as producer,
        			PC.categoryid
				FROM producttranslation PT
				LEFT JOIN product P ON P.idproduct = PT.productid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
                LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
				LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
				LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
				LEFT JOIN productphoto PP ON PP.productid = P.idproduct AND PP.mainphoto = 1
				LEFT JOIN file F ON F.idfile = PP.photoid
				LEFT JOIN vat V ON P.vatid = V.idvat
				LEFT JOIN currency BUYCUR ON P.buycurrencyid = BUYCUR.idcurrency
				LEFT JOIN currency SELLCUR ON P.sellcurrencyid = SELLCUR.idcurrency
				WHERE 
                    PT.languageid = :languageid AND 
                    C.categoryid = PC.categoryid AND
                    VC.viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY P.idproduct';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'name' => $rs['name'],
                'ean' => $rs['ean'],
                'delivelercode' => $rs['delivelercode'],
                'barcode' => $rs['barcode'],
                'buyprice' => $rs['buyprice'],
                'buycurrency' => $rs['buycurrency'],
                'sellprice' => $rs['sellprice'],
                'sellcurrency' => $rs['sellcurrency'],
                'stock' => $rs['stock'],
                'weight' => $rs['weight'],
                'vat' => $rs['vat'],
                'photo' => $rs['photo'],
                'producer' => $rs['producer'],
                'category' => (isset($categories[$rs['categoryid']])) ? $categories[$rs['categoryid']] : ''
            );
        }
        
        $filename = 'products_' . date('Y_m_d_H_i_s') . '.csv';
        $header = Array();
        if (isset($Data[0])){
            $header = array_keys($Data[0]);
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $fp = fopen("php://output", 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, $header, ";");
        foreach ($Data as $key => $values){
            fputcsv($fp, $values, ";");
        }
        fclose($fp);
        exit();
    }
}