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

use FormEngine;

class LimitsForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$instance = new Instance();
		
		$limits = $instance->getLimits();
		
		$currentLimits = $instance->getCurrentLimits();
		
		$limitsData = new FormEngine\Elements\Form(Array(
			'name' => 'globalsettings',
			'action' => '',
			'method' => 'post',
			'tabs' => 1
		));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'products',
			'label' => 'Produkty - <span style="font-weight: 400;">łączny limit produktów we wszystkich sklepach</span>',
			'total' => $limits['result']['limits']['products'],
			'completed' => $currentLimits['products']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'categories',
			'label' => 'Kategorie - <span style="font-weight: 400;">łączny limit kategorii we wszystkich sklepach</span>',
			'total' => $limits['result']['limits']['categories'],
			'completed' => $currentLimits['categories']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'orders',
			'label' => 'Zamówienia - <span style="font-weight: 400;">maksymalna ilość zamówień miesięcznie we wszystkich sklepach</span>',
			'total' => $limits['result']['limits']['orders'],
			'completed' => $currentLimits['orders']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'clients',
			'label' => 'Klienci - <span style="font-weight: 400;">maksymalna ilość nowych klientów miesięcznie we wszystkich sklepach</span>',
			'total' => $limits['result']['limits']['clients'],
			'completed' => $currentLimits['clients']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'users',
			'label' => 'Użytkownicy - <span style="font-weight: 400;">maksymalna ilość administratorów we wszystkich sklepach</span>',
			'total' => $limits['result']['limits']['users'],
			'completed' => $currentLimits['users']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'views',
			'label' => 'Sklepy - <span style="font-weight: 400;">maksymalna ilość sklepów w ramach wykupionego abonamentu</span>',
			'total' => $limits['result']['limits']['views'],
			'completed' => $currentLimits['views']
		)));
		
		$limitsData->AddFilter(new FormEngine\Filters\NoCode());
		$limitsData->AddFilter(new FormEngine\Filters\Trim());
		$limitsData->AddFilter(new FormEngine\Filters\Secure());
		
		return $limitsData;
	}
}