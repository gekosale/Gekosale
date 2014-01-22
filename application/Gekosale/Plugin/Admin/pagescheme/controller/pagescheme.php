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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: pagescheme.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

use FormEngine;

class PageSchemeController extends Component\Controller\Admin
{

	public function index ()
	{
		if ((int) $this->id == 0){
			App::redirect(__ADMINPANE__ . '/pagescheme/edit/' . $this->registry->loader->getParam('pageschemeid'));
		}
		$schemes = Array();
		$pagescheme = $this->model->getPageschemeAll();
		foreach ($pagescheme as $scheme){
			$schemes[$scheme['id']]['name'] = $scheme['name'];
			$schemes[$scheme['id']]['parent'] = NULL;
			$schemes[$scheme['id']]['weight'] = $scheme['id'];
		}
		
		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'scheme_tree',
			'action' => '',
			'method' => 'post'
		));
		
		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'pagescheme',
			'sortable' => false,
			'retractable' => false,
			'selectable' => false,
			'clickable' => true,
			'deletable' => false,
			'addable' => false,
			'items' => $schemes,
			'onClick' => 'openPageSchemeEditor',
			'active' => $this->registry->loader->getParam('pageschemeid')
		)));
		
		$this->renderLayout(Array(
			'tree' => $tree->Render()
		));
	}

	public function edit ()
	{
		$templateMainInfo = App::getModel('pagescheme')->getTemplateNameToEdit($this->registry->core->getParam());
		
		if(strlen($this->id) == 0 || empty($templateMainInfo)){
			App::redirect(__ADMINPANE__ . '/pagescheme');
		}
		
		$templateMainInfo = App::getModel('pagescheme')->getTemplateNameToEdit($this->registry->core->getParam());
		$fieldGenerator = App::getModel('fieldgenerator/fieldgenerator')->LoadSchemeFields('.layout-box', NULL, $this->registry->core->getParam());
		$templateCss = App::getModel('pagescheme')->getTemplateCssToEdit($this->registry->core->getParam());
		App::getModel('contextmenu')->add($this->trans('TXT_TEMPLATE_LIBRARY'), $this->getRouter()->url('admin', 'templateeditor'));
		
		$schemes = Array();
		$pagescheme = $this->model->getPageschemeAll();
		foreach ($pagescheme as $scheme){
			$schemes[$scheme['id']]['name'] = $scheme['name'];
			$schemes[$scheme['id']]['parent'] = NULL;
			$schemes[$scheme['id']]['weight'] = $scheme['id'];
		}
		
		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'scheme_tree',
			'action' => '',
			'method' => 'post'
		));
		
		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'pagescheme',
			'sortable' => false,
			'retractable' => false,
			'selectable' => false,
			'clickable' => true,
			'deletable' => false,
			'addable' => false,
			'items' => $schemes,
			'onClick' => 'openPageSchemeEditor',
			'active' => $this->registry->core->getParam()
		)));
		
		$this->registry->template->assign('tree', $tree->Render());
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'pagescheme',
			'action' => '',
			'method' => 'post'
		));
		
		$fieldGenerator->AddFields($form);
		
		$cssData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'css_data',
			'label' => 'Edycja plików CSS'
		)));
		
		$cssData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p align="center">Pliki CSS możesz edytować w <a href="' . $this->registry->router->generate('admin', true, Array(
				'controller' => 'templateeditor',
				'action' => 'edit',
				'param' => $templateMainInfo['templatefolder']
			)) . '" target="_blank">Biblioteka szablonów &raquo; ' . $templateMainInfo['templatefolder'] . '</a>.</p>'
		)));
		
		$populate = $fieldGenerator->GetDefaultValues();
		
		$populate = $fieldGenerator->PopulateFormWithValues($form, $templateCss) + $populate;
		
		$form->Populate($populate);
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			App::getModel('pagescheme')->editPageScheme($this->_performArtificialMechanics($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT)), $this->registry->core->getParam());
			App::getmodel('cssgenerator')->createPageSchemeStyleSheetDocument($templateMainInfo['templatefolder']);
			if (FormEngine\FE::IsAction('continue')){
				App::redirect(__ADMINPANE__ . '/pagescheme/edit/' . $this->registry->core->getParam());
			}
			else{
				App::redirect(__ADMINPANE__ . '/pagescheme');
			}
		}
		// //////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$this->renderLayout(array(
			'form' => $form->Render()
		));
	}

	protected function _performArtificialMechanics ($data)
	{
		if (isset($data['db_border-radius']['value'])){
			$value = max(0, substr($data['db_border-radius']['value'], 0, - 2) - 1);
			$data['db_content_border-radius'] = Array(
				'selector' => '.layout-box-content',
				'bottom-left' => Array(
					'value' => "{$value}px"
				),
				'bottom-right' => Array(
					'value' => "{$value}px"
				)
			);
			$data['db_header_border-radius'] = Array(
				'selector' => '.layout-box-header',
				'top-left' => Array(
					'value' => "{$value}px"
				),
				'top-right' => Array(
					'value' => "{$value}px"
				)
			);
			$data['db_header_collapsed_border-radius'] = Array(
				'selector' => '.layout-box-collapsed .layout-box-header, .layout-box-option-header-false .layout-box-content',
				'top-left' => Array(
					'value' => "{$value}px"
				),
				'top-right' => Array(
					'value' => "{$value}px"
				),
				'bottom-left' => Array(
					'value' => "{$value}px"
				),
				'bottom-right' => Array(
					'value' => "{$value}px"
				)
			);
		}
		if (isset($data['hn_height'])){
			$data['hn_line-height'] = Array(
				'selector' => '#horizontal-navigation ul li a',
				'value' => "{$data['hn_height']['value']}px"
			);
		}
		if (isset($data['db_header_line-height'])){
			$data['db_icon_height'] = Array(
				'selector' => '.layout-box-icons .layout-box-icon',
				'value' => $data['db_header_line-height']['value']
			);
		}
		return $data;
	}
}