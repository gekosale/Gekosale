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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class AttributeGroupForm extends Component\Form
{

	protected $populateData;
	
	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'attributegroup',
			'action' => '',
			'method' => 'post',
			'class' => 'attributeGroupEditor'
		));
		
		$groupData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'group_data',
			'class' => 'group-data',
			'label' => $this->trans('TXT_ATTRIBUTE_GROUP_DATA')
		)));
		
		$groupData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'attributegroupname',
			'label' => $this->trans('TXT_ATTRIBUTE_GROUP_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ATTRIBUTE_GROUP_NAME')),
				new FormEngine\Rules\Unique($this->trans('ERR_ATTRIBUTE_PRODUCT_GROUP_ALREADY_EXISTS'), 'attributegroupname', 'name', null, Array(
					'column' => 'idattributegroupname',
					'values' => (int)$this->registry->core->getParam()
				))
			)
		)));
		
		$category = $groupData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'category',
			'label' => $this->trans('TXT_CATEGORY'),
			'choosable' => false,
			'selectable' => true,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getChildCategories(),
			'load_children' => Array(
				App::getModel('category'),
				'getChildCategories'
			)
		)));
		
		$attributeData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'attribute_data',
			'class' => 'attribute-data',
			'label' => $this->trans('TXT_ATTRIBUTES')
		)));
		
		$attributeData->AddChild(new FormEngine\Elements\AttributeEditor(Array(
			'name' => 'attributes',
			'label' => $this->trans('TXT_ATTRIBUTES'),
			'set' => $this->registry->core->getParam()
		)));
		
		$Data = Event::dispatch($this, 'admin.attributegroup.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}

}