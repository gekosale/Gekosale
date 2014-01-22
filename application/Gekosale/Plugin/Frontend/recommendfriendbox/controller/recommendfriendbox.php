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
 * $Id: recommendfriendbox.php 484 2011-09-07 11:42:04Z gekosale $
 */

namespace Gekosale\Plugin;
use SimpleForm;

class RecommendFriendBoxController extends Component\Controller\Box
{

	public function index ()
	{
		$form = new SimpleForm\Form(Array(
			'name' => 'recommendform',
			'action' => '',
			'method' => 'post'
		));

		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'fromname',
			'label' => $this->trans('TXT_YOUR_NAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));

		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'fromemail',
			'label' => $this->trans('TXT_YOUR_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));

		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'friendemail',
			'label' => $this->trans('TXT_FRIEND_EMAIL'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FRIEND_EMAIL')),
				new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));

		$form->AddChild(new SimpleForm\Elements\TextArea(Array(
			'name' => 'content',
			'label' => $this->trans('TXT_CONTENT'),
			'rows' => 10,
			'cols' => 72,
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_CONTENT_TEXTAREA'))
			)
		)));

		$this->registry->template->assign('form', $form->getForm());


		if ($form->validate()){
			$formData = $form->getSubmitValues();
			$clean = App::getModel('formprotection')->cropDangerousCodeSubmitedValues($formData);
			if ($clean == true){
				$getURL = App::getModel('recommendfriendbox')->getPageURL();
				$clientModel = App::getModel('client');
				$clientdata = $clientModel->getClient();
				$this->registry->template->assign('recommendurl', $getURL);
				$this->registry->template->assign('fromname', $formData['fromname']);
				$this->registry->template->assign('fromemail', $formData['fromemail']);
				$this->registry->template->assign('comment', $formData['content']);

				App::getModel('mailer')->sendEmail(Array(
					'template' => 'recommendfriend',
					'email' => Array(
						$formData['friendemail']
					),
					'bcc' => false,
					'subject' => $this->trans('TXT_RECOMMENDATION'),
					'viewid' => Helper::getViewId()
				));
				App::getContainer()->get('session')->setVolatileRecommendationMessage('Wiadomość została wysłana');

			}
		}

		if(($message = App::getContainer()->get('session')->getVolatileRecommendationMessage()) && ! is_array($message)) {
			$this->registry->template->assign('message', $message);
		}

		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}