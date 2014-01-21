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
 * $Revision: 566 $
 * $Author: gekosale $
 * $Date: 2011-10-19 10:34:01 +0200 (Śr, 19 paź 2011) $
 * $Id: invoice.php 566 2011-10-19 08:34:01Z gekosale $
 */
namespace Gekosale;

use FormEngine;
use sfEvent;

class invoiceController extends Component\Controller\Admin
{

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_SALES_STATS'), $this->getRouter()->url('admin', 'statssales'));
		App::getModel('contextmenu')->add($this->trans('TXT_CLIENTS'), $this->getRouter()->url('admin', 'client'));

		$this->registry->xajax->registerFunction(array(
			'doDeleteInvoice',
			$this->model,
			'doAJAXDeleteInvoice'
		));

		$this->registry->xajax->registerFunction(array(
			'LoadAllInvoice',
			$this->model,
			'getInvoiceForAjax'
		));

		$this->renderLayout();
	}

	public function view ()
	{
		$this->model->getInvoiceById((int) $this->registry->core->getParam(0), (int) $this->registry->core->getParam(1));
	}

	public function confirm ()
	{
		$this->model->exportInvoice(json_decode(base64_decode($this->registry->core->getParam())));
	}

	public function add ()
	{
		$orderData = App::getModel('order')->getOrderById((int) $this->registry->core->getParam());

		if ( !$orderData) {
			App::redirect(__ADMINPANE__ . '/inpost/');
		}

		$viewData = App::getModel('view')->getView($orderData['viewid']);
		$invoiceType = (int) $this->registry->core->getParam(1);

		$invoiceNumber = $this->model->generateInvoiceNumber($viewData['invoicenumerationkind'], $invoiceType, $orderData['order_date'], $orderData['viewid']);

		$form = new FormEngine\Elements\Form(Array(
			'name' => 'invoice',
			'action' => '',
			'method' => 'post'
		));

		$invoiceData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'invoice_data',
			'label' => $this->trans('TXT_INVOICE')
		)));

		$invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'salesperson',
			'label' => $this->trans('TXT_SALES_PERSON'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_SALES_PERSON'))
			),
			'default' => App::getModel('users')->getUserFullName()
		)));

		$invoiceDate = $invoiceData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'invoicedate',
			'label' => $this->trans('TXT_INVOICE_DATE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_INVOICE_DATE'))
			),
			'default' => date('Y-m-d')
		)));

		$wFirmaSettings = $this->registry->core->loadModuleSettings('wfirma', $orderData['viewid']);
		$inFaktSettings = $this->registry->core->loadModuleSettings('infakt', $orderData['viewid']);

		if (! empty($wFirmaSettings) && $wFirmaSettings['wfirmalogin'] != '' && $wFirmaSettings['wfirmapassword'] != ''){
			$invoiceData->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => '<p>Numer faktury zostanie wygenerowany poprzez API wFirma automatycznie.'
			)));
		}
		elseif (! empty($inFaktSettings) && $inFaktSettings['infaktlogin'] != '' && $inFaktSettings['infaktpassword'] != ''){
			$invoiceData->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => '<p>Numer faktury zostanie wygenerowany poprzez API inFakt automatycznie.'
			)));
		}
		else{
			$invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
				'name' => 'invoicenumber',
				'label' => $this->trans('TXT_INVOICE_NUMBER'),
				'rules' => Array(
					new FormEngine\Rules\Required($this->trans('ERR_EMPTY_INVOICE_NUMBER'))
				),
				'default' => $invoiceNumber,
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SUGGEST, $invoiceDate, Array(
						$this->model,
						'getInvoiceNumberFormat'
					))
				)
			)));
		}

		$invoiceData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'duedate',
			'label' => $this->trans('TXT_MATURITY'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MATURITY'))
			),
			'default' => date('Y-m-d', strtotime('+' . $viewData['invoicedefaultpaymentdue'] . ' days'))
		)));

		$invoiceData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'comment',
			'label' => $this->trans('TXT_COMMENT'),
			'default' => $this->trans('TXT_ORDER') . ': ' . $orderData['order_id']
		)));

		$invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'totalpayed',
			'label' => $this->trans('TXT_TOTAL_PAYED'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TOTAL_PAYED'))
			),
			'default' => '0.00',
			'suffix' => $orderData['currencysymbol']
		)));

		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());

		if ($form->Validate(FormEngine\FE::SubmittedData())){

			$formData = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			if (! empty($wFirmaSettings) && $wFirmaSettings['wfirmalogin'] != '' && $wFirmaSettings['wfirmapassword'] != ''){
				App::getModel('wfirma')->addInvoice($formData, (int) $this->registry->core->getParam(0), (int) $this->registry->core->getParam(1), $orderData);
			}
			elseif (! empty($inFaktSettings) && $inFaktSettings['infaktlogin'] != '' && $inFaktSettings['infaktpassword'] != ''){
				App::getModel('infakt')->addInvoice($formData, (int) $this->registry->core->getParam(0), (int) $this->registry->core->getParam(1), $orderData);
			}
			else{
				$invoiceNo = $this->model->addInvoice($formData, (int) $this->registry->core->getParam(0), (int) $this->registry->core->getParam(1), $orderData);
			}
			App::getContainer()->get('session')->setVolatileMessage("Dodano fakturę {$invoiceNo} do zamówienia {$this->registry->core->getParam()}");
			App::redirect(__ADMINPANE__ . '/order/edit/' . (int) $this->registry->core->getParam());
		}

		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}
}