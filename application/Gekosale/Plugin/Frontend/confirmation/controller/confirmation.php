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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: confirmation.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class ConfirmationController extends Component\Controller\Frontend
{

	public function index ()
	{
		$URLOrderLink = $this->getParam();
		if (isset($URLOrderLink) && $URLOrderLink != NULL){
			$isValidURL = App::getModel('confirmation')->getURLParamToValidOrderLink($URLOrderLink);
			if (is_array($isValidURL) && $isValidURL != NULL){
				$upateOrder = App::getModel('confirmation')->changeOrderStatus($isValidURL['idorder'], $isValidURL['paymentmethodid']);
				$this->registry->template->assign('upateOrder', $upateOrder);
				$this->registry->template->assign('isValidURL', $isValidURL);
			}
		}
		else{
			$isValidURL = 0;
			$this->registry->template->assign('isValidURL', $isValidURL);
		}
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}