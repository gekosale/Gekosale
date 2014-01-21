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

class ProducerBoxModel extends Component\Model
{

	public function _addFieldsContentTypeProducerBox ($form, $boxContent)
	{
		$ct_ProducerBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_ProducerBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		
		$ct_ProducerBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('ProducerBox')));
		
		$ct_ProducerBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'view',
			'label' => 'Domyślny widok',
			'options' => Array(
				new FormEngine\Option('0', 'Lista'),
				new FormEngine\Option('1', 'Select')
			)
		)));
		
		$ct_ProducerBox->AddChild(new FormEngine\Elements\MultiSelect(Array(
			'name' => 'producers',
			'label' => $this->trans('TXT_AVAILABLE_PRODUCERS'),
			'options' => FormEngine\Option::Make(App::getModel('producer')->getProducerToSelect())
		)));
	}

	public function _populateFieldsContentTypeProducerBox (&$populate, $ctValues = Array())
	{
		$populate['ct_ProducerBox']['view'] = '0';
		$populate['ct_ProducerBox']['producers'] = Array();
		isset($ctValues['view']) and $populate['ct_ProducerBox']['view'] = $ctValues['view'];
		isset($ctValues['producers']) and $populate['ct_ProducerBox']['producers'] = explode(',', $ctValues['producers']);
		return $populate;
	}
}