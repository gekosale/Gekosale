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
namespace Gekosale;

use SimpleForm;

class ClientLoginForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new SimpleForm\Form(Array(
			'name' => 'login',
			'action' => '',
			'method' => 'post'
		));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'login',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'password',
			'label' => $this->trans('TXT_PASSWORD'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PASSWORD'))
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'autologin',
			'label' => $this->trans('TXT_AUTOLOGIN')
		)));		
		
		return $form;
	}
}