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

class FacebookLikeBoxModel extends Component\Model
{

	public function _addFieldsContentTypeFacebookLikeBox ($form, $boxContent)
	{

		$ct_FacebookLikeBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_FacebookLikeBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));

		$ct_FacebookLikeBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('FacebookLikeBox')));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'url',
			'label' => 'Adres profilu Facebook'
		)));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'width',
			'label' => $this->trans('TXT_WIDTH'),
			'suffix' => 'px'
		)));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'height',
			'label' => $this->trans('TXT_HEIGHT'),
			'suffix' => 'px'
		)));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'scheme',
			'label' => 'Schemat kolorów',
			'options' => Array(
				new FormEngine\Option('light', 'Jasny'),
				new FormEngine\Option('dark', 'Ciemny')
			)
		)));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'faces',
			'label' => 'Pokazuj twarze',
			'options' => Array(
				new FormEngine\Option('true', 'Tak'),
				new FormEngine\Option('false', 'Nie')
			)
		)));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'stream',
			'label' => 'Pokazuj stream',
			'options' => Array(
				new FormEngine\Option('true', 'Tak'),
				new FormEngine\Option('false', 'Nie')
			)
		)));

		$ct_FacebookLikeBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'header',
			'label' => 'Pokazuj nagłówek',
			'options' => Array(
				new FormEngine\Option('true', 'Tak'),
				new FormEngine\Option('false', 'Nie')
			)
		)));

	}

	public function _populateFieldsContentTypeFacebookLikeBox (&$populate, $ctValues = Array())
	{
		$populate['ct_FacebookLikeBox']['url'] = '';
		$populate['ct_FacebookLikeBox']['width'] = '';
		$populate['ct_FacebookLikeBox']['height'] = '';
		$populate['ct_FacebookLikeBox']['scheme'] = '';
		$populate['ct_FacebookLikeBox']['faces'] = '';
		$populate['ct_FacebookLikeBox']['stream'] = '';
		$populate['ct_FacebookLikeBox']['header'] = '';
		isset($ctValues['url']) and $populate['ct_FacebookLikeBox']['url'] = $ctValues['url'];
		isset($ctValues['width']) and $populate['ct_FacebookLikeBox']['width'] = $ctValues['width'];
		isset($ctValues['height']) and $populate['ct_FacebookLikeBox']['height'] = $ctValues['height'];
		isset($ctValues['scheme']) and $populate['ct_FacebookLikeBox']['scheme'] = $ctValues['scheme'];
		isset($ctValues['faces']) and $populate['ct_FacebookLikeBox']['faces'] = $ctValues['faces'];
		isset($ctValues['stream']) and $populate['ct_FacebookLikeBox']['stream'] = $ctValues['stream'];
		isset($ctValues['header']) and $populate['ct_FacebookLikeBox']['header'] = $ctValues['header'];

		return $populate;
	}
}