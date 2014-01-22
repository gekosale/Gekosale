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
 * $Id: newsletter.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;

use FormEngine;

class NewsletterController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_CLIENTS'), $this->getRouter()->url('admin', 'client'));
		App::getModel('contextmenu')->add($this->trans('TXT_TEMPLATE_LIBRARY'), $this->getRouter()->url('admin', 'templateeditor'));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteNewsletter',
			$this->model,
			'doAJAXDeleteNewsletter'
		));
		$this->registry->xajax->registerFunction(array(
			'LoadAllNewsletter',
			$this->model,
			'getNewsletterForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewNewsletterHistory($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/newsletter/add');
			}
			
			elseif (FormEngine\FE::IsAction('send')){
				$newsletter = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				if (is_array($newsletter['recipient'])){
					
					$clients = $newsletter['recipient'];
					$clientModel = App::getModel('client');
					
					$clientid = $clientModel->selectClient($clients);
					$clientsData = $clientModel->selectClientsFromNewsletter($clientid);
					
					$clientnewsletterid = $clientModel->selectClientNewsletter($clients);
					$clientsNewsletterData = $clientModel->selectClientsNewsletterFromNewsletter($clientnewsletterid);
					
					$clientgroupid = $clientModel->selectClientGroup($clients);
					$clientsGroupData = $clientModel->selectClientsGroupFromNewsletter($clientgroupid);
					
					$sum = array_keys(array_unique(array_merge($clientsData, $clientsNewsletterData, $clientsGroupData)));
					$qty = count($sum);
					if (strlen($newsletter['htmlform']) == 0){
						$newsletter['htmlform'] = '&nbsp;';
					}
					$this->registry->template->assign('newsletter', $newsletter);
					
					foreach ($sum as $email){
						App::getModel('mailer')->sendEmail(Array(
							'template' => 'newsletter',
							'email' => Array(
								$email
							),
							'bcc' => false,
							'disableLayout' => true,
							'subject' => $newsletter['subject'],
							'viewid' => Helper::getViewId()
						));
					}
					
					App::getContainer()->get('session')->setVolatileMessage("Newsletter został wysłany do {$qty} klientów.");
				}
				else{
					App::getContainer()->get('session')->setVolatileMessage("Newsletter nie został wysłany - brak odbiorców newslettera.");
				}
				App::redirect(__ADMINPANE__ . '/newsletter');
			}
			else{
				App::redirect(__ADMINPANE__ . '/newsletter');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$rawNewsletterData = $this->model->getNewsletterData($this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $rawNewsletterData['name'],
				'email' => $rawNewsletterData['email'],
				'subject' => $rawNewsletterData['subject'],
				'textform' => $rawNewsletterData['textform'],
				'htmlform' => $rawNewsletterData['htmlform']
			),
			'recipient_data' => Array(
				'recipient' => $rawNewsletterData['recipient']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->updateNewsletter($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
				if (FormEngine\FE::IsAction('send')){
					$newsletter = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
					if (is_array($newsletter['recipient'])){
						
						$clients = $newsletter['recipient'];
						$clientModel = App::getModel('client');
						
						$clientid = $clientModel->selectClient($clients);
						$clientsData = $clientModel->selectClientsFromNewsletter($clientid);
						
						$clientnewsletterid = $clientModel->selectClientNewsletter($clients);
						$clientsNewsletterData = $clientModel->selectClientsNewsletterFromNewsletter($clientnewsletterid);
						
						$clientgroupid = $clientModel->selectClientGroup($clients);
						$clientsGroupData = $clientModel->selectClientsGroupFromNewsletter($clientgroupid);
						
						$sum = array_values(array_unique(array_merge($clientsData, $clientsNewsletterData, $clientsGroupData)));
						$qty = count($sum);
						
						if (strlen($newsletter['htmlform']) == 0){
							$newsletter['htmlform'] = '&nbsp;';
						}
						$this->registry->template->assign('newsletter', $newsletter);
						
						foreach ($sum as $email){
							if (strlen($email) > 4){
								App::getModel('mailer')->sendEmail(Array(
									'template' => 'newsletter',
									'email' => Array(
										$email
									),
									'bcc' => false,
									'disableLayout' => true,
									'subject' => $newsletter['subject'],
									'viewid' => Helper::getViewId()
								));
							}
						}
						
						App::getContainer()->get('session')->setVolatileMessage("Newsletter został wysłany do {$qty} klientów.");
					}
					else{
						App::getContainer()->get('session')->setVolatileMessage("Newsletter nie został wysłany - brak odbiorców newslettera.");
					}
				}
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/newsletter');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}