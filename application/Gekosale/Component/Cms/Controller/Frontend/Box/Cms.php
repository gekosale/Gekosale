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
 * $Id: cmsbox.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Component\Cms\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

class Cms extends Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->model = App::getModel('staticcontent');

	}

	public function index ()
	{
		$cms = $this->model->getStaticContent((int) $this->registry->core->getParam());
		if (empty($cms)){
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
		$this->registry->template->assign('cms', $cms);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function getBoxHeading ()
	{
		$cms = $this->model->getBoxHeadingName((int) $this->registry->core->getParam());
		if (isset($cms['name'])){
			return $cms['name'];
		}
		else{
			return $this->trans('ERR_CMS_NO_EXIST');
		}
	}
}