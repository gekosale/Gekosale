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
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: translation.php 583 2011-10-28 20:19:07Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class TranslationController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteTranslation',
			$this->model,
			'doAJAXDeleteTranslation'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doUpdateTranslation',
			$this->model,
			'doAJAXUpdateTranslation'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllTranslation',
			$this->model,
			'getTranslationForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetTranslationSuggestions',
			$this->model,
			'getTranslationNameForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addTranslation($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/translation/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/translation');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawTranslationData = $this->model->getTranslationView($this->id);
		
		if(empty($rawTranslationData)){
			App::redirect(__ADMINPANE__ . '/translation');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $rawTranslationData['name'],
				'language_data' => $rawTranslationData['language']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editTranslation($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/translation');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function view ()
	{
		$sql = 'SELECT 
					T.name, 
					TD.translation 
				FROM translation T 
				LEFT JOIN translationdata TD ON T.idtranslation = TD.translationid AND TD.languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$xml = new SimpleXMLElement('<rows></rows>');
		while ($rs = $stmt->fetch()){
			$node = $xml->addChild('row');
			$name = $node->addChild('field', $rs['name']);
			$name->addAttribute('name', 'name');
			$translation = $node->addChild('field', htmlspecialchars($rs['translation']));
			$translation->addAttribute('name', 'translation');
		}
		header('Content-type: text/xml; charset=utf-8');
		header('Content-disposition: attachment; filename=pl_PL.xml');
		header('Content-type: text/xml');
		header('Cache-Control: max-age=0');
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->formatOutput = true;
		$domnode = dom_import_simplexml($xml);
		$domnode = $doc->importNode($domnode, true);
		$domnode = $doc->appendChild($domnode);
		echo $doc->saveXML();
	}
}