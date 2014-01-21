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
 * version 2.1 of the License, or (at your option) any later version.
 */

namespace Gekosale;

class ProducerBoxController  extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->producers = App::getModel('producerbox')->getProducerAll();
		$this->total = count($this->producers);
	}

	public function index ()
	{
		$this->registry->template->assign('producers', $this->producers);
		$this->registry->template->assign('activePath', App::getModel('staticcontent')->getActivePath());
		$this->registry->template->assign('view', $this->_boxAttributes['view']);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function getBoxTypeClassname ()
	{
		if ($this->total > 0){
			return 'layout-box-type-producer-list';
		}
	}

	public function boxVisible ()
	{
		return ($this->total > 0) ? true : false;
	}

}