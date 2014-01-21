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
 * $Revision: 520 $
 * $Author: gekosale $
 * $Date: 2011-09-08 13:37:54 +0200 (Cz, 08 wrz 2011) $
 * $Id: layoutbox.php 520 2011-09-08 11:37:54Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class MainCategoriesBoxModel extends Component\Model
{

	public function _addFieldsContentTypeMainCategoriesBox ($form, $boxContent)
	{
		
		$ct_MainCategoriesBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_MainCategoriesBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		
		$ct_MainCategoriesBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('MainCategoriesBox')));
		
		$showall = $ct_MainCategoriesBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'showall',
			'label' => 'Pokazuj kategorie',
			'options' => Array(
				new FormEngine\Option('1', 'Wszystkie'),
				new FormEngine\Option('0', 'Wybrane')
			)
		)));
		
		$ct_MainCategoriesBox->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categoryIds',
			'label' => 'Kategoria',
			'selectable' => true,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getParentCategories(),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $showall, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals(1)))
			)
		)));
		
	}

	public function _populateFieldsContentTypeMainCategoriesBox (&$populate, $ctValues = Array())
	{
		$populate['ct_MainCategoriesBox']['showall'] = 1;
		$populate['ct_MainCategoriesBox']['categoryIds'] = Array();
		isset($ctValues['showall']) and $populate['ct_MainCategoriesBox']['showall'] = $ctValues['showall'];
		isset($ctValues['categoryIds']) and $populate['ct_MainCategoriesBox']['categoryIds'] = explode(',', $ctValues['categoryIds']);
		return $populate;
	}

}