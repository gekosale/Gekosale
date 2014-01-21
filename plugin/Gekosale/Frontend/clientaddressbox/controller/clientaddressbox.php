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
 * $Id: clientaddressbox.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;

use SimpleForm;

class ClientAddressBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		if (App::getContainer()->get('session')->getActiveClientid() == NULL){
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
		$this->model = App::getModel('client');
	}

	public function index ()
	{
		$clientBillingAddress = $this->model->getClientAddress(1);
		$clientShippingAddress = $this->model->getClientAddress(0);
		
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
			$this->model->updateClientAddress($formData, 1);
			if($clientShippingAddress['idclientaddress'] == 0){
				$this->model->updateClientAddress($formData, 0);
			}
			App::redirectUrl($this->registry->router->generate('frontend.clientaddress', true));
		}
		
		$formShipping = new SimpleForm\Form(Array(
			'name' => 'shippingForm',
			'action' => '',
			'method' => 'post'
		));
		
		$clientType = $formShipping->AddChild(new SimpleForm\Elements\Radio(Array(
			'name' => 'clienttype',
			'label' => $this->trans('TXT_CLIENT_TYPE'),
			'options' => Array(
				'1' => $this->trans('TXT_INDIVIDUAL_CLIENT'),
				'2' => $this->trans('TXT_COMPANY_CLIENT')
			)
		)));
		
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
			'label' => $this->trans('TXT_COMPANYNAME'),
			'rules' => Array(
				new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_COMPANYNAME'), $clientType, new SimpleForm\Conditions\Equals('2'))
			)
		)));
		
		$formShipping->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'nip',
			'label' => $this->trans('TXT_NIP'),
			'rules' => Array(
				new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_COMPANYNAME'), $clientType, new SimpleForm\Conditions\Equals('2'))
			)
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
		
		$clientShippingAddress = $this->model->getClientAddress(0);
		
		$formShipping->Populate(Array(
			'clienttype' => $clientShippingAddress['clienttype'],
			'firstname' => $clientShippingAddress['firstname'],
			'surname' => $clientShippingAddress['surname'],
			'companyname' => $clientShippingAddress['companyname'],
			'nip' => $clientShippingAddress['nip'],
			'street' => $clientShippingAddress['street'],
			'streetno' => $clientShippingAddress['streetno'],
			'postcode' => $clientShippingAddress['postcode'],
			'placename' => $clientShippingAddress['placename'],
			'placeno' => $clientShippingAddress['placeno'],
			'countryid' => $clientShippingAddress['countryid']
		));
		
		if ($formShipping->Validate()){
			$formData = $formShipping->getSubmitValues();
			$this->model->updateClientAddress($formData, 0);
			App::getContainer()->get('session')->setVolatileMessage("Zapisano zmiany w adresie dostawy.");
			App::redirectUrl($this->registry->router->generate('frontend.clientaddress', true));
		}
		$this->registry->template->assign('clientBillingAddress', $clientBillingAddress);
		$this->registry->template->assign('clientShippingAddress', $clientShippingAddress);
		$this->registry->template->assign('formBilling', $formBilling->getForm());
		$this->registry->template->assign('formShipping', $formShipping->getForm());
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}