<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */

namespace Gekosale\Plugin;

use FormEngine;
use xajaxResponse;
use Exception;

class CouponsModel extends Component\Model {

    public function getCouponsViews($id) {
        $sql = "SELECT
					viewid
				FROM couponsview
				WHERE couponsid= :id AND viewid= :viewid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('viewid', Helper::getViewId());
        $viewid = 0;
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs) {
            $viewid = $rs['viewid'];
        }
        return $viewid;
    }

    public function getCouponsTranslation($id) {
        $sql = "SELECT
					name,
					description,
					languageid
				FROM couponstranslation
				WHERE couponsid = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            $Data[$rs['languageid']] = Array(
                'name' => $rs['name'],
                'description' => $rs['description']
            );
        }
        return $Data;
    }

    public function getCategoryIds($id) {
        $sql = 'SELECT
					categoryid AS id
				FROM couponscategory
				WHERE couponid = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            $Data[] = $rs['id'];
        }
        return $Data;
    }

    public function getProductId($id) {
        $sql = 'SELECT
					productid
				FROM
					couponsproduct
				WHERE
					couponid = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            $Data[] = $rs['productid'];
        }
        return $Data;
    }

    public function getCouponByCode($code) {
        $sql = "SELECT
					C.idcoupons as id,
					IF(C.suffixtypeid = 1, C.discount, C.discount * CR.exchangerate) AS discount,
					C.datefrom,
					C.dateto,
					C.suffixtypeid,
					C.globalqty,
					C.clientqty,
					C.code,
					C.currencyid,
					C.minimumordervalue,
					C.freeshipping,
					C.excludepromotions,
					S.symbol,
					CT.name,
					CT.description,
        			COUNT(DISTINCT O.idorder) AS used
				FROM coupons C
				LEFT JOIN couponstranslation CT ON CT.couponsid = C.idcoupons AND CT.languageid = :languageid
				LEFT JOIN suffixtype S ON C.suffixtypeid = S.idsuffixtype
        		LEFT JOIN `order` O ON C.idcoupons = O.couponid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = C.currencyid AND CR.currencyto = :currencyto
				WHERE code= :code AND (C.datefrom IS NULL OR C.datefrom <= NOW()) AND (C.dateto IS NULL OR C.dateto >= NOW())
		";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', App::getContainer()->get('session')->getActiveLanguageId());
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $stmt->bindValue('code', $code);
        $stmt->execute();
        $Data = Array();
        $rs = $stmt->fetch();
        if ($rs) {
            $Data = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'description' => $rs['description'],
                'discount' => $rs['discount'],
                'datefrom' => $rs['datefrom'],
                'dateto' => $rs['dateto'],
                'suffixtypeid' => $rs['suffixtypeid'],
                'globalqty' => $rs['globalqty'],
                'used' => $rs['used'],
                'clientqty' => $rs['clientqty'],
                'code' => $rs['code'],
                'minimumordervalue' => $rs['minimumordervalue'],
                'freeshipping' => $rs['freeshipping'],
                'excludepromotions' => $rs['excludepromotions'],
                'currencyid' => $rs['currencyid'],
                'symbol' => $rs['symbol'],
                'language' => $this->getCouponsTranslation($rs['id']),
                'view' => $this->getCouponsViews($rs['id']),
                'category' => $this->getCategoryIds($rs['id']),
                'product' => $this->getProductId($rs['id'])
            );
        }
        return $Data;
    }

    public function checkAvailableCoupons() {
        $sql = 'SELECT
					C.idcoupons,
					C.globalqty,
					COUNT(DISTINCT O.idorder) AS used,
					C.minimumordervalue
				FROM coupons C
				INNER JOIN couponsview CV ON CV.viewid = :viewid AND CV.couponsid = C.idcoupons
				LEFT JOIN `order` O ON C.idcoupons = O.couponid
				WHERE (C.datefrom IS NULL OR C.datefrom <= NOW()) AND (C.dateto IS NULL OR C.dateto >= NOW())
				GROUP BY C.idcoupons
				ORDER BY C.minimumordervalue ASC
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            if (($rs['globalqty'] > $rs['used']) && $rs['minimumordervalue'] < App::getContainer()->get('session')->getActiveGlobalPrice()) {
                $Data[] = 1;
            }
        }
        return count($Data);
    }

    public function getCouponsClients($id) {
        $sql = "SELECT
					clientid
				FROM couponsclient
				WHERE couponid =:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            $Data[] = $rs['clientid'];
        }
        return $Data;
    }

    public function checkCouponForClient($coupon) {
        if (App::getContainer()->get('session')->getActiveClientid() == NULL) {
            return 1;
        } else {
            $clients = $this->getCouponsClients($coupon['id']);
            if (!empty($clients)) {
                if (in_array(App::getContainer()->get('session')->getActiveClientid(), $clients)) {
                    $sql = 'SELECT
								COUNT(O.idorder) AS used
							FROM coupons C
							LEFT JOIN `order` O ON C.idcoupons = O.couponid
							WHERE O.couponid = :id AND O.clientid = :clientid
					';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
                    $stmt->bindValue('id', $coupon['id']);
                    $stmt->execute();
                    $rs = $stmt->fetch();
                    if ($rs) {
                        if ($rs['used'] >= $coupon['clientqty']) {
                            return 'Nie możesz ponownie wykorzystać tego kuponu';
                        } else {
                            return 1;
                        }
                    }
                } else {
                    return 'Nie możesz ponownie wykorzystać tego kuponu';
                }
            } else {
                $sql = 'SELECT
							COUNT(O.idorder) AS used
							FROM coupons C
							LEFT JOIN `order` O ON C.idcoupons = O.couponid
							WHERE O.couponid = :id AND O.clientid = :clientid
				';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
                $stmt->bindValue('id', $coupon['id']);
                $stmt->execute();
                $rs = $stmt->fetch();
                if ($rs) {
                    if ($rs['used'] >= $coupon['clientqty']) {
                        return 'Nie możesz ponownie wykorzystać tego kuponu.';
                    } else {
                        return 1;
                    }
                }
            }
        }
    }

    public function useCoupon($couponCode = NULL) {
        $objResponse = new xajaxResponse();

        if (NULL !== $couponCode) {
            $coupon = $this->getCouponByCode($couponCode);
            $productCart = App::getModel('cart')->getShortCartList();
            if (!empty($coupon)) {

                if (!$coupon['globalqty']) {
                    App::getContainer()->get('session')->setActiveCouponMessage(NULL);
                    App::getContainer()->get('session')->setActiveCoupon(NULL);
                    return $objResponse->script("GError('Nie ma takiego kuponu.', 'Wprowadzony kod jest nieprawidłowy.');");
                } else if ($coupon['globalqty'] < $coupon['used']) {
                    App::getContainer()->get('session')->setActiveCouponMessage(NULL);
                    App::getContainer()->get('session')->setActiveCoupon(NULL);
                    return $objResponse->script("GError('Nie możesz wykorzystać tego kuponu.', 'Wszystkie kupony o tym kodzie zostały już wykorzystane.');");
                }

                $check = $this->checkCouponForClient($coupon);

                if (!empty($coupon['product'])) {

                    $checkProducts = false;

                    foreach ($productCart as $key => $product) {
                        if (in_array($key, $coupon['product'])) {
                            $checkProducts = true;
                        }
                    }

                    if ($checkProducts == false) {
                        App::getContainer()->get('session')->setActiveCouponMessage(NULL);
                        App::getContainer()->get('session')->setActiveCoupon(NULL);
                        $objResponse->script("GError('Nie możesz wykorzystać tego kuponu.', 'W koszyku nie ma produktów, które spełniają warunki wykorzystania kuponu.');");
                        return $objResponse;
                    }
                }

                if ($check == 1) {
                    $total = 0;
                    foreach ($productCart as $key => $product) {
                        $productPrices = App::getModel('product')->getProductPrices($product['idproduct']);
                        if (isset($product['standard']) && $product['standard'] == 1) {
                            if ($this->checkProductCategory($product['idproduct'], $coupon['category']) == 0) {
                                if ($coupon['excludepromotions'] == 0 || ($coupon['excludepromotions'] == 1 && $productPrices['discountprice'] == NULL)) {
                                    $total += $product['qtyprice'];
                                }
                            }
                        }
                        if (isset($product['attributes']) && $product['attributes'] != NULL) {
                            foreach ($product['attributes'] as $attrtab) {
                                if ($this->checkProductCategory($attrtab['idproduct'], $coupon['category']) == 0) {
                                    if ($coupon['excludepromotions'] == 0 || ($coupon['excludepromotions'] == 1 && $productPrices['discountprice'] == NULL)) {
                                        $total += $attrtab['qtyprice'];
                                    }
                                }
                            }
                        }
                    }

                    if ($total > 0) {
                        App::getContainer()->get('session')->setActiveCoupon($coupon);
                        App::getContainer()->get('session')->setActiveCouponMessage(NULL);
                        $objResponse->clear("cart-contents", "innerHTML");
						$objResponse->append("cart-contents", "innerHTML", App::getModel('cart')->getCartTableTemplate());
						$objResponse->script("qtySpinner();");
			
                    } else {
                        App::getContainer()->get('session')->setActiveCouponMessage(NULL);
                        App::getContainer()->get('session')->setActiveCoupon(NULL);
                        $objResponse->script("GError('Nie możesz wykorzystać tego kuponu.', 'W koszyku nie ma produktów, które spełniają warunki wykorzystania kuponu.');");
                    }
                } else {
                    App::getContainer()->get('session')->setActiveCouponMessage($check);
                    App::getContainer()->get('session')->setActiveCoupon(NULL);
                    $dispatchmethod = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
                    App::getModel('delivery')->setDispatchmethodChecked($dispatchmethod['dispatchmethodid']);
                    $objResponse->script("GError('{$check}');");
                }
            } else {
                App::getContainer()->get('session')->setActiveCoupon(NULL);
                App::getContainer()->get('session')->setActiveCouponMessage(NULL);
                $dispatchmethod = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
                App::getModel('delivery')->setDispatchmethodChecked($dispatchmethod['dispatchmethodid']);
                $objResponse->script("GError('Niepoprawny kod kuponu.', 'Wprowadź kod ponownie lub skontaktuj się z obsługą sklepu.');");
            }
        } else {
            App::getContainer()->get('session')->setActiveCoupon(NULL);
            App::getContainer()->get('session')->setActiveCouponMessage(NULL);
            $dispatchmethod = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
            App::getModel('delivery')->setDispatchmethodChecked($dispatchmethod['dispatchmethodid']);
           
            $objResponse->clear("cart-contents", "innerHTML");
			$objResponse->append("cart-contents", "innerHTML", App::getModel('cart')->getCartTableTemplate());
			$objResponse->script("qtySpinner();");
        }
        return $objResponse;
    }

    public function checkProductCategory($idproduct, $couponCategories) {
        $couponCategories = (empty($couponCategories)) ? Array(
            0
                ) : $couponCategories;
        $sql = 'SELECT
					COUNT(categoryid) AS total
				FROM productcategory
				WHERE productid = :id AND categoryid IN (' . implode(',', $couponCategories) . ')
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $idproduct);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs) {
            return $rs['total'];
        }
    }

    public function setCouponData($request) {
        $dispatchmethod = $request['dispatchmethod'];
        $coupon = App::getContainer()->get('session')->getActiveCoupon();
        $couponInfo = $this->getCouponByCode($coupon['code']);
        $total = 0;
        $couponValue = 0;

        $checkCouponValue = ($coupon['minimumordervalue'] < App::getContainer()->get('session')->getActiveGlobalPrice());
        $checkCouponGlobalQuantity = ($couponInfo['globalqty'] > $couponInfo['used']);

        if ($checkCouponValue && $checkCouponGlobalQuantity && !empty($dispatchmethod)) {
            if (isset($coupon['discount']) && $coupon['discount'] > 0) {
                foreach ($request['cart'] as $key => $product) {
                    if (isset($product['standard']) && $product['standard'] == 1) {
                        if ($this->checkProductCategory($product['idproduct'], $coupon['category']) == 0) {
                            if ($coupon['excludepromotions'] == 0 || ($coupon['excludepromotions'] == 1 && $product['pricebeforepromotionnetto'] == NULL)) {
                                if (!empty($couponInfo['product'])) {
                                    if (in_array($product['idproduct'], $couponInfo['product'])) {
                                        $total += $product['qtyprice'];
                                    }
                                } else {
                                    $total += $product['qtyprice'];
                                }
                            }
                        }
                    }

                    if (isset($product['attributes']) && $product['attributes'] != NULL) {
                        foreach ($product['attributes'] as $attrtab) {
                            if ($this->checkProductCategory($attrtab['idproduct'], $coupon['category']) == 0) {
                                if ($coupon['excludepromotions'] == 0 || ($coupon['excludepromotions'] == 1 && $attrtab['pricebeforepromotionnetto'] == NULL)) {
                                    if (!empty($couponInfo['product'])) {
                                        if (in_array($product['idproduct'], $couponInfo['product'])) {
                                            $total += $attrtab['qtyprice'];
                                        }
                                    } else {
                                        $total += $attrtab['qtyprice'];
                                    }
                                }
                            }
                        }
                    }
                }

                switch ($couponInfo['suffixtypeid']) {
                    case 1:
                        $discount = $total * (1 - ($couponInfo['discount'] / 100));
                        break;
                    case 3:
                        $discount = $couponInfo['discount'];
                        break;
                }
                if ($discount > 0) {
                    $couponValue = number_format($discount, 2, '.', '');
                }
            }

            App::getContainer()->get('session')->setActiveCouponValue($couponValue);

            App::getModel('delivery')->setDispatchmethodChecked($dispatchmethod['dispatchmethodid']);

            if (isset($couponInfo['freeshipping']) && $couponInfo['freeshipping'] == 1) {
                $dispatchmethod['dispatchmethodcost'] = 0;
                $dispatchmethod['dispatchmethodcostnetto'] = 0;
                $activeDispatchmethod = Array(
                    'dispatchmethodid' => $dispatchmethod['dispatchmethodid'],
                    'dispatchmethodcost' => 0,
                    'dispatchmethodcostnetto' => 0,
                    'dispatchmethodname' => $dispatchmethod['dispatchmethodname']
                );
                App::getContainer()->get('session')->setActiveDispatchmethodChecked($activeDispatchmethod);
                if (($priceWithDispatchmethod = App::getContainer()->get('session')->getActiveGlobalPrice() - $couponValue) < 0) {
                    $priceWithDispatchmethod = 0;
                }
                App::getContainer()->get('session')->setActiveglobalPriceWithDispatchmethod($priceWithDispatchmethod);
                App::getContainer()->get('session')->setActiveglobalPriceWithDispatchmethodNetto(App::getContainer()->get('session')->getActiveGlobalPriceWithoutVat());
            } else {
                if (($finalBrutto = App::getContainer()->get('session')->getActiveglobalPriceWithDispatchmethod() - $couponValue) < 0) {
                    $finalBrutto = 0;
                }
                App::getContainer()->get('session')->setActiveglobalPriceWithDispatchmethod($finalBrutto);
                App::getContainer()->get('session')->setActiveglobalPriceWithDispatchmethodNetto(App::getContainer()->get('session')->getActiveGlobalPriceWithoutVat());
            }
        } else {
            App::getContainer()->get('session')->setActiveCoupon(NULL);
            $dispatchmethod = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
            App::getModel('delivery')->setDispatchmethodChecked($dispatchmethod['dispatchmethodid']);
        }
    }

    public function updateOrderCouponData($request) {
        $orderid = $request['id'];
        $coupon = App::getContainer()->get('session')->getActiveCoupon();

        $sql = 'UPDATE `order` SET
					couponid = :couponid,
					couponcode = :couponcode,
					couponfreedelivery = :couponfreedelivery,
					coupondiscount = :coupondiscount
				WHERE idorder = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('coupondiscount', App::getContainer()->get('session')->getActiveCouponValue());
        $stmt->bindValue('couponcode', $coupon['code']);
        if (isset($coupon['freeshipping']) && $coupon['freeshipping'] == 1) {
            $stmt->bindValue('couponfreedelivery', 1);
        } else {
            $stmt->bindValue('couponfreedelivery', 0);
        }
        $stmt->bindValue('id', $orderid);
        $stmt->bindValue('couponid', $coupon['id']);
        $stmt->execute();

        App::getContainer()->get('session')->setActiveCoupon(NULL);

        App::getModel('hotprice')->couponUse($coupon['code']);
    }

}
