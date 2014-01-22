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
 * $Revision: 466 $
 * $Author: gekosale $
 * $Date: 2011-08-31 15:52:57 +0200 (Śr, 31 sie 2011) $
 * $Id: wishlist.php 466 2011-08-31 13:52:57Z gekosale $
 */

namespace Gekosale\Plugin;

class WishlistModel extends Component\Model\Dataset
{

	public function initDataset ($dataset)
	{
		$clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();

		if (! empty($clientGroupId)){
			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'WL.productid'
				),
				'attributeid' => Array(
					'source' => 'WL.productattributesetid'
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
				'pricenetto' => Array(
					'source' => 'IF(PGP.groupprice = 1,
								 	PGP.sellprice,
								 	P.sellprice
								 ) * CR.exchangerate',
				),
				'price' => Array(
					'source' => 'IF(PGP.groupprice = 1,
									PGP.sellprice,
									P.sellprice
								 ) * (1 + (V.value / 100)) * CR.exchangerate',
				),
				'onstock' => Array(
					'source' => 'IF(P.trackstock = 1, IF(P.stock > 0, 1, 0), 1)'
				),
				'buypricenetto' => Array(
					'source' => 'P.buyprice * CR.exchangerate',
				),
				'buyprice' => Array(
					'source' => 'P.buyprice * (1 + (V.value / 100)) * CR.exchangerate',
				),
				'discountpricenetto' => Array(
					'source' => 'IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
								 	PGP.discountprice,
								 	IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, NULL)
								 ) * CR.exchangerate',
				),
				'discountprice' => Array(
					'source' => 'IF(PGP.promotion = 1 AND IF(PGP.promotionstart IS NOT NULL, PGP.promotionstart <= CURDATE(), 1) AND IF(PGP.promotionend IS NOT NULL, PGP.promotionend >= CURDATE(), 1),
								 	PGP.discountprice,
								 	IF(PGP.groupprice IS NULL AND P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice, NULL)
								 ) * (1 + (V.value / 100)) * CR.exchangerate',
				),
				'photo' => Array(
					'source' => 'IF(Photo.photoid IS NULL, 1, Photo.photoid)',
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
				)
			));

			$dataset->setFrom('
				wishlist WL
				LEFT JOIN productcategory PC ON PC.productid = WL.productid
				LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
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
		else {
			$dataset->setTableData(Array(
				'id' => Array(
					'source' => 'WL.productid'
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
				'pricenetto' => Array(
					'source' => 'P.sellprice * CR.exchangerate',
				),
				'price' => Array(
					'source' => 'P.sellprice * (1 + (V.value / 100)) * CR.exchangerate',
				),
				'buypricenetto' => Array(
					'source' => 'ROUND(P.buyprice * CR.exchangerate, 2)',
				),
				'buyprice' => Array(
					'source' => 'ROUND((P.buyprice + (P.buyprice * V.`value`)/100) * CR.exchangerate, 2)',
				),
				'discountpricenetto' => Array(
					'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * CR.exchangerate, NULL)',
				),
				'discountprice' => Array(
					'source' => 'IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL)',
				),
				'photo' => Array(
					'source' => 'IF(Photo.photoid IS NULL, 1, Photo.photoid)',
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
				)
			));

			$dataset->setFrom('
				wishlist WL
				LEFT JOIN productcategory PC ON PC.productid = WL.productid
				LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
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
			WL.clientid = :clientid AND
			VC.viewid = :viewid
		');

		$dataset->setGroupBy('
			P.idproduct
		');

		$dataset->setSQLParams(Array(
			'clientid' => App::getContainer()->get('session')->getActiveClientid()
		));

	}

	public function getProductDataset ()
	{
		return $this->getDataset()->getDatasetRecords();
	}

	public function getClientWishList ()
	{
		$sql = "SELECT
					WL.idwishlist,
					WL.productid,
					WL.productattributesetid,
					PT.name,
					PT.shortdescription,
					WL.viewid,
					PHOTO.photoid,
					P.sellprice,
					V.`value`,
					CG.idclientgroup,
							IF (WL.productattributesetid>0,
							(SELECT GROUP_CONCAT(APV.name ORDER BY APV.name DESC SEPARATOR '; ')
									FROM attributeproductvalue APV
									JOIN productattributevalueset PAVS ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
									JOIN productattributeset PAS ON PAS.idproductattributeset=PAVS.productattributesetid
								WHERE PAS.idproductattributeset=WL.productattributesetid)
							, NULL) as productattributes,
							IF (WL.productattributesetid>0, PAS.stock, P.stock) as stock,
							IF (WL.productattributesetid>0, PAS.attributeprice, P.sellprice) as pricewithoutvat,
							IF (WL.productattributesetid>0,
								ROUND(PAS.attributeprice+(PAS.attributeprice*V.`value`)/100, 4),
								ROUND((P.sellprice*V.`value`/100)+P.sellprice, 4)
							) AS price
					FROM wishlist WL
					LEFT JOIN productattributeset AS PAS ON WL.productattributesetid = PAS.idproductattributeset
					LEFT JOIN product AS P ON WL.productid= P.idproduct
					LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid = :languageid
					LEFT JOIN `vat` V ON V.idvat = P.vatid
					LEFT JOIN clientdata AS CD ON CD.clientid = :clientid
					LEFT JOIN productphoto PHOTO ON PHOTO.productid=P.idproduct
					LEFT JOIN clientgroup AS CG ON CG.idclientgroup = CD.clientgroupid
					WHERE WL.clientid = :clientid
						AND PHOTO.mainphoto = 1
					AND WL.viewid= :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('viewid', Helper::getViewId());
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$price = 0;
				$priceWithoutVat = 0;
				$productid = $rs['productid'];
				$clientGroupid = App::getContainer()->get('session')->getActiveClientGroupid();
				$attrId = $rs['productattributesetid'];
				// price for clientgorup
				if ($clientGroupid > 0){
					// price for product with attribute
					if ($attrId > 0){
						$priceWithoutVat = App::getModel('product')->getProductAttributePromotionPriceForClients($productid, $attrId, $clientGroupid);
						if ($priceWithoutVat > 0){
							$price = sprintf('%01.2f', ($priceWithoutVat + ($priceWithoutVat * $rs['value'] / 100)));
						}
						// price for standadrd product
					}
					else{
						$priceWithoutVat = App::getModel('product')->getProductPromotionPriceForClients($productid, $clientGroupid);
						if ($priceWithoutVat > 0){
							$price = sprintf('%01.2f', ($priceWithoutVat + ($priceWithoutVat * $rs['value'] / 100)));
						}
					}
					// price for all clients
				}
				else{
					// price for product with attribute
					if ($attrId > 0){
						$priceWithoutVat = App::getModel('product')->getProductAttributePromotionPrice($productid, $attrId);
						if ($priceWithoutVat > 0){
							$price = sprintf('%01.2f', ($priceWithoutVat + ($priceWithoutVat * $rs['value'] / 100)));
						}
						// price for standadrd product
					}
					else{
						$priceWithoutVat = App::getModel('product')->getProductPromotionPrice($productid);
						if ($priceWithoutVat > 0){
							$price = sprintf('%01.2f', ($priceWithoutVat + ($priceWithoutVat * $rs['value'] / 100)));
						}
					}
				}
				// there isn't price promotion for product
				if ($price == 0){
					// price for product with attribute
					if ($attrId > 0){
						$priceWithoutVat = sprintf('%01.2f', $rs['pricewithoutvat']);
						$price = sprintf('%01.2f', $rs['price']);
						// price for standadrd product
					}
					else{
						$priceWithoutVat = sprintf('%01.2f', $rs['pricewithoutvat']);
						$price = sprintf('%01.2f', $rs['price']);
					}
				}
				$Data[] = Array(
					'idwishlist' => $rs['idwishlist'],
					'productattributesetid' => $rs['productattributesetid'],
					'idproduct' => $rs['productid'],
					'name' => $rs['name'],
					'photoid' => $rs['photoid'],
					'price' => $price,
					'stock' => $rs['stock'],
					'productattributes' => $rs['productattributes'],
					'pricewithoutvat' => $priceWithoutVat
				);
			}
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage('ERR_QUERY_WISHLIST'));
		}
		return $Data;
	}

	public function deleteAJAXProductFromWishList ($idproduct, $attribute = 0)
	{
		$objResponseDeleteProd = new \xajaxResponse();

		try{
			$this->deleteProductFromWishList($idproduct, $attribute);
			App::getContainer()->get('session')->setVolatileWishlistMessage('Produkt został usunięty ze schowka.');
			$objResponseDeleteProd->script('window.location.reload(false)');
		}
		catch (Exception $e){
			App::getContainer()->get('session')->setVolatileWishlistError('Wystąpił błąd przy usuwaniu produktu ze schowka.');
			$objResponseDeleteProd->script('window.location.reload(false)');
		}

		return $objResponseDeleteProd;
	}

	public function deleteProductFromWishList ($idproduct, $idattribute = 0)
	{
		$sql = 'DELETE
				FROM `wishlist`
				WHERE clientid= :clientid
					AND productid= :idproduct
					AND productattributesetid= :productattributesetid';

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productattributesetid', $idattribute);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('idproduct', $idproduct);
		try{
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage(''));
		}
	}

	public function addProductToWishList ($idproduct, $idattribute = 0)
	{
		$product = App::getModel('product');
		$prices = $product->getProductPrices($idproduct);
		$price = $prices['discountprice'] > 0 ? $prices['discountprice'] : $prices['price'];
		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : App::getRegistry()->loader->getLayerViewId();

		$sql = "INSERT IGNORE
			INTO `wishlist`
			VALUES (NULL, :idproduct, :productattributesetid, :clientid, CURRENT_TIMESTAMP, :price, :viewid)";

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idproduct', $idproduct);
		$stmt->bindValue('productattributesetid', $idattribute);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('price', $price);
		$stmt->bindValue('viewid', $viewid);

		try {
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage(''));
		}
	}

	public function addAjaxProductToWishlist($idproduct, $idattribute = 0)
	{
		$response = new \xajaxResponse();
		
		try {
			if (App::getContainer()->get('session')->getActiveClientid() > 0){
				$this->addProductToWishList($idproduct, $idattribute);
				$wishlistUrl = App::getRegistry()->router->generate('frontend.wishlist');
				$response->script('$("#wishlist-message").remove()');
				$response->script('$(\'body\').append(\'<div id="wishlist-message" class="modal fade hide"><button class="close" data-dismiss="modal"></button><div class="modal-body" style="text-align:center;"><h1 style="text-align:center;">'.$this->trans('TXT_WISHLIST_MODAL_HEADING').'</h1>'.$this->trans('TXT_WISHLIST_MODAL_TEXT_1').' <strong><a href="' . $wishlistUrl . '">'.$this->trans('TXT_WISHLIST_MODAL_TEXT_2').'</a></strong></div></div>\')');
				$response->script('$("#wishlist-message").modal("show")');
				

			}
			else {
				App::getContainer()->get('session')->setVolatileUserLoginError($this->trans('TXT_WISHLIST_MUST_BE_LOGGED'));
				App::getContainer()->get('session')->setActiveWishlistItem(array('idproduct' => $idproduct, 'idattribute' => $idattribute));
				$loginUrl = App::getRegistry()->router->generate('frontend.clientlogin', true);
				$response->script('$("#wishlist-message").remove()');
				$response->script('$(\'body\').append(\'<div id="wishlist-message" class="modal fade hide"><button class="close" data-dismiss="modal"></button><div class="modal-body" style="text-align:center;"><h1 style="text-align:center;">'.$this->trans('TXT_WISHLISTERR_MODAL_HEADING').'</h1>'.$this->trans('TXT_WISHLISTERR_MODAL_TEXT_1').' <strong><a href="' . $loginUrl . '">'.$this->trans('TXT_WISHLISTERR_MODAL_TEXT_2').'</a></strong></div></div>\')');
				$response->script('$("#wishlist-message").modal("show")');

			}
		}
		catch (Exception $e) {
			$response->script('$.colorbox({html:\'<div class="mgh-colorbox"><div class="infobox red">'.$this->trans('TXT_WISHLIST_ERROR').'</div></div>\'})');
		}

		return $response;
	}
	
}