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
 * $Revision: 547 $
 * $Author: gekosale $
 * $Date: 2011-09-27 08:51:30 +0200 (Wt, 27 wrz 2011) $
 * $Id: textbox.php 547 2011-09-27 06:51:30Z gekosale $
 */
namespace Gekosale\Plugin;

class TextBoxController extends Component\Controller\Box
{

	public function index ()
	{
		$this->registry->template->assign('content', isset($this->_boxAttributes['content']) ? $this->registry->template->parse($this->_boxAttributes['content']) : '');
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}
}