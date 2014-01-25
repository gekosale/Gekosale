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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: paymentbox.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Component\Payment\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

class PaymentBoxController  extends Box
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'setPeymentChecked',
			App::getModel('payment'),
			'setAJAXPaymentMethodChecked'
		));
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$clientModel = App::getModel('client');
		$this->registry->template->assign('payments', App::getModel('payment')->getPaymentMethods());
		$this->registry->template->assign('checkedPayment', App::getContainer()->get('session')->getActivePaymentMethodChecked());
		$this->registry->template->assign('priceWithDispatch', App::getContainer()->get('session')->getActiveglobalPriceWithDispatchmethod());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function accept ()
	{
		$clientorder = App::getContainer()->get('session')->getActivePaymentData();
		if (isset($clientorder) && $clientorder != NULL){
			
			$footerJs = '';
			$footerJs .= App::getModel('googleanalytics')->getTransGoogleAnalyticsJs($clientorder);
			$footerJs .= App::getModel('integration/ceneo')->addTransJs($clientorder);
			
			$paymentMethodModel = App::getModel('payment')->getPaymentMethodById($clientorder['orderData']['payment']['idpaymentmethod']);
			$paymentMethodData = App::getModel('payment/' . $paymentMethodModel)->getPaymentData($clientorder);
			$this->registry->template->assign('content', $paymentMethodData);
			$this->registry->template->assign('orderId', $clientorder['orderId']);
			$this->registry->template->assign('orderData', $clientorder['orderData']);
			$this->registry->template->assign('footerJS', $footerJs);
			$this->registry->template->assign('affirmeo', App::getModel('kodyrabatowe')->getAffirmeoTrackBackUrl($clientorder));
			return $this->registry->template->fetch($this->loadTemplate($paymentMethodModel . '.tpl'));
		}
		else{
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
	}

	public function confirm ()
	{
		$clientorder = App::getContainer()->get('session')->getActivePaymentData();
		if (isset($clientorder) && $clientorder != NULL){
			$paymentMethodModel = App::getModel('payment')->getPaymentMethodById($clientorder['orderData']['payment']['idpaymentmethod']);
			$paymentMethodData = App::getModel('payment/' . $paymentMethodModel)->getPaymentData($clientorder);
			$paymentMethodData = App::getModel('payment/' . $paymentMethodModel)->confirmPayment($clientorder, $this->registry->core->getParam());
			$this->registry->template->assign('content', $paymentMethodData);
			$this->registry->template->assign('orderId', $clientorder['orderId']);
			$this->registry->template->assign('orderData', $clientorder['orderData']);
			return $this->registry->template->fetch($this->loadTemplate($paymentMethodModel . '.tpl'));
		}
		else{
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
	}

	public function cancel ()
	{
		$clientorder = App::getContainer()->get('session')->getActivePaymentData();
		if (isset($clientorder) && $clientorder != NULL){
			$paymentMethodModel = App::getModel('payment')->getPaymentMethodById($clientorder['orderData']['payment']['idpaymentmethod']);
			$paymentMethodData = App::getModel('payment/' . $paymentMethodModel)->getPaymentData($clientorder);
			$paymentMethodData = App::getModel('payment/' . $paymentMethodModel)->cancelPayment($clientorder, $this->registry->core->getParam());
			$this->registry->template->assign('content', $paymentMethodData);
			$this->registry->template->assign('orderId', $clientorder['orderId']);
			$this->registry->template->assign('orderData', $clientorder['orderData']);
			return $this->registry->template->fetch($this->loadTemplate($paymentMethodModel . '.tpl'));
		}
		else{
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
	}

	public function getBoxHeading ()
	{
		$clientorder = App::getContainer()->get('session')->getActivePaymentData();
		if ($clientorder != NULL && isset($clientorder['orderData']['payment']['paymentmethodname'])){
			return $clientorder['orderData']['payment']['paymentmethodname'];
		}
	}

}