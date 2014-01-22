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

class CustomProductListBoxModel extends Component\Model
{

	public function _addFieldsContentTypeCustomProductListBox ($form, $boxContent)
	{
		
		$ct_CustomProductListBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_CustomProductListBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		
		$ct_CustomProductListBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('CustomProductListBox')));
		
		$ct_CustomProductListBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Maks. liczba produktów na stronę',
			'comment' => 'Domyślnie 0 (wyświetla wszystkie produkty)'
		)));
		
		$ct_CustomProductListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Siatka'),
				new FormEngine\Option('1', 'Lista')
			)
		)));
		
		$ct_CustomProductListBoxOrderBy = $ct_CustomProductListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_CustomProductListBox_orderBy',
			'label' => 'Domyślne sortowanie',
			'options' => Array(
				new FormEngine\Option('id', 'ID produktu'),
				new FormEngine\Option('name', 'Nazwa'),
				new FormEngine\Option('price', 'Cena'),
				new FormEngine\Option('rating', 'Ocena klientów'),
				new FormEngine\Option('opinions', 'Ilość recenzji'),
				new FormEngine\Option('total', 'Ilość kupionych produktów'),
				new FormEngine\Option('adddate', 'Data dodania'),
				new FormEngine\Option('random', 'Losowo')
			)
		)));
		
		$ct_CustomProductListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_CustomProductListBox_orderDir',
			'label' => 'Kolejność sortowania',
			'options' => Array(
				new FormEngine\Option('asc', 'Rosnąco'),
				new FormEngine\Option('desc', 'Malejąco')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $ct_CustomProductListBoxOrderBy, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('random')))
			)
		)));
		
		$ct_CustomProductListBox->AddChild(new FormEngine\Elements\ProductSelect(Array(
			'name' => 'custom_products',
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE
		)));
	
	}

	public function _populateFieldsContentTypeCustomProductListBox (&$populate, $ctValues = Array())
	{
		$populate['ct_CustomProductListBox']['productsCount'] = '0';
		$populate['ct_CustomProductListBox']['view'] = '0';
		$populate['ct_CustomProductListBox']['ct_CustomProductListBox_orderBy'] = 'id';
		$populate['ct_CustomProductListBox']['ct_CustomProductListBox_orderDir'] = 'asc';
		isset($ctValues['productsCount']) and $populate['ct_CustomProductListBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['pagination']) and $populate['ct_CustomProductListBox']['pagination'] = (bool) $ctValues['pagination'];
		isset($ctValues['view']) and $populate['ct_CustomProductListBox']['view'] = $ctValues['view'];
		isset($ctValues['orderBy']) and $populate['ct_CustomProductListBox']['ct_CustomProductListBox_orderBy'] = $ctValues['orderBy'];
		isset($ctValues['orderDir']) and $populate['ct_CustomProductListBox']['ct_CustomProductListBox_orderDir'] = $ctValues['orderDir'];
		isset($ctValues['products']) and $populate['ct_CustomProductListBox']['custom_products'] = explode(',', $ctValues['products']);
		return $populate;
	}
}