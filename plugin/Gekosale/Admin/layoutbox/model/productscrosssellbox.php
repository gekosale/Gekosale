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

class ProductsCrossSellBoxModel extends Component\Model
{
	
	/*
	 * CONTENT-TYPE: PRODUCTS CROSS SELL
	 */
	public function _addFieldsContentTypeProductsCrossSellBox ($form, $boxContent)
	{
		$ct_ProductsCrossSellBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProductsCrossSellBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_ProductsCrossSellBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProductsCrossSellBox')));
		
		$ct_ProductsCrossSellBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Liczba wyświetlanych produktów',
			'comment' => 'Domyślnie 0 (wyświetla wszystkie produkty)'
		)));
		
		$ct_ProductsCrossSellBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Siatka'),
				new FormEngine\Option('1', 'Lista')
			)
		)));
		
		$ct_ProductsCrossSellBoxOrderBy = $ct_ProductsCrossSellBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ProductsCrossSellBox_orderBy',
			'label' => 'Domyślne sortowanie',
			'options' => Array(
				new FormEngine\Option('related', 'Domyślnie'),
				new FormEngine\Option('id', 'ID produktu'),
				new FormEngine\Option('name', 'Nazwa'),
				new FormEngine\Option('price', 'Cena'),
				new FormEngine\Option('rating', 'Ocena klientów'),
				new FormEngine\Option('opinions', 'Ilość recenzji'),
				new FormEngine\Option('adddate', 'Data dodania'),
				new FormEngine\Option('random', 'Losowo')
			)
		)));
		
		$ct_ProductsCrossSellBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ProductsCrossSellBox_orderDir',
			'label' => 'Kolejność sortowania',
			'options' => Array(
				new FormEngine\Option('asc', 'Rosnąco'),
				new FormEngine\Option('desc', 'Malejąco')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $ct_ProductsCrossSellBoxOrderBy, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('random')))
			)
		)));
	}

	public function _populateFieldsContentTypeProductsCrossSellBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProductsCrossSellBox']['productsCount'] = '0';
		$populate['ct_ProductsCrossSellBox']['view'] = '0';
		$populate['ct_ProductsCrossSellBox']['ct_ProductsCrossSellBox_orderBy'] = 'id';
		$populate['ct_ProductsCrossSellBox']['ct_ProductsCrossSellBox_orderDir'] = 'asc';
		isset($ctValues['productsCount']) and $populate['ct_ProductsCrossSellBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['view']) and $populate['ct_ProductsCrossSellBox']['view'] = $ctValues['view'];
		isset($ctValues['orderBy']) and $populate['ct_ProductsCrossSellBox']['ct_ProductsCrossSellBox_orderBy'] = $ctValues['orderBy'];
		isset($ctValues['orderDir']) and $populate['ct_ProductsCrossSellBox']['ct_ProductsCrossSellBox_orderDir'] = $ctValues['orderDir'];
		return $populate;
	}
}