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

class ProductDescriptionBoxModel extends Component\Model
{

	public function _addFieldsContentTypeProductDescriptionBox ($form, $boxContent)
	{
		
		$ct_GraphicsBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProductDescriptionBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_GraphicsBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProductDescriptionBox')));
		
		$ct_GraphicsBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'tabbed',
			'label' => 'Używaj zakładek w karcie produktu',
			'options' => Array(
				new FormEngine\Option(1, 'Tak'),
				new FormEngine\Option(0, 'Nie')
			)
		)));
	}

	public function _populateFieldsContentTypeProductDescriptionBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProductDescriptionBox']['tabbed'] = '1';
		isset($ctValues['tabbed']) and $populate['ct_ProductDescriptionBox']['tabbed'] = $ctValues['tabbed'];
		return $populate;
	}
}