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
 * $Id: forgotpasswordbox.php 484 2011-09-07 11:42:04Z gekosale $
 */
namespace Gekosale\Component\Forgotpassword\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

use SimpleForm;

class ForgotPassword extends Box
{

	public function index ()
	{
		$form = new SimpleForm\Form(Array(
			'name' => 'forgotpassword',
			'action' => '',
			'method' => 'post'
		));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		if ($form->Validate()){
			$formData = $form->getSubmitValues();
			$result = App::getModel('forgotpassword')->authProccess($formData['email']);
			
			if ($result > 0){
				
				$hash = App::getModel('forgotpassword')->generateLink($formData['email']);
				
				$link = $this->registry->router->generate('frontend.forgotpassword', true, Array(
					'action' => 'confirm',
					'param' => $hash
				));
				
				$this->registry->template->assign('link', $link);
				
				App::getModel('mailer')->sendEmail(Array(
					'template' => 'forgotPassword',
					'email' => Array(
						$formData['email']
					),
					'bcc' => false,
					'subject' => $this->trans('TXT_PASSWORD_FORGOT'),
					'viewid' => Helper::getViewId()
				));
				
				App::getContainer()->get('session')->setVolatileSendPassword(1, false);
				App::getContainer()->get('session')->setVolatileForgotPasswordError();
			}
			elseif ($result < 0){
				App::getContainer()->get('session')->setVolatileForgotPasswordError(2, false);
			}
			else{
				App::getContainer()->get('session')->setVolatileSendPassword();
				App::getContainer()->get('session')->setVolatileForgotPasswordError(1, false);
			}
		}
		
		$error = App::getContainer()->get('session')->getVolatileForgotPasswordError();
		if ($error[0] == 1){
			$this->registry->template->assign('emailerror', $this->trans('ERR_EMAIL_NO_EXIST'));
		}
		elseif ($error[0] == 2){
			$this->registry->template->assign('emailerror', $this->trans('TXT_BLOKED_USER'));
		}
		$sendPasswd = App::getContainer()->get('session')->getVolatileSendPassword();
		if ($sendPasswd[0] == 1){
			$this->registry->template->assign('sendPasswd', $this->trans('TXT_CHECK_PRIVATE_MAIL_WITH_NEW_PASSWD'));
		}
		$this->registry->template->assign('form', $form->getForm());
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function confirm ()
	{
		$result = App::getModel('forgotpassword')->validateLink($this->getParam());
		
		if ($result > 0){
			
			$form = new SimpleForm\Form(Array(
				'name' => 'forgotpassword',
				'action' => '',
				'method' => 'post'
			));
			
			$newPassword = $form->AddChild(new SimpleForm\Elements\Password(Array(
				'name' => 'newpassword',
				'label' => $this->trans('TXT_PASSWORD_NEW'),
				'rules' => Array(
					new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PASSWORD')),
					new SimpleForm\Rules\MinLength($this->trans('ERR_PASSWORD_NEW_INVALID'), 6)
				)
			)));
			
			$form->AddChild(new SimpleForm\Elements\Password(Array(
				'name' => 'confirmpassword',
				'label' => $this->trans('TXT_PASSWORD_REPEAT'),
				'rules' => Array(
					new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_CONFIRM_PASSWORD')),
					new SimpleForm\Rules\Compare($this->trans('ERR_PASSWORDS_NOT_COMPATIBILE'), $newPassword)
				)
			)));
			
			if ($form->Validate()){
				$formData = $form->getSubmitValues();
				
				App::getModel('forgotpassword')->forgotPassword($result, $formData['newpassword']);
				App::getContainer()->get('session')->setActiveClientid($result);
				App::getModel('clientlogin')->checkClientGroup();
				App::getModel('client')->saveClientData();
				App::getContainer()->get('session')->setVolatilePasswordChanged(1, false);
				App::redirectUrl($this->registry->router->generate('frontend.clientsettings', true));
			}
			
			$this->registry->template->assign('form', $form->getForm());
			return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
		}
		else{
			return $this->registry->template->fetch($this->loadTemplate('error.tpl'));
		}
	}
}
