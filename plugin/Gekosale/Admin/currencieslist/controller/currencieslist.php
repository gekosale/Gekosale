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
 * $Id: currencieslist.php 619 2011-12-19 21:09:00Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class CurrenciesListController extends Component\Controller\Admin
{

	public function index ()
	{
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllCurrencieslist',
			$this->model,
			'getCurrencieslistForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteCurrencieslist',
			$this->model,
			'doAJAXDeleteCurrencieslist'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doUpdateCurrency',
			$this->model,
			'doAJAXUpdateCurrencieslist'
		));
		
		$this->registry->xajax->registerFunction(array(
			'refreshAllCurrencies',
			$this->model,
			'doAJAXRefreshAllCurrencies'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData(),
			'dgFilterDefaultCurrency' => $this->registry->loader->getParam('currencysymbol')
		));
	
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->addCurrencieslist($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/currencieslist');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$Data = $this->model->getCurrencieslistView($this->id);
		
		if(empty($Data)){
			App::redirect(__ADMINPANE__ . '/currencieslist');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $Data['name'],
				'symbol' => $Data['symbol'],
				'decimalseparator' => $Data['decimalseparator'],
				'decimalcount' => $Data['decimalcount'],
				'thousandseparator' => $Data['thousandseparator'],
				'positivepreffix' => $Data['positivepreffix'],
				'positivesuffix' => $Data['positivesuffix'],
				'negativepreffix' => $Data['negativepreffix'],
				'negativesuffix' => $Data['negativesuffix']
			),
			'exchange_data' => Array(
				$Data['exchangerates']
			),
			'view_data' => Array(
				'view' => $Data['view']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editCurrencieslist($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/currencieslist');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}