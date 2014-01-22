<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: cart.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale\Plugin;
use Doctrine\DBAL\Schema\View;
use xajaxResponse;

class CartModel extends Component\Model
{

    protected $Cart;

    protected $globalPrice;

    protected $globalWeight;

    protected $globalPriceWithoutVat;

    protected $globalPriceWithDispatchmethod;

    protected $globalPriceWithDispatchmethodNetto;

    protected $count;

    protected $product;

    public function __construct ($registry)
    {
        parent::__construct($registry);
        if (($this->Cart = App::getContainer()->get('session')->getActiveCart()) === NULL){
            $this->Cart = Array();
        }
        if (($this->globalPrice = App::getContainer()->get('session')->getActiveGlobalPrice()) === NULL){
            $this->globalPrice = 0.00;
        }
        if (($this->globalWeight = App::getContainer()->get('session')->getActiveGlobalWeight()) === NULL){
            $this->globalWeight = 0.00;
        }
        if (($this->globalPriceWithoutVat = App::getContainer()->get('session')->getActiveGlobalPriceWithoutVat()) === NULL){
            $this->globalPriceWithoutVat = 0.00;
        }
        if (($this->globalPriceWithDispatchmethod = App::getContainer()->get('session')->getActiveGlobalPriceWithDispatchmethod()) === NULL){
            $this->globalPriceWithDispatchmethod = 0.00;
        }
        if (($this->globalPriceWithDispatchmethodNetto = App::getContainer()->get('session')->getActiveGlobalPriceWithDispatchmethodNetto()) === NULL){
            $this->globalPriceWithDispatchmethodNetto = 0.00;
        }
        if ($this->count = App::getContainer()->get('session')->getActiveCount() === NULL){
            $this->count = 0;
        }
    }

    public function addAJAXProductToCart ($idproduct, $attr = NULL, $qty)
    {
        $objResponse = new xajaxResponse();
        $this->product = App::getModel('product')->getProductAndAttributesById($idproduct);
        
        if (empty($this->product)){
            $objResponse->script('GError("' . $this->trans('ERR_SHORTAGE_OF_STOCK') . '")');
            return $objResponse;
        }
        
        $attr = ($attr == 0) ? NULL : $attr;
        $qty = ((int) $qty > 0) ? $qty : 1;
        $trackstock = $this->product['trackstock'];
        
        if (NULL !== $attr){
            foreach ($this->product['attributes'] as $variant){
                if ($variant['idproductattributeset'] == $attr){
                    $stock = $variant['stock'];
                    break;
                }
            }
        }
        else{
            $stock = $this->product['stock'];
        }
        
        $maxQty = $this->checkProductQuantity($trackstock, $qty, $stock);
        
        if ($maxQty == 0){
            $objResponse->script('GError("' . $this->trans('ERR_SHORTAGE_OF_STOCK') . '")');
            return $objResponse;
        }
        
        if ($trackstock == 1 && ($stock < $qty)){
            $objResponse->script('GError("' . $this->trans('ERR_SHORTAGE_OF_STOCK') . '", "' . sprintf($this->trans('ERR_LOW_OF_STOCK'), $maxQty) . '")');
            $objResponse->assign('product-qty', 'value', $maxQty);
            return $objResponse;
        }
        
        if (NULL !== $attr){
            if (isset($this->Cart[$idproduct]['attributes'][$attr])){
                $oldqty = $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                $newqty = $this->Cart[$idproduct]['attributes'][$attr]['qty'] + $qty;
                if (($oldqty == $stock) && $trackstock == 1){
                    $objResponse->assign('product-qty', 'value', 1);
                    $objResponse->script('GError("' . $this->trans('ERR_STOCK_LESS_THAN_QTY') . '", "' . $this->trans('ERR_MAX_STORAGE_STATE_ON_CART') . ' (' . $stock . ' ' . $this->trans('TXT_QTY') . ')' . '")');
                    return $objResponse;
                }
                else 
                    if (($newqty > $stock) && $trackstock == 1){
                        $this->Cart[$idproduct]['attributes'][$attr]['qty'] = $stock;
                        $this->Cart[$idproduct]['attributes'][$attr]['qtyprice'] = $this->$this->Cart[$idproduct]['attributes'][$attr]['newprice'] * ($this->Cart[$idproduct]['attributes'][$attr]['qty']);
                        $this->updateSession();
                    }
                    else{
                        $this->Cart[$idproduct]['attributes'][$attr]['qty'] = $newqty;
                        $this->Cart[$idproduct]['attributes'][$attr]['qtyprice'] = $this->Cart[$idproduct]['attributes'][$attr]['newprice'] * $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                        $this->updateSession();
                    }
            }
            elseif (isset($this->Cart[$idproduct])){
                $this->cartAddProductWithAttr($idproduct, $qty, $attr);
                $this->getProductFeatures($idproduct, $attr);
            }
            else{
                $this->cartAddProductWithAttr($idproduct, $qty, $attr);
                $this->getProductFeatures($idproduct, $attr);
            }
        }
        else{
            if (isset($this->Cart[$idproduct]) && isset($this->Cart[$idproduct]['standard'])){
                $oldqty = $this->Cart[$idproduct]['qty'];
                $newqty = $this->Cart[$idproduct]['qty'] + $qty;
                if (($oldqty >= $stock) && $trackstock == 1){
                    $objResponse->assign('product-qty', 'value', 1);
                    $objResponse->script('GError("' . $this->trans('ERR_STOCK_LESS_THAN_QTY') . '", "' . $this->trans('ERR_MAX_STORAGE_STATE_ON_CART') . ' (' . $stock . ' ' . $this->trans('TXT_QTY') . ')' . '")');
                    return $objResponse;
                }
                else 
                    if (($newqty >= $stock) && $trackstock == 1){
                        $this->Cart[$idproduct]['qty'] = $stock;
                        $this->Cart[$idproduct]['qtyprice'] = $this->Cart[$idproduct]['newprice'] * $this->Cart[$idproduct]['qty'];
                        $this->updateSession();
                    }
                    else{
                        $this->Cart[$idproduct]['qty'] = $newqty;
                        $this->Cart[$idproduct]['qtyprice'] = $this->Cart[$idproduct]['newprice'] * $this->Cart[$idproduct]['qty'];
                        $this->updateSession();
                    }
            }
            else{
                $this->cartAddStandardProduct($idproduct, $qty);
            }
        }
        
        $objResponse->clear("topBasket", "innerHTML");
        $objResponse->append("topBasket", "innerHTML", $this->getCartPreviewTemplate());
        
        $objResponse->clear("basketModal", "innerHTML");
        $objResponse->append("basketModal", "innerHTML", $this->getBasketModalTemplate());
        
        if ($this->registry->loader->getParam('cartredirect') == 0){
            $objResponse->script("$('#basketModal').modal('show');");
        }
        else{
            $url = $this->registry->router->generate('frontend.cart', true);
            $objResponse->script("window.location.href = '{$url}'");
        }
        return $objResponse;
    }

    public function doQuickAddCart ($id)
    {
        $this->product = App::getModel('product')->getProductAndAttributesById($id);
        if (empty($this->product['attributes'])){
            return $this->addAJAXProductToCart($id, 0, 1);
        }
        else{
            $objResponse = new xajaxResponse();
            
            App::getModel('product/product')->getPhotos($this->product);
            $selectAttributes = App::getModel('product/product')->getProductAttributeGroups($this->product);
            $attset = App::getModel('product/product')->getProductVariant($this->product);
            
            foreach ($selectAttributes as $key => $val){
                $selectAttributes[$key]['primary'] = key($val['attributes']);
                natsort($val['attributes']);
                $selectAttributes[$key]['attributes'] = $val['attributes'];
            }
            
            $Data = Array();
            foreach ($attset as $group => $data){
                $keys = array_keys($data['variant']);
                natsort($keys);
                $Data[implode(',', $keys)] = Array(
                    'setid' => $group,
                    'stock' => $data['stock'],
                    'sellprice' => $this->registry->core->processPrice($data['sellprice']),
                    'sellpricenetto' => $this->registry->core->processPrice($data['sellpricenetto']),
                    'sellpriceold' => $this->registry->core->processPrice($data['attributepricegrossbeforepromotion']),
                    'sellpricenettoold' => $this->registry->core->processPrice($data['attributepricenettobeforepromotion']),
                    'availablity' => $data['availablity'],
                    'photos' => $data['photos']
                );
            }
            
            $delivery = App::getModel('delivery')->getDispatchmethodPriceForProduct($this->product['price'], $this->product['weight']);
            
            $deliverymin = PHP_INT_MAX;
            foreach ($delivery as $i){
                $deliverymin = min($deliverymin, $i['dispatchmethodcost']);
            }
            
            $variants = json_encode($Data);
            
            $this->registry->template->assign('product', $this->product);
            $this->registry->template->assign('attributes', $selectAttributes);
            $this->registry->template->assign('attset', $attset);
            $this->registry->template->assign('deliverymin', $deliverymin);
            $result = $this->registry->template->fetch('product_modal.tpl');
            $objResponse->clear("productModal", "innerHTML");
            $objResponse->append("productModal", "innerHTML", $result);
            $objResponse->script("$('#productModal .modal-body').GProductAttributes({aoVariants: {$variants}, bTrackStock: {$this->product['trackstock']}});");
            $objResponse->script("$('#productModal').modal('show');");
            $objResponse->script("qtySpinner();");
            return $objResponse;
        }
    }

    public function addProductsToCartFromMissingCart ($Data)
    {
        foreach ($Data as $idproduct => $values){
            $product = App::getModel('product')->getProductAndAttributesById($idproduct);
            if (isset($values['standard']) && $values['standard'] == 1){
                $qty = $this->checkProductQuantity($product['trackstock'], $values['qty'], $product['stock']);
                if ($qty > 0){
                    $this->cartAddStandardProduct($idproduct, $qty);
                }
            }
            else{
                if (isset($values['attributes'])){
                    foreach ($values['attributes'] as $attr => $variant){
                        if (isset($product['attributes'])){
                            foreach ($product['attributes'] as $k => $v){
                                if ($v['idproductattributeset'] == $attr){
                                    $qty = $this->checkProductQuantity($product['trackstock'], $variant['qty'], $v['stock']);
                                    if ($qty > 0){
                                        $this->cartAddProductWithAttr($idproduct, $qty, $attr);
                                        $this->getProductFeatures($idproduct, $attr);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function checkProductQuantity ($trackStock, $qty, $stock)
    {
        if ($trackStock == 0){
            return $qty;
        }
        else{
            if ($qty > $stock){
                return $stock;
            }
            else{
                return $qty;
            }
        }
        return 0;
    }

    public function deleteAJAXProductFromCart ($idproduct, $attr = NULL)
    {
        $objResponseDel = new xajaxResponse();
        try{
            // product without attributes- simple product
            if (! isset($this->Cart[$idproduct]['attributes']) && $attr == NULL){
                $this->deleteProductCart($idproduct);
                // product with attributes and standard product
            }
            elseif ($this->Cart[$idproduct]['attributes'] != NULL && $attr != NULL){
                // if standard product
                if (isset($this->Cart[$idproduct]['standard'])){
                    // then delete chosen attribute only and leave standard
                    // product
                    $this->deleteProductAttributeCart($idproduct, $attr);
                }
                else{
                    // first- delete attributes of this product
                    $this->deleteProductAttributeCart($idproduct, $attr);
                    if ($this->Cart[$idproduct]['attributes'] == NULL){
                        // if there isnt other prodcut attributes or isnt set-up
                        // standard product
                        // delete product from cart
                        $this->deleteProductAtributesCart($idproduct);
                    }
                }
                // if there are product attributes on cart
            }
            elseif ($this->Cart[$idproduct]['attributes'] != NULL && $attr == NULL){
                if (isset($this->Cart[$idproduct])){
                    // then delete only product standard
                    $this->deleteProductAttributeCart($idproduct, NULL);
                }
                // if there arent attributes of product on cart
            }
            elseif ($this->Cart[$idproduct]['attributes'] == NULL && $attr == NULL){
                // then delete only product standard
                unset($this->Cart[$idproduct]);
            }
            else{
                throw new Exception('No such product (id=' . $idproduct . ') on cart');
            }
        }
        catch (Exception $e){
            $objResponseDel->alert($e->getMessage());
        }
        
        if ($idproduct == App::getContainer()->get('session')->getActiveGiftWrapProduct()){
            App::getModel('giftwrap')->unsetGiftWrapData();
        }
        $this->updateSession();
        $objResponseDel->script('window.location.reload( false )');
        return $objResponseDel;
    }

    public function checkPackageQty ($qty, $packagesize)
    {
        $qty = number_format($qty);
        $modulo = number_format(fmod($qty, $packagesize), 4);
        if ($modulo > 0){
            $newqty = $qty - $modulo;
        }
        else{
            $newqty = $qty;
        }
        return $newqty;
    }

    public function changeQuantity ($idproduct, $attr = NULL, $newqty)
    {
        $objResponseInc = new xajaxResponse();
        if ($newqty == 0){
            $this->deleteAJAXProductFromCart($idproduct, $attr);
        }
        else{
            
            try{
                if (isset($this->Cart[$idproduct])){
                    // standard product (of product with attributes)
                    if (isset($this->Cart[$idproduct]['standard']) && $this->Cart[$idproduct]['standard'] == 1 && $attr == NULL){
                        $newqty = $this->checkPackageQty($newqty, $this->Cart[$idproduct]['packagesize']);
                        
                        $oldQty = $this->Cart[$idproduct]['stock'];
                        if (($newqty > $this->Cart[$idproduct]['stock']) && $this->Cart[$idproduct]['trackstock'] == 1){
                            $objResponseInc->script('GError("' . $this->trans('ERR_COULDNT_INCREASE_QTY') . $this->trans('ERR_MAX_STORAGE_STATE_ON_CART') . '");');
                        }
                        else{
                            $this->Cart[$idproduct]['qty'] = $newqty;
                            $this->Cart[$idproduct]['qtyprice'] = $this->Cart[$idproduct]['newprice'] * $this->Cart[$idproduct]['qty'];
                            $this->Cart[$idproduct]['weighttotal'] = $this->Cart[$idproduct]['weight'] * $this->Cart[$idproduct]['qty'];
                            $this->updateSession();
                        }
                    }
                    // product with attributes
                    if ($this->Cart[$idproduct]['attributes'] != NULL && $attr != NULL){
                        $modulo = $newqty % $this->Cart[$idproduct]['attributes'][$attr]['packagesize'];
                        $newqty = abs(($modulo > 0) ? $newqty - $modulo : $newqty);
                        $oldQty = $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                        $this->Cart[$idproduct]['attributes'][$attr]['qty'] = $newqty;
                        $this->Cart[$idproduct]['attributes'][$attr]['qtyprice'] = $this->Cart[$idproduct]['attributes'][$attr]['newprice'] * $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                        $this->Cart[$idproduct]['attributes'][$attr]['weighttotal'] = $this->Cart[$idproduct]['attributes'][$attr]['weight'] * $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                        if ($this->Cart[$idproduct]['attributes'][$attr]['trackstock'] == 0){
                            $this->updateSession();
                        }
                        else{
                            if ($this->Cart[$idproduct]['attributes'][$attr]['qty'] <= $this->Cart[$idproduct]['attributes'][$attr]['stock']){
                                $this->updateSession();
                            }
                            else{
                                $this->Cart[$idproduct]['attributes'][$attr]['qty'] = $oldQty;
                                $this->Cart[$idproduct]['attributes'][$attr]['qtyprice'] = $this->Cart[$idproduct]['attributes'][$attr]['newprice'] * $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                                $this->Cart[$idproduct]['attributes'][$attr]['weighttotal'] = $this->Cart[$idproduct]['attributes'][$attr]['weight'] * $this->Cart[$idproduct]['attributes'][$attr]['qty'];
                                $objResponseInc->script('GError("' . $this->trans('ERR_COULDNT_INCREASE_QTY') . '<br />' . $this->trans('ERR_MAX_STORAGE_STATE_ON_CART') . '");');
                            }
                        }
                    }
                }
            }
            catch (Exception $e){
                $objResponseInc->alert($e->getMessage());
            }
        }
        
        $objResponseInc->clear("topBasket", "innerHTML");
        $objResponseInc->append("topBasket", "innerHTML", $this->getCartPreviewTemplate());
        $objResponseInc->clear("cart-contents", "innerHTML");
        $objResponseInc->append("cart-contents", "innerHTML", $this->getCartTableTemplate());
        $objResponseInc->script("qtySpinner();");
        return $objResponseInc;
    }

    public function getCartTableTemplate ()
    {
        $this->clientModel = App::getModel('client');
        $this->paymentModel = App::getModel('payment');
        $this->deliveryModel = App::getModel('delivery');
        $this->dispatchMethods = $this->deliveryModel->getDispatchmethodPrice();
        
        $globalprice = $this->getGlobalPrice();
        
        $checkRulesCart = App::getModel('cart')->checkRulesCart();
        if (is_array($checkRulesCart) && count($checkRulesCart) > 0){
            $this->registry->template->assign('checkRulesCart', $checkRulesCart);
        }
        if (App::getContainer()->get('session')->getActiveDispatchmethodChecked() == NULL){
            usort($this->dispatchMethods, function  ($a, $b)
            {
                return $a['hierarchy'] - $b['hierarchy'];
            });
            $default = current($this->dispatchMethods);
            App::getModel('delivery')->setDispatchmethodChecked($default['dispatchmethodid']);
        }
        
        $paymentMethods = App::getModel('payment')->getPaymentMethods();
        if (App::getContainer()->get('session')->getActivePaymentMethodChecked() != 0){
            $paymentid = App::getContainer()->get('session')->getActivePaymentMethodChecked();
            $paymentid = $paymentid['idpaymentmethod'];
            $exists = FALSE;
            
            foreach ($paymentMethods as $payment){
                if ($payment['idpaymentmethod'] == $paymentid){
                    $exists = TRUE;
                    break;
                }
            }
            
            if (! $exists){
                App::getContainer()->get('session')->setActivePaymentMethodChecked(0);
            }
        }
        
        if (App::getContainer()->get('session')->getActivePaymentMethodChecked() == 0){
            if (isset($paymentMethods[0])){
                App::getModel('payment')->setPaymentMethodChecked($paymentMethods[0]['idpaymentmethod'], $paymentMethods[0]['name']);
            }
        }
        
        $minimumordervalue = $this->getMinimumOrderValue();
        
        $order = App::getModel('finalization')->setClientOrder();
        
        $productCart = $this->getShortCartList();
        $productCart = $this->getProductCartPhotos($productCart);
        
        $assignData = Array(
            'productCartCombinations' => $this->getProductCartCombinations(),
            'globalPrice' => $this->getGlobalPrice(),
            'productCart' => $productCart,
            'deliverymethods' => $this->dispatchMethods,
            'checkedDelivery' => App::getContainer()->get('session')->getActiveDispatchmethodChecked(),
            'checkedPayment' => App::getContainer()->get('session')->getActivePaymentMethodChecked(),
            'checkedDeliveryOption' => App::getContainer()->get('session')->getActiveDispatchmethodOption(),
            'payments' => $paymentMethods,
            'minimumordervalue' => $minimumordervalue,
            'coupon' => App::getContainer()->get('session')->getActiveCoupon(),
            'couponvalue' => App::getContainer()->get('session')->getActiveCouponValue(),
            'priceWithDispatchMethod' => App::getContainer()->get('session')->getActiveglobalPriceWithDispatchmethod(),
            'summary' => App::getModel('finalization')->getOrderSummary(),
            'order' => App::getContainer()->get('session')->getActiveClientOrder()
        );
        
        foreach ($assignData as $key => $assign){
            $this->registry->template->assign($key, $assign);
        }
        return $this->registry->template->fetch('cartbox/index/table.tpl');
    }

    public function deleteProductCart ($idproduct)
    {
        try{
            if (isset($this->Cart[$idproduct])){
                unset($this->Cart[$idproduct]);
            }
        }
        catch (Exception $e){
            throw new Exception('No such product on cart');
        }
    }

    public function deleteProductAttributeCart ($idproduct, $attr = NULL)
    {
        try{
            if (isset($this->Cart[$idproduct]['attributes']) && $this->Cart[$idproduct]['attributes'] != NULL && $attr == NULL){
                unset($this->Cart[$idproduct]['standard']);
                unset($this->Cart[$idproduct]['qty']);
                unset($this->Cart[$idproduct]['qtyprice']);
                unset($this->Cart[$idproduct]['weight']);
                unset($this->Cart[$idproduct]['weighttotal']);
                unset($this->Cart[$idproduct]['newprice']);
                unset($this->Cart[$idproduct]['vat']);
                unset($this->Cart[$idproduct]['pricewithoutvat']);
                unset($this->Cart[$idproduct]['mainphotoid']);
                unset($this->Cart[$idproduct]['shortdescription']);
                unset($this->Cart[$idproduct]['name']);
                unset($this->Cart[$idproduct]['stock']);
            }
            elseif ($this->Cart[$idproduct]['attributes'] == NULL && $attr == NULL){
                $this->deleteProductCart($idproduct);
            }
            else{
                if (isset($this->Cart[$idproduct]['attributes'][$attr]) && $attr != NULL){
                    unset($this->Cart[$idproduct]['attributes'][$attr]);
                }
            }
        }
        catch (Exception $e){
            throw new Exception('There is not product with attributes on cart.');
        }
    }

    public function deleteProductAtributesCart ($idproduct)
    {
        try{
            if ($this->Cart[$idproduct]['attributes'] == NULL && ! isset($this->Cart[$idproduct]['standard'])){
                unset($this->Cart[$idproduct]);
            }
        }
        catch (Exception $e){
            throw new Exception('There are not attributes for this' . $idproduct . ' product');
        }
    }

    public function cartAddStandardProduct ($idproduct, $qty)
    {
        $product = (empty($this->product)) ? App::getModel('product')->getProductAndAttributesById($idproduct) : $this->product;
        
        if (is_null($product['discountpricenetto'])){
            $price = $product['price'];
            $priceWithoutVat = $product['pricewithoutvat'];
            $pricebeforepromotionnetto = NULL;
            $pricebeforepromotiongross = NULL;
        }
        else{
            $price = $product['discountprice'];
            $priceWithoutVat = $product['discountpricenetto'];
            $pricebeforepromotionnetto = $product['pricewithoutvat'];
            $pricebeforepromotiongross = $product['price'];
        }
        
        $qtyprice = $qty * $price;
        $weighttotal = $qty * $product['weight'];
        
        $this->Cart[$idproduct] = Array(
            'idproduct' => $idproduct,
            'ean' => $product['ean'],
            'delivelercode' => $product['delivelercode'],
            'seo' => $product['seo'],
            'name' => $product['productname'],
            'mainphotoid' => $product['mainphotoid'],
            'shortdescription' => $product['shortdescription'],
            'stock' => $product['stock'],
            'trackstock' => $product['trackstock'],
            'newprice' => $price,
            'pricewithoutvat' => $priceWithoutVat,
            'pricebeforepromotionnetto' => $pricebeforepromotionnetto,
            'pricebeforepromotiongross' => $pricebeforepromotiongross,
            'unit' => $product['unit'],
            'packagesize' => $product['packagesize'],
            'qty' => $qty,
            'qtyprice' => $qtyprice,
            'weight' => $product['weight'],
            'weighttotal' => $weighttotal,
            'vat' => $product['vatvalue'],
            'standard' => 1,
            'attributes' => isset($this->Cart[$idproduct]['attributes']) ? $this->Cart[$idproduct]['attributes'] : null
        );
        
        $this->updateSession();
    }

    public function getProductCartCombinations ()
    {
        return App::getContainer()->get('session')->getActiveCartCombinations();
    }

    public function setProductCartCombinations ($combinations)
    {
        return App::getContainer()->get('session')->setActiveCartCombinations($combinations);
    }

    public function addCombinationToCart ($id, $multiplier)
    {
        $sql = "SELECT
					PC.productid,
					PC.numberofitems as qty,
					PC.productattributesetid,
					C.value
            	FROM productcombination PC
				LEFT JOIN combination C ON C.idcombination = PC.combinationid
				WHERE PC.combinationid = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $product = App::getModel('product')->getProductAndAttributesById($rs['productid']);
            $qty = $rs['qty'] * $multiplier;
            $attr = $rs['productattributesetid'];
            $idproduct = $rs['productid'];
            $internalid = sha1($idproduct . $id);
            
            if (isset($product['attributes']) && (int) $rs['productattributesetid'] > 0){
                foreach ($product['attributes'] as $key => $variant){
                    if ($variant['idproductattributeset'] == $attr){
                        $priceWithoutVat = $variant['attributeprice'] * (1 - ($rs['value'] / 100));
                        $price = $variant['price'] * (1 - ($rs['value'] / 100));
                        $weight = $variant['weight'];
                        $ean = $variant['symbol'];
                        $photo = ((int) $variant['photoid'] > 0) ? $variant['photoid'] : $product['mainphotoid'];
                        $stock = $variant['stock'];
                        $pricebeforepromotionnetto = $variant['attributepricenettobeforepromotion'];
                        $pricebeforepromotiongross = $variant['attributepricegrossbeforepromotion'];
                        break;
                    }
                }
                
                if (! (isset($this->Cart[$internalid]))){
                    $this->Cart[$internalid] = Array(
                        'idproduct' => $product['idproduct'],
                        'combinationid' => $id,
                        'maxcombinations' => $stock / $qty
                    );
                }
                
                $qtyprice = $price * $qty;
                $weighttotal = $weight * $qty;
                
                $this->Cart[$internalid]['attributes'][$attr] = Array(
                    'attr' => $attr,
                    'idproduct' => $product['idproduct'],
                    'seo' => $product['seo'],
                    'ean' => $ean,
                    'name' => $product['productname'],
                    'mainphotoid' => $photo,
                    'stock' => $stock,
                    'unit' => $product['unit'],
                    'packagesize' => $product['packagesize'],
                    'trackstock' => $product['trackstock'],
                    'newprice' => $price,
                    'pricewithoutvat' => $priceWithoutVat,
                    'pricebeforepromotionnetto' => $pricebeforepromotionnetto,
                    'pricebeforepromotiongross' => $pricebeforepromotiongross,
                    'qty' => $qty,
                    'qtyprice' => $qtyprice,
                    'weight' => $weight,
                    'weighttotal' => $weighttotal,
                    'vat' => $product['vatvalue']
                );
                
                $this->getProductFeaturesForCombination($idproduct, $attr, $internalid);
            }
            
            if (empty($product['attributes'])){
                $promotionPriceNetto = $product['pricewithoutvat'] * (1 - ($rs['value'] / 100));
                $promotionPriceGross = $product['price'] * (1 - ($rs['value'] / 100));
                
                $price = $promotionPriceGross;
                $priceWithoutVat = $promotionPriceNetto;
                $pricebeforepromotionnetto = $product['pricewithoutvat'];
                $pricebeforepromotiongross = $product['price'];
                
                $qtyprice = $qty * $price;
                $weighttotal = $qty * $product['weight'];
                
                $this->Cart[$internalid] = Array(
                    'idproduct' => $idproduct,
                    'currentstock' => $product['stock'],
                    'combinationid' => $id,
                    'ean' => $product['ean'],
                    'seo' => $product['seo'],
                    'name' => $product['productname'],
                    'mainphotoid' => $product['mainphotoid'],
                    'shortdescription' => $product['shortdescription'],
                    'stock' => $product['stock'],
                    'trackstock' => $product['trackstock'],
                    'newprice' => $price,
                    'pricewithoutvat' => $priceWithoutVat,
                    'pricebeforepromotionnetto' => $pricebeforepromotionnetto,
                    'pricebeforepromotiongross' => $pricebeforepromotiongross,
                    'unit' => $product['unit'],
                    'packagesize' => $product['packagesize'],
                    'qty' => $qty,
                    'qtyprice' => $qtyprice,
                    'weight' => $product['weight'],
                    'weighttotal' => $weighttotal,
                    'vat' => $product['vatvalue'],
                    'standard' => 1,
                    'attributes' => isset($this->Cart[$idproduct]['attributes']) ? $this->Cart[$idproduct]['attributes'] : null
                );
            }
        }
        
        $this->updateSession();
    }

    public function changeCombinationQty ($id, $qty)
    {
        foreach ($this->Cart as $index => $product){
            if (isset($product['combinationid']) && $product['combinationid'] == $id){
                unset($this->Cart[$index]);
            }
        }
        $this->addCombinationToCart($id, $qty);
    }

    public function doChangeAJAXCombinationQty ($id, $qty)
    {
        $objResponse = new xajaxResponse();
        $cartCombinations = $this->getProductCartCombinations();
        $currentQty = $cartCombinations[$id]['currentqty'];
        $combination = App::getModel('productcombination')->getCombinationById($id, $qty);
        if (! empty($combination)){
            $this->changeCombinationQty($id, $qty);
            $cartCombinations[$id] = $combination;
            $cartCombinations[$id]['currentqty'] = $qty;
            $this->setProductCartCombinations($cartCombinations);
        }
        else{
            $objResponse->script('GError("Za mały stan magazynowy", "Nie wszystkie produkty w zestawie posiadają wystarczający stan magazynowy. Nie można dodać zestawu do koszyka.")');
        }
        $objResponse->clear("topBasket", "innerHTML");
        $objResponse->append("topBasket", "innerHTML", $this->getCartPreviewTemplate());
        $objResponse->clear("cart-contents", "innerHTML");
        $objResponse->append("cart-contents", "innerHTML", $this->getCartTableTemplate());
        $objResponse->script("qtySpinner();");
        return $objResponse;
    }

    public function doQuickAddCombinationCart ($id)
    {
        $objResponse = new xajaxResponse();
        
        $cartCombinations = $this->getProductCartCombinations();
        
        if (is_array($cartCombinations) && array_key_exists($id, $cartCombinations)){
            $combination = $cartCombinations[$id];
            $currentQty = $cartCombinations[$id]['currentqty'];
            $combination = App::getModel('productcombination')->getCombinationById($id, $currentQty + 1);
            if (! empty($combination)){
                $this->changeCombinationQty($id, $currentQty + 1);
                $cartCombinations[$id] = $combination;
                $cartCombinations[$id]['currentqty'] = $currentQty + 1;
                $objResponse->clear("basketModal", "innerHTML");
                $this->registry->template->assign('product', Array(
                    'productname' => 'Zestaw produktów'
                ));
                $objResponse->append("basketModal", "innerHTML", $this->registry->template->fetch('basket_modal.tpl'));
                if ($this->registry->loader->getParam('cartredirect') == 0){
                    $objResponse->script("$('#basketModal').modal('show');");
                }
                else{
                    $url = $this->registry->router->generate('frontend.cart', true);
                    $objResponse->script("window.location.href = '{$url}'");
                }
            }
            else{
                $objResponse->script('GError("Za mały stan magazynowy", "Nie wszystkie produkty w zestawie posiadają wystarczający stan magazynowy. Nie można dodać zestawu do koszyka.")');
            }
        }
        else{
            $combination = App::getModel('productcombination')->getCombinationById($id, 1);
            $cartCombinations[$id] = $combination;
            $multiplier = 1;
            $cartCombinations[$id]['currentqty'] = $multiplier;
            $this->addCombinationToCart($id, $multiplier);
            
            $objResponse->clear("basketModal", "innerHTML");
            $this->registry->template->assign('product', Array(
                'productname' => 'Zestaw produktów'
            ));
            $objResponse->append("basketModal", "innerHTML", $this->registry->template->fetch('basket_modal.tpl'));
            
            if ($this->registry->loader->getParam('cartredirect') == 0){
                $objResponse->script("$('#basketModal').modal('show');");
                $objResponse->clear("topBasket", "innerHTML");
                $objResponse->append("topBasket", "innerHTML", $this->getCartPreviewTemplate());
            }
            else{
                $url = $this->registry->router->generate('frontend.cart', true);
                $objResponse->script("window.location.href = '{$url}'");
            }
        }
        
        $this->setProductCartCombinations($cartCombinations);
        
        return $objResponse;
    }

    public function deleteCombinationFromCart ($id)
    {
        $activeCartCombinationsFlag = 0;
        foreach ($this->Cart as $index => $product){
            if (isset($product['combinationid']) && $product['combinationid'] == $id){
                unset($this->Cart[$index]);
            }
            else 
                if (isset($product['combinationid']) && $product['combinationid'] != $id){
                    $activeCartCombinationsFlag = 1;
                }
        }
        
        if ($activeCartCombinationsFlag == 0)
            App::getContainer()->get('session')->setActiveCartCombinations(NULL);
        
        $this->updateSession();
    }

    public function deleteAJAXsCombinationFromCart ($id)
    {
        $objResponse = new xajaxResponse();
        $this->deleteCombinationFromCart($id);
        $objResponse->script('window.location.reload( false )');
        return $objResponse;
    }

    public function cartAddProductWithAttr ($idproduct, $qty, $attr)
    {
        $product = (empty($this->product)) ? App::getModel('product')->getProductAndAttributesById($idproduct) : $this->product;
        
        foreach ($product['attributes'] as $key => $variant){
            if ($variant['idproductattributeset'] == $attr){
                $priceWithoutVat = $variant['attributeprice'];
                $price = $variant['price'];
                $weight = $variant['weight'];
                $ean = $variant['symbol'];
                $photo = ((int) $variant['photoid'] > 0) ? $variant['photoid'] : $product['mainphotoid'];
                $stock = $variant['stock'];
                if ($variant['attributeprice'] > $variant['attributepricenettobeforepromotion']){
                    $pricebeforepromotionnetto = $variant['attributepricenettobeforepromotion'];
                    $pricebeforepromotiongross = $variant['attributepricegrossbeforepromotion'];
                }
                else{
                    $pricebeforepromotionnetto = NULL;
                    $pricebeforepromotiongross = NULL;
                }
                break;
            }
        }
        
        if (! (isset($this->Cart[$idproduct]))){
            $this->Cart[$idproduct] = Array(
                'idproduct' => $product['idproduct']
            );
        }
        $qtyprice = $price * $qty;
        $weighttotal = $weight * $qty;
        
        $this->Cart[$idproduct]['attributes'][$attr] = Array(
            'attr' => $attr,
            'idproduct' => $product['idproduct'],
            'seo' => $product['seo'],
            'ean' => $ean,
            'name' => $product['productname'],
            'delivelercode' => $product['delivelercode'],
            'mainphotoid' => $photo,
            'stock' => $stock,
            'unit' => $product['unit'],
            'packagesize' => $product['packagesize'],
            'trackstock' => $product['trackstock'],
            'newprice' => $price,
            'pricewithoutvat' => $priceWithoutVat,
            'pricebeforepromotionnetto' => $pricebeforepromotionnetto,
            'pricebeforepromotiongross' => $pricebeforepromotiongross,
            'qty' => $qty,
            'qtyprice' => $qtyprice,
            'weight' => $weight,
            'weighttotal' => $weighttotal,
            'vat' => $product['vatvalue']
        );
        
        $this->updateSession();
    }

    public function getProductFeatures ($idproduct, $attr)
    {
        $sql = "SELECT
					PAVS.idproductattributevalueset as idfeature,
					PAVS.attributeproductvalueid as feature,
					AP.name AS groupname,
					APV.name AS attributename
				FROM productattributeset AS PAS
			    LEFT JOIN productattributevalueset AS PAVS ON PAS.idproductattributeset = PAVS.productattributesetid
				LEFT JOIN attributeproductvalue AS APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
			    LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct
			    WHERE PAS.productid= :idproduct	AND PAVS.productattributesetid = :attr";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('idproduct', $idproduct);
        $stmt->bindValue('attr', $attr);
        try{
            $rs = $stmt->execute();
            while ($rs = $stmt->fetch()){
                $this->Cart[$idproduct]['attributes'][$attr]['features'][$rs['idfeature']] = Array(
                    'feature' => $rs['feature'],
                    'group' => $rs['groupname'],
                    'attributename' => $rs['attributename']
                );
            }
            $this->updateSession();
        }
        catch (Exception $e){
            throw new Exception('Error while doing sql query- product features (cartModel).');
        }
    }

    public function getProductFeaturesForCombination ($idproduct, $attr, $internalid)
    {
        $sql = "SELECT
					PAVS.idproductattributevalueset as idfeature,
					PAVS.attributeproductvalueid as feature,
					AP.name AS groupname,
					APV.name AS attributename
				FROM productattributeset AS PAS
			    LEFT JOIN productattributevalueset AS PAVS ON PAS.idproductattributeset = PAVS.productattributesetid
				LEFT JOIN attributeproductvalue AS APV ON PAVS.attributeproductvalueid = APV.idattributeproductvalue
			    LEFT JOIN attributeproduct AS AP ON APV.attributeproductid = AP.idattributeproduct
			    WHERE PAS.productid= :idproduct	AND PAVS.productattributesetid = :attr";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('idproduct', $idproduct);
        $stmt->bindValue('attr', $attr);
        try{
            $rs = $stmt->execute();
            while ($rs = $stmt->fetch()){
                $this->Cart[$internalid]['attributes'][$attr]['features'][$rs['idfeature']] = Array(
                    'feature' => $rs['feature'],
                    'group' => $rs['groupname'],
                    'attributename' => $rs['attributename']
                );
            }
            $this->updateSession();
        }
        catch (Exception $e){
            throw new Exception('Error while doing sql query- product features (cartModel).');
        }
    }

    public function setGlobalPrice ()
    {
        $price = 0.00;
        $priceWithoutVat = 0.00;
        foreach ($this->Cart as $key => $product){
            if ((! isset($product['attributes']) || $product['attributes'] == NULL)){
                $price += $product['newprice'] * $product['qty'];
            }
            else{
                if (isset($product['standard'])){
                    $price += $product['newprice'] * $product['qty'];
                    foreach ($product['attributes'] as $attrtab){
                        $price += $attrtab['newprice'] * $attrtab['qty'];
                    }
                }
                else{
                    foreach ($product['attributes'] as $attrtab){
                        $price += $attrtab['newprice'] * $attrtab['qty'];
                    }
                }
            }
        }
        $this->globalPrice = $price;
    }

    public function setGlobalWeight ()
    {
        $weight = 0.00;
        foreach ($this->Cart as $product){
            if ((! isset($product['attributes']) || $product['attributes'] == NULL)){
                $weight += $product['weight'] * $product['qty'];
            }
            else{
                if (isset($product['standard'])){
                    $weight += $product['weight'] * $product['qty'];
                    foreach ($product['attributes'] as $attrtab){
                        $weight += $attrtab['weight'] * $attrtab['qty'];
                    }
                }
                else{
                    foreach ($product['attributes'] as $attrtab){
                        $weight += $attrtab['weight'] * $attrtab['qty'];
                    }
                }
            }
        }
        $this->globalWeight = $weight;
    }

    public function setCartForDelivery ()
    {
        $weight = 0.00;
        $price = 0.00;
        $priceWithoutVat = 0.00;
        $shippingCost = 0.00;
        foreach ($this->Cart as $product){
            if ((! isset($product['attributes']) || $product['attributes'] == NULL)){
                $weight += $product['weight'] * $product['qty'];
                $price += $product['newprice'] * $product['qty'];
            }
            else{
                if (isset($product['standard'])){
                    $weight += $product['weight'] * $product['qty'];
                    $price += $product['newprice'] * $product['qty'];
                    
                    foreach ($product['attributes'] as $attrtab){
                        $weight += $attrtab['weight'] * $attrtab['qty'];
                        $price += $attrtab['newprice'] * $attrtab['qty'];
                    }
                }
                else{
                    foreach ($product['attributes'] as $attrtab){
                        $weight += $attrtab['weight'] * $attrtab['qty'];
                        $price += $attrtab['newprice'] * $attrtab['qty'];
                    }
                }
            }
        }
        $Data = Array(
            'weight' => $weight,
            'price' => $price
        );
        App::getContainer()->get('session')->setActiveCartForDelivery($Data);
    }

    public function setGlobalPriceWithoutVat ()
    {
        $priceWithoutVat = 0.00;
        foreach ($this->Cart as $product){
            if (! isset($product['attributes']) || $product['attributes'] == NULL){
                $priceWithoutVat += $product['pricewithoutvat'] * $product['qty'];
            }
            else{
                if (isset($product['standard'])){
                    $priceWithoutVat += $product['pricewithoutvat'] * $product['qty'];
                    foreach ($product['attributes'] as $attrtab){
                        $priceWithoutVat += $attrtab['pricewithoutvat'] * $attrtab['qty'];
                    }
                }
                else{
                    foreach ($product['attributes'] as $attrtab){
                        $priceWithoutVat += $attrtab['pricewithoutvat'] * $attrtab['qty'];
                    }
                }
            }
        }
        $this->globalPriceWithoutVat = $priceWithoutVat;
    }

    public function getGlobalPrice ()
    {
        return $this->globalPrice;
    }

    public function getGlobalWeight ()
    {
        return $this->globalWeight;
    }

    public function getGlobalPriceWithoutVat ()
    {
        return $this->globalPriceWithoutVat;
    }

    public function getShortCartList ()
    {
        return $this->Cart;
    }

    public function getCount ()
    {
        return $this->count;
    }

    public function updateSession ()
    {
        $this->setGlobalPrice();
        $this->setGlobalPriceWithoutVat();
        $this->setGlobalWeight();
        $this->setCartForDelivery();
        
        App::getContainer()->get('session')->setActiveCart($this->Cart);
        App::getContainer()->get('session')->setActiveGlobalPrice($this->globalPrice);
        App::getContainer()->get('session')->setActiveGlobalWeight($this->globalWeight);
        App::getContainer()->get('session')->setActiveGlobalPriceWithoutVat($this->globalPriceWithoutVat);
        App::getContainer()->get('session')->setActiveDispatchmethodChecked(0);
        App::getContainer()->get('session')->setActiveglobalPriceWithDispatchmethod($this->globalPrice);
        App::getContainer()->get('session')->setActiveglobalPriceWithDispatchmethodNetto($this->globalPriceWithoutVat);
        App::getContainer()->get('session')->setActivePaymentMethodChecked(0);
        App::getContainer()->get('session')->unsetActiveClientOrder();
    }

    public function getProductAllCount ()
    {
        $count = 0;
        foreach ($this->Cart as $product){
            if (isset($product['standard']) && $product['standard'] > 0){
                $count += $product['qty'];
                if (isset($product['attributes']) && $product['attributes'] != NULL){
                    foreach ($product['attributes'] as $attrtab){
                        $count += $attrtab['qty'];
                    }
                }
            }
            else{
                if (isset($product['attributes']) && $product['attributes'] != NULL){
                    foreach ($product['attributes'] as $attrtab){
                        $count += $attrtab['qty'];
                    }
                }
            }
        }
        return $count;
    }

    public function getProductIds ()
    {
        $Data = Array(
            0
        );
        foreach ($this->Cart as $product){
            if (isset($product['standard']) && $product['standard'] > 0){
                $Data[] = $product['idproduct'];
                if (isset($product['attributes']) && $product['attributes'] != NULL){
                    foreach ($product['attributes'] as $attrtab){
                        $Data[] = $attrtab['idproduct'];
                    }
                }
            }
            else{
                if (isset($product['attributes']) && $product['attributes'] != NULL){
                    foreach ($product['attributes'] as $attrtab){
                        $Data[] = $attrtab['idproduct'];
                    }
                }
            }
        }
        return $Data;
    }

    public function getProductCartPhotos (&$productCart)
    {
        if (! is_array($productCart)){
            throw new FrontendException('Wrong array given.');
        }
        foreach ($productCart as $index => $key){
            if ((isset($key['mainphotoid']) && $key['mainphotoid'] > 0)){
                $productCart[$index]['smallphoto'] = App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($key['mainphotoid']), App::getURLAdress());
            }
            if (isset($key['attributes']) && $key['attributes'] != NULL){
                foreach ($key['attributes'] as $attrindex => $attrkey){
                    if ($attrkey['mainphotoid'] > 0){
                        $productCart[$index]['attributes'][$attrindex]['smallphoto'] = App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($attrkey['mainphotoid']), App::getURLAdress());
                    }
                }
            }
        }
        return $productCart;
    }

    public function checkRulesCart ()
    {
        $Data = Array();
        $condition = Array();
        if ($this->globalPriceWithoutVat > 0){
            $clientGroupId = App::getContainer()->get('session')->getActiveClientGroupid();
            if ($clientGroupId > 0){
                $sql = "SELECT
							RCCG.rulescartid,
							RCR.ruleid,
							RCR.pkid,
							RCR.pricefrom,
							RCR.priceto,
							RCCG.suffixtypeid,
							RCCG.discount,
							RCCG.freeshipping,
							S.symbol,
							RCCG.clientgroupid,
							RCT.name,
							RCT.description
						FROM rulescartclientgroup RCCG
							LEFT JOIN rulescart RC ON RCCG.rulescartid = RC.idrulescart
							LEFT JOIN rulescarttranslation RCT ON RCT.rulescartid = RC.idrulescart AND RCT.languageid = :languageid
							LEFT JOIN rulescartrule RCR ON RCR.rulescartid = RC.idrulescart
							LEFT JOIN rulescartview RCV ON RCV.rulescartid = RC.idrulescart
							LEFT JOIN suffixtype S ON RCCG.suffixtypeid = S.idsuffixtype
						WHERE
							RCV.viewid= :viewid
							AND RCCG.clientgroupid= :clientgroupid
							AND IF(RC.datefrom is not null, (cast(RC.datefrom as date) <= curdate()), 1)
							AND IF(RC.dateto is not null, (cast(RC.dateto as date)>= curdate()),1)
						ORDER BY RCR.rulescartid";
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('clientgroupid', $clientGroupId);
                $stmt->bindValue('viewid', Helper::getViewId());
                $stmt->bindValue('languageid', Helper::getLanguageId());
            }
            else{
                $sql = "SELECT
							RCR.rulescartid,
							RCR.ruleid,
							RCR.pkid,
							RCR.pricefrom,
							RCR.priceto,
							RC.suffixtypeid,
							RC.discount,
							RC.freeshipping,
							S.symbol,
							'clientgroupid'=NULL as clientgroupid,
							RCT.name,
							RCT.description
						FROM  rulescart RC
							LEFT JOIN rulescarttranslation RCT ON RCT.rulescartid = RC.idrulescart AND RCT.languageid = :languageid
							LEFT JOIN rulescartrule RCR ON RCR.rulescartid = RC.idrulescart
							LEFT JOIN rulescartview RCV ON RCV.rulescartid = RC.idrulescart
							LEFT JOIN suffixtype S ON RC.suffixtypeid = S.idsuffixtype
	      				WHERE
	      					RC.discountforall =1
	        				AND RCV.viewid= :viewid
	        				AND IF(RC.datefrom is not null, (cast(RC.datefrom as date) <= curdate()), 1)
							AND IF(RC.dateto is not null, (cast(RC.dateto as date)>= curdate()),1)
						ORDER BY RCR.rulescartid";
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('viewid', Helper::getViewId());
                $stmt->bindValue('languageid', Helper::getLanguageId());
            }
            try{
                $stmt->execute();
                $rs = $stmt->fetch();
                while ($rs = $stmt->fetch()){
                    $rulescartid = $rs['rulescartid'];
                    $ruleid = $rs['ruleid'];
                    $currencySymbol = App::getContainer()->get('session')->getActiveCurrencySymbol();
                    if ($rs['symbol'] == '%'){
                        $Data[$rulescartid]['discount'] = abs($rs['discount'] - 100) . $rs['symbol'];
                        $Data[$rulescartid]['type'] = ($rs['discount'] > 100) ? 1 : 0;
                    }
                    else{
                        $Data[$rulescartid]['discount'] = $rs['symbol'] . $rs['discount'];
                        $Data[$rulescartid]['type'] = ($rs['symbol'] == '+') ? 1 : 0;
                    }
                    $Data[$rulescartid]['freeshipping'] = $rs['freeshipping'];
                    $Data[$rulescartid]['name'] = $rs['name'];
                    $Data[$rulescartid]['description'] = $rs['description'];
                    
                    switch ($ruleid) {
                        case 9: // delivery
                            if (isset($Data[$rulescartid][$ruleid])){
                                $Data[$rulescartid][$ruleid]['condition'] = $Data[$rulescartid][$ruleid]['condition'] . " " . $this->trans('TXT_OR') . " " . $this->getDeliveryToCondition($rs['pkid']);
                                $Data[$rulescartid][$ruleid]['deliveryid'][] = $rs['pkid'];
                            }
                            else{
                                $Data[$rulescartid][$ruleid] = Array(
                                    'is' => 0,
                                    'ruleid' => $ruleid,
                                    'condition' => $this->trans('TXT_DELIVERY_TYPE') . ": " . $this->getDeliveryToCondition($rs['pkid']),
                                    'deliveryid' => array(
                                        $rs['pkid']
                                    )
                                );
                            }
                            break;
                        case 10: // paymentmethod
                            if (isset($Data[$rulescartid][$ruleid])){
                                $Data[$rulescartid][$ruleid]['condition'] = $Data[$rulescartid][$ruleid]['condition'] . " " . $this->trans('TXT_OR') . " " . $this->getPaymentToCondition($rs['pkid']);
                                $Data[$rulescartid][$ruleid]['paymentmethodid'][] = $rs['pkid'];
                            }
                            else{
                                $Data[$rulescartid][$ruleid] = Array(
                                    'is' => 0,
                                    'ruleid' => $ruleid,
                                    'condition' => $this->trans('TXT_PAYMENT_TYPE') . ": " . $this->getPaymentToCondition($rs['pkid']),
                                    'paymentmethodid' => array(
                                        $rs['pkid']
                                    )
                                );
                            }
                            break;
                        case 11: // final cart price
                            if (isset($Data[$rulescartid][$ruleid])){
                                $Data[$rulescartid][$ruleid]['condition'] = $Data[$rulescartid][$ruleid]['condition'] . " " . $this->trans('TXT_OR') . " " . $rs['pricefrom'];
                                if ($rs['pricefrom'] < $Data[$rulescartid][$ruleid]['min_cart_price']){
                                    $Data[$rulescartid][$ruleid]['min_cart_price'] = $rs['pricefrom'];
                                }
                            }
                            else{
                                $Data[$rulescartid][$ruleid] = Array(
                                    'is' => 0,
                                    'ruleid' => $ruleid,
                                    'condition' => $this->trans('TXT_CART_VALUE_AMOUNT_EXCEED') . ": " . $rs['pricefrom'] . $currencySymbol,
                                    'min_cart_price' => $rs['pricefrom']
                                );
                            }
                            break;
                        case 12: // final cart price
                            if (isset($Data[$rulescartid][$ruleid])){
                                $Data[$rulescartid][$ruleid]['condition'] = $Data[$rulescartid][$ruleid]['condition'] . " " . $this->trans('TXT_OR') . " " . $rs['priceto'] . $currencySymbol;
                                if ($rs['priceto'] > $Data[$rulescartid][$ruleid]['max_cart_price']){
                                    $Data[$rulescartid][$ruleid]['max_cart_price'] = $rs['priceto'];
                                }
                            }
                            else{
                                $Data[$rulescartid][$ruleid] = Array(
                                    'is' => 0,
                                    'ruleid' => $ruleid,
                                    'condition' => $this->trans('TXT_CART_VALUE_NOT_GREATER_THAN') . ": " . $rs['priceto'] . $currencySymbol,
                                    'max_cart_price' => $rs['priceto']
                                );
                            }
                            break;
                        case 13: // final cart price with dispatch method
                            if (isset($Data[$rulescartid][$ruleid])){
                                $Data[$rulescartid][$ruleid]['condition'] = $Data[$rulescartid][$ruleid]['condition'] . " " . $this->trans('TXT_OR') . " " . $rs['pricefrom'] . $currencySymbol;
                                if ($rs['pricefrom'] < $Data[$rulescartid][$ruleid]['min_cart_dispatch_price']){
                                    $Data[$rulescartid][$ruleid]['min_cart_dispatch_price'] = $rs['pricefrom'];
                                }
                            }
                            else{
                                $Data[$rulescartid][$ruleid] = Array(
                                    'is' => 0,
                                    'ruleid' => $ruleid,
                                    'condition' => $this->trans('TXT_CART_DELIVERY_VALUE_AMOUNT') . ": " . $rs['pricefrom'] . $currencySymbol,
                                    'min_cart_dispatch_price' => $rs['pricefrom']
                                );
                            }
                            break;
                        case 14: // final cart price with dispatch method
                            if (isset($Data[$rulescartid][$ruleid])){
                                $Data[$rulescartid][$ruleid]['condition'] = $Data[$rulescartid][$ruleid]['condition'] . " " . $this->trans('TXT_OR') . " " . $rs['priceto'] . $currencySymbol;
                                if ($rs['priceto'] > $Data[$rulescartid][$ruleid]['max_cart_dispatch_price']){
                                    $Data[$rulescartid][$ruleid]['max_cart_dispatch_price'] = $rs['priceto'];
                                }
                            }
                            else{
                                $Data[$rulescartid][$ruleid] = Array(
                                    'is' => 0,
                                    'ruleid' => $ruleid,
                                    'condition' => $this->trans('TXT_CART_DELIVERY_VALUE_NOT_GREATER_THAN') . ": " . $rs['priceto'] . App::getContainer()->get('session')->getActiveCurrencySymbol(),
                                    'max_cart_dispatch_price' => $rs['priceto']
                                );
                            }
                            break;
                    }
                }
                
                $paymentmethodid = App::getContainer()->get('session')->getActivePaymentMethodChecked();
                if (is_array($paymentmethodid)){
                    $paymentmethodid = $paymentmethodid['idpaymentmethod'];
                }
                
                $dispatchmethodid = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
                if (is_array($dispatchmethodid)){
                    $dispatchmethodid = $dispatchmethodid['dispatchmethodid'];
                }
                
                $globalprice = round(App::getContainer()->get('session')->getActiveGlobalPrice(), 2);
                $globaldispatchprice = round(App::getContainer()->get('session')->getActiveglobalPriceWithDispatchmethod(), 2);
                
                if (count($Data) > 0){
                    foreach ($Data as $rulescart => $rules){
                        
                        foreach ($rules as $rule){
                            if (is_array($rule) && $rule['is'] == 0){
                                
                                $r = FALSE;
                                
                                switch ($rule['ruleid']) {
                                    case 9:
                                        if (empty($rule['deliveryid'])){
                                            $r = TRUE;
                                        }
                                        else{
                                            $r = in_array($dispatchmethodid, $rule['deliveryid']);
                                        }
                                        break;
                                    case 10:
                                        if (empty($rule['paymentmethodid'])){
                                            $r = TRUE;
                                        }
                                        else{
                                            $r = in_array($paymentmethodid, $rule['paymentmethodid']);
                                        }
                                        break;
                                    case 11:
                                        if (! empty($rule['min_cart_price'])){
                                            $r = $globalprice >= $rule['min_cart_price'];
                                        }
                                        else{
                                            $r = TRUE;
                                        }
                                        break;
                                    case 12:
                                        if ($rule['max_cart_price']){
                                            $r = $globalprice <= $rule['max_cart_price'];
                                        }
                                        else{
                                            $r = TRUE;
                                        }
                                        break;
                                    case 13:
                                        if ($rule['min_cart_dispatch_price']){
                                            $r = $globaldispatchprice >= $rule['min_cart_dispatch_price'];
                                        }
                                        else{
                                            $r = TRUE;
                                        }
                                        break;
                                    case 14:
                                        if ($rule['max_cart_dispatch_price']){
                                            $r = $globaldispatchprice <= $rule['max_cart_dispatch_price'];
                                        }
                                        else{
                                            $r = TRUE;
                                        }
                                        break;
                                }
                                
                                if (! $r){
                                    $condition[$rulescart]['conditions'][$rule['ruleid']] = $rule['condition'];
                                }
                            }
                        }
                        
                        if (empty($condition[$rulescart]['conditions'])){
                            unset($condition[$rulescart]);
                            continue;
                        }
                        
                        $condition[$rulescart]['discount'] = $rules['discount'];
                        $condition[$rulescart]['freeshipping'] = $rules['freeshipping'];
                        $condition[$rulescart]['name'] = $rules['name'];
                        $condition[$rulescart]['description'] = $rules['description'];
                        $condition[$rulescart]['type'] = $rules['type'];
                    }
                }
                else{
                    $condition = 0;
                }
            }
            catch (Exception $e){
                throw new FrontendException($this->trans('ERR_RULES_CART'));
            }
        }
        else{
            $condition = 0;
        }
        return $condition;
    }

    public function getDeliveryToCondition ($iddispatchmethod)
    {
        $dispatchmethodname = '';
        $sql = "SELECT
					DM.name
				FROM dispatchmethod DM
				WHERE DM.iddispatchmethod = :iddispatchmethod";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('iddispatchmethod', $iddispatchmethod);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $dispatchmethodname = $rs['name'];
            }
        }
        catch (Exception $e){
            throw new FrontendException($this->trans('ERR_DELIVERER_CHECK'));
        }
        return $dispatchmethodname;
    }

    public function getPaymentToCondition ($idpaymentmethod)
    {
        $paymentname = '';
        $sql = "SELECT
					PM.name as paymentname
				FROM paymentmethod PM
				WHERE PM.idpaymentmethod = :idpaymentmethod";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('idpaymentmethod', $idpaymentmethod);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $paymentname = $rs['paymentname'];
            }
        }
        catch (Exception $e){
            throw new FrontendException($this->trans('ERR_PAYMENT_CHECK'));
        }
        return $paymentname;
    }

    public function setTempCartAfterCurrencyChange ()
    {
        $cart = App::getContainer()->get('session')->getActiveCart();
        App::getContainer()->get('session')->setActiveCart(NULL);
        if (is_array($cart)){
            foreach ($cart as $product){
                $productid = $product['idproduct'];
                if ($productid > 0){
                    if (isset($product['standard']) && $product['standard'] == 1){
                        $this->cartAddStandardProduct($productid, $product['qty']);
                    }
                    if (isset($product['attributes']) || ! empty($product['attributes'])){
                        foreach ($product['attributes'] as $attributes){
                            $attr = $attributes['attr'];
                            $this->cartAddProductWithAttr($productid, $attributes['qty'], $attributes['attr']);
                        }
                    }
                }
            }
        }
    }

    public function getBasketModalTemplate ()
    {
        $recommendations = App::getModel('recommendations')->getPromotions(3, $this->product['idproduct']);
        App::getModel('product/product')->getPhotos($this->product);
        $this->registry->template->assign('product', $this->product);
        $this->registry->template->assign('recommendations', $recommendations);
        return $this->registry->template->fetch('basket_modal.tpl');
    }

    public function getProductModalTemplate ($product)
    {
        App::getModel('product/product')->getPhotos($product);
        $selectAttributes = App::getModel('product/product')->getProductAttributeGroups($product);
        $attset = App::getModel('product/product')->getProductVariant($product);
        
        foreach ($selectAttributes as $key => $val){
            natsort($val['attributes']);
            $selectAttributes[$key]['attributes'] = $val['attributes'];
        }
        
        $Data = Array();
        foreach ($attset as $group => $data){
            $Data[implode(',', array_keys($data['variant']))] = Array(
                'setid' => $group,
                'stock' => $data['stock'],
                'sellprice' => $this->registry->core->processPrice($data['sellprice']),
                'sellpricenetto' => $this->registry->core->processPrice($data['sellpricenetto']),
                'sellpriceold' => $this->registry->core->processPrice($data['attributepricegrossbeforepromotion']),
                'sellpricenettoold' => $this->registry->core->processPrice($data['attributepricenettobeforepromotion']),
                'availablity' => $data['availablity'],
                'photos' => $data['photos']
            );
        }
        
        $this->registry->template->assign('product', $product);
        $this->registry->template->assign('attributes', $selectAttributes);
        $this->registry->template->assign('variants', json_encode($Data));
        $this->registry->template->assign('attset', $attset);
        return $this->registry->template->fetch('product_modal.tpl');
    }

    public function getCartPreviewTemplate ()
    {
        $productCart = $this->getShortCartList();
        $productCart = $this->getProductCartPhotos($productCart);
        
        $sql = 'SELECT
					((DP.from * CR.exchangerate) - :globalprice) AS required
				FROM dispatchmethodprice DP
				LEFT JOIN dispatchmethodview DV ON DP.dispatchmethodid = DV.dispatchmethodid
				LEFT JOIN dispatchmethod D ON D.iddispatchmethod = DP.dispatchmethodid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = D.currencyid AND CR.currencyto = :currencyto
				WHERE DP.dispatchmethodcost = 0 AND DV.viewid = :viewid AND D.type = 1 AND ((DP.to + DP.from) > 0)
				AND FIND_IN_SET(:countryid, D.countryids) > 0
				ORDER BY DP.from ASC LIMIT 1';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('globalprice', $this->getGlobalPrice());
        $stmt->bindValue('countryid', ((int) App::getContainer()->get('session')->getActiveDeliveryCountry() == 0) ? $this->registry->loader->getParam('countryid') : App::getContainer()->get('session')->getActiveDeliveryCountry());
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $this->registry->template->assign('freeshipping', $rs['required']);
        }
        else{
            $rs = $this->getWeightFreedelivery();
            if ($rs['issetFreedelivery'] > 0)
                $this->registry->template->assign('freeshipping', $rs['required']);
        }
        $this->registry->template->assign('count', $this->getProductAllCount());
        $this->registry->template->assign('globalPrice', App::getContainer()->get('session')->getActiveglobalPriceWithDispatchmethod());
        $this->registry->template->assign('productCart', $productCart);
        $this->registry->template->assign('productCartCombinations', $this->getProductCartCombinations());
        return $this->registry->template->fetch('cart_preview.tpl');
    }

    public function getMinimumOrderValue ()
    {
        $sql = 'SELECT
					ROUND((V.minimumordervalue * CR.exchangerate) - :globalprice, 2) AS required
				FROM view V
				LEFT JOIN currencyrates CR ON CR.currencyfrom = V.currencyid AND CR.currencyto = :currencyto
				WHERE V.idview = :viewid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('globalprice', $this->getGlobalPrice());
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['required'];
        }
        return 0;
    }

    public function getWeightFreedelivery ()
    {
        $sql = 'SELECT
					(SELECT COUNT(*) AS isFreeDelivery FROM dispatchmethod WHERE freedelivery > 0 AND type = 2) AS issetFreedelivery,
					((D.freedelivery * CR.exchangerate) - :globalprice) AS required
				FROM dispatchmethodprice DP
				LEFT JOIN dispatchmethodview DV ON DP.dispatchmethodid = DV.dispatchmethodid
				LEFT JOIN dispatchmethod D ON D.iddispatchmethod = DP.dispatchmethodid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = D.currencyid AND CR.currencyto = :currencyto
				WHERE D.freedelivery > 0 AND DV.viewid = :viewid AND D.type = 2
				ORDER BY D.freedelivery ASC LIMIT 1';
        
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('globalprice', $this->getGlobalPrice());
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }
}
