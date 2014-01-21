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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: clientorderbox.php 612 2011-11-28 20:02:10Z gekosale $
 */
namespace Gekosale;

use SimpleForm;

class ClientOrderBoxController extends Component\Controller\Box
{

	public function index ()
	{
		if (App::getContainer()->get('session')->getActiveClientid() == NULL){
			
			$form = new SimpleForm\Form(array(
				'name' => 'order',
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
			
			$form->AddChild(new SimpleForm\Elements\TextField(Array(
				'name' => 'orderid',
				'label' => $this->trans('TXT_ORDER_NUMER'),
				'rules' => Array(
					new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_ORDER_ID'))
				)
			)));
			
			if ($form->Validate()){
				$formData = $form->getSubmitValues();
				$order = App::getModel('order')->getOrderStatusByEmailAndId($formData['email'], $formData['orderid']);
				if ($order != NULL){
					$this->registry->template->assign('status', Array(
						'orderid' => $formData['orderid'],
						'name' => $order
					));
				}
				else{
					$this->registry->template->assign('status', NULL);
				}
			}
			
			$this->registry->template->assign('form', $form->getForm());
			return $this->registry->template->fetch($this->loadTemplate('check.tpl'));
		}
		else{
			if ((int) $this->registry->core->getParam() > 0){
				$order = App::getModel('order')->getOrderByClient((int) $this->registry->core->getParam());
				if (empty($order)){
					App::redirectUrl($this->registry->router->generate('frontend.clientorder', true));
				}
				$this->registry->template->assign('order', $order);
				$this->registry->template->assign('orderproductlist', App::getModel('order')->getOrderProductListByClient((int) $this->registry->core->getParam()));
				return $this->registry->template->fetch($this->loadTemplate('view.tpl'));
			}
			else{
				$this->registry->template->assign('orderlist', App::getModel('order')->getOrderListByClient());
				return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
			}
		}
	}
}