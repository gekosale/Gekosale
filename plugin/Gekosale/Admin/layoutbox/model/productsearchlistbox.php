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

class ProductSearchListBoxModel extends Component\Model
{

	public function _addFieldsContentTypeProductSearchListBox ($form, $boxContent)
	{
		
		$ct_ProductSearchListBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProductSearchListBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_ProductSearchListBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProductSearchListBox')));
		
		$ct_ProductSearchListBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Maks. liczba produktów na stronę',
			'comment' => 'Domyślnie 0 (wyświetla wszystkie produkty)'
		)));
		
		$ct_ProductSearchListBox->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'pagination',
			'label' => $this->trans('TXT_PAGINATION')
		)));
		
		$ct_ProductSearchListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Siatka'),
				new FormEngine\Option('1', 'Lista')
			)
		)));
		
		$ct_ProductSearchListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'orderBy',
			'label' => 'Domyślne sortowanie',
			'options' => Array(
				new FormEngine\Option('id', 'ID produktu'),
				new FormEngine\Option('name', 'Nazwa'),
				new FormEngine\Option('price', 'Cena'),
				new FormEngine\Option('rating', 'Ocena klientów'),
				new FormEngine\Option('opinions', 'Ilość recenzji'),
				new FormEngine\Option('adddate', 'Data dodania')
			)
		)));
		
		$ct_ProductSearchListBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'orderDir',
			'label' => 'Kolejność sortowania',
			'options' => Array(
				new FormEngine\Option('asc', 'Rosnąco'),
				new FormEngine\Option('desc', 'Malejąco')
			)
		)));
	
	}

	public function _populateFieldsContentTypeProductSearchListBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProductSearchListBox']['productsCount'] = '0';
		$populate['ct_ProductSearchListBox']['pagination'] = false;
		$populate['ct_ProductSearchListBox']['view'] = '0';
		$populate['ct_ProductSearchListBox']['orderBy'] = 'id';
		$populate['ct_ProductSearchListBox']['orderDir'] = 'asc';
		isset($ctValues['productsCount']) and $populate['ct_ProductSearchListBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['pagination']) and $populate['ct_ProductSearchListBox']['pagination'] = (bool) $ctValues['pagination'];
		isset($ctValues['view']) and $populate['ct_ProductSearchListBox']['view'] = $ctValues['view'];
		isset($ctValues['orderBy']) and $populate['ct_ProductSearchListBox']['orderBy'] = $ctValues['orderBy'];
		isset($ctValues['orderDir']) and $populate['ct_ProductSearchListBox']['orderDir'] = $ctValues['orderDir'];
		return $populate;
	}
}