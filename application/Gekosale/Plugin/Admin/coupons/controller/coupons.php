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
 */
namespace Gekosale\Plugin;

use FormEngine;

class CouponsController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'LoadAllCoupons',
			$this->model,
			'getCouponsForAjax'
		));

		$this->registry->xajax->registerFunction(array(
			'doDeleteCoupons',
			$this->model,
			'doAJAXDeleteCoupons'
		));

		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){

			try{
				$this->model->addCoupons($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/coupons');
		}

		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$form = $this->formModel->initForm();

		$couponsData = $this->model->getCouponsView($this->id);

		$populateData = Array(
			'required_data' => Array(
				'language_data' => $couponsData['language'],
				'code' => $couponsData['code'],
				'datefrom' => $couponsData['datefrom'],
				'dateto' => $couponsData['dateto'],
				'globalqty' => $couponsData['globalqty'],
				'clientqty' => $couponsData['clientqty'],
				'clients' => $couponsData['clients']
			),
			'product_data' => array(
				'product' => $couponsData['product']
			),
			'additional_data' => Array(
				'discount' => $couponsData['discount'],
				'minimumordervalue' => $couponsData['minimumordervalue'],
				'currencyid' => $couponsData['currencyid'],
				'freeshipping' => $couponsData['freeshipping'],
				'suffixtypeid' => $couponsData['suffixtypeid'],
				'excludepromotions' => $couponsData['excludepromotions'],
				'currencyid' => $couponsData['currencyid']
			),
			'exclude_data' => Array(
				'category' => $couponsData['category']
			),
			'view_data' => Array(
				'view' => $couponsData['view']
			)
		);

		$this->formModel->setPopulateData($populateData);

		$form = $this->formModel->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editCoupons($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/coupons');
		}

		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function generate ()
	{
		$model = App::getModel('hotprice');
		$form = $model->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try {
				$this->model->generateCodes($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
				App::redirect(__ADMINPANE__ . '/coupons');
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/coupons');
		}

		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

        public function export() {
		$model = App::getModel('hotprice');
		$form = $model->initExportForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);

			if ($model->exportCoupons($data['name']) == 0) {
				$this->registry->template->assign('error', 'Brak danych do wyeksportowania.');
			}
		}

		$this->renderLayout(Array(
			'form' => $form->Render()
		));
        }
}