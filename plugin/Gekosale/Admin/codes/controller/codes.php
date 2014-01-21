<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 111 $
 * $Author: gekosale $
 * $Date: 2011-05-06 21:54:00 +0200 (Pt, 06 maj 2011) $
 * $Id: news.php 111 2011-05-06 19:54:00Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class CodesController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->model = App::getModel('codes');
		$dir = ROOTPATH . 'upload' . DS . 'keys';
		if (! is_dir($dir)){
			mkdir($dir, 0770);
		}
	}

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'LoadAllLicence',
			$this->model,
			'getLicenceForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'GetTopicSuggestions',
			$this->model,
			'getTopicForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'doDeleteLicence',
			$this->model,
			'doAJAXDeleteLicence'
		));
		$this->registry->xajax->registerFunction(array(
			'disableLicence',
			$this->model,
			'doAJAXDisableLicence'
		));
		$this->registry->xajax->registerFunction(array(
			'enableLicence',
			$this->model,
			'doAJAXEnableLicence'
		));
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('datagrid_filter', $this->model->getDatagridFilterData());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function add ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'exchange',
			'action' => '',
			'method' => 'post'
		));
		
		$typePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'type_pane',
			'label' => 'Wybór pliku'
		)));
		
		$filesPane = $typePane->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'files_pane'
		)));
		
		$filesPane->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Wybierz plik z biblioteki lub wgraj z dysku komputera. W każdej chwili możesz pobrać przykładowy plik CSV aby zobaczyć jego strukturę.</p>
						<ul>
						<li><a href="' . App::getURLAdressWithAdminPane() . 'codes/view/' . '">Pobierz przykładowy plik</a></li>
						</ul>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$files = $filesPane->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'files',
			'label' => 'Plik',
			'file_source' => 'upload/keys/',
			'file_types' => Array(
				'csv'
			)
		)));
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			App::getModel('codes')->importFromFile($Data['files']['file']);
			App::redirect(__ADMINPANE__ . '/codes/');
		}
		else{
			$this->renderLayout(Array(
				'form' => $form->Render()
			));
		}
	}

	public function view ()
	{
		$Data = Array();
		$Data[] = Array(
			'ean' => 'EAN_123456',
			'code' => 'KLUCZ_LICENCYJNY_123456'
		);
		
		$filename = 'codes_' . date('Y_m_d_H_i_s') . '.csv';
		$header = Array();
		if (isset($Data[0])){
			$header = array_keys($Data[0]);
		}
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$fp = fopen("php://output", 'w');
		fputcsv($fp, $header, ";");
		foreach ($Data as $key => $values){
			fputcsv($fp, $values, ";");
		}
		fclose($fp);
		exit();
	}

	public function edit ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'edit_news',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_KEYS')
		)));
		
		$keysData = $requiredData->AddChild(new FormEngine\Elements\FieldsetRepeatable(Array(
			'name' => 'keys_data',
			'repeat_min' => 1,
			'repeat_max' => FE::INFINITE
		)));
		
		$keysData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'licencekey',
			'rows' => 3
		)));
		
		$filesPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'files_pane',
			'label' => $this->trans('TXT_FILES')
		)));
		
		$type = $filesPane->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'type',
			'label' => $this->trans('TXT_FILE_TYPE'),
			'options' => Array(
				new FE_Option(1, $this->trans('TXT_INTERNAL')),
				new FE_Option(2, $this->trans('TXT_EXTERNAL'))
			),
			'default' => 1
		)));
		
		$external = $filesPane->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'external',
			'label' => $this->trans('TXT_EXTERNAL_FILE')
		)));
		
		$external->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $type, new FormEngine\Conditions\Eqals(2)));
		
		$internal = $filesPane = $filesPane->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'files',
			'label' => 'Plik',
			'file_source' => 'upload/',
			'file_types' => Array(
				'zip',
				'rar',
				'exe',
				'tgz',
				'tar.gz',
				'iso'
			)
		)));
		
		$internal->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $type, new FormEngine\Conditions\Eqals(1)));
		
		$form->AddFilter(new FE_FilterTrim());
		
		$rawLicenceData = $this->model->getLicenceView($this->registry->core->getParam());
		
		$newsData = Array(
			'required_data' => Array(
				'keys_data' => Array(
					'licencekey' => $rawLicenceData['keys']
				)
			),
			'files_pane' => Array(
				'type' => $rawLicenceData['type'],
				'external' => ($rawLicenceData['type'] == 2) ? $rawLicenceData['file'] : '',
				'files' => ($rawLicenceData['type'] == 1) ? Array(
					'file' => $rawLicenceData['file']
				) : ''
			)
		);
		
		$form->Populate($newsData);
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editLicence($form->getSubmitValues(FormEngine\Elements::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/licence');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}