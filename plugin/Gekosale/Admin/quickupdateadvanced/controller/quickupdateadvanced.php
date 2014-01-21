<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 377 $
 * $Author: gekosale $
 * $Date: 2011-08-15 23:41:14 +0200 (Pn, 15 sie 2011) $
 * $Id: exchange.php 377 2011-08-15 21:41:14Z gekosale $
 */

namespace Gekosale;
use FormEngine;

class QuickUpdateAdvancedController extends Component\Controller\Admin
{

	public function index ()
	{

		$form = new FormEngine\Elements\Form(Array(
			'name' => 'quickupdate',
			'action' => '',
			'method' => 'post'
		));

		$typePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'type_pane',
			'label' => 'Ustawienia'
		)));

		$typePane->AddChild(new FormEngine\Elements\Tip(array(
			'tip' => '<p>Dzięki szybkiej aktualizacji w możesz zaktualizować stan magazynowy, cenę bądź dostępność produktu.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$quickupdatetype = $typePane->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'type',
			'label' => $this->trans('TXT_EXCHANGE_TYPE'),
			'options' => Array(
				new FormEngine\Option(1, $this->trans('TXT_EXCHANGE_TYPE_EXPORT')),
				new FormEngine\Option(2, $this->trans('TXT_EXCHANGE_TYPE_IMPORT'))
			),
			'default' => 1
		)));

		$filesPane = $typePane->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'files_pane',
			'label' => $this->trans('TXT_FILES')
		)));

		$files = $filesPane->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'files',
			'label' => 'Plik',
			'file_source' => 'upload/',
			'file_types' => Array(
				'csv'
			)
		)));

		$filesPane->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $quickupdatetype, new FormEngine\Conditions\Equals(2)));

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			switch ($Data['type']) {
				case 1:
					$products = App::getModel('quickupdate')->exportProducts();
					if($products == 0){
						$this->registry->template->assign('error', 'Brak danych do wyeksportowania.');
					}
					break;
				case 2:
					$total = App::getModel('quickupdate')->importProducts($Data['files']['file']);
					$this->registry->template->assign('success', $total);
					break;
			}

		}

		$this->renderLayout(array(
			'form' => $form->Render())
		);
	}

}