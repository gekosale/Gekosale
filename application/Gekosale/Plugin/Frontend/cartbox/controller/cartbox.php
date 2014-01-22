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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: cartbox.php 612 2011-11-28 20:02:10Z gekosale $
 */
namespace Gekosale\Plugin;
use FormEngine;
use xajaxResponse;

class CartBoxController extends Component\Controller\Box
{

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $this->clientModel = App::getModel('client');
        $this->cartModel = App::getModel('cart');
        $this->paymentModel = App::getModel('payment');
        $this->deliveryModel = App::getModel('delivery');
        $this->dispatchMethods = $this->deliveryModel->getDispatchmethodPrice();
    }

    public function index ()
    {
        $this->registry->xajax->registerFunction(array(
            'setDispatchmethodChecked',
            $this->deliveryModel,
            'setAJAXDispatchmethodChecked'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'setPeymentChecked',
            $this->paymentModel,
            'setAJAXPaymentMethodChecked'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'changeQuantity',
            $this->cartModel,
            'changeQuantity'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'changeCombinationQuantity',
            $this->cartModel,
            'doChangeAJAXCombinationQty'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'useCoupon',
            App::getModel('coupons'),
            'useCoupon'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'addGiftWrap',
            App::getModel('giftwrap'),
            'addGiftWrap'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'deleteGiftWrap',
            App::getModel('giftwrap'),
            'deleteGiftWrap'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'selectDeliveryOption',
            App::getModel('delivery'),
            'selectDeliveryOption'
        ));
        
        $globalprice = $this->cartModel->getGlobalPrice();
        
        $checkRulesCart = App::getModel('cart')->checkRulesCart();
        if (is_array($checkRulesCart) && count($checkRulesCart) > 0){
            $this->registry->template->assign('checkRulesCart', $checkRulesCart);
        }
        
        $dispatchMethodChecked = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
        
        if ($dispatchMethodChecked == NULL || (isset($dispatchMethodChecked['dispatchmethodid']) && ! in_array($dispatchMethodChecked['dispatchmethodid'], array_keys($this->dispatchMethods)))){
            reset($this->dispatchMethods);
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
        
        $minimumordervalue = App::getModel('cart')->getMinimumOrderValue();
        
        $order = App::getModel('finalization')->setClientOrder();
        
        $giftwrap = App::getModel('giftwrap')->getGiftWrap();
        
        $assignData = Array(
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
            'order' => App::getContainer()->get('session')->getActiveClientOrder(),
            'giftwrap' => $giftwrap
        );
        
        foreach ($assignData as $key => $assign){
            $this->registry->template->assign($key, $assign);
        }
        
        return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
    }
}
