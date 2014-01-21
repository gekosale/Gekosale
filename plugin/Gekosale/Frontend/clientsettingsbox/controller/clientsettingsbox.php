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
 * $Revision: 484 $
 * $Author: gekosale $
 * $Date: 2011-09-07 13:42:04 +0200 (Śr, 07 wrz 2011) $
 * $Id: clientsettingsbox.php 484 2011-09-07 11:42:04Z gekosale $
 */
namespace Gekosale;

use SimpleForm;

class ClientSettingsBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->model = App::getModel('client');
	}

	public function index ()
	{
		$formPass = new SimpleForm\Form(Array(
			'name' => 'changePassword',
			'action' => '',
			'method' => 'post'
		));

		$oldPassword = $formPass->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'password',
			'label' => $this->trans('TXT_PASSWORD'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PASSWORD'))
			)
		)));

		$newPassword = $formPass->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'newpassword',
			'label' => $this->trans('TXT_PASSWORD_NEW'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PASSWORD')),
				new SimpleForm\Rules\MinLength($this->trans('ERR_PASSWORD_NEW_INVALID'), 6)
			)
		)));

		$formPass->AddChild(new SimpleForm\Elements\Password(Array(
			'name' => 'confirmpassword',
			'label' => $this->trans('TXT_PASSWORD_REPEAT'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_CONFIRM_PASSWORD')),
				new SimpleForm\Rules\Compare($this->trans('ERR_PASSWORDS_NOT_COMPATIBILE'), $newPassword)
			)
		)));

		if ($formPass->Validate()){
			$formData = $formPass->getSubmitValues();
			$BaseTable = $this->model->getClientPass();
			$PostValidatePass = $formData['password'];

			$hash = new \PasswordHash\PasswordHash();
			if ($hash->CheckPassword($PostValidatePass, $BaseTable['password'])){
				$this->model->updateClientPass($formData['newpassword']);
				$email = App::getContainer()->get('session')->getActiveClientEmail();
				$this->registry->template->assign('PASS_NEW', $formData['newpassword']);

				App::getModel('mailer')->sendEmail(Array(
					'template' => 'editPassword',
					'email' => Array(
						$email
					),
					'bcc' => false,
					'subject' => $this->trans('TXT_PASSWORD_EDIT'),
					'viewid' => Helper::getViewId()
				));

				App::getContainer()->get('session')->setVolatileChangePassOk(1, false);
				App::redirectUrl($this->registry->router->generate('frontend.clientsettings', true));
			}
			else{
				App::getContainer()->get('session')->setVolatileOldPassError(1, false);
			}
		}

		$this->registry->template->assign('formPass', $formPass->getForm());

		$erroroldpass = App::getContainer()->get('session')->getVolatileOldPassError();
		if ($erroroldpass[0] == 1){
			$this->registry->template->assign('error', $this->trans('TXT_ERROR_OLD_PASSWORD'));
		}

		$changepassok = App::getContainer()->get('session')->getVolatileChangePassOk();
		if ($changepassok[0] == 1){
			$this->registry->template->assign('changedPasswd', $this->trans('TXT_DATA_CHANGED_MAIL_SEND'));
		}

		$formUserEmail = new SimpleForm\Form(Array(
			'name' => 'changeEmail',
			'action' => '',
			'method' => 'post'
		));

		$newEmail = $formUserEmail->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));

		$formUserEmail->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'phone',
			'label' => $this->trans('TXT_PHONE'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PHONE')),
				new SimpleForm\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[0-9 -+]+$/')
			)
		)));

		$formUserEmail->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'phone2',
			'label' => $this->trans('TXT_ADDITIONAL_PHONE'),
			'rules' => Array(
				new SimpleForm\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[0-9 -+]+$/')
			)
		)));

		$clientData = App::getModel('client')->getClient();

		$formUserEmail->Populate(Array(
			'email' => $clientData['email'],
			'phone' => $clientData['phone'],
			'phone2' => $clientData['phone2']
		));

		if ($formUserEmail->Validate()){
			$formData = $formUserEmail->getSubmitValues();
			$this->model->updateClientPhone($formData['phone'], $formData['phone2']);
			if ($clientData['email'] != $formData['email']){

				$result = $this->model->checkClientNewMail($formData);
				if ($result == 0){
					$changedMail = $this->model->updateClientEmail($formData);
					$changedLogin = $this->model->updateClientLogin($formData['email']);
					App::getContainer()->get('session')->killSession();
					App::redirectUrl($this->registry->router->generate('frontend.clientlogin', true, Array(
						'param' => 'changed'
					)));
				}
				else{
					App::getContainer()->get('session')->setVolatileUserEmailDuplicateError(1, false);
				}
			}
			else{
				App::getContainer()->get('session')->setVolatileUserSettingsSaved(1, false);
			}

			App::redirectUrl($this->registry->router->generate('frontend.clientsettings', true));
		}

		$registrationok = App::getContainer()->get('session')->getVolatileRegistrationOk();

		if ($registrationok[0] == 1){
			$this->registry->template->assign('registrationok', $this->trans('TXT_REGISTER_USER_OK'));
		}

		$errorMail = App::getContainer()->get('session')->getVolatileUserEmailDuplicateError();

		if ($errorMail[0] == 1){
			$this->registry->template->assign('duplicateMailError', $this->trans('ERR_DUPLICATE_EMAIL'));
		}

		$settingsChanged = App::getContainer()->get('session')->getVolatileUserSettingsSaved();

		if ($settingsChanged[0] == 1){
			$this->registry->template->assign('settingsSaved', $this->trans('TXT_SETTINGS_SAVED'));
		}

		$this->registry->template->assign('formEmail', $formUserEmail->getForm());

		$changedPasswd = App::getContainer()->get('session')->getVolatilePasswordChanged();
		if ($changedPasswd[0] == 1){
			$this->registry->template->assign('changedPasswd', $this->trans('TXT_PASSWORD_CHANGED'));
		}

		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}
