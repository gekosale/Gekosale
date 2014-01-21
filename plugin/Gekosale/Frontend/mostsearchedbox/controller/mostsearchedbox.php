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
 *
 *
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: tagsbox.php 576 2011-10-22 08:23:55Z gekosale $
 */

namespace Gekosale;

class MostSearchedBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->phrases = App::getModel('MostSearchedBox')->getAllMostSearched();
		$this->total = count($this->phrases);
	}

	public function index ()
	{
		foreach ($this->phrases as $key => $tag){
			$max[] = $tag['textcount'];
		}
		foreach ($this->phrases as $key => $tag){
			$search[$key]['percentage'] = ceil(($tag['textcount'] / max($max)) * 10);
		}
		
		$this->registry->template->assign('mostsearched', $this->phrases);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function boxVisible ()
	{
		return ($this->total > 0) ? true : false;
	}
}