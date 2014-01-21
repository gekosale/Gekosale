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
 * $Id: rulescart.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class RulesCartController extends Component\Controller\Admin
{

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_BUYALSO_STATS'), $this->getRouter()->url('admin', 'buyalso'));
		App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_STATS'), $this->getRouter()->url('admin', 'statsproducts'));
		App::getModel('contextmenu')->add($this->trans('TXT_SALES_STATS'), $this->getRouter()->url('admin', 'statssales'));
		App::getModel('contextmenu')->add($this->trans('TXT_CLIENTS'), $this->getRouter()->url('admin', 'client'));
		App::getModel('contextmenu')->add($this->trans('TXT_CLIENT_GROUPS'), $this->getRouter()->url('admin', 'clientgroup'));

		$rulescartArray = Array();
		$rulescartRaw = App::getModel('rulescart')->getRulesCartAll();
		foreach ($rulescartRaw as $rulescartruleRaw){
			$rulescartArray[$rulescartruleRaw['id']]['name'] = $rulescartruleRaw['name'];
			$rulescartArray[$rulescartruleRaw['id']]['parent'] = $rulescartruleRaw['parent'];
			$rulescartArray[$rulescartruleRaw['id']]['weight'] = $rulescartruleRaw['distinction'];
		}

		if (count($rulescartArray) > 0 && $this->id == ''){
			App::redirect(__ADMINPANE__ . '/rulescart/edit/' . current(array_keys($rulescartArray)));
		}

		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'rulescart_tree',
			'class' => 'rulescart-select',
			'action' => '',
			'method' => 'post'
		));

		$tree->AddChild(new FormEngine\Elements\SortableList(Array(
			'name' => 'rulescart',
			'label' => $this->trans('TXT_RULES_CART'),
			'add_item_prompt' => $this->trans('TXT_ENTER_NEW_CART_RULE_NAME'),
			'delete_item_prompt' => $this->trans('TXT_DELETE_CART_RULE'),
			'sortable' => false,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'total' => count($rulescartArray),
			'items' => $rulescartArray,
			'onClick' => 'openRulesCartEditor',
			'onAdd' => 'xajax_AddRulesCart',
			'onAfterAdd' => 'openRulesCartEditor',
			'onDelete' => 'xajax_DeleteRulesCart',
			'onAfterDelete' => 'openRulesCartEditor',
			'onSaveOrder' => 'xajax_ChangeRulesCartOrder'
		)));

		$this->registry->template->assign('tree', $tree->Render());

		$this->registry->xajaxInterface->registerFunction(Array(
			'DeleteRulesCart',
			App::getModel('rulescart'),
			'deleteRulesCart'
		));

		$this->registry->xajaxInterface->registerFunction(Array(
			'AddRulesCart',
			App::getModel('rulescart'),
			'addEmptyRulesCart'
		));

		$this->registry->xajaxInterface->registerFunction(Array(
			'ChangeRulesCartOrder',
			App::getModel('rulescart'),
			'changeRulesCartOrder'
		));

		$this->renderLayout(Array(
			'total' => count($rulescartArray)
		));
	}

	public function edit ()
	{
		$rulescartArray = Array();
		$rulescartRaw = App::getModel('rulescart')->getRulesCartAll();
		foreach ($rulescartRaw as $rulescartruleRaw){
			$rulescartArray[$rulescartruleRaw['id']]['name'] = $rulescartruleRaw['name'];
			$rulescartArray[$rulescartruleRaw['id']]['parent'] = $rulescartruleRaw['parent'];
			$rulescartArray[$rulescartruleRaw['id']]['weight'] = $rulescartruleRaw['distinction'];
		}

		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'rulescart_tree',
			'class' => 'rulescart-select',
			'action' => '',
			'method' => 'post'
		));
		$tree->AddChild(new FormEngine\Elements\SortableList(Array(
			'name' => 'rulescart',
			'label' => $this->trans('TXT_RULES_CART'),
			'add_item_prompt' => $this->trans('TXT_ENTER_NEW_CART_RULE_NAME'),
			'delete_item_prompt' => $this->trans('TXT_DELETE_CART_RULE'),
			'sortable' => false,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'total' => count($rulescartArray),
			'items' => $rulescartArray,
			'onClick' => 'openRulesCartEditor',
			'onSaveOrder' => 'xajax_ChangeRulesCartOrder',
			'onAdd' => 'xajax_AddRulesCart',
			'onAfterAdd' => 'openRulesCartEditor',
			'onDelete' => 'xajax_DeleteRulesCart',
			'onAfterDelete' => 'openRulesCartEditor',
			'active' => $this->registry->core->getParam()
		)));

		$this->registry->template->assign('tree', $tree->Render());

		// //////////////////////////////////// EDIT RULE CART
		// ///////////////////////////////////////////////
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'rulescart',
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
			'label' => $this->trans('TXT_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'rulescarttranslation', 'name', null, Array(
					'column' => 'rulescartid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));

		$languageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_DESCRIPTION')
		)));

		$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Jeśli chcesz, by reguła obowiązywała zawsze, zostaw puste pola z datą</strong></p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$datefrom = $requiredData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'datefrom',
			'label' => $this->trans('TXT_START_DATE')
		)));

		$requiredData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'dateto',
			'label' => $this->trans('TXT_END_DATE')
		)));

		// //////////////////////////////// CLIENT GROUPS
		// //////////////////////////////////////////
		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => $this->trans('TXT_PROMOTIONRULE_DISCOUNT_DATA')
		)));

		$additionalData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Możesz podać wysokość zniżki kwotowo (modyfikator "-") lub procentową wartość zamówienia. Wprowadzając wartość 10:</p>
				<ul>
					<li><strong>dla modyfikatora "-"</strong> obniżysz kwotę zamówienia o 10 w danej walucie</li>
					<li><strong>dla modyfikatora "%"</strong> kwota zamówienia wyniesie 10% pierwotnej wartości</li>
				</ul>
				<p><strong>Przykład:</strong> Chcąc udzielić 10% rabatu na zamówienie, wprowadź wartość 90 i wybierz modyfikator "%".</p>
			',
		)));

		$discountForAll = $additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'discountforall',
			'label' => $this->trans('TXT_DISCOUNT_FOR_ALL_GROUP')
		)));

		$suffixtypeid = $additionalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'suffixtypeid',
			'label' => $this->trans('TXT_SUFFIXTYPE'),
			'options' => FormEngine\Option::Make(App::getModel('suffix/suffix')->getRulesSuffixTypesForSelect()),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::HIDE, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
			)
		)));

		$additionalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'discount',
			'label' => $this->trans('TXT_VALUE'),
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_VALUE_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::HIDE, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
			)
		)));

		$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'freeshipping',
			'label' => $this->trans('TXT_FREE_DELIVERY'),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::HIDE, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
			)
		)));

		$clientGroups = App::getModel('clientgroup/clientgroup')->getClientGroupAll();

		foreach ($clientGroups as $clientGroup){
			$additionalData->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'field_' . $clientGroup['id'],
				'label' => 'Rabat dla ' . $clientGroup['name'],
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				)
			)));

			$groups[$clientGroup['id']] = $additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'groupid_' . $clientGroup['id'],
				'label' => $clientGroup['name'],
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				)
			)));

			$suffix = $additionalData->AddChild(new FormEngine\Elements\Select(Array(
				'name' => 'suffixtypeid_' . $clientGroup['id'],
				'label' => $this->trans('TXT_SUFFIXTYPE'),
				'options' => FormEngine\Option::Make(App::getModel('suffix/suffix')->getRulesSuffixTypesForSelect()),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::HIDE, $groups[$clientGroup['id']], new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals(1))),
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				)
			)));

			$additionalData->AddChild(new FormEngine\Elements\TextField(Array(
				'name' => 'discount_' . $clientGroup['id'],
				'label' => $this->trans('TXT_VALUE'),
				'default' => '0.00',
				'rules' => Array(
					new FormEngine\Rules\Format($this->trans('ERR_VALUE_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/'),
					new FormEngine\Rules\Custom($this->trans('ERR_VALUE_INVALID'), Array(
						$this,
						'checkDiscountValue'
					), Array(
						'suffixType' => $suffix
					))
				),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::HIDE, $groups[$clientGroup['id']], new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals(1))),
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				),
				'filters' => Array(
					new FormEngine\Filters\CommaToDotChanger()
				)
			)));

			$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'freeshipping_' . $clientGroup['id'],
				'label' => $this->trans('TXT_FREE_DELIVERY'),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $discountForAll, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				)
			)));
		}

		$deliverers = App::getModel('dispatchmethod')->getDispatchmethodToSelect();

		$deliverersData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'deliverers_data',
			'label' => $this->trans('TXT_DELIVER')
		)));
		if (count($deliverers)){
			$deliverersData->AddChild(new FormEngine\Elements\MultiSelect(Array(
				'name' => 'deliverers',
				'label' => $this->trans('TXT_DISPATCHMETHOD'),
				'options' => FormEngine\Option::Make($deliverers)
			)));
		}
		else{
			$deliverersData->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => '<p><strong>' . $this->trans('TXT_EMPTY_DISPATCHMETHODS') . '</strong><br/>
						<a href="/admin/dispatchmethod/add" target="_blank">' . $this->trans('TXT_ADD_DISPATCHMETHOD') . '</a></p>'
			)));
		}

		// ///////////////////////////////// PAYMENT METHODS
		// /////////////////////////////////////////////
		$payments = App::getModel('paymentmethod')->getPaymentmethodToSelect();

		$paymentsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'payments_data',
			'label' => $this->trans('TXT_PAYMENTMETHODS')
		)));
		if (count($payments)){
			$paymentsData->AddChild(new FormEngine\Elements\MultiSelect(Array(
				'name' => 'payments',
				'label' => $this->trans('TXT_PAYMENTMETHODS'),
				'options' => FormEngine\Option::Make($payments)
			)));
		}
		else{
			$paymentsData->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => '<p><strong>' . $this->trans('TXT_EMPTY_PAYMENTMETHODS') . '</strong><br/>
						<a href="/admin/paymentmethod/add" target="_blank">' . $this->trans('TXT_ADD_PAYMENTMETHOD') . '</a></p>'
			)));
		}

		$pricePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'price_pane',
			'label' => $this->trans('TXT_SUM_PRICE')
		)));

		$pricePane->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p><strong>' . $this->trans('TXT_FINAL_CART_PRICE') . '</strong></p>'
		)));

		$pricePane->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'cart_price_from',
			'label' => $this->trans('TXT_PRICE_FROM'),
			'suffix' => 'brutto',
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
			)
		)));

		$pricePane->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'cart_price_to',
			'label' => $this->trans('TXT_PRICE_TO'),
			'suffix' => 'brutto',
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
			)
		)));

		$pricePane->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p><strong>' . $this->trans('TXT_TOTAL_COST') . '</strong></p>'
		)));

		$pricePane->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'dispatch_price_from',
			'label' => $this->trans('TXT_PRICE_FROM'),
			'suffix' => 'brutto',
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
			)
		)));

		$pricePane->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'dispatch_price_to',
			'label' => $this->trans('TXT_PRICE_TO'),
			'suffix' => 'brutto',
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
			)
		)));

		// /////////////////////////////////// VIEW DATA
		// /////////////////////////////////////////////
		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));

		$layers = $layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW')
		)));

		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());

		// //////////////////////////////////// POPULATE
		// //////////////////////////////////////////////////
		// ////////////////////////////////// REQUIRED_DATA
		// ////////////////////////////////////////////////
		$rawRulesCartData = App::getModel('rulescart')->getRulesCartView($this->registry->core->getParam());
		$rulesCartRuleData['required_data'] = Array(
			'language_data' => $rawRulesCartData['language'],
			'suffixtypeid' => $rawRulesCartData['suffixtypeid'],
			'discount' => round($rawRulesCartData['discount'], 2),
			'datefrom' => $rawRulesCartData['datefrom'],
			'dateto' => $rawRulesCartData['dateto'],
			'freeshipping' => $rawRulesCartData['freeshipping']
		);

		// ////////////////////////////////// ADDITIONAL_DATA
		// ////////////////////////////////////////////////
		$rawRulesCartClientGroupData = App::getModel('rulescart')->getRulesCartClientGroupView($this->registry->core->getParam());
		if (isset($rawRulesCartData['discountforall']) && $rawRulesCartData['discountforall'] == 1){
			$rulesCartRuleData['additional_data']['discountforall'] = $rawRulesCartData['discountforall'];
			$rulesCartRuleData['additional_data']['suffixtypeid'] = $rawRulesCartData['suffixtypeid'];
			$rulesCartRuleData['additional_data']['discount'] = round($rawRulesCartData['discount'], 2);
			$rulesCartRuleData['additional_data']['freeshipping'] = $rawRulesCartData['freeshipping'];
		}
		else{
			$rawRulesCartClientGroupData = App::getModel('rulescart')->getRulesCartClientGroupView($this->registry->core->getParam());
			if (count($rawRulesCartClientGroupData) > 0){
				foreach ($rawRulesCartClientGroupData as $clientGroupKey => $clientGroupValue){
					$rulesCartRuleData['additional_data']['groupid_' . $clientGroupValue['clientgroupid']] = 1;
					$rulesCartRuleData['additional_data']['discount_' . $clientGroupValue['clientgroupid']] = $clientGroupValue['discount'];
					$rulesCartRuleData['additional_data']['suffixtypeid_' . $clientGroupValue['clientgroupid']] = $clientGroupValue['suffixtypeid'];
					$rulesCartRuleData['additional_data']['freeshipping_' . $clientGroupValue['clientgroupid']] = $clientGroupValue['freeshipping'];
				}
			}
		}
		// ////////////////////////////////// DELIVERER_DATA
		// ////////////////////////////////////////////////
		$rawRulesCartDeliverersData = App::getModel('rulescart')->getRulesCartDeliverersView($this->registry->core->getParam());
		if (count($rawRulesCartDeliverersData) > 0){
			foreach ($rawRulesCartDeliverersData as $delivererId){
				$rulesCartRuleData['deliverers_data']['deliverers'][] = $delivererId;
			}
		}
		// ///////////////////////////////// PAYMENT METHODS
		// /////////////////////////////////////////////
		$rawRulesCartPaymentData = App::getModel('rulescart')->getRulesCartPaymentsView($this->registry->core->getParam());
		if (count($rawRulesCartPaymentData) > 0){
			foreach ($rawRulesCartPaymentData as $paymentId){
				$rulesCartRuleData['payments_data']['payments'][] = $paymentId;
			}
		}
		// /////////////////////////////////// CART PRICE
		// ////////////////////////////////////////////////
		$rawRulesCartDynamicData = App::getModel('rulescart')->getRulesCartOtherDinamicDataConditionsView($this->registry->core->getParam());
		if (count($rawRulesCartDynamicData) > 0){
			foreach ($rawRulesCartDynamicData as $dynamicData){
				if ($dynamicData['ruleid'] == 11 && $dynamicData['field'] == 'globalpricefrom'){
					$rulesCartRuleData['price_pane']['cart_price_from'] = $dynamicData['pricefrom'];
				}
				if ($dynamicData['ruleid'] == 12 && $dynamicData['field'] == 'globalpriceto'){
					$rulesCartRuleData['price_pane']['cart_price_to'] = $dynamicData['priceto'];
				}
				if ($dynamicData['ruleid'] == 13 && $dynamicData['field'] == 'globalpricewithdispatchmethodfrom'){
					$rulesCartRuleData['price_pane']['dispatch_price_from'] = $dynamicData['pricefrom'];
				}
				if ($dynamicData['ruleid'] == 14 && $dynamicData['field'] == 'globalpricewithdispatchmethodto'){
					$rulesCartRuleData['price_pane']['dispatch_price_to'] = $dynamicData['priceto'];
				}
			}
		}
		// /////////////////////////////////// VIEW DATA
		// /////////////////////////////////////////////
		$rawRulesCartViewData = App::getModel('rulescart')->getRulesCartViews($this->registry->core->getParam());
		if (count($rawRulesCartViewData) > 0){
			foreach ($rawRulesCartViewData as $viewKey => $viewValue){
				$rulesCartRuleData['view_data']['view'][] = $viewValue;
			}
		}
		$form->Populate($rulesCartRuleData);

		// ///////////////////////////////////// SUBMIT
		// /////////////////////////////////////////////////////
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$formData = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				App::getModel('rulescart')->editRulesCart($formData, $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/rulescart/edit/' . $this->id);
		}
		// //////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajaxInterface->registerFunction(Array(
			'DeleteRulesCart',
			App::getModel('rulescart'),
			'deleteRulesCart'
		));
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddRulesCart',
			App::getModel('rulescart'),
			'addEmptyRulesCart'
		));
		$this->registry->xajaxInterface->registerFunction(Array(
			'ChangeRulesCartOrder',
			App::getModel('rulescart'),
			'changeRulesCartOrder'
		));
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('edit.tpl'));
	}

	public function checkDiscountValue ($value, $params)
	{
		if (isset($params['suffixType']) && ($params['suffixType'] == '1')){
			if (intval($value) >= 100){
				return false;
			}
		}
		return true;
	}

	public function checkDiscountValueSuffix ($value, $params)
	{
		if (isset($params['discountValue']) && ($params['discountValue'] > 0)){
			if (intval($value) == '1' && (int) $params['discountValue'] >= 100){
				return false;
			}
		}
		return true;
	}
}