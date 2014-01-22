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

class ShowcaseBoxModel extends Component\Model
{

	public function _addFieldsContentTypeShowcaseBox ($form, $boxContent)
	{
		
		$ct_ShowcaseBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ShowcaseBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_ShowcaseBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ShowcaseBox')));
		
		$ct_ShowcaseBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'productsCount',
			'label' => 'Maks. liczba produktów',
			'comment' => 'Domyślnie 0 (bez ograniczenia)'
		)));
		
		$ct_ShowcaseBoxOrderBy = $ct_ShowcaseBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ShowcaseBox_orderBy',
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
		
		$ct_ShowcaseBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'ct_ShowcaseBox_orderDir',
			'label' => 'Kolejność sortowania',
			'options' => Array(
				new FormEngine\Option('asc', 'Rosnąco'),
				new FormEngine\Option('desc', 'Malejąco')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $ct_ShowcaseBoxOrderBy, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('random')))
			)
		)));
		
		$ct_ShowcaseBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'statusId',
			'label' => 'Status',
			'comment' => 'Będą wyświetlone tylko produkty o tych statusach',
			'options' => FormEngine\Option::Make(App::getModel('productstatus')->getProductstatusAll(false))
		)));
	
	}

	public function _populateFieldsContentTypeShowcaseBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ShowcaseBox']['productsCount'] = '0';
		$populate['ct_ShowcaseBox']['view'] = '0';
		$populate['ct_ShowcaseBox']['ct_ShowcaseBox_orderBy'] = 'id';
		$populate['ct_ShowcaseBox']['ct_ShowcaseBox_orderDir'] = 'asc';
		$populate['ct_ShowcaseBox']['statusId'] = '0';
		isset($ctValues['productsCount']) and $populate['ct_ShowcaseBox']['productsCount'] = $ctValues['productsCount'];
		isset($ctValues['view']) and $populate['ct_ShowcaseBox']['view'] = $ctValues['view'];
		isset($ctValues['orderBy']) and $populate['ct_ShowcaseBox']['ct_ShowcaseBox_orderBy'] = $ctValues['orderBy'];
		isset($ctValues['orderDir']) and $populate['ct_ShowcaseBox']['ct_ShowcaseBox_orderDir'] = $ctValues['orderDir'];
		isset($ctValues['statusId']) and $populate['ct_ShowcaseBox']['statusId'] = $ctValues['statusId'];
		return $populate;
	}
}