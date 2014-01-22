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

namespace Gekosale\Plugin;
use FormEngine;

class ExchangeController extends Component\Controller\Admin
{

	public function index ()
	{
        App::getModel('contextmenu')->add($this->trans('TXT_CLIENTS'), $this->getRouter()->url('admin', 'client'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
        App::getModel('contextmenu')->add($this->trans('TXT_CATEGORIES'), $this->getRouter()->url('admin', 'category'));
        App::getModel('contextmenu')->add($this->trans('TXT_ORDERS'), $this->getRouter()->url('admin', 'order'));



		$form = new FormEngine\Elements\Form(Array(
			'name' => 'exchange',
			'action' => '',
			'method' => 'post'
		));

		$typePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'type_pane',
			'label' => $this->trans('TXT_EXCHANGE_FILES')
		)));

		$exchangetype = $typePane->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'type',
			'label' => $this->trans('TXT_EXCHANGE_TYPE'),
			'options' => Array(
				new FormEngine\Option(1, $this->trans('TXT_EXCHANGE_TYPE_EXPORT')),
				new FormEngine\Option(2, $this->trans('TXT_EXCHANGE_TYPE_IMPORT'))
			),
			'default' => 1
		)));

		$entity = $typePane->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'entity',
			'label' => $this->trans('TXT_EXCHANGE_ENTITY'),
			'options' => Array(
				new FormEngine\Option(1, $this->trans('TXT_PRODUCTS')),
				new FormEngine\Option(2, $this->trans('TXT_CATEGORIES')),
				new FormEngine\Option(3, $this->trans('TXT_CLIENTS')),
				new FormEngine\Option(4, $this->trans('TXT_ORDERS'))
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::EXCHANGE_OPTIONS, $exchangetype, Array(
					$this,
					'getEntityTypes'
				))
			),
			'default' => 1
		)));

		$filesPane = $typePane->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'files_pane',
			'label' => $this->trans('TXT_EXCHANGE_FILES')
		)));

		$filesPane->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Wybierz plik z biblioteki lub wgraj z dysku komputera. W każdej chwili możesz pobrać przykładowy plik CSV aby zobaczyć jego strukturę.</p>
		<ul>
		<li><a href="' . App::getURLAdressWithAdminPane() . 'exchange/view/1' . '">Pobierz przykładowy plik dla produktów</a></li>
		<li><a href="' . App::getURLAdressWithAdminPane() . 'exchange/view/2' . '">Pobierz przykładowy plik dla kategorii</a></li>
		</ul>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$files = $filesPane->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'files',
			'label' => 'Plik',
			'file_source' => 'upload/',
			'file_types' => Array(
				'csv'
			)
		)));

		$filesPane->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $exchangetype, new FormEngine\Conditions\Equals(2)));

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);

			switch ($Data['type']) {
				case 1:
					App::getContainer()->get('session')->setActiveExchangeEntityType($Data['entity']);
					App::getModel('exchange')->exportFile($Data['entity']);
					break;
				case 2:
					App::getModel('exchange')->importFromFile($Data['files']['file'], $Data['entity']);
					App::redirect(__ADMINPANE__ . '/exchange/confirm');
					break;
			}

		}
		else{

			$this->renderLayout(array('form' => $form->Render()));
		}
	}

	public function add ()
	{

		$form = new FormEngine\Elements\Form(Array(
			'name' => 'add_migration',
			'action' => '',
			'method' => 'post'
		));

		$progress = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'progres_data',
			'label' => 'Aktualizacja'
		)));

		$progress->AddChild(new FE_ProgressIndicator(Array(
			'name' => 'progress',
			'label' => 'Postęp migracji',
			'chunks' => 1,
			'load' => Array(
				App::getModel('exchange/migration'),
				'doLoadQueque'
			),
			'process' => Array(
				App::getModel('exchange/migration'),
				'doProcessQueque'
			),
			'success' => Array(
				App::getModel('exchange/migration'),
				'doSuccessQueque'
			),
			'preventSubmit' => true
		)));

		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function confirm ()
	{

		$form = new FormEngine\Elements\Form(Array(
			'name' => 'confirm_exchange',
			'action' => '',
			'method' => 'post'
		));

		$parsePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'parse',
			'label' => 'Import danych'
		)));

		$parsePane->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Import zakończony powodzeniem</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function view () {
		App::getModel('exchange')->exportFile($this->id);
	}

	public function getEntityTypes ($type)
	{
		$tmp[1] = $this->trans('TXT_PRODUCTS');
		$tmp[2] = $this->trans('TXT_CATEGORIES');
		if ($type == 1){
			$tmp[3] = $this->trans('TXT_CLIENTS');
			$tmp[4] = $this->trans('TXT_ORDERS');
		}
		return FormEngine\Option::Make($tmp);
	}
}