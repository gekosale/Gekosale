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

class ProductNewsBoxModel extends Component\Model
{

	public function _addFieldsContentTypeProductNewsBox ($form, $boxContent)
	{
		
		$ct_ProductNewsBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProductNewsBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_ProductNewsBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProductNewsBox')));
		
		$ct_ProductNewsBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Maks. liczba produktów na stronę',
			'comment' => 'Domyślnie 0 (wyświetla wszystkie produkty)'
		)));
		
		$ct_ProductNewsBox->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'pagination',
			'label' => $this->trans('TXT_PAGINATION')
		)));
		
		$ct_ProductNewsBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Siatka'),
				new FormEngine\Option('1', 'Lista')
			)
		)));
		
		$ct_ProductNewsBoxOrderBy = $ct_ProductNewsBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ProductNewsBox_orderBy',
			'label' => 'Domyślne sortowanie',
			'options' => Array(
				new FormEngine\Option('id', 'ID produktu'),
				new FormEngine\Option('name', 'Nazwa'),
				new FormEngine\Option('price', 'Cena'),
				new FormEngine\Option('rating', 'Ocena klientów'),
				new FormEngine\Option('opinions', 'Ilość recenzji'),
				new FormEngine\Option('adddate', 'Data dodania'),
				new FormEngine\Option('random', 'Losowo')
			)
		)));
		
		$ct_ProductNewsBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ProductNewsBox_orderDir',
			'label' => 'Kolejność sortowania',
			'options' => Array(
				new FormEngine\Option('asc', 'Rosnąco'),
				new FormEngine\Option('desc', 'Malejąco')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $ct_ProductNewsBoxOrderBy, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('random')))
			)
		)));
	
	}

	public function _populateFieldsContentTypeProductNewsBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProductNewsBox']['productsCount'] = '0';
		$populate['ct_ProductNewsBox']['pagination'] = false;
		$populate['ct_ProductNewsBox']['view'] = '0';
		$populate['ct_ProductNewsBox']['ct_ProductNewsBox_orderBy'] = 'id';
		$populate['ct_ProductNewsBox']['ct_ProductNewsBox_orderDir'] = 'asc';
		isset($ctValues['productsCount']) and $populate['ct_ProductNewsBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['pagination']) and $populate['ct_ProductNewsBox']['pagination'] = (bool) $ctValues['pagination'];
		isset($ctValues['view']) and $populate['ct_ProductNewsBox']['view'] = $ctValues['view'];
		isset($ctValues['orderBy']) and $populate['ct_ProductNewsBox']['ct_ProductNewsBox_orderBy'] = $ctValues['orderBy'];
		isset($ctValues['orderDir']) and $populate['ct_ProductNewsBox']['ct_ProductNewsBox_orderDir'] = $ctValues['orderDir'];
		return $populate;
	}
}