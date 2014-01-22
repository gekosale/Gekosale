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
 * $Id: store.php 583 2011-10-28 20:19:07Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class storeController extends Component\Controller\Admin
{

	public function index ()
	{
        App::getModel('contextmenu')->add($this->trans('TXT_STORE_SETTINGS'), $this->getRouter()->url('admin', 'view'));

        
		$this->registry->xajax->registerFunction(array(
			'doDeleteStore',
			$this->model,
			'doAJAXDeleteStore'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllStore',
			$this->model,
			'getStoreForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addStore($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/store/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/store');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawStoreData = $this->model->getStoreView($this->id);
		
		if (empty($rawStoreData)){
			App::redirect(__ADMINPANE__ . '/store');
		}
		
		$slogan = 0;
		if ($rawStoreData['isinvoiceshopname'] == 1){
			$slogan = 1;
		}
		if ($rawStoreData['isinvoiceshopslogan'] == 1){
			$slogan = 2;
		}
		
		$populateData = Array(
			'address_data' => Array(
				'placename' => $rawStoreData['placename'],
				'postcode' => $rawStoreData['postcode'],
				'street' => $rawStoreData['street'],
				'streetno' => $rawStoreData['streetno'],
				'placeno' => $rawStoreData['placeno'],
				'province' => $rawStoreData['province'],
				'countries' => $rawStoreData['countryid']
			),
			'company_data' => Array(
				'companyname' => $rawStoreData['companyname'],
				'shortcompanyname' => $rawStoreData['shortcompanyname'],
				'nip' => $rawStoreData['nip'],
				'krs' => $rawStoreData['krs']
			),
			'bank_data' => Array(
				'bankname' => $rawStoreData['bankname'],
				'banknr' => $rawStoreData['banknr']
			),
			'photos_pane' => Array(
				'photo' => $rawStoreData['photo']
			),
			'invoice_data' => Array(
				'isinvoiceshopslogan' => Array(
					'value' => $slogan
				),
				'invoiceshopslogan' => $rawStoreData['invoiceshopslogan']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editStore($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/store');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

}