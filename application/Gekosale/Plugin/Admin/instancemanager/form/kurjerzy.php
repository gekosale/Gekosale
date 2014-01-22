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

use FormEngine;

class KurjerzyForm extends Component\Form
{
	protected $populateData;

	public function __construct ($registry)
	{
		parent::__construct($registry);
	}

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'kurjerzy',
			'action' => '',
			'method' => 'post'
		));

		$form->AddChild(new FormEngine\Elements\Constant(Array(
				'name' => 'apikey',
				'label' => 'Klucz (apiKey)'
		)));

		$form->AddChild(new FormEngine\Elements\Constant(Array(
				'name' => 'apipin',
				'label' => 'Pin (apiPin)'
		)));

		$form->AddChild(new FormEngine\Elements\Constant(Array(
				'name' => 'login',
				'label' => $this->trans('TXT_LOG')
		)));

		$form->AddChild(new FormEngine\Elements\Constant(Array(
				'name' => 'password',
				'label' => $this->trans('TXT_PASSWORD')
		)));

		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());

		return $form;
	}
}