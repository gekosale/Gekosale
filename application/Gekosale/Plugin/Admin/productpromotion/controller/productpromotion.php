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
 * $Revision: 263 $
 * $Author: gekosale $
 * $Date: 2011-07-24 16:23:40 +0200 (N, 24 lip 2011) $
 * $Id: productpromotion.php 263 2011-07-24 14:23:40Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class ProductPromotionController extends Component\Controller\Admin
{

	public function index ()
	{
        App::getModel('contextmenu')->add($this->trans('TXT_BUYALSO_STATS'), $this->getRouter()->url('admin', 'buyalso'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_STATS'), $this->getRouter()->url('admin', 'statsproducts'));
        App::getModel('contextmenu')->add($this->trans('TXT_SALES_STATS'), $this->getRouter()->url('admin', 'statssales'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));

        
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			App::getModel('product'),
			'loadCategoryChildren'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllProductPromotion',
			$this->model,
			'getProductPromotionForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteProductPromotion',
			$this->model,
			'doAJAXDeleteProductPromotion'
		));

		$this->renderLayout(array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'promotion',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_SELECT_PRODUCTS')
		)));
		
		$productid = $requiredData->AddChild(new FormEngine\Elements\ProductSelect(Array(
			'name' => 'productid',
			'label' => $this->trans('TXT_SELECT_PRODUCTS'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_SELECT_PRODUCTS'))
			),
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE
		)));
		
		$pricePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'discount_pane',
			'label' => $this->trans('TXT_DISCOUNT')
		)));
		
		$standardPrice = $pricePane->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'standard_price',
			'label' => $this->trans('TXT_STANDARD_SELLPRICE')
		)));
		
		$enablePromotion = $standardPrice->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'promotion',
			'label' => $this->trans('TXT_ENABLE_PROMOTION'),
			'default' => '0'
		)));
		
		$standardPrice->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'discount',
			'label' => $this->trans('TXT_DISCOUNT'),
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_VALUE_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
			),
			'default' => '0.00',
			'suffix' => '%',
			'filters' => Array(
				new FormEngine\Filters\CommaToDotChanger()
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $enablePromotion, new FormEngine\Conditions\Equals(1))
			)
		)));
		
		$standardPrice->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'promotionstart',
			'label' => $this->trans('TXT_START_DATE'),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $enablePromotion, new FormEngine\Conditions\Equals(1))
			)
		)));
		
		$standardPrice->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'promotionend',
			'label' => $this->trans('TXT_END_DATE'),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $enablePromotion, new FormEngine\Conditions\Equals(1))
			)
		)));
		
		$clientGroups = App::getModel('clientgroup/clientgroup')->getClientGroupAll();
		
		foreach ($clientGroups as $clientGroup){
			
			$pricePane->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'field_' . $clientGroup['id'],
				'label' => $clientGroup['name']
			)));
			
			$promotion[$clientGroup['id']] = $pricePane->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'promotion_' . $clientGroup['id'],
				'label' => $this->trans('TXT_ENABLE_CLIENTGROUP_PROMOTION'),
				'default' => '0'
			)));
			
			$pricePane->AddChild(new FormEngine\Elements\TextField(Array(
				'name' => 'discount_' . $clientGroup['id'],
				'label' => $this->trans('TXT_DISCOUNT'),
				'rules' => Array(
					new FormEngine\Rules\Format($this->trans('ERR_VALUE_INVALID'), '/^([0-9]*\.*([0-9]{1,2})|(\0)?)$/')
				),
				'default' => '0.00',
				'suffix' => '%',
				'filters' => Array(
					new FormEngine\Filters\CommaToDotChanger()
				),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::HIDE, $promotion[$clientGroup['id']], new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals(1)))
				)
			)));
			
			$pricePane->AddChild(new FormEngine\Elements\Date(Array(
				'name' => 'promotionstart_' . $clientGroup['id'],
				'label' => $this->trans('TXT_START_DATE'),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $promotion[$clientGroup['id']], new FormEngine\Conditions\Equals(1))
				)
			)));
			
			$pricePane->AddChild(new FormEngine\Elements\Date(Array(
				'name' => 'promotionend_' . $clientGroup['id'],
				'label' => $this->trans('TXT_END_DATE'),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $promotion[$clientGroup['id']], new FormEngine\Conditions\Equals(1))
				)
			)));
		
		}
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			$this->model->addPromotion($Data);
			App::getModel('product')->updateProductAttributesetPricesAll();
			App::redirect(__ADMINPANE__ . '/productpromotion');
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}
}