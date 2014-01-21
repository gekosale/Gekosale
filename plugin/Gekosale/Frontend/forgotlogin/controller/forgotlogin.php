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
 * $Id: forgotlogin.php 484 2011-09-07 11:42:04Z gekosale $
 */

namespace Gekosale;
use FormEngine;

class ForgotLoginController extends Component\Controller\Frontend
{

	public function index ()
	{
		if (App::getContainer()->get('session')->getActiveUserid() != null){
			App::redirect(__ADMINPANE__ . '/mainside');
		}
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'forgotlogin',
			'action' => '',
			'method' => 'post',
			'class' => 'login-form'
		));
		
		$form->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'login',
			'label' => $this->trans('TXT_EMAIL_FORM_LOGIN'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_LOGIN_FORM_LOGIN'))
			)
		)));
		
		$form->AddChild(new FormEngine\Elements\Submit(Array(
			'name' => 'log_in',
			'label' => $this->trans('TXT_FORGOT_PASSWORD')
		)));
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$loginValues = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			$result = App::getModel('login')->checkUsers($loginValues['login']);
			if ($result == 0){
				App::getContainer()->get('session')->setVolatileLoginError(1, false);
			}
			else{
				$password = Core::passwordGenerate();
				App::getModel('login')->changeUsersPassword($result, $password);
				$this->registry->template->assign('password', $password);
				
				App::getModel('mailer')->sendEmail(Array(
					'template' => 'forgotUsers',
					'email' => Array(
						$_POST['login']
					),
					'bcc' => false,
					'subject' => $this->trans('TXT_FORGOT_PASSWORD'),
					'viewid' => Helper::getViewId()
				));
				App::getContainer()->get('session')->setVolatileMessage("Nowe hasło zostało wysłane na podany adres e-mail.");
				App::redirect('login');
			}
		}
		
		$error = App::getContainer()->get('session')->getVolatileLoginError();
		if ($error[0] == 1){
			$this->registry->template->assign('error', $this->trans('ERR_BAD_EMAIL'));
		}
		
		$languages = App::getModel('language')->getLanguages();
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->assign('languages', json_encode($languages));
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}
}
