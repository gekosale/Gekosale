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
 * $Revision: 624 $
 * $Author: gekosale $
 * $Date: 2012-01-20 20:53:04 +0100 (Pt, 20 sty 2012) $
 * $Id: graphicsbox.php 624 2012-01-20 19:53:04Z gekosale $
 */

namespace Gekosale\Plugin;

class GraphicsBoxController extends Component\Controller\Box
{

	public function index ()
	{
		if (substr($this->_boxAttributes['url'], 0, 4) == 'http'){
			$url = $this->_boxAttributes['url'];
		}
		elseif (substr($this->_boxAttributes['url'], 0, 3) == 'www'){
			$url = 'http://' . $this->_boxAttributes['url'];
		}
		else if( !empty($this->_boxAttributes['url'])){
			$url = App::getURLAdress() . $this->_boxAttributes['url'];
		}
		else {
			$url = '';
		}

		$this->registry->template->assign('url', $url);
		$this->registry->template->assign('height', $this->_boxAttributes['height']);
		$url = str_replace('/design', '', DESIGNPATH);
		$this->_style = "height: {$this->_boxAttributes['height']}px;cursor:hand; background: url('{$url}{$this->_boxAttributes['image']}') {$this->_boxAttributes['align']} no-repeat;";
		$this->registry->template->assign('style', $this->_style);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

}