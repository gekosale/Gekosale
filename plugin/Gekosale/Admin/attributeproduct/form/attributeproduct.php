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

namespace Gekosale;
use FormEngine;

class AttributeProductForm extends Component\Form
{

	protected $populateData;
	
	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'attributeproduct',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'attributeproductname',
			'label' => $this->trans('TXT_ATTRIBUTE_PRODUCT_GROUP_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ATTRIBUTE_PRODUCT_GROUP')),
				new FormEngine\Rules\Unique($this->trans('ERR_ATTRIBUTE_PRODUCT_GROUP_ALREADY_EXISTS'), 'attributeproduct', 'name', null, Array(
					'column' => 'idattributeproduct',
					'values' => $this->registry->core->getParam()
				))
			)
		)));
		
		$attributesData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'attributes_data',
			'label' => $this->trans('TXT_ATTRIBUTES_DATA')
		)));
		
		$attributesData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'attributeproductvalues',
			'label' => $this->trans('TXT_ATTRIBUTE_PRODUCT'),
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE,
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ATTRIBUTE_PRODUCT'))
			)
		)));
		
		$Data = Event::dispatch($this, 'admin.attributeproduct.initForm', Array(
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