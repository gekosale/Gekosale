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
namespace Gekosale;

use SimpleForm;
use xajaxResponse;

class FinalizationBoxController extends Component\Controller\Box
{

	public function index ()
	{
		$clientorder = App::getModel('finalization')->setClientOrder();
		
		if (App::getModel('cart')->getMinimumOrderValue() > 0 || empty($clientorder['cart'])){
			App::redirectUrl($this->registry->router->generate('frontend.cart', true));
		}
		
		$this->registry->xajax->registerFunction(array(
			'saveOrder',
			App::getModel('finalization'),
			'saveOrder'
		));
		
		$formContact = new SimpleForm\Form(Array(
			'name' => 'contactForm',
			'action' => '',
			'method' => 'post'
		));
		
		$formContact->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'phone',
			'label' => $this->trans('TXT_PHONE')
		)));
		
		$formContact->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'phone2',
			'label' => $this->trans('TXT_ADDITIONAL_PHONE')
		)));		
		
		$formContact->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		$clientContact = App::getContainer()->get('session')->getActiveOrderContactData();
		
		$formContact->Populate(Array(
			'phone' => $clientContact['phone'],
			'phone2' => $clientContact['phone2'],
			'email' => $clientContact['email']
		));
		
		if ($formContact->Validate()){
			$formData = $formContact->getSubmitValues();
			App::getContainer()->get('session')->setActiveOrderContactData($formData);
			App::redirectUrl($this->registry->router->generate('frontend.finalization', true));
		}
		
		$formBilling = new SimpleForm\Form(Array(
			'name' => 'billingForm',
			'action' => '',
			'method' => 'post'
		));
		
		$clientType = $formBilling->AddChild(new SimpleForm\Elements\Radio(Array(
			'name' => 'clienttype',
			'label' => $this->trans('TXT_CLIENT_TYPE'),
			'options' => Array(
				'1' => $this->trans('TXT_INDIVIDUAL_CLIENT'),
				'2' => $this->trans('TXT_COMPANY_CLIENT')
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'companyname',
			'label' => $this->trans('TXT_COMPANYNAME'),
			'rules' => Array(
				new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_COMPANYNAME'), $clientType, new SimpleForm\Conditions\Equals('2'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'nip',
			'label' => $this->trans('TXT_NIP'),
			'rules' => Array(
				new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_NIP'), $clientType, new SimpleForm\Conditions\Equals('2'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'street',
			'label' => $this->trans('TXT_STREET'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_STREET'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'streetno',
			'label' => $this->trans('TXT_STREETNO'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_STREETNO'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'placeno',
			'label' => $this->trans('TXT_PLACENO')
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'placename',
			'label' => $this->trans('TXT_PLACE'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PLACE'))
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'postcode',
			'label' => $this->trans('TXT_POSTCODE'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_POSTCODE')),
				new SimpleForm\Rules\Format($this->trans('ERR_WRONG_FORMAT_POSTCODE'), '/^\d{2}-\d{3}?$/')
			)
		)));
		
		$formBilling->AddChild(new SimpleForm\Elements\Select(Array(
			'name' => 'countryid',
			'label' => $this->trans('TXT_NAME_OF_COUNTRY'),
			'options' => App::getModel('lists')->getCountryForSelect(),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_NAME_OF_COUNTRY'))
			)
		)));
		
		$clientBillingAddress = App::getContainer()->get('session')->getActiveOrderClientAddress();
		
		$formBilling->Populate(Array(
			'clienttype' => $clientBillingAddress['clienttype'],
			'firstname' => $clientBillingAddress['firstname'],
			'surname' => $clientBillingAddress['surname'],
			'companyname' => $clientBillingAddress['companyname'],
			'nip' => $clientBillingAddress['nip'],
			'street' => $clientBillingAddress['street'],
			'streetno' => $clientBillingAddress['streetno'],
			'postcode' => $clientBillingAddress['postcode'],
			'placename' => $clientBillingAddress['placename'],
			'placeno' => $clientBillingAddress['placeno'],
			'countryid' => $clientBillingAddress['countryid']
		));
		
		if ($formBilling->Validate()){
			$formData = $formBilling->getSubmitValues();
			if ($formData['clienttype'] != 2){
				$formData['companyname'] = '';
				$formData['nip'] = '';
			}
			App::getContainer()->get('session')->setActiveOrderClientAddress($formData);
			App::redirectUrl($this->registry->router->generate('frontend.finalization', true));
		}
		
		$formShipping = new SimpleForm\Form(Array(
			'name' => 'shippingForm',
			'action' => '',
			'method' => 'post'
		));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'companyname',
			'label' => $this->trans('TXT_COMPANYNAME')
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'street',
			'label' => $this->trans('TXT_STREET'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_STREET'))
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'streetno',
			'label' => $this->trans('TXT_STREETNO'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_STREETNO'))
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'placeno',
			'label' => $this->trans('TXT_PLACENO')
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'placename',
			'label' => $this->trans('TXT_PLACE'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PLACE'))
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'postcode',
			'label' => $this->trans('TXT_POSTCODE'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_POSTCODE')),
				new SimpleForm\Rules\Format($this->trans('ERR_WRONG_FORMAT_POSTCODE'), '/^\d{2}-\d{3}?$/')
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\Select(Array(
			'name' => 'countryid',
			'label' => $this->trans('TXT_NAME_OF_COUNTRY'),
			'options' => App::getModel('lists')->getCountryForSelect(),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_NAME_OF_COUNTRY'))
			)
		)));
		
		$clientShippingAddress = App::getContainer()->get('session')->getActiveOrderDeliveryAddress();
		
		$formShipping->Populate(Array(
			'firstname' => $clientShippingAddress['firstname'],
			'surname' => $clientShippingAddress['surname'],
			'companyname' => $clientShippingAddress['companyname'],
			'street' => $clientShippingAddress['street'],
			'streetno' => $clientShippingAddress['streetno'],
			'postcode' => $clientShippingAddress['postcode'],
			'placename' => $clientShippingAddress['placename'],
			'placeno' => $clientShippingAddress['placeno'],
			'countryid' => $clientShippingAddress['countryid']
		));
		
		if ($formShipping->Validate()){
			$formData = $formShipping->getSubmitValues();
			App::getContainer()->get('session')->setActiveOrderDeliveryAddress($formData);
			App::redirectUrl($this->registry->router->generate('frontend.finalization', true));
		}
		
		$this->registry->template->assign('formContact', $formContact->getForm());
		$this->registry->template->assign('formBilling', $formBilling->getForm());
		$this->registry->template->assign('formShipping', $formShipping->getForm());
		$this->registry->template->assign('clientOrder', App::getContainer()->get('session')->getActiveClientOrder());
		$this->registry->template->assign('summary', App::getModel('finalization')->getOrderSummary());
		$this->registry->template->assign('coupon', App::getContainer()->get('session')->getActiveCoupon());
		$this->registry->template->assign('couponvalue', App::getContainer()->get('session')->getActiveCouponValue());
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}