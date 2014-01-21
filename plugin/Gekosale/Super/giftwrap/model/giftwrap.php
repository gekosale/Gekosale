<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

use xajaxResponse;

class GiftWrapModel extends Component\Model
{

	public function getGiftWrap ()
	{
		$sql = "SELECT 
					enablegiftwrap,
					giftwrapproduct
				FROM view
				WHERE idview= :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$viewid = 0;
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			if ($rs['enablegiftwrap'] == 1 && $rs['giftwrapproduct'] != NULL){
				App::getContainer()->get('session')->setActiveGiftWrapProduct($rs['giftwrapproduct']);
				$Data = Array(
					'product' => App::getModel('product')->getProductAndAttributesById($rs['giftwrapproduct'], 1),
					'active' => (int) App::getContainer()->get('session')->getActiveGiftWrap(),
					'message' => App::getContainer()->get('session')->getActiveGiftWrapMessage()
				);
			}
		}
		return $Data;
	}

	public function addGiftWrap ($message)
	{
		$objResponse = new xajaxResponse();
		App::getContainer()->get('session')->setActiveGiftWrap(1);
		App::getContainer()->get('session')->setActiveGiftWrapMessage($message);
		App::getModel('cart')->deleteProductCart(App::getContainer()->get('session')->getActiveGiftWrapProduct());
		App::getModel('cart')->cartAddStandardProduct(App::getContainer()->get('session')->getActiveGiftWrapProduct(), 1);
		$objResponse->script('window.location.reload(false);');
		return $objResponse;
	}

	public function deleteGiftWrap ()
	{
		$objResponse = new xajaxResponse();
		$this->unsetGiftWrapData();
		App::getModel('cart')->deleteProductCart(App::getContainer()->get('session')->getActiveGiftWrapProduct());
		App::getModel('cart')->updateSession();
		$objResponse->script('window.location.reload(false);');
		return $objResponse;
	}

	public function unsetGiftWrapData ()
	{
		App::getContainer()->get('session')->setActiveGiftWrap(0);
		App::getContainer()->get('session')->setActiveGiftWrapMessage('');
	}
}