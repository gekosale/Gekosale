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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: contactbox.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;

use SimpleForm;

class ContactBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		if ((int) $this->registry->core->getParam() > 0){
			$this->product = App::getModel('product')->getProductAndAttributesById((int) $this->registry->core->getParam());
			
			if (empty($this->product)){
				App::redirectUrl($this->registry->router->generate('frontend.home', true));
			}
		}
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	public function index ()
	{
		$contacts = App::getModel('Contact')->getContactToSelect();
		
		$contactList = App::getModel('Contact')->getContactList();
		
		$form = new SimpleForm\Form(array(
			'name' => 'contactform',
			'action' => '',
			'method' => 'post'
		));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
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
		
		$form->AddChild(new SimpleForm\Elements\TextField(Array(
			'name' => 'phone',
			'label' => $this->trans('TXT_PHONE')
		)));
		
		$form->AddChild(new SimpleForm\Elements\Select(Array(
			'name' => 'contactsubject',
			'label' => $this->trans('TXT_DEPARTMENT'),
			'options' => $this->registry->core->getDefaultValueToSelect() + $contacts
		)));
		
		if ((int) $this->registry->core->getParam() == 0){
			$form->AddChild(new SimpleForm\Elements\TextField(Array(
				'name' => 'topic',
				'label' => $this->trans('TXT_TOPIC'),
				'rules' => Array(
					new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_TOPIC'))
				)
			)));
		}
		
		$form->AddChild(new SimpleForm\Elements\TextArea(Array(
			'name' => 'content',
			'label' => $this->trans('TXT_CONTENT'),
			'rows' => 10,
			'cols' => 100,
			'rules' => Array(
				new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_CONTACT_CONTENT'))
			)
		)));
		
		$client = App::getModel('client')->getClient();
		
		if (! empty($client)){
			$form->Populate(Array(
				'firstname' => $client['firstname'],
				'surname' => $client['surname'],
				'phone' => $client['phone'],
				'email' => $client['email']
			));
		}
		
		if ($form->Validate()){
			$formData = $form->getSubmitValues();
			
			if (! isset($_POST['confirmemail']) || (isset($_POST['confirmemail']) && strlen($_POST['confirmemail']) > 0)){
				App::redirectUrl($this->registry->router->generate('frontend.contact', true));
			}
			
			$emails = Array(
				$formData['email']
			);
			
			if (isset($formData['contactsubject']) && (int) $formData['contactsubject'] > 0){
				$emails[] = App::getModel('Contact')->getDepartmentMail($formData['contactsubject']);
			}
			
			$content = $formData['content'];
			
			if ((int) $this->registry->core->getParam() > 0){
				$subject = $this->trans('TXT_PRODUCT_QUOTE') . ' ' . $this->product['productname'];
				
				$this->registry->template->assign('productLink', $this->registry->router->generate('frontend.productcart', true, Array(
					'param' => $this->product['seo']
				)));
			}
			else{
				$subject = $formData['topic'];
			}
			
			$this->registry->template->assign('CONTACT_CONTENT', $formData['content']);
			$this->registry->template->assign('firstname', $formData['firstname']);
			$this->registry->template->assign('surname', $formData['surname']);
			$this->registry->template->assign('email', $formData['email']);
			$this->registry->template->assign('phone', $formData['phone']);
			
			App::getModel('mailer')->sendEmail(Array(
				'template' => 'contact',
				'email' => $emails,
				'bcc' => true,
				'subject' => $subject,
				'viewid' => Helper::getViewId()
			));
			
			App::getContainer()->get('session')->setVolatileSendContact(1, false);
			
			App::redirectUrl($this->registry->router->generate('frontend.contact', true));
		}
		
		$sendContact = App::getContainer()->get('session')->getVolatileSendContact();
		if ($sendContact[0] == 1){
			$this->registry->template->assign('sendContact', $this->trans('TXT_CONTACT_SENT'));
		}
		$this->registry->template->assign('form', $form->getForm());
		if ((int) $this->registry->core->getParam() > 0){
			$this->registry->template->assign('productid', $this->getParam());
		}
		$footerJs = "
		<script>
			$(document).ready(function(){
				$('#contactform').append('<input type=\"text\" name=\"confirmemail\" value=\"\" style=\"display:none;\" />');
			});
		</script>";
		$footerJs .= App::getModel('staticcontent')->renderPolicyJS();
		
		$this->registry->template->assign('content', App::getModel('staticcontent')->getContentByRoute('frontend.contact'));
		$this->registry->template->assign('contactList', $contactList);
		$this->registry->template->assign('footerJS', $footerJs);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function getBoxHeading ()
	{
		if (isset($this->product) && (int) $this->registry->core->getParam() > 0){
			return $this->trans('TXT_PRODUCT_QUOTE') . ' ' . $this->product['productname'];
		}
		else{
			return $this->_heading;
		}
	}
}