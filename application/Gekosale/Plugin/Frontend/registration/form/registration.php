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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale\Plugin;

use SimpleForm;

class RegistrationForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new SimpleForm\Form(Array(
			'name' => 'registration',
			'action' => '',
			'method' => 'post'
		));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'phone',
			'label' => $this->trans('TXT_PHONE'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PHONE'))
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		$newPassword = $form->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'password',
			'label' => $this->trans('TXT_PASSWORD'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PASSWORD')),
				new SimpleForm\Rules\MinLength($this->trans('ERR_PASSWORD_NEW_INVALID'), 6)
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'confirmpassword',
			'label' => $this->trans('TXT_PASSWORD_REPEAT'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_CONFIRM_PASSWORD')),
				new SimpleForm\Rules\Compare($this->trans('ERR_PASSWORDS_NOT_COMPATIBILE'), $newPassword)
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\Checkbox(Array(
			'name' => 'confirmterms',
			'label' => sprintf($this->trans('TXT_ACCEPT_TERMS_AND_POLICY_OF_PRIVATE'), App::getModel('staticcontent')->getConditionsLink(), App::getContainer()->get('session')->getActiveShopName()),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_TERMS_NOT_AGREED'))
			),
			'default' => 0
		)));
		
		$form->AddChild(new SimpleForm\Elements\Checkbox(Array(
			'name' => 'newsletter',
			'label' => $this->trans('TXT_NEWSLETTER_SIGNUP'),
			'default' => 0
		)));
		
		return $form;
	}
}