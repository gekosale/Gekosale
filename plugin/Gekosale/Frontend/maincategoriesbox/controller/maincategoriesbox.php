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
 */
namespace Gekosale;

class MainCategoriesBoxController extends Component\Controller\Box
{

	public function index ()
	{
		if (isset($this->_boxAttributes['showall']) && $this->_boxAttributes['showall'] == 0 && isset($this->_boxAttributes['categoryIds']) && ! empty($this->_boxAttributes['categoryIds'])){
			$this->registry->template->assign('exclude', $this->_boxAttributes['categoryIds']);
		}
		else{
			$this->registry->template->assign('exclude', Array());
		}
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function getBoxTypeClassname ()
	{
		return 'layout-box-type-product-list';
	}
}