<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
//  * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: productnewsbox.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale\Plugin;

class ProductCombinationBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->productid = App::getModel('product')->getProductIdBySeo($this->getParam());
		$this->combinationlist = App::getModel('productcombination')->getCombinationListForProduct($this->productid);
	}

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doQuickAddCombinationCart',
			App::getModel('cart'),
			'doQuickAddCombinationCart'
		));
		
		$this->registry->template->assign('combinationlist', $this->combinationlist);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function getBoxTypeClassname ()
	{
		return 'layout-box-type-product-list';
	}

	public function boxVisible ()
	{
		return ! empty($this->combinationlist);
	}
}