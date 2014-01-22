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

class CouponsForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'coupons',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_TOPIC')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_CONTENT'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 5000',
			'max_length' => 5000
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'code',
			'label' => 'Kod kuponu',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_COUPON_CODE')),
				new FormEngine\Rules\Unique($this->trans('ERR_CODE_ALREADY_EXISTS'), 'coupons', 'code', null, Array(
					'column' => 'idcoupons',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Jeśli chcesz, by kupon obowiązywał zawsze, zostaw puste pola z datą</strong></p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'datefrom',
			'label' => $this->trans('TXT_START_DATE')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'dateto',
			'label' => $this->trans('TXT_END_DATE')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'globalqty',
			'label' => 'Globalna ilość kuponów',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			),
			'default' => 1000
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'clientqty',
			'label' => 'Ilość użyć per klient',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			),
			'default' => 1
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clients',
			'label' => $this->trans('TXT_CLIENT'),
			'key' => 'idclient',
			'datagrid_init_function' => Array(
				App::getModel('client'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientDatagridColumns()
		)));
		
		$productData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'product_data',
			'label' => $this->trans('TXT_PRODUCT')
		)));
		
		$productData->AddChild(new FormEngine\Elements\ProductSelect(Array(
			'name' => 'product',
			'label' => $this->trans('TXT_PRODUCTS'),
			'repeat_min' => 0,
			'repeat_max' => FormEngine\FE::INFINITE
		)));
		
		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => $this->trans('TXT_PROMOTIONRULE_DISCOUNT_DATA')
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Możesz podać wysokość zniżki kwotowo (modyfikator "-") lub procentową wartość zamówienia po wykorzystaniu kuponu. Wprowadzając wartość 10:</p>
			<ul>
				<li><strong>dla modyfikatora "-"</strong> obniżysz kwotę zamówienia o 10 w danej walucie</li>
				<li><strong>dla modyfikatora "%"</strong> kwota zamówienia wyniesie 10% pierwotnej wartości</li>
			</ul>
			<p><strong>Przykład:</strong> Chcąc udzielić klientowi 10% rabatu na zamówienie, wprowadź wartość 90 i wybierz modyfikator "%".</p>
		'
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'suffixtypeid',
			'label' => $this->trans('TXT_SUFFIXTYPE'),
			'options' => FormEngine\Option::Make(App::getModel('coupons')->getCouponSuffixTypesForSelect())
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'discount',
			'label' => $this->trans('TXT_VALUE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			)
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'freeshipping',
			'label' => 'Darmowa wysyłka',
			'comment' => 'Zwolnienie z kosztów wysyłki przy wykorzystaniu kuponu'
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'excludepromotions',
			'label' => $this->trans('TXT_COUPON_EXCLUDE_PROMOTIONS'),
			'comment' => $this->trans('TXT_COUPON_EXCLUDE_PROMOTIONS_HELP')
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'minimumordervalue',
			'label' => $this->trans('TXT_MINIMUM_ORDER_VALUE'),
			'comment' => $this->trans('TXT_MINIMUM_ORDER_VALUE_HELP'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			),
			'default' => 0
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'currencyid',
			'label' => $this->trans('TXT_KIND_OF_CURRENCY'),
			'options' => FormEngine\Option::Make(App::getModel('currencieslist')->getCurrencyForSelect()),
			'default' => 0,
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CURRENCY'))
			),
			'default' => App::getContainer()->get('session')->getActiveShopCurrencyId()
		)));
		
		$excludeData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'exclude_data',
			'label' => 'Wykluczenie kategorii'
		)));
		
		$excludeData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'category',
			'label' => $this->trans('TXT_CATEGORY'),
			'choosable' => false,
			'selectable' => true,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getChildCategories(),
			'load_children' => Array(
				App::getModel('category'),
				'getChildCategories'
			)
		)));
		
		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));
		
		$layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW'),
			'default' => Helper::getViewIdsDefault()
		)));
		
		$Data = Event::dispatch($this, 'admin.coupons.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}

	protected function getClientDatagridColumns ()
	{
		return Array(
			Array(
				'id' => 'idclient',
				'caption' => $this->trans('TXT_ID'),
				'appearance' => Array(
					'width' => 50
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_BETWEEN
				)
			),
			Array(
				'id' => 'firstname',
				'caption' => $this->trans('TXT_FIRSTNAME'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			),
			Array(
				'id' => 'surname',
				'caption' => $this->trans('TXT_SURNAME'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			),
			Array(
				'id' => 'email',
				'caption' => $this->trans('TXT_EMAIL'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			),
			Array(
				'id' => 'clientorder',
				'caption' => $this->trans('TXT_CLIENTORDER_VALUE'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_BETWEEN
				)
			),
			Array(
				'id' => 'adddate',
				'caption' => $this->trans('TXT_DATE'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO,
					'visible' => false
				)
			)
		);
	}
}