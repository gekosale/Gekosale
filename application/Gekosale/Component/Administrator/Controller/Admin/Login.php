<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: login.php 612 2011-11-28 20:02:10Z gekosale $
 */
namespace Gekosale\Component\Login\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend;

use FormEngine;

class Login extends Frontend
{

	public function index ()
	{
		if (strlen($this->registry->core->getParam()) > 0){
			$result = App::getModel('login')->authProccessSha($this->registry->core->getParam());
			if ($result == 1){
				App::getContainer()->get('session')->setActiveLoginError(null);
				App::getContainer()->get('session')->setActiveUserid($result);
				App::getModel('login')->checkInstanceIsValid();
				App::getModel('login')->setLoginTime();
				App::getModel('login')->getUserData();
				App::getModel('login')->setDefaultView($result);
				App::redirect(__ADMINPANE__ . '/mainside');
			}
		}
		
		if (App::getContainer()->get('session')->getActiveUserid() != null){
			App::redirect(__ADMINPANE__ . '/mainside');
		}
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'login',
			'action' => '',
			'method' => 'post',
			'class' => 'login-form',
			'csrf' => false
		));
		
		$form->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'login',
			'label' => $this->trans('TXT_EMAIL_FORM_LOGIN'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_EMAIL_FORM_LOGIN'))
			)
		)));
		
		$form->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'password',
			'label' => $this->trans('TXT_LOGIN_FORM_PASSWORD'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_LOGIN_FORM_PASSWORD'))
			)
		)));
		
		$form->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'autologin',
			'label' => $this->trans('TXT_AUTOLOGIN')
		)));

		$form->AddChild(new FormEngine\Elements\Submit(Array(
			'name' => 'log_in',
			'label' => $this->trans('TXT_LOG_IN')
		)));
		
		$form->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<a href="' . App::getURLAdress() . 'forgotlogin">' . $this->trans('TXT_LOGIN_FORM_RESET_PASSWORD') . '</a>'
		)));
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$loginValues = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			$result = App::getModel('login')->authProccess($loginValues['login'], $loginValues['password']);
			if ($result == 0){
				App::getContainer()->get('session')->setVolatileLoginError(1, false);
			}
			else{
				if (isset($loginValues['autologin']) && $loginValues['autologin'] == 1) {
					App::getModel('login')->setAdminAutologinKey($result);
				}
				else {
					App::getModel('login')->destroyAdminAutologinKey();
				}

				App::getContainer()->get('session')->setActiveLoginError(null);
				App::getContainer()->get('session')->setActiveUserid($result);
				App::getModel('login')->setLoginTime();
				App::getModel('login')->getUserData();
				App::getModel('login')->setDefaultView($result);
				App::getModel('login')->checkInstanceIsValid();
				App::redirect(__ADMINPANE__ . '/mainside');
			}
		}
		
		$error = App::getContainer()->get('session')->getVolatileLoginError();
		if ($error[0] == 1){
			$this->registry->template->assign('error', $this->trans('ERR_BAD_LOGIN_OR_PASSWORD'));
		}
		
		$languages = App::getModel('language')->getLanguages();
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->assign('languages', json_encode($languages));
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}
}
?>
