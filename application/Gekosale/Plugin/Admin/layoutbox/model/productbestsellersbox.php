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

class ProductBestsellersBoxModel extends Component\Model
{

	public function _addFieldsContentTypeProductBestsellersBox ($form, $boxContent)
	{
		
		$ct_ProductBestsellersBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProductBestsellersBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		
		$ct_ProductBestsellersBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProductBestsellersBox')));
		
		$ct_ProductBestsellersBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'minProductsCount',
			'label' => 'Minimalna liczba produktów',
			'comment' => 'Liczba przy której boks zostanie pokazany'
		)));
		
		$ct_ProductBestsellersBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Liczba wyświetlanych produktów',
			'comment' => 'Domyślnie 0 (wyświetla wszystkie produkty)'
		)));
		
		$ct_ProductBestsellersBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Siatka'),
				new FormEngine\Option('1', 'Lista')
			)
		)));
		
		$ct_ProductBestsellersBoxOrderBy = $ct_ProductBestsellersBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ProductBestsellersBox_orderBy',
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
		
		$ct_ProductBestsellersBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ProductBestsellersBox_orderDir',
			'label' => 'Kolejność sortowania',
			'options' => Array(
				new FormEngine\Option('asc', 'Rosnąco'),
				new FormEngine\Option('desc', 'Malejąco')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $ct_ProductBestsellersBoxOrderBy, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('random')))
			)
		)));
	
	}

	public function _populateFieldsContentTypeProductBestsellersBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProductBestsellersBox']['productsCount'] = '0';
		$populate['ct_ProductBestsellersBox']['minProductsCount'] = '0';
		$populate['ct_ProductBestsellersBox']['view'] = '0';
		$populate['ct_ProductBestsellersBox']['ct_ProductBestsellersBox_orderBy'] = 'id';
		$populate['ct_ProductBestsellersBox']['ct_ProductBestsellersBox_orderDir'] = 'asc';
		isset($ctValues['productsCount']) and $populate['ct_ProductBestsellersBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['minProductsCount']) and $populate['ct_ProductBestsellersBox']['minProductsCount'] = $ctValues['minProductsCount'];
		isset($ctValues['view']) and $populate['ct_ProductBestsellersBox']['view'] = $ctValues['view'];
		isset($ctValues['orderBy']) and $populate['ct_ProductBestsellersBox']['ct_ProductBestsellersBox_orderBy'] = $ctValues['orderBy'];
		isset($ctValues['orderDir']) and $populate['ct_ProductBestsellersBox']['ct_ProductBestsellersBox_orderDir'] = $ctValues['orderDir'];
		return $populate;
	}
}