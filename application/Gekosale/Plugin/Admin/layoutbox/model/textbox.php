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

class TextBoxModel extends Component\Model
{

	public function _addFieldsContentTypeTextBox ($form, $boxContent)
	{
		$ct_TextBox = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ct_TextBox',
			'label' => $this->trans('TXT_LAYOUT_BOX_CONTENT_SETTINGS')
		)));
		
		$ct_TextBox->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $boxContent, new FormEngine\Conditions\Equals('TextBox')));
		
		$languageData = $ct_TextBox->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'textbox_content_translation',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'textbox_content',
			'label' => 'Treść'
		)));
	
	}

	public function _populateFieldsContentTypeTextBox (&$populate, $ctValues = Array())
	{
		if (isset($ctValues['content']) and is_array($ctValues['content'])){
			foreach ($ctValues['content'] as $languageId => $content){
				$populate['ct_TextBox']['textbox_content_translation'][$languageId]['textbox_content'] = $content;
			}
		}
		else{
			$populate['ct_TextBox']['textbox_content_translation'][Helper::getLanguageId()]['textbox_content'] = '';
			isset($ctValues['content']) and $populate['ct_TextBox']['textbox_content_translation'][Helper::getLanguageId()]['textbox_content'] = $ctValues['content'];
		}
		return $populate;
	}
}