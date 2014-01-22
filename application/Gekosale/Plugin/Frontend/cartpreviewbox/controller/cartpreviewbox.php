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
 * $Id: cartpreviewbox.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Plugin;

class CartPreviewBoxController extends Component\Controller\Box
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'RefreshCart',
			$this,
			'ajax_refreshCart'
		));
		$this->registry->template->assign('cart', $this->getCartTemplate());
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	protected function getCartTemplate ()
	{
		$qty = App::getModel('cart/cart')->getProductAllCount();
		$result = $this->registry->template->fetch($this->loadTemplate('items.tpl'));
		return $result;
	}

	public function ajax_refreshCart ()
	{
		$objResponse = new xajaxResponse();
		$objResponse->clear("cart-contents", "innerHTML");
		$objResponse->append("cart-contents", "innerHTML", $this->getCartTemplate());
		return $objResponse;
	}

	public function getBoxTypeClassname ()
	{
		return 'layout-box-type-cart-summary';
	}
}
?>