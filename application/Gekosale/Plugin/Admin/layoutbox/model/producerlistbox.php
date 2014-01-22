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

namespace Gekosale\Plugin;
use FormEngine;

class ProducerListBoxModel extends Component\Model
{

	public function _addFieldsContentTypeProducerListBox ($form, $boxContent)
	{
		
		$ct_ProducerListBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProducerListBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_ProducerListBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProducerListBox')));
		
		$ct_ProducerListBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Maks. liczba produktów na stronę',
			'comment' => 'Domyślnie 0 (wyświetla wszystkie produkty)'
		)));
		
		$ct_ProducerListBox->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'pagination',
			'label' => $this->trans('TXT_PAGINATION')
		)));
		
		$ct_ProducerListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Siatka'),
				new FormEngine\Option('1', 'Lista')
			)
		)));
		
	}

	public function _populateFieldsContentTypeProducerListBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProducerListBox']['productsCount'] = '0';
		$populate['ct_ProducerListBox']['pagination'] = false;
		$populate['ct_ProducerListBox']['view'] = '0';
		isset($ctValues['productsCount']) and $populate['ct_ProducerListBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['pagination']) and $populate['ct_ProducerListBox']['pagination'] = (bool) $ctValues['pagination'];
		isset($ctValues['view']) and $populate['ct_ProducerListBox']['view'] = $ctValues['view'];
		return $populate;
	}
}