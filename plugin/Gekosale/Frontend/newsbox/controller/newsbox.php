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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: newsbox.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;

class NewsBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		if ((int) $this->registry->core->getParam() > 0 && $this->registry->router->getCurrentController() == 'news'){
			$this->news = App::getModel('News')->getNewsById((int) $this->registry->core->getParam());
			$this->_heading = $this->news['topic'];
			$this->_template = 'view.tpl';
		}
		else{
			if (($this->news = App::getContainer()->get('cache')->load('news')) === FALSE){
				$this->news = App::getModel('News')->getNews();
				App::getContainer()->get('cache')->save('news', $this->news);
			}
			$this->_heading = $this->trans('TXT_NEWS');
			$this->_template = 'index.tpl';
		}
	}

	public function index ()
	{
		$this->registry->template->assign('news', $this->news);
		return $this->registry->template->fetch($this->loadTemplate($this->_template));
	}
}