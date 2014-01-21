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
 * $Id: exchange.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;

class FirmesController extends Component\Controller\Admin
{

	public function index ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'firmes',
			'action' => '',
			'method' => 'post'
		));
		
		$statuses = App::getModel('orderstatus')->getOrderStatusToSelect();
		
		$groups = App::getModel('clientgroup')->getClientGroupAllToSelect();
		
		$groups[0] = 'Pomiń importowanie cen dla tego poziomu';
		
		$subiektgt = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'firmes_data',
			'label' => 'Integracja z Subiekt GT'
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Ustaw powiązania statusów Subiekt GT ze sklepem</p>'
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_1',
			'label' => 'Przyjęte',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_2',
			'label' => 'Niezrealizowane bez rezerwacji',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_3',
			'label' => 'Niezrealizowane z rezerwacją',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_4',
			'label' => 'Zrealizowane',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_5',
			'label' => 'Zakończone',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_6',
			'label' => 'Usunięte',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_7',
			'label' => 'Zwrot',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'subiekt_status_8',
			'label' => 'Reklamacja',
			'options' => FormEngine\Option::Make($statuses)
		)));
		
		$subiektgt->AddChild(new FormEngine\Elements\Tip(Array(
			'short_tip' => '<p>Ustaw powiązania cen w kartotekach Subiekt GT ze sklepem</p>',
			'retractable' => true,
			'tip' => '<p>Poziomy możesz sprawdzić wykonując <strong>zestawienie własne SQL w Subiekt GT</strong></p>
					  <p>Treść zapytania:</p>
					  <p>select [1] = twp_NazwaCeny1,[2] = twp_NazwaCeny2,[3] = twp_NazwaCeny3,[4] = twp_NazwaCeny4,[5] = twp_NazwaCeny5,[6] = twp_NazwaCeny6,[7] = twp_NazwaCeny7,[8] = twp_NazwaCeny8,[9] = twp_NazwaCeny9,[10] = twp_NazwaCeny10 from tw_parametr</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		for ($i = 1; $i < 11; $i ++){
			$subiektgt->AddChild(new FormEngine\Elements\Select(Array(
				'name' => 'subiekt_price_' . $i,
				'label' => 'Poziom cenowy ' . $i,
				'options' => FormEngine\Option::Make($groups),
				'default' => 0
			)));
		}
		
		$form->Populate(Array(
			'firmes_data' => $this->registry->core->loadModuleSettings('subiektgt', 0)
		));
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			
			$Settings = Array(
				'subiekt_status_1' => $Data['subiekt_status_1'],
				'subiekt_status_2' => $Data['subiekt_status_2'],
				'subiekt_status_3' => $Data['subiekt_status_3'],
				'subiekt_status_4' => $Data['subiekt_status_4'],
				'subiekt_status_5' => $Data['subiekt_status_5'],
				'subiekt_status_6' => $Data['subiekt_status_6'],
				'subiekt_status_7' => $Data['subiekt_status_7'],
				'subiekt_status_8' => $Data['subiekt_status_8']
			);
			
			for ($i = 1; $i < 11; $i ++){
				$Settings['subiekt_price_' . $i] = $Data['subiekt_price_' . $i];
			}
			
			$this->registry->core->saveModuleSettings('subiektgt', $Settings, 0);
			App::getContainer()->get('session')->setVolatileMessage("Zapisano zmiany w ustawieniach.");
			App::redirect(__ADMINPANE__ . '/firmes');
		}
		
		$this->renderLayout(array(
			'form' => $form->Render()
		));
	}
}