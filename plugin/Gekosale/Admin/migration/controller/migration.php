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
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: exchange.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class MigrationController extends Component\Controller\Admin
{

	public function index ()
	{
		if (App::getConfig('devmode') == 0){
			App::redirect(__ADMINPANE__ . '/mainside');
		}

		if ((int)$this->getParam() == 0){
			$form = new FormEngine\Elements\Form(Array(
				'name' => 'exchange',
				'action' => '',
				'method' => 'post'
			));

			$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'type_pane',
				'label' => $this->trans('TXT_EXCHANGE_TYPE_MIGRATION_SETTINGS')
			)));

			$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Podaj adres URL wtyczki integracyjnej.</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));

			$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
				'name' => 'apiurl',
				'label' => $this->trans('TXT_MIGRATION_API_URL')
			)));

			$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Podaj klucz jaki został ustawiony w pliku integracyjnym ($key)</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));

			$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
				'name' => 'apikey',
				'label' => $this->trans('TXT_MIGRATION_API_KEY')
			)));

			$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Wybierz rodzaj importowanych danych.Sugerujemy import w następującej kolejności:
							<ul>
							<li>Zdjęcia</li>
							<li>Producenci</li>
							<li>Kategorie</li>
							<li>Wartości cech</li>
							<li>Produkty</li>
							</ul></p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));

			$requiredData->AddChild(new FormEngine\Elements\Select(Array(
				'name' => 'entity',
				'label' => $this->trans('TXT_EXCHANGE_ENTITY'),
				'options' => Array(
					new FormEngine\Option(1, $this->trans('TXT_PRODUCTS')),
					new FormEngine\Option(2, $this->trans('TXT_CATEGORIES')),
					new FormEngine\Option(3, $this->trans('TXT_PRODUCERS')),
					new FormEngine\Option(4, $this->trans('TXT_PHOTOS')),
					new FormEngine\Option(5, $this->trans('TXT_ATTRIBUTES'))	,
					new FormEngine\Option(6, $this->trans('TXT_ORDERS'))	,
					new FormEngine\Option(7, $this->trans('TXT_SIMILARPRODUCT'))	,
				),
				'default' => 1
			)));

			$form->AddFilter(new FormEngine\Filters\Trim());
			$form->AddFilter(new FormEngine\Filters\Secure());

			if ($form->Validate(FormEngine\FE::SubmittedData())){
				$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				App::getContainer()->get('session')->setActiveMigrationData(json_encode($Data));
				App::redirect(__ADMINPANE__ . '/migration/index/'.$Data['entity']);
			}

			$this->registry->template->assign('form', $form->Render());
			$this->registry->xajax->processRequest();
			$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
			$this->registry->template->display($this->loadTemplate('index.tpl'));
		}
		else{

			if(App::getContainer()->get('session')->getActiveMigrationData() == NULL){
				App::redirect(__ADMINPANE__ . '/migration/');
			}
			$form = new FormEngine\Elements\Form(Array(
				'name' => 'add_migration',
				'action' => '',
				'method' => 'post'
			));

			$progress = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'progres_data',
				'label' => 'Aktualizacja'
			)));

			$progress->AddChild(new FormEngine\Elements\ProgressIndicator(Array(
				'name' => 'progress',
				'label' => 'Postęp migracji',
				'chunks' => 1,
				'load' => Array(
					App::getModel('migration'),
					'doLoadQueque'
				),
				'process' => Array(
					App::getModel('migration'),
					'doProcessQueque'
				),
				'success' => Array(
					App::getModel('migration'),
					'doSuccessQueque'
				),
				'preventSubmit' => true
			)));

			$form->AddFilter(new FormEngine\Filters\Trim());
			$form->AddFilter(new FormEngine\Filters\Secure());

			$this->registry->template->assign('form', $form->Render());
			$this->registry->xajax->processRequest();
			$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
			$this->registry->template->display($this->loadTemplate('index.tpl'));
		}
	}
}