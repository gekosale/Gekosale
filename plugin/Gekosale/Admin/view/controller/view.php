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
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: view.php 576 2011-10-22 08:23:55Z gekosale $
 */
namespace Gekosale;

use Facebook;

require ROOTPATH . 'lib' . DS . 'social' . DS . 'facebook' . DS . 'facebook.php';
class ViewController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteView',
			$this->model,
			'doAJAXDeleteView'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllView',
			$this->model,
			'getViewForAjax'
		));
		
		$this->renderLayout();
	}

	public function add ()
	{
		$this->registry->xajax->registerFunction(array(
			'AddSendingoGroup',
			App::getModel('sendingo'),
			'addGroup'
		));
		
		$this->registry->xajaxInterface->registerFunction(array(
			'AddDomain',
			App::getModel('view'),
			'AddDomain'
		));
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(\FormEngine\FE::SubmittedData())){
			$this->model->addView($form->getSubmitValues(\FormEngine\Elements\Form::FORMAT_FLAT));
			App::getContainer()->get('session')->setActiveViewIds(NULL);
			if (\FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/view/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/view');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$this->registry->xajax->registerFunction(array(
			'AddSendingoGroup',
			App::getModel('sendingo'),
			'addGroup'
		));
		
		$this->registry->xajaxInterface->registerFunction(array(
			'AddDomain',
			App::getModel('view'),
			'AddDomain'
		));
		
		$ViewData = $this->model->getView((int) $this->id);
		
		if (empty($ViewData)){
			App::redirect(__ADMINPANE__ . '/view');
		}
		
		$privacySettings = $this->registry->core->loadModuleSettings('privacy', (int) $this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $ViewData['name'],
				'namespace' => $ViewData['namespace'],
				'store' => $ViewData['store'],
				'defaultvatid' => $ViewData['defaultvatid'],
				'showtax' => $ViewData['showtax'],
				'offline' => $ViewData['offline'],
				'offlinetext' => $ViewData['offlinetext'],
				'pageschemeid' => $ViewData['pageschemeid'],
				'currencyid' => $ViewData['currencyid'],
				'contactid' => $ViewData['contactid']
			),
			'policy_data' => Array(
				'privacypolicyenabled' => isset($privacySettings['privacypolicyenabled']) ? $privacySettings['privacypolicyenabled'] : 0,
				'privacypolicyid' => isset($privacySettings['privacypolicyid']) ? $privacySettings['privacypolicyid'] : ''
			),
			'meta_data' => Array(
				'language_data' => $ViewData['language']
			),
			'url_pane' => Array(
				'url' => $ViewData['url'],
				'wwwredirection' => $ViewData['wwwredirection']
			),
			'category_pane' => Array(
				'category' => $ViewData['category']
			),
			'paymentmethod_pane' => Array(
				'paymentmethod' => $ViewData['paymentmethods']
			),
			'dispatchmethod_pane' => Array(
				'dispatchmethod' => $ViewData['dispatchmethods']
			),
			'giftwrap_pane' => Array(
				'enablegiftwrap' => $ViewData['enablegiftwrap'],
				'giftwrapproduct' => $ViewData['giftwrapproduct']
			),
			'assigntogroup_data' => Array(
				'taxes' => $ViewData['taxes'],
				'periodid' => $ViewData['periodid'],
				'orderstatusgroupsid' => $ViewData['orderstatusgroupsid'],
				'table' => App::getModel('clientgroup')->getAssignToGroupPerView((int) $this->id)
			),
	
			'googleapps_data' => Array(
				'googleappstag' => $ViewData['googleappstag'],
				'googleappsfile' => $ViewData['googleappsfile']
			),
			'registration_data' => Array(
				'forcelogin' => $ViewData['forcelogin'],
				'confirmregistration' => $ViewData['confirmregistration'],
				'enableregistration' => $ViewData['enableregistration']
			),
			'cart_data' => Array(
				'cartredirect' => $ViewData['cartredirect'],
				'minimumordervalue' => $ViewData['minimumordervalue']
			),
			'logo' => Array(
				'photo' => $ViewData['photo'],
				'favicon' => $ViewData['favicon'],
				'watermark' => $ViewData['watermark']
			),
			'order_uploader_data' => Array(
				'uploaderenabled' => $ViewData['uploaderenabled'],
				'uploadmaxfilesize' => $ViewData['uploadmaxfilesize'],
				'uploadchunksize' => $ViewData['uploadchunksize'],
				'uploadextensions' => $ViewData['uploadextensions']
			),
			'invoice_data' => Array(
				'invoicenumerationkind' => $ViewData['invoicenumerationkind'],
				'invoicedefaultpaymentdue' => $ViewData['invoicedefaultpaymentdue']
			),
			'mailer_data' => Array(
				'mailer' => $ViewData['mailer']['mailer'],
				'fromname' => $ViewData['mailer']['fromname'],
				'fromemail' => $ViewData['mailer']['fromemail'],
				'server' => $ViewData['mailer']['server'],
				'port' => $ViewData['mailer']['port'],
				'smtpsecure' => $ViewData['mailer']['smtpsecure'],
				'smtpauth' => $ViewData['mailer']['smtpauth'],
				'smtpusername' => $ViewData['mailer']['smtpusername'],
				'smtppassword' => $ViewData['mailer']['smtppassword']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(\FormEngine\FE::SubmittedData())){
			try{
				$this->model->editView($form->getSubmitValues(\FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/view');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}