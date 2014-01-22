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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: newsletterbox.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

use SimpleForm;

class NewsletterBoxController extends Component\Controller\Box
{

	public function index ()
	{
		$param = $this->registry->core->getParam();
		if (! empty($param) && $this->registry->router->getCurrentController() == 'newsletter'){
			$linkActive = App::getModel('newsletter')->checkLinkToActivate($param);
			if ($linkActive > 0){
				$change = App::getModel('newsletter')->changeNewsletterStatus($linkActive);
				$this->registry->template->assign('activelink', 1);
			}
			else{
				$inactiveLink = App::getModel('newsletter')->checkInactiveNewsletter($param);
				if ($inactiveLink > 0){
					App::getModel('newsletter')->deleteClientNewsletter($inactiveLink);
					$this->registry->template->assign('inactivelink', 1);
				}
				else{
					$this->registry->template->assign('errlink', 1);
				}
			}
		}
		
		$form = new SimpleForm\Form(Array(
			'name' => 'newsletter',
			'action' => '',
			'method' => 'post'
		));
		
		$action = $form->AddChild(new SimpleForm\Elements\Radio(Array(
			'name' => 'action',
			'options' => Array(
				'1' => $this->trans('TXT_SIGNUP'),
				'2' => $this->trans('TXT_REMOVE_SUBSCRIPTION')
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		$url = $this->registry->router->generate('frontend.conditions', true);
		
		$form->AddChild(new SimpleForm\Elements\Checkbox(Array(
			'name' => 'confirmterms',
			'label' => $this->trans('TXT_NEWSLETTER_ACCEPT') . ' <a href="' . $url . '" target="_blank">' . $this->trans('TXT_NEWSLETTER_CONDITIONS') . '</a>',
			'rules' => Array(
				new SimpleForm\Rules\RequiredDependency($this->trans('TXT_NEWSLETTER_ACCEPT_CONDITIONS'), $action, new SimpleForm\Conditions\Equals('1'))
			),
			'default' => 0
		)));
		
		$form->Populate(Array(
			'action' => 1
		));
		
		if ($form->Validate()){
			$formData = $form->getSubmitValues();
			$this->model = App::getModel('newsletter');
			
			if ($formData['action'] == 1){
				
				$checkEmailExists = $this->model->checkEmailIfExists($formData['email']);
				
				if ($checkEmailExists > 0){
					$this->registry->template->assign('signup_error', $this->trans('ERR_EMAIL_NOT_EXISTS'));
				}
				else{
					$newId = $this->model->addClientAboutNewsletter($formData['email']);
					if ($newId > 0){
						$this->model->updateNewsletterActiveLink($newId, $formData['email']);
					}
					$this->registry->template->assign('signup_success', $this->trans('TXT_RECEIVE_EMAIL_WITH_ACTIVE_LINK'));
				}
			}
			
			if ($formData['action'] == 2){
				$checkEmailExists = $this->model->checkEmailIfExists($formData['email']);
				if ($checkEmailExists > 0){
					$this->model->unsetClientAboutNewsletter($checkEmailExists, $formData['email']);
					$this->registry->template->assign('signup_success', $this->trans('TXT_RECEIVE_EMAIL_WITH_DEACTIVE_LINK'));
				}
				else{
					$this->registry->template->assign('signup_error', $this->trans('ERR_EMAIL_NO_EXIST'));
				}
			}
		}
		
		$this->registry->template->assign('newsletter', $form->getForm());
		
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}