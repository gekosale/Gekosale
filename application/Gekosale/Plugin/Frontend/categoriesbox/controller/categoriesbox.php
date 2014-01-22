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

namespace Gekosale\Plugin;

class CategoriesBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
	}

	public function index ()
	{
		$include = '';
		if (! isset($this->_boxAttributes['showall'])){
			$showall = 1;
		}
		else{
			$showall = $this->_boxAttributes['showall'];
			$include = isset($this->_boxAttributes['categoryIds']) ? explode(',', $this->_boxAttributes['categoryIds']) : Array();
		}
		
		$showcount = (isset($this->_boxAttributes['showcount']) && $this->_boxAttributes['showcount'] == 1) ? 1 : 0;
		$hideempty = (isset($this->_boxAttributes['hideempty']) && $this->_boxAttributes['hideempty'] == 1) ? 1 : 0;
		
		if (($categories = App::getContainer()->get('cache')->load('categories')) === FALSE){
			$categories = App::getModel('CategoriesBox')->getCategoriesTree();
			App::getContainer()->get('cache')->save('categories', $categories);
		}

		$path = App::getModel('categoriesbox')->getCurrentCategoryPath($this->getParam());
		
		if ($this->registry->router->getCurrentController() == 'productcart'){
			$path = App::getModel('categoriesbox')->getCategoryPathForProductById($this->registry->core->getParam());
			foreach ($categories as $key => $category){
				if (in_array($category['id'], $path)){
					$categories[$key]['current'] = 1;
				}
				foreach ($category['children'] as $k => $subcategory){
					if (in_array($subcategory['id'], $path)){
						$categories[$key]['children'][$k]['current'] = 1;
					}
				}
			}
		}
		if (App::getContainer()->get('session')->getActiveForceLogin() == 1 && App::getContainer()->get('session')->getActiveClientid() == 0){
			$categories = Array();
		}
		
		$this->total = count($categories);
		$this->registry->template->assign('categories', $categories);
		$this->registry->template->assign('showcount', $showcount);
		$this->registry->template->assign('path', $path);
		$this->registry->template->assign('showall', $showall);
		$this->registry->template->assign('include', $include);
		$this->registry->template->assign('hideempty', $hideempty);
		$this->registry->template->assign('current', (int) $this->registry->core->getParam());
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function getBoxTypeClassname ()
	{
		return 'layout-box-type-categorymenu';
	}

}