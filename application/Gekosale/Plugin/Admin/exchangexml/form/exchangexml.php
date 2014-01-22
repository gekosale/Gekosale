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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;

use FormEngine;

class ExchangexmlForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
        	$form = new FormEngine\Elements\Form(Array(
			'name' => 'exchange',
			'action' => '',
			'method' => 'post'
		));

		// Import / Export
		$profilePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'profile_pane',
			'label' => $this->trans('TXT_MAIN_INFORMATION'),
			'default' => 1
		)));

		$name = $profilePane->addChild(new FormEngine\Elements\TextField(array(
			'name' => 'profile_name',
			'label' => $this->trans('TXT_NAME'),
			'rules' => array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$type = $profilePane->AddChild(new FormEngine\Elements\Select(array(
			'name' => 'profile_type',
			'label' => $this->trans('TXT_TYPE'),
			'options' => Array(
				new FormEngine\Option(1, $this->trans('TXT_IMPORT')),
				new FormEngine\Option(2, $this->trans('TXT_EXPORT')),
			),
			'default' => 1
		)));

		$datatype = $profilePane->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'profile_datatype',
			'label' => $this->trans('TXT_BACKUP_TYPE'),
			'options' => Array(
				new FormEngine\Option(1, $this->trans('TXT_PRODUCTS')),
				new FormEngine\Option(2, $this->trans('TXT_CATEGORIES')),
				new FormEngine\Option(3, $this->trans('TXT_CLIENTS')),
				new FormEngine\Option(5, $this->trans('TXT_CLIENTS_INCREMENTALLY')),
				new FormEngine\Option(4, $this->trans('TXT_ORDERS')),
				new FormEngine\Option(6, $this->trans('TXT_ORDERS_INCREMENTALLY'))
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::EXCHANGE_OPTIONS, $type, Array(
					App::getModel('exchangexml'),
					'getEntityTypes'
				))
			),
		)));

		$importPattern = $profilePane->addChild(new FormEngine\Elements\Tip(Array(
			'tip' => '
				<p>
					Pobierz przykładowy plik wzorca:
					<table>
						<tr>
							<th>Import</th>
							<th>Eksport</th>
						</tr>
						<tr>
							<td valign="top">
								<ul class="links">
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/import_ceneo.xml.txt">Import produktów z ceneo</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/import_nokaut.xml.txt">Import produktów z nokaut</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/import_products_attributes.xml.txt">Import produktów i atrybutów</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/import_categories.xml.txt">Import kategorii</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/import_clients.xml.txt">Import klientów</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/import_orders.xml.txt">Import zamówień</a></li>
								</ul>
							</td>
							<td valign="top">
								<ul class="links">
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/export_products.xml.txt">Eksport produktów</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/export_products_attributes.xml.txt">Eksport produktów i atrybutów</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/export_categories.xml.txt">Eksport kategorii</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/export_clients.xml.txt">Eksport klientów</a></li>
									<li><a target=_"blank" href="' . DESIGNPATH . '_data_panel/export_orders.xml.txt">Eksport zamówień</a></li>
								</ul>
							</td>
						</tr>
					</table>
				</p>',
		)));

		$pattern = $profilePane->addChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'profile_pattern',
			'label' => $this->trans('TXT_EXCHANGE_PROFILE_PATTERN'),
			'cols' => 120,
			'rows' => 18,
			'rules' => array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			),
		)));

		$categoryseparator = $profilePane->addChild(new FormEngine\Elements\TextField(Array(
			'name' => 'profile_categoryseparator',
			'label' => $this->trans('TXT_EXCHANGE_CATEGORYSEPARATOR'),
		)));

		$periodically = $profilePane->addChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'profile_periodically',
			'label' => $this->trans('TXT_EXCHANGE_PERIODICALLY'),
		)));

		$profilePane->addChild(new FormEngine\Elements\TextField(Array(
			'name' => 'profile_interval',
			'label' => $this->trans('TXT_EXCHANGE_INTERVAL'),
			'comment' => 'czas w sekundach',
			'default' => 28800,
			'rules' => array(
				new FormEngine\Rules\Format($this->trans('ERR_ALPHANUMERIC_INVALID'), '/^\d+$/')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $periodically, new FormEngine\Conditions\Equals(1)),
			)
		)));

		$remotePane = $profilePane->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'remote_pane',
			'label' => $this->trans('TXT_EXCHANGE_REMOTE_FILE')
		)));

		$url = $remotePane->addChild(new FormEngine\Elements\TextField(Array(
			'name' => 'profile_url',
			'label' => $this->trans('TXT_ADDRESS'),
			'comment' => 'Adres pliku źródłowego lub docelowego',
			'default' => URL . 'upload/export-' . substr(md5(uniqid()), 0, 8) . '.xml'
		)));

		$login = $remotePane->addChild(new FormEngine\Elements\TextField(Array(
			'name' => 'profile_url_username',
			'label' => $this->trans('TXT_LOG')
		)));

		$password = $remotePane->addChild(new FormEngine\Elements\TextField(Array(
			'name' => 'profile_url_password',
			'label' => $this->trans('TXT_PASSWORD'),
		)));

		$filesPane = $profilePane->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'files_pane',
			'label' => $this->trans('TXT_EXCHANGE_LOCAL_FILE')
		)));

		$file = $filesPane->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'files',
			'label' => 'Plik',
			'file_source' => 'upload/',
			'file_types' => Array(
				'xml'
			)
		)));

		$filesPane->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $type, new FormEngine\Conditions\Equals(1)));

		if (! empty($this->populateData)){
			$form->Populate($this->populateData);
		}

		return $form;
	}
}