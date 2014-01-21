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
 * $Id: product.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale;
use xajaxResponse;

class productModel extends Component\Model\Dataset
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
                'producername' => Array(
                    'source' => 'PRT.name'
                ),
                'producerseo' => Array(
                    'source' => 'PRT.seo'
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
                'finalprice' => Array(
                    'source' => 'IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
								 	PGP.discountprice,
								 	IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, IF(PGP.groupprice = 1, PGP.sellprice, P.sellprice))
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
                        $this,
                        'getProductStatuses'
                    )
                )
            ));
            
            $dataset->setFrom('
                                productcategory PC
                                LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid AND VC.viewid = :viewid
                                LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
                                LEFT JOIN product P ON PC.productid = P.idproduct
                                LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid = :clientgroupid
                                LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
                                LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PT.languageid = :languageid
                                LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
                                LEFT JOIN productnew PN ON P.idproduct = PN.productid
                                LEFT JOIN vat V ON P.vatid= V.idvat
                                LEFT JOIN productreview PREV ON PREV.productid = P.idproduct AND PREV.enable = 1
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
                'producername' => Array(
                    'source' => 'PRT.name'
                ),
                'producerseo' => Array(
                    'source' => 'PRT.seo'
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
                'finalprice' => Array(
                    'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, P.sellprice * (1 + (V.value / 100)) * CR.exchangerate)'
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
                        $this,
                        'getProductStatuses'
                    )
                )
            ));
            
            $dataset->setFrom('
                                productcategory PC
                                LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid AND VC.viewid = :viewid
                                LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
                                LEFT JOIN product P ON PC.productid= P.idproduct
                                LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
                                LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PT.languageid = :languageid
                                LEFT JOIN productphoto Photo ON P.idproduct= Photo.productid AND Photo.mainphoto = 1
                                LEFT JOIN productnew PN ON P.idproduct = PN.productid
                                LEFT JOIN vat V ON P.vatid= V.idvat
                                LEFT JOIN productreview PREV ON PREV.productid = P.idproduct AND PREV.enable = 1
                                LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
                                LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
                            ');
        }
        
        $dataset->setAdditionalWhere('
                        PC.categoryid = :categoryid AND
                        IF(:filterbyproducer > 0, FIND_IN_SET(CAST(P.producerid as CHAR), :producer), 1) AND
                        P.enable = 1 AND
                        IF(:enablelayer > 0, FIND_IN_SET(CAST(P.idproduct as CHAR), :products), 1)
                    ');
        
        $dataset->setGroupBy('
                        P.idproduct
                    ');
        
        $dataset->setHavingString('
                        finalprice BETWEEN IF(:pricefrom > 0, :pricefrom, 0) AND IF( :priceto > 0, :priceto, 999999)
                    ');
        
        $dataset->setSQLParams(Array(
            'categoryid' => (int) $this->registry->core->getParam(),
            'producer' => 0,
            'pricefrom' => 0,
            'priceto' => 0,
            'filterbyproducer' => 0,
            'enablelayer' => 0,
            'products' => Array()
        ));
    }

    public function getProductDataset ()
    {
        return $this->getDataset()->getDatasetRecords();
    }

    public function addAJAXOpinionAboutProduct ($productid, $params)
    {
        $params = App::getModel('formprotection')->filterArray($params);
        $product = $this->getProductById($productid);
        $objResponse = new xajaxResponse();
        if (strlen($params['nick']) > 0){
            $lastId = $this->addOpinionAboutProduct((int) $productid, $params);
            if (App::getContainer()->get('session')->getActiveClientid() > 0){
                App::getContainer()->get('session')->setVolatileOpinionAdded(1, false);
            }
            else{
                App::getContainer()->get('session')->setVolatileOpinionAdded(2, false);
            }
            
            $url = $this->registry->router->generate('admin', true, Array(
                'controller' => 'productrange',
                'action' => 'edit',
                'param' => $lastId
            ));
            
            $this->registry->template->assign('opinion', array(
                'productname' => $product['productname'],
                'nick' => $params['nick'],
                'review' => trim(strip_tags($params['htmlopinion'])),
                'url' => $this->registry->router->generate('admin', true, Array(
                    'controller' => 'productrange',
                    'action' => 'edit',
                    'param' => $lastId
                ))
            ));
            
            $mailer = App::getModel('mailer');
            $settings = $mailer->getSettings(Helper::getViewId());
            
            App::getModel('mailer')->sendEmail(Array(
                'template' => 'notifyOpinion',
                'email' => array(
                    $settings['fromemail']
                ),
                'bcc' => false,
                'subject' => $this->trans('TXT_CONTROLLER_PRODUCTREVIEW'),
                'viewid' => Helper::getViewId()
            ));
            
            $url = $this->registry->router->generate('frontend.productcart', true, Array(
                'param' => $product['seo']
            ));
            
            $objResponse->script("window.location.href = '{$url}';");
        }
        else{
            $objResponse->script('GError("' . $this->trans('ERR_OPINION_ADD_FAILED') . '", "' . $this->trans('ERR_OPINION_NICK_REQUIRED') . '");');
        }
        
        return $objResponse;
    }

    /**
     * Adding product to client's wishlist
     * Xajax method
     *
     * @param
     *            integer idproduct
     * @param
     *            integer idattribute (0 by default)
     *
     * @return object response
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function addAJAXProductToWishList ($idproduct, $idattribute = 0)
    {
        $objResponseAddToWishList = new xajaxResponse();
        try{
            if ($idproduct == null){
                $objResponseAddToWishList->script('alert("' . $this->trans('ERR_WISHLIST_NO_PRODUCT_SELECTED') . '")');
            }
            else{
                if ($idattribute == 0){
                    $check = $this->addProductToClientWishList($idproduct, 0);
                    $objResponseAddToWishList->script('window.location.reload( false )');
                }
                else{
                    $check = $this->addProductToClientWishList($idproduct, $idattribute);
                }
                if ($check == true){
                    $objResponseAddToWishList->script('alert("' . $this->trans('TXT_WISHLIST_PRODUCT_WAS_ADDED') . '")');
                    $objResponseAddToWishList->script('window.location.reload( false )');
                }
                else{
                    $objResponseAddToWishList->script('alert("' . $this->trans('ERR_WISHLIST_HAS_THIS_PRODUCT') . '")');
                }
            }
        }
        catch (Exception $fe){
            $objResponseAddToWishList->script('alert("' . $this->trans('ERR_ADD_PRODUCT_TO_WISH_LIST') . '")');
        }
        
        return $objResponseAddToWishList;
    }

    /**
     * Adding product to client's wishlist
     *
     * @param
     *            integer idproduct
     * @param
     *            integer idattribute (0 by default)
     *
     * @return bool TRUE if adding was successful or FALSE otherwise
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function addProductToClientWishList ($idproduct, $idattribute = 0)
    {
        $sql = "SELECT
					COUNT(WL.idwishlist) as counter
				FROM wishlist WL
				WHERE WL.clientid = :clientid
				AND WL.productid = :productid
				AND WL.productattributesetid = :productattributesetid
				AND WL.viewid = :viewid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
        $stmt->bindValue('productattributesetid', $idattribute);
        $stmt->bindValue('productid', $idproduct);
        $stmt->bindValue('viewid', Helper::getViewId());
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $counter = $rs['counter'];
                if ($counter == 0){
                    $sql = "INSERT INTO wishlist (productid, productattributesetid, clientid, wishprice, viewid)
							VALUES (:productid, :productattributesetid, :clientid, 0, :viewid)";
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('productid', $idproduct);
                    $stmt->bindValue('productattributesetid', $idattribute);
                    $stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
                    $stmt->bindValue('viewid', Helper::getViewId());
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new FrontendException($e->getMessage());
                    }
                    
                    return true;
                }
                else{
                    return false;
                }
            }
        }
        catch (Exception $fe){
            throw new FrontendException($fe->getMessage());
        }
    }

    public function getProductPrices ($idproduct)
    {
        $clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();
        if (! empty($clientGroupId)){
            $sql = 'SELECT
						C.currencysymbol,
						V.value AS vatvalue,
						IF(PGP.groupprice = 1, PGP.sellprice, P.sellprice) * CR.exchangerate AS pricenetto,
						IF(PGP.groupprice = 1, PGP.sellprice, P.sellprice) * (1 + (V.value / 100)) * CR.exchangerate AS price,
						P.buyprice * CR.exchangerate AS buypricenetto,
						P.buyprice * (1 + (V.value / 100)) * CR.exchangerate AS buyprice,
						IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
							PGP.discountprice,
							IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, NULL)
						) * CR.exchangerate AS discountpricenetto,
						IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
						 	PGP.discountprice,
							IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, NULL)
						) * (1 + (V.value / 100)) * CR.exchangerate AS discountprice
					FROM product P
					LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid = :group
					LEFT JOIN vat V ON P.vatid= V.idvat
					LEFT JOIN currency C ON C.idcurrency = P.sellcurrencyid
					LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
					WHERE P.idproduct = :id';
        }
        else{
            $sql = 'SELECT
						C.currencysymbol,
						V.value AS vatvalue,
						P.sellprice * CR.exchangerate AS pricenetto,
						P.sellprice * (1 + (V.value / 100)) * CR.exchangerate AS price,
						P.buyprice * CR.exchangerate AS buypricenetto,
						P.buyprice * (1 + (V.value / 100)) * CR.exchangerate AS buyprice,
						IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * CR.exchangerate, NULL) AS discountpricenetto,
						IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice
					FROM product P
					LEFT JOIN vat V ON P.vatid= V.idvat
					LEFT JOIN currency C ON C.idcurrency = P.sellcurrencyid
					LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
					WHERE P.idproduct = :id';
        }
        $stmt = Db::getInstance()->prepare($sql);
        if (! empty($clientGroupId)){
            $stmt->bindValue('group', $clientGroupId);
        }
        $stmt->bindValue('id', $idproduct);
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $Data = Array();
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $Data = Array(
                    'pricenetto' => $rs['pricenetto'],
                    'price' => $rs['price'],
                    'buypricenetto' => $rs['buypricenetto'],
                    'buyprice' => $rs['buyprice'],
                    'discountpricenetto' => $rs['discountpricenetto'],
                    'discountprice' => $rs['discountprice'],
                    'vatvalue' => $rs['vatvalue'],
                    'currencysymbol' => $rs['currencysymbol']
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        
        return $Data;
    }

    public function nextProduct ($productid, $categoryid)
    {
        $sql = 'SELECT
					PT.seo
				FROM product P
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN category C ON PC.categoryid = C.idcategory
				LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
				WHERE P.idproduct > :productid AND PC.categoryid = :categoryid AND IF(:userid = 0, P.enable = 1, 1) AND VC.viewid = :viewid
				LIMIT 1';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('userid', (int) App::getContainer()->get('session')->getActiveUserid());
        $stmt->bindValue('categoryid', $categoryid);
        $stmt->bindValue('productid', $productid);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $Data = Array();
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['seo'];
        }
        else{
            return null;
        }
    }

    public function previousProduct ($productid, $categoryid)
    {
        $sql = 'SELECT
					PT.seo
				FROM product P
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				LEFT JOIN category C ON PC.categoryid = C.idcategory
				LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
				WHERE P.idproduct < :productid AND PC.categoryid = :categoryid AND IF(:userid = 0, P.enable = 1, 1) AND VC.viewid = :viewid
				ORDER BY P.idproduct DESC LIMIT 1';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('userid', (int) App::getContainer()->get('session')->getActiveUserid());
        $stmt->bindValue('categoryid', $categoryid);
        $stmt->bindValue('productid', $productid);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $Data = Array();
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['seo'];
        }
        else{
            return null;
        }
    }

    public function getProductById ($id, $isgiftwrap = 0)
    {
        $sql = "SELECT
					P.`status`,
					P.enable,
					P.ean,
					P.delivelercode,
					P.stock,
					IF(P.trackstock IS NULL, 0, P.trackstock) AS trackstock,
					PT.name as productname,
					PT.shortdescription,
					PT.description,
					PT.longdescription,
					PT.seo,
					PRODT.name AS producername,
					PRODT.seo AS producerurl,
					COLT.name AS collectionname,
					COLT.seo AS collectionseo,
					PROD.photoid AS producerphoto,
					IF(PHOTO.photoid IS NOT NULL, IF(PHOTO.mainphoto = 1, PHOTO.photoid, 0), 1) as mainphotoid,
					PT.keyword_title AS keyword_title,
					IF(PT.keyword = '', VT.keyword, PT.keyword) AS keyword,
					IF(PT.keyword_description = '',VT.keyword_description,PT.keyword_description) AS keyword_description,
					P.weight,
					P.packagesize,
					IF(PN.active = 1 AND (PN.enddate IS NULL OR PN.enddate >= CURDATE()), 1, 0) AS new,
					P.unit,
					COUNT(DISTINCT PREV.idproductreview) AS opinions,
					IF(CEILING(AVG(PRANGE.value)) IS NULL, 0, CEILING(AVG(PRANGE.value))) AS rating,
					UT.name AS unit,
					C.photoid AS categoryphoto,
					C.idcategory AS categoryid,
					CT.name AS categoryname,
					CT.seo AS categoryseo,
					AT.name AS availablityname,
				   	AT.description AS availablitydescription
				FROM product P
					LEFT JOIN producttranslation PT ON P.idproduct= PT.productid AND PT.languageid= :languageid
					LEFT JOIN productcategory PROCAT ON P.idproduct = PROCAT.productid
					LEFT JOIN categorytranslation CT ON PROCAT.categoryid = CT.categoryid AND CT.languageid = :languageid
					LEFT JOIN category C ON PROCAT.categoryid = C.idcategory
					LEFT JOIN viewcategory VC ON PROCAT.categoryid = VC.categoryid
					LEFT JOIN viewtranslation VT ON VT.viewid = VC.viewid
					LEFT JOIN producer AS PROD ON P.producerid= PROD.idproducer
					LEFT JOIN producertranslation PRODT ON PROD.idproducer= PRODT.producerid AND PRODT.languageid= :languageid
					LEFT JOIN collection COL ON COL.idcollection = P.collectionid
					LEFT JOIN collectiontranslation COLT ON COL.idcollection = COLT.collectionid AND COLT.languageid= :languageid
					LEFT JOIN productphoto PHOTO ON P.idproduct= PHOTO.productid AND PHOTO.mainphoto = 1
					LEFT JOIN productnew PN ON P.idproduct = PN.productid
					LEFT JOIN productreview PREV ON PREV.productid = P.idproduct AND PREV.enable = 1
					LEFT JOIN productrange PRANGE ON PRANGE.productid = P.idproduct
					LEFT JOIN unitmeasuretranslation UT ON P.unit = UT.unitmeasureid AND UT.languageid= :languageid
					LEFT JOIN availablity A ON A.idavailablity = P.availablityid
					LEFT JOIN availablitytranslation AT ON AT.availablityid = P.availablityid AND AT.languageid = :languageid
					WHERE P.idproduct= :productid AND IF(:userid = 0 OR :isgiftwrap = 0, P.enable = 1, 1) AND IF(:userid = 0, VC.viewid = :viewid, 1)
					GROUP BY P.idproduct";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('userid', (int) App::getContainer()->get('session')->getActiveUserid());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('productid', $id);
        $stmt->bindValue('isgiftwrap', $isgiftwrap);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $Data = Array();
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                
                $price = $this->getProductPrices($id);
                $Data = Array(
                    'idproduct' => $id,
                    'seo' => $rs['seo'],
                    'enable' => $rs['enable'],
                    'previous' => $this->previousProduct($id, $rs['categoryid']),
                    'next' => $this->nextProduct($id, $rs['categoryid']),
                    'ean' => $rs['ean'],
                    'unit' => $rs['unit'],
                    'delivelercode' => $rs['delivelercode'],
                    'producername' => $rs['producername'],
                    'producerurl' => urlencode($rs['producerurl']),
                    'collectionname' => $rs['collectionname'],
                    'collectionseo' => urlencode($rs['collectionseo']),
                    'producerphotoid' => $rs['producerphoto'],
                    'producerphoto' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($rs['producerphoto'], 0)),
                    'stock' => $rs['stock'],
                    'trackstock' => $rs['trackstock'],
                    'new' => $rs['new'],
                    'pricewithoutvat' => $price['pricenetto'],
                    'pricenetto' => $price['pricenetto'],
                    'price' => $price['price'],
                    'discountpricenetto' => $price['discountpricenetto'],
                    'discountprice' => $price['discountprice'],
                    'buypricenetto' => $price['buypricenetto'],
                    'buyprice' => $price['buyprice'],
                    'vatvalue' => $price['vatvalue'],
                    'currencysymbol' => $price['currencysymbol'],
                    'mainphotoid' => $rs['mainphotoid'],
                    'description' => $rs['description'],
                    'longdescription' => $rs['longdescription'],
                    'productname' => $rs['productname'],
                    'shortdescription' => $rs['shortdescription'],
                    'keyword_title' => ($rs['keyword_title'] == null || $rs['keyword_title'] == '') ? $rs['productname'] : $rs['keyword_title'],
                    'keyword_description' => $rs['keyword_description'],
                    'keyword' => $rs['keyword'],
                    'weight' => $rs['weight'],
                    'packagesize' => $rs['packagesize'],
                    'unit' => $rs['unit'],
                    'categoryphoto' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($rs['categoryphoto'], 0)),
                    'categoryname' => $rs['categoryname'],
                    'categoryid' => $rs['categoryid'],
                    'categoryseo' => $rs['categoryseo'],
                    'availablityname' => $rs['availablityname'],
                    'availablitydescription' => $rs['availablitydescription'],
                    'opinions' => $rs['opinions'],
                    'rating' => $rs['rating'],
                    'statuses' => $this->getProductStatuses($id)
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        
        return $Data;
    }

    public function getProductStatuses ($id)
    {
        $sql = 'SELECT
					PS.name,
					PS.symbol
				FROM productstatuses P
				LEFT JOIN productstatus PS ON PS.idproductstatus = P.productstatusid
				WHERE productid = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'name' => $this->trans($rs['name']),
                'symbol' => $rs['symbol']
            );
        }
        
        return $Data;
    }

    public function getMetadataForProduct ()
    {
        $this->productid = App::getModel('product')->getProductIdBySeo($this->getParam());
        if ($this->productid === null){
            return '';
        }
        $Data = $this->getProductById($this->productid);
        if (! empty($Data)){
            $KeywordData = Array(
                'keyword_title' => $Data['keyword_title'],
                'keyword' => $Data['keyword'],
                'keyword_description' => $Data['keyword_description']
            );
            
            return $KeywordData;
        }
    }

    /**
     * Get all attributes for selected product.
     *
     * Each attribute has a new price.
     *
     * @param
     *            integer idproduct
     * @param
     *            integer idclient (0 by default)
     *
     * @return array with attributes' product informations
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function getAttributesForProductById ($id)
    {
        $clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();
        
        if (! empty($clientGroupId)){
            $sql = "SELECT
						P.idproduct as id,
						PAS.stock,
						PAS.idproductattributeset,
						PAS.`value`,
						PAS.symbol,
						AT.name as availablity,
						PAS.photoid as photoid,
						PAS.symbol,
						IF(PAS.weight IS NULL, P.weight, PAS.weight) AS weight,
						PAVS.idproductattributevalueset,
						PAVS.productattributesetid AS attributesgroup,
						APV.name AS attributename,
						APV.idattributeproductvalue AS attributeid,
						AP.name AS attributegroupname,
						AP.idattributeproduct AS attributegroupid,
						IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
							CASE PAS.suffixtypeid
                            	WHEN 1 THEN PGP.discountprice * (PAS.value / 100)
                                WHEN 2 THEN PGP.discountprice + PAS.value
                                WHEN 3 THEN PGP.discountprice - PAS.value
                            	WHEN 4 THEN PAS.`value`
                            END,
							IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1),
								PAS.discountprice,
								IF(PGP.sellprice IS NOT NULL,
									CASE PAS.suffixtypeid
		                            	WHEN 1 THEN PGP.sellprice * (PAS.value / 100)
		                                WHEN 2 THEN PGP.sellprice + PAS.value
		                                WHEN 3 THEN PGP.sellprice - PAS.value
		                            	WHEN 4 THEN PAS.`value`
	                            	END,
									PAS.attributeprice
								)
							)
						) * CR.exchangerate AS attributeprice,
						(PAS.attributeprice * CR.exchangerate) AS attributepricenettobeforepromotion,
						(PAS.attributeprice * (1 + (V.value / 100)) * CR.exchangerate) AS attributepricegrossbeforepromotion,
						IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
							CASE PAS.suffixtypeid
                            	WHEN 1 THEN PGP.discountprice * (PAS.value / 100)
                                WHEN 2 THEN PGP.discountprice + PAS.value
                                WHEN 3 THEN PGP.discountprice - PAS.value
                            	WHEN 4 THEN PAS.`value`
                            END,
							IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1),
								PAS.discountprice,
								IF(PGP.sellprice IS NOT NULL,
									CASE PAS.suffixtypeid
		                            	WHEN 1 THEN PGP.sellprice * (PAS.value / 100)
		                                WHEN 2 THEN PGP.sellprice + PAS.value
		                                WHEN 3 THEN PGP.sellprice - PAS.value
		                            	WHEN 4 THEN PAS.`value`
	                            	END,
									PAS.attributeprice
								)
							)
						) * (1 + (V.value / 100)) * CR.exchangerate AS price
	                FROM productattributeset AS PAS
				    LEFT JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				    LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
				    LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct
				    LEFT JOIN availablitytranslation AT ON PAS.availablityid = AT.availablityid AND AT.languageid = :languageid
				    LEFT JOIN product AS P ON PAS.productid = P.idproduct
				    LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid = :clientgroupid
				    LEFT JOIN `vat` V ON P.vatid = V.idvat
				    LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				    WHERE PAS.productid = :id AND PAS.status = 1
				    ORDER BY PAS.stock DESC";
        }
        else{
            $sql = "SELECT
						P.idproduct as id,
						PAS.stock,
						PAS.idproductattributeset,
						PAS.`value`,
						PAS.symbol,
						AT.name as availablity,
						PAS.photoid as photoid,
						IF(PAS.weight IS NULL, P.weight, PAS.weight) AS weight,
						PAVS.idproductattributevalueset,
						PAVS.productattributesetid AS attributesgroup,
						APV.name AS attributename,
						APV.idattributeproductvalue AS attributeid,
						AP.name AS attributegroupname,
						AP.idattributeproduct AS attributegroupid,
						IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), PAS.discountprice, PAS.attributeprice) * CR.exchangerate AS attributeprice,
						IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), PAS.discountprice, PAS.attributeprice) * (1 + (V.value / 100)) * CR.exchangerate AS price,
						(PAS.attributeprice * CR.exchangerate) AS attributepricenettobeforepromotion,
						(PAS.attributeprice * (1 + (V.value / 100)) * CR.exchangerate) AS attributepricegrossbeforepromotion
	                FROM productattributeset AS PAS
				    LEFT JOIN productattributevalueset PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
				    LEFT JOIN attributeproductvalue APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
				    LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct
					LEFT JOIN availablitytranslation AT ON PAS.availablityid = AT.availablityid AND AT.languageid = :languageid
				    LEFT JOIN product AS P ON PAS.productid = P.idproduct
				    LEFT JOIN `vat` V ON P.vatid = V.idvat
				    LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				    WHERE PAS.productid = :id AND PAS.status = 1
				    ORDER BY PAS.stock DESC";
        }
        
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        if (! empty($clientGroupId)){
            $stmt->bindValue('clientgroupid', $clientGroupId);
        }
        $Data = Array();
        try{
            $stmt->execute();
            while ($rs = $stmt->fetch()){
                $price = 0;
                $priceWithoutVat = 0;
                $attrId = $rs['idproductattributeset'];
                
                $Data[] = Array(
                    'id' => $rs['id'],
                    'stock' => $rs['stock'],
                    'symbol' => $rs['symbol'],
                    'weight' => $rs['weight'],
                    'availablity' => $rs['availablity'],
                    'photoid' => $rs['photoid'],
                    'idproductattributeset' => $rs['idproductattributeset'],
                    'idproductattributevalueset' => $rs['idproductattributevalueset'],
                    'attributesgroup' => $rs['attributesgroup'],
                    'attributename' => $rs['attributename'],
                    'attributeid' => $rs['attributeid'],
                    'attributegroupname' => $rs['attributegroupname'],
                    'attributegroupid' => $rs['attributegroupid'],
                    'attributeprice' => $rs['attributeprice'],
                    'value' => $rs['value'],
                    'price' => $rs['price'],
                    'attributepricenettobeforepromotion' => $rs['attributepricenettobeforepromotion'],
                    'attributepricegrossbeforepromotion' => $rs['attributepricegrossbeforepromotion'],
                    'photos' => Array(
                        'small' => ((int) $rs['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($rs['photoid'])) : '',
                        'normal' => ((int) $rs['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getNormalImageById($rs['photoid'])) : '',
                        'large' => ((int) $rs['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getLargeImageById($rs['photoid'])) : '',
                        'orginal' => ((int) $rs['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($rs['photoid'])) : ''
                    )
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        
        return $Data;
    }

    /**
     * Get product's photos
     *
     * @param
     *            integer idproduct
     *
     * @return array with ids' of product's photos
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function getPhotosByProductId ($id)
    {
        $sql = "SELECT photoid
					FROM productphoto
					WHERE productid= :id
					AND mainphoto= 1";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new FrontendException('Error while doing sql query.', 11, $e->getMessage());
        }
        
        return $stmt->fetchAll();
    }

    public function getOtherPhotosByProductId ($id)
    {
        $sql = "SELECT photoid FROM productphoto WHERE productid= :id AND mainphoto = 0";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new FrontendException('Error while doing sql query.', 11, $e->getMessage());
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Get data of product and product's attributes
     *
     * @param
     *            integer idproduct
     *
     * @return array with attributes and photos
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function getFilesByProductId ($id)
    {
        $sql = "SELECT F.name, F.idfile
					FROM productfile PF
					LEFT JOIN file F ON PF.fileid = F.idfile
					WHERE PF.productid = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new FrontendException('Error while doing sql query.', 11, $e->getMessage());
        }
        
        return $stmt->fetchAll();
    }

    public function updateViewedCount ($id)
    {
        $sql = "UPDATE product SET viewed = viewed+1 WHERE idproduct = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
    }

    public function getProductAndAttributesById ($id, $giftwrap = 0)
    {
        try{
            $Data = $this->getProductById($id, $giftwrap);
            if ($Data != null){
                $Data['attributes'] = $this->getAttributesForProductById($id);
                $Data['photo'] = $this->getPhotosByProductId($id);
                $Data['otherphoto'] = $this->getOtherPhotosByProductId($id);
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        
        return $Data;
    }

    /**
     * Get product's photos
     *
     * @param
     *            pointer to member product array
     *
     * @return array product with photos (small, norma, and original)
     * @access public
     */
    public function getPhotos (&$product)
    {
        $gallery = App::getModel('gallery');
        
        if (is_array($product['photo'])){
            if (isset($product['mainphotoid']) && $product['mainphotoid'] > 0){
                $product['mainphoto']['small'] = $gallery->getImagePath($gallery->getSmallImageById($product['mainphotoid']));
                $product['mainphoto']['normal'] = $gallery->getImagePath($gallery->getNormalImageById($product['mainphotoid']));
                $product['mainphoto']['large'] = $gallery->getImagePath($gallery->getLargeImageById($product['mainphotoid']));
                $product['mainphoto']['orginal'] = $gallery->getImagePath($gallery->getOrginalImageById($product['mainphotoid']));
            }
            foreach ($product['photo'] as $photo){
                $product['photo']['small'][] = $gallery->getImagePath($gallery->getSmallImageById($photo['photoid']));
                $product['photo']['normal'][] = $gallery->getImagePath($gallery->getNormalImageById($photo['photoid']));
                $product['photo']['large'][] = $gallery->getImagePath($gallery->getLargeImageById($photo['photoid']));
                $product['photo']['orginal'][] = $gallery->getImagePath($gallery->getOrginalImageById($photo['photoid']));
            }
            if (isset($product['producerphotoid']) && $product['producerphotoid'] > 0){
                $product['producerphoto'] = Array(
                    'small' => $gallery->getImagePath($gallery->getSmallImageById($product['producerphotoid'])),
                    'normal' => $gallery->getImagePath($gallery->getNormalImageById($product['producerphotoid'])),
                    'large' => $gallery->getImagePath($gallery->getLargeImageById($product['producerphotoid'])),
                    'orginal' => $gallery->getImagePath($gallery->getOrginalImageById($product['producerphotoid']))
                );
            }
        }
    }

    public function getOtherPhotos (&$product)
    {
        $gallery = App::getModel('gallery');
        
        if (is_array($product['otherphoto'])){
            if (isset($product['mainphotoid']) && $product['mainphotoid'] = 0){
                $product['mainphoto']['small'] = $gallery->getImagePath($gallery->getSmallImageById($product['mainphotoid']));
                $product['mainphoto']['normal'] = $gallery->getImagePath($gallery->getNormalImageById($product['mainphotoid']));
                $product['mainphoto']['large'] = $gallery->getImagePath($gallery->getLargeImageById($product['mainphotoid']));
                $product['mainphoto']['orginal'] = $gallery->getImagePath($gallery->getOrginalImageById($product['mainphotoid']));
            }
            foreach ($product['otherphoto'] as $photo){
                $product['otherphoto']['small'][] = $gallery->getImagePath($gallery->getSmallImageById($photo['photoid']));
                $product['otherphoto']['normal'][] = $gallery->getImagePath($gallery->getNormalImageById($photo['photoid']));
                $product['otherphoto']['large'][] = $gallery->getImagePath($gallery->getLargeImageById($photo['photoid']));
                $product['otherphoto']['orginal'][] = $gallery->getImagePath($gallery->getOrginalImageById($photo['photoid']));
            }
        }
    }

    public function getProductAttributes (&$product, $groupid = null, $groupname = null)
    {
        $Data = Array();
        if (isset($product['attributes'])){
            if (count($product['attributes']) == 0){
                return $Data;
            }
            foreach ($product['attributes'] as $attribute){
                if ($groupname !== null && $attribute['attributegroupname'] == $groupname && $groupid == null){
                    $Data[$attribute['attributeid']] = $attribute['attributename'];
                }
                if ($attribute['attributegroupid'] == $groupid){
                    $Data[$attribute['attributeid']] = $attribute['attributename'];
                }
            }
        }
        
        return $Data;
    }

    public function getProductAttributeGroups (&$product, $withAttr = true)
    {
        $Data = Array();
        if (isset($product['attributes'])){
            if (count($product['attributes']) == 0)
                return $Data;
            foreach ($product['attributes'] as $attribute){
                $Data[$attribute['attributegroupid']]['name'] = $attribute['attributegroupname'];
                if ($withAttr == true){
                    $Data[$attribute['attributegroupid']]['attributes'] = $this->getProductAttributes($product, $attribute['attributegroupid']);
                }
            }
        }
        
        return $Data;
    }

    public function getProductVariant (&$product, $withVariants = true)
    {
        $Data = Array();
        if (isset($product['attributes'])){
            if (count($product['attributes']) == 0){
                return $Data;
            }
            else{
                foreach ($product['attributes'] as $variant){
                    $Data[$variant['attributesgroup']]['grid'] = $variant['attributesgroup'];
                    $Data[$variant['attributesgroup']]['value'] = $variant['value'];
                    if ($withVariants == true){
                        $Data[$variant['attributesgroup']]['variant'] = $this->getVariants($product, $variant['attributesgroup']);
                        $Data[$variant['attributesgroup']]['stock'] = $variant['stock'];
                        $Data[$variant['attributesgroup']]['sellprice'] = $variant['price'];
                        $Data[$variant['attributesgroup']]['price'] = $variant['price'];
                        $Data[$variant['attributesgroup']]['photos'] = $variant['photos'];
                        $Data[$variant['attributesgroup']]['availablity'] = $variant['availablity'];
                        $Data[$variant['attributesgroup']]['sellpricenetto'] = $variant['attributeprice'];
                        $Data[$variant['attributesgroup']]['attributepricenettobeforepromotion'] = $variant['attributepricenettobeforepromotion'];
                        $Data[$variant['attributesgroup']]['attributepricegrossbeforepromotion'] = $variant['attributepricegrossbeforepromotion'];
                    }
                }
            }
            
            return $Data;
        }
    }

    public function getVariants (&$product, $attributesgroup)
    {
        $Data = Array();
        if (isset($product['attributes'])){
            if (count($product['attributes']) == 0)
                return $Data;
            foreach ($product['attributes'] as $variant){
                if ($attributesgroup !== null && $variant['attributesgroup'] == $attributesgroup){
                    $Data[$variant['attributeid']] = $variant['attributename'];
                }
                if ($variant['attributesgroup'] == $attributesgroup){
                    $Data[$variant['attributeid']] = $variant['attributename'];
                }
            }
        }
        
        return $Data;
    }

    /**
     * Adding an opinion
     *
     * @param
     *            integer idproduct
     * @param
     *            string review
     *
     * @return id from generator
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function addOpinionAboutProduct ($productid, $params)
    {
        $sql = 'INSERT INTO productreview (productid, clientid, review, viewid, nick, enable)
				VALUES (:productid, :clientid, :review, :viewid, :nick, :enable)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('productid', $productid);
        if (App::getContainer()->get('session')->getActiveClientid() > 0){
            $stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
            $stmt->bindValue('enable', 1);
        }
        else{
            $stmt->bindValue('clientid', null);
            $stmt->bindValue('enable', 0);
        }
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('nick', $params['nick']);
        $stmt->bindValue('review', trim(strip_tags($params['htmlopinion'])));
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
        
        $reviewid = Db::getInstance()->lastInsertId();
        
        foreach ($params as $rangetypeid => $value){
            if (is_numeric($rangetypeid) && ($value > 0)){
                $sql = 'INSERT INTO productrange SET
							productid = :productid,
							rangetypeid = :rangetypeid,
							productreviewid = :productreviewid,
							value = :value';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('productid', $productid);
                $stmt->bindValue('rangetypeid', $rangetypeid);
                $stmt->bindValue('productreviewid', $reviewid);
                $stmt->bindValue('value', $value);
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new FrontendException($e->getMessage());
                }
            }
        }
        
        return $reviewid;
    }

    /**
     * Get product's range
     *
     * @param
     *            integer idproduct
     *
     * @return array wiht range's information
     * @throws on error FrontendException object will be returned
     * @access public
     */
    public function getProductRange ($productid)
    {
        $sql = "SELECT
					rangetypeid,
					COUNT(PR.`rangetypeid`) as qty,
					SUM(PR.`value`) as sum,
					ROUND(SUM(PR.`value`)/COUNT(PR.`rangetypeid`) , 0) as pkt
				FROM productrange PR
				WHERE productid=:productid
				GROUP BY rangetypeid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('productid', $productid);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $Data = Array();
        try{
            while ($rs = $stmt->fetch()){
                $Data[] = Array(
                    'rangeid' => $rs['rangeid'],
                    'pkt' => $rs['pkt']
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException('error product range');
        }
        
        return $Data;
        ;
    }

    public function getRangeType ($productId)
    {
        $sql = "SELECT
					RT.idrangetype as id,
					RTT.name,
					CEILING(AVG(PR.value)) AS mean
				FROM rangetype RT
				LEFT JOIN rangetypecategory RTC ON RTC.rangetypeid = RT.idrangetype
				LEFT JOIN rangetypetranslation RTT ON RTT.rangetypeid = RT.idrangetype AND RTT.languageid = :languageid
				LEFT JOIN productrange PR ON PR.rangetypeid = RT.idrangetype AND PR.productid = :productid
				LEFT JOIN productcategory PC ON PC.categoryid = RTC.categoryid
				LEFT JOIN viewcategory VC ON VC.categoryid = RTC.categoryid
				WHERE VC.viewid=:viewid
				AND PC.productid = :productid
				GROUP BY idrangetype";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('productid', $productId);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        try{
            while ($rs = $stmt->fetch()){
                $Data[] = Array(
                    'id' => $rs['id'],
                    'name' => $rs['name'],
                    'values' => $this->getRangeValues(),
                    'mean' => $rs['mean']
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException('Error RangeType Or RangeTypeCategory', 11, $e->getMessage());
        }
        
        return $Data;
    }

    public function getProductRangeValues ($rangetypeid, $productid)
    {
        $sql = "SELECT AVG(value) FROM productrange WHERE productid = 5011 AND rangetypeid = 21";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
    }

    public function getRangeValues ()
    {
        return range(1, 5, 1);
    }

    public function getBuyAlsoProduct ($id)
    {
        $sql = "SELECT name FROM orderproduct WHERE productid= :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'name' => $rs['name'],
                'products' => $this->getAlsoProduct($id)
            );
        }
        
        return $Data;
    }

    public function getAlsoProduct ($id)
    {
        $sql = "SELECT OP.orderid
				FROM orderproduct OP
				LEFT JOIN `order` O ON O.idorder = OP.orderid
				WHERE OP.productid= :id and O.viewid= :viewid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = $rs['orderid'];
        }
        
        return $Data;
    }

    public function GetTechnicalDataForProduct ($productId)
    {
        $languageId = Helper::getLanguageId();
        $sql = '
				SELECT
					TG.idtechnicaldatagroup AS id,
					TGT.name AS name
				FROM
					technicaldatagroup TG
					LEFT JOIN technicaldatagrouptranslation TGT ON TGT.technicaldatagroupid = TG.idtechnicaldatagroup AND TGT.languageid = :languageId
					LEFT JOIN producttechnicaldatagroup TSG ON TG.idtechnicaldatagroup = TSG.technicaldatagroupid
				WHERE
					TSG.productid = :productId
				GROUP BY
					TG.idtechnicaldatagroup
				ORDER BY
					TSG.order ASC
			';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('productId', $productId);
        $stmt->bindValue('languageId', $languageId);
        $stmt->execute();
        $groups = Array();
        $groupIndices = Array();
        while ($rs = $stmt->fetch()){
            $groupIndices[] = $rs['id'];
            $groups[] = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'attributes' => Array()
            );
        }
        if (count($groups)){
            $sql = '
					SELECT
						TA.idtechnicaldataattribute AS id,
						TA.type AS type,
						IF (TA.type = 2, TAV.value, TGA.value) AS value,
						TSG.technicaldatagroupid AS group_id,
						TAT.name AS name
					FROM
						technicaldataattribute TA
						LEFT JOIN technicaldataattributetranslation TAT ON TAT.technicaldataattributeid = TA.idtechnicaldataattribute
						LEFT JOIN producttechnicaldatagroupattribute TGA ON TA.idtechnicaldataattribute = TGA.technicaldataattributeid
						LEFT JOIN producttechnicaldatagroupattributetranslation TAV ON TAV.producttechnicaldatagroupattributeid = TGA.idproducttechnicaldatagroupattribute
						LEFT JOIN producttechnicaldatagroup TSG ON TGA.producttechnicaldatagroupid = TSG.idproducttechnicaldatagroup
					WHERE
						TSG.productid = :productId
						AND TAT.languageId = :languageId
						AND ((TA.type <> 2) OR (TAV.languageid = :languageId))
					GROUP BY
						TA.idtechnicaldataattribute,
						TGA.idproducttechnicaldatagroupattribute
					ORDER BY
						TSG.order ASC,
						TGA.order ASC
				';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productId', $productId);
            $stmt->bindValue('languageId', $languageId);
            $stmt->execute();
            $groupIndex = 0;
            while ($rs = $stmt->fetch()){
                $currentGroupIndex = $rs['group_id'];
                if ($currentGroupIndex != $groups[$groupIndex]['id']){
                    if ($currentGroupIndex != $groups[++ $groupIndex]['id']){
                        throw new CoreException('Something\'s wrong with the technical data indices...');
                    }
                }
                $groups[$groupIndex]['attributes'][] = Array(
                    'id' => $rs['id'],
                    'type' => $rs['type'],
                    'value' => str_replace("\n", "<br />", $rs['value']),
                    'name' => $rs['name']
                );
            }
        }
        
        return $groups;
    }

    public function getImagePath ($id)
    {
        $Image = App::getModel('gallery')->getSmallImageById($id);
        
        return App::getModel('gallery')->getImagePath($Image);
    }

    public function getNormalImagePath ($id)
    {
        $Image = App::getModel('gallery')->getNormalImageById($id);
        
        return App::getModel('gallery')->getImagePath($Image);
    }

    public function getProducerAll ($Categories = Array())
    {
        if (! empty($Categories)){
            $sql = 'SELECT
						P.idproducer AS id,
						PT.name,
						PT.seo
					FROM producer P
					INNER JOIN product PR ON PR.producerid = P.idproducer
					LEFT JOIN productcategory PC ON PC.productid = PR.idproduct
					LEFT JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :language
					WHERE PC.categoryid IN (' . implode(',', $Categories) . ') AND PR.enable = 1
					GROUP BY P.idproducer
					ORDER BY PT.name ASC';
        }
        else{
            $sql = 'SELECT
						P.idproducer AS id,
						PT.name,
						PT.seo
					FROM producer P
					LEFT JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :language
					GROUP BY P.idproducer
					ORDER BY PT.name ASC';
        }
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('language', Helper::getLanguageId());
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'seo' => $rs['seo']
            );
        }
        
        return $Data;
    }

    public function getProducerAllByProducts ($ids)
    {
        $sql = 'SELECT
					P.idproducer AS id,
					PT.name,
					PT.seo
				FROM producer P
				INNER JOIN product PR ON PR.producerid = P.idproducer
				LEFT JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :language
				WHERE FIND_IN_SET(CAST(PR.idproduct as CHAR), :products)
				GROUP BY P.idproducer';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('language', Helper::getLanguageId());
        $stmt->bindValue('products', implode(',', $ids));
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'seo' => $rs['seo']
            );
        }
        
        return $Data;
    }

    public function checkEraty ()
    {
        $sql = "SELECT
						ES.wariantsklepu,
						ES.numersklepu,
						ES.`char`
					FROM paymentmethod PM
					LEFT JOIN eratysettings ES ON ES.paymentmethodid  = PM.idpaymentmethod
					INNER JOIN paymentmethodview PV ON PM.idpaymentmethod  = PV.paymentmethodid
					WHERE PV.viewid = :viewid AND PM.controller = :controller AND PM.active = 1";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('controller', 'eraty');
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $Data = Array(
                'wariantsklepu' => $rs['wariantsklepu'],
                'numersklepu' => $rs['numersklepu'],
                'char' => $rs['char']
            );
            
            return $Data;
        }
        
        return 0;
    }

    public function getProductIdBySeo ($seo)
    {
        $sql = "SELECT productid FROM producttranslation WHERE seo =:seo AND languageid = :languageid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('seo', $seo);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                return $rs['productid'];
            }
        }
        catch (Exception $e){
            throw new FrontendException($e->getMessage());
        }
    }

    public function getProductRecommendations ($product)
    {
        $dataset = $this->getDataset();
        $dataset->setPagination(3);
        $dataset->setCurrentPage(1);
        $dataset->setOrderBy('random', 'random');
        $dataset->setSQLParams(Array(
            'categoryid' => $product['categoryid'],
            'clientid' => App::getContainer()->get('session')->getActiveClientid(),
            'producer' => 0,
            'pricefrom' => 0,
            'priceto' => 0,
            'filterbyproducer' => 0,
            'enablelayer' => 0,
            'products' => Array()
        ));
        $products = $this->getProductDataset();
        
        return $products;
    }
}