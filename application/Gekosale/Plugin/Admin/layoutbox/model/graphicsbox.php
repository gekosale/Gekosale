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

class GraphicsBoxModel extends Component\Model
{

	public function _addFieldsContentTypeGraphicsBox ($form, $boxContent)
	{

		$ct_GraphicsBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_GraphicsBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		$ct_GraphicsBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('GraphicsBox')));

		$ct_GraphicsBox->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'image',
			'label' => 'Obraz',
			'file_source' => 'design/_images_frontend/upload/'
		)));

		$ct_GraphicsBox->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'align',
			'label' => 'Wyrównanie obrazu',
			'options' => Array(
				new FormEngine\Option('center center', 'Środek'),
				new FormEngine\Option('left center', 'Do lewej'),
				new FormEngine\Option('right center', 'Do prawej')
			)
		)));

		$ct_GraphicsBox->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'url',
			'label' => 'Adres strony po kliknięciu',
			'comment' => 'Linkując wewnątrz sklepu możesz podawać samą nazwę kontrolera np. kontakt, promocje.'
		)));
	}

	public function _populateFieldsContentTypeGraphicsBox (&$populate, $ctValues = Array())
	{
		$populate['ct_GraphicsBox']['align'] = 'center';
		$populate['ct_GraphicsBox']['align'] = 'url';
		$populate['ct_GraphicsBox']['image'] = '';
		isset($ctValues['align']) and $populate['ct_GraphicsBox']['align'] = $ctValues['align'];
		isset($ctValues['url']) and $populate['ct_GraphicsBox']['url'] = $ctValues['url'];
		// FIXME: Ponizsze nalezy uzaleznic od rzeczywistej sciezki do katalogu
		// 'upload'.
		isset($ctValues['image']) and $populate['ct_GraphicsBox']['image'] = Array(
			'file' => substr($ctValues['image'], strlen('design/_images_frontend/upload/'))
		);
		return $populate;
	}
}