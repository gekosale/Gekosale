<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 552 $
 * $Author: gekosale $
 * $Date: 2011-10-08 17:56:59 +0200 (So, 08 paź 2011) $
 * $Id: registrationcartbox.php 552 2011-10-08 15:56:59Z gekosale $
 */
namespace Gekosale\Plugin;

use SimpleForm;
use PHPMailer;

class RegistrationBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->model = App::getModel('client');
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	public function index ()
	{
		if (strlen($this->registry->core->getParam()) > 10){
			$checkClient = $this->model->checkClientLink($this->registry->core->getParam());
			if (count($checkClient > 0)){
				$result = App::getModel('clientlogin')->authProccessConfirmation($checkClient['email'], $checkClient['password']);
				if ($result != 0){
					App::getContainer()->get('session')->setActiveClientid($result);
					App::getModel('clientlogin')->checkClientGroup();
					$this->model->saveClientData();
				}
				if (($this->Cart = App::getContainer()->get('session')->getActiveCart()) != NULL){
					App::redirectUrl($this->registry->router->generate('frontend.cart', true));
				}
				else{
					App::redirectUrl($this->registry->router->generate('frontend.home', true));
				}
			}
		}
		
		$form = App::getFormModel('registration')->initForm();
		if ($form->Validate() && $this->layer['enableregistration']){
			$formData = $form->getSubmitValues();
			$recurMail = $this->model->checkClientNewMail($formData);
			if ($recurMail == 0){
				$clientId = $this->model->addNewClient($formData);
				if (isset($this->layer['confirmregistration']) && $this->layer['confirmregistration'] == 1){
					$link = $this->model->updateClientDisable($clientId, 1, sha1($formData['email'] . time()));
					$this->registry->template->assign('activelink', $link);
				}
				$this->registry->template->assign('address', $formData);
				
				App::getModel('mailer')->sendEmail(Array(
					'template' => 'addClient',
					'email' => Array(
						$formData['email']
					),
					'bcc' => false,
					'subject' => $this->trans('TXT_REGISTRATION_NEW'),
					'viewid' => Helper::getViewId()
				));
				
				if (isset($this->layer['confirmregistration']) && $this->layer['confirmregistration'] == 1){
					App::getContainer()->get('session')->setVolatileActivationRequired(1, false);
				}
				else{
					App::getContainer()->get('session')->setVolatileRegistrationOk(1, false);
					$result = App::getModel('clientlogin')->authProccess($formData['email'], $formData['password']);
					if ($result != 0){
						App::getContainer()->get('session')->setActiveClientid($result);
						App::getModel('clientlogin')->checkClientGroup();
						$this->model->saveClientData();
					}
					App::getContainer()->get('session')->setVolatileRegistrationOk(1, false);
					App::redirectUrl($this->registry->router->generate('frontend.clientsettings', true));
				}
			}
			else{
				$result = App::getModel('clientlogin')->authProccess($formData['email'], $formData['password']);
				if ($result != 0){
					App::getContainer()->get('session')->setActiveClientid($result);
					App::getModel('clientlogin')->checkClientGroup();
					$this->model->saveClientData();
					App::getContainer()->get('session')->setVolatileRegistrationOk(1, false);
					App::redirectUrl($this->registry->router->generate('frontend.clientsettings', true));
				}
				
				App::getContainer()->get('session')->setVolatileRecureMail(1, false);
			}
		}
		
		$this->registry->template->assign('form', $form->getForm());
		
		$this->registry->template->assign('enableregistration', $this->layer['enableregistration']);
		$this->registry->template->assign('registrationmode', ($this->registry->router->getCurrentController() === 'registration'));
		
		$activationrequired = App::getContainer()->get('session')->getVolatileActivationRequired();
		if ($activationrequired[0] == 1){
			$this->registry->template->assign('error', $this->trans('TXT_ACTIVATION_REQUIRED') . '. ' . $this->trans('TXT_CHECK_PRIVATE_MAIL'));
		}
		
		$recureMailError = App::getContainer()->get('session')->getVolatileRecureMail();
		if ($recureMailError[0] == 1){
			$this->registry->template->assign('error', $this->trans('ERR_DUPLICATE_EMAIL'));
		}
		
		$forbiddenCode = App::getContainer()->get('session')->getVolatileForbiddenCode();
		if ($forbiddenCode[0] == 1){
			$this->registry->template->assign('error', $this->trans('TXT_ERROR_FORBIDDEN_CODE'));
		}
		
		$passwdGenError = App::getContainer()->get('session')->getVolatilePasswordGenerateError();
		if ($passwdGenError[0] == 1){
			$this->registry->template->assign('error', $this->trans('ERROR_PASSWORD_GENERATE'));
		}
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}
