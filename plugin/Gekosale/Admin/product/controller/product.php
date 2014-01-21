<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: product.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class ProductController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			$this->model,
			'loadCategoryChildren'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteProduct',
			$this->model,
			'doAJAXDeleteProduct'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllProduct',
			$this->model,
			'getProductForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNamesForAjax'
		));
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doUpdateProduct',
			$this->model,
			'doAJAXUpdateProduct'
		));
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXCreateSeo',
			App::getModel('seo'),
			'doAJAXCreateSeo'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddProducer',
			App::getModel('producer'),
			'addEmptyProducer'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddDeliverer',
			App::getModel('deliverer'),
			'addEmptyDeliverer'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddVat',
			App::getModel('vat'),
			'addEmptyVat'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddProductStatus',
			App::getModel('product'),
			'addEmptyProductStatus'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddUnitMeasure',
			App::getModel('unitmeasure'),
			'addEmptyUnitMeasure'
		));
	}

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_CATEGORY_DATA'), $this->getRouter()->url('admin', 'category'));
		App::getModel('contextmenu')->add($this->trans('TXT_VARIANTS'), $this->getRouter()->url('admin', 'attributegroup'));
		App::getModel('contextmenu')->add($this->trans('TXT_ATTRIBUTES'), $this->getRouter()->url('admin', 'technicaldata'));
		App::getModel('contextmenu')->add($this->trans('TXT_PROMOTIONS'), $this->getRouter()->url('admin', 'productpromotion'));
		App::getModel('contextmenu')->add($this->trans('TXT_SHIPPING_METHDOS'), $this->getRouter()->url('admin', 'dispatchmethod'));
		
		$this->renderLayout(array(
			'datagrid_filter' => $this->model->getDatagridFilterData(),
			'status_filter' => App::getModel('productstatus')->getProductstatusAllToFilter(),
			'productStatuses' => json_encode(App::getModel('productstatus')->getProductstatusAll())
		));
	}

	public function add ()
	{
		$CurrentViewData = Array(
			'basic_pane' => Array(
				'enable' => 1,
				'producerid' => 0
			),
			'price_pane' => Array(
				'buycurrencyid' => App::getContainer()->get('session')->getActiveShopCurrencyId(),
				'sellcurrencyid' => App::getContainer()->get('session')->getActiveShopCurrencyId(),
				'vatid' => App::getModel('view')->getDefaultVatId()
			),
			'weight_pane' => Array(
				'weight' => 0,
				'width' => 0,
				'height' => 0,
				'deepth' => 0,
				'unit' => 0,
				'packagesize' => 1
			),
			'stock_pane' => Array(
				'stock' => 0,
				'trackstock' => 0,
				'availablityid' => 0,
				'disableatstockenabled' => 0,
				'disableatstock' => 0
			)
		);
		
		$this->formModel->setPopulateData($CurrentViewData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewProduct($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/product/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/product');
			}
		}
		
		$this->renderLayout(array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$PopulateData = $this->model->getProductAndAttributesById((int) $this->id);
		
		$groupPrice = App::getModel('product')->getProductGroupPrice($this->id, false);
		
		$CurrentViewData = Array(
			'basic_pane' => Array(
				'language_data' => $PopulateData['language'],
				'ean' => $PopulateData['ean'],
				'enable' => $PopulateData['enable'],
				'delivelercode' => $PopulateData['delivelercode'],
				'producerid' => $PopulateData['producerid'],
				'collectionid' => $PopulateData['collectionid'],
				'delivererid' => $PopulateData['delivererid']
			),
			'meta_data' => Array(
				'language_data' => $PopulateData['language']
			),
			'category_pane' => Array(
				'category' => $PopulateData['category']
			),
			'price_pane' => Array(
				'vatid' => $PopulateData['vatid'],
				'buyprice' => $PopulateData['buyprice'],
				'buycurrencyid' => $PopulateData['buycurrencyid'],
				'sellcurrencyid' => $PopulateData['sellcurrencyid'],
				'standard_price' => Array(
					'sellprice' => $PopulateData['sellprice'],
					'promotion' => $PopulateData['promotion'],
					'discountprice' => $PopulateData['discountprice'],
					'promotionstart' => $PopulateData['promotionstart'],
					'promotionend' => $PopulateData['promotionend']
				)
			),
			'weight_pane' => Array(
				'weight' => $PopulateData['weight'],
				'width' => $PopulateData['width'],
				'height' => $PopulateData['height'],
				'deepth' => $PopulateData['deepth'],
				'unit' => $PopulateData['unit'],
				'packagesize' => $PopulateData['packagesize']
			),
			'stock_pane' => Array(
				'stock' => $PopulateData['standardstock'],
				'trackstock' => $PopulateData['trackstock'],
				'availablityid' => $PopulateData['availablityid'],
				'disableatstockenabled' => $PopulateData['disableatstockenabled'],
				'disableatstock' => $PopulateData['disableatstock']
			),
			'description_pane' => Array(
				'language_data' => $PopulateData['language']
			),
			'technical_data_pane' => Array(
				'technical_data' => App::getModel('TechnicalData')->GetValuesForProduct((int) $this->id, $PopulateData['technicaldatasetid']),
				'technicaldatasetid' => $PopulateData['technicaldatasetid']
			),
			'crosssell_products' => Array(
				'crosssell' => App::getModel('crosssell')->getProductsDataGrid((int) $this->id)
			),
			'upsell_products' => Array(
				'upsell' => App::getModel('upsell')->getProductsDataGrid((int) $this->id)
			),
			'similar_products' => Array(
				'similar' => App::getModel('similarproduct')->getProductsDataGrid((int) $this->id)
			),
			'photos_pane' => Array(
				'photo' => $PopulateData['photo'],
				'mainphotoid' => $PopulateData['mainphotoid']
			),
			'files_pane' => Array(
				'file' => $PopulateData['file']
			),
			'statusproduct_pane' => Array(
				'productstatuses' => $PopulateData['productstatuses'],
				'newactive' => $PopulateData['productnew']['newactive'],
				'new_data' => Array(
					'startnew' => $PopulateData['productnew']['startnew'],
					'endnew' => $PopulateData['productnew']['endnew']
				)
			),
			'variants_pane' => Array(
				'variants' => $PopulateData['variants']
			)
		);
		
		foreach ($groupPrice as $key => $val){
			$CurrentViewData['price_pane'][$key] = $val;
		}
		
		$this->formModel->setPopulateData($CurrentViewData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->productUpdateAll($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			if (FormEngine\FE::IsAction('continue')){
				App::redirect(__ADMINPANE__ . '/product/edit/' . $this->id);
			}
			else{
				App::redirect(__ADMINPANE__ . '/product');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render(),
			'productName' => isset($PopulateData['language'][Helper::getLanguageId()]['name']) ? $PopulateData['language'][Helper::getLanguageId()]['name'] : $PopulateData['language'][1]['name'],
			'productLink' => App::getURLAdress() . Seo::getSeo('productcart') . '/' . (isset($PopulateData['language'][Helper::getLanguageId()]['seo']) ? $PopulateData['language'][Helper::getLanguageId()]['seo'] : $PopulateData['language'][1]['seo'])
		));
	}

	public function duplicate ()
	{
		$PopulateData = $this->model->getProductAndAttributesById((int) $this->id, true);
		
		foreach ($PopulateData['language'] as $languageid => $values){
			$copy = App::getModel('product')->checkDuplicateNames($values['name'], $values['seo'], $languageid, 0);
			$PopulateData['language'][$languageid]['name'] = $copy['name'];
			$PopulateData['language'][$languageid]['seo'] = $copy['seo'];
		}
		
		foreach ($PopulateData['variants'] as $key => $variant){
			$PopulateData['variants'][$key]['deletable'] = 1;
		}
		
		$groupPrice = App::getModel('product')->getProductGroupPrice($this->id, false);
		
		$CurrentViewData = Array(
			'basic_pane' => Array(
				'language_data' => $PopulateData['language'],
				'ean' => $PopulateData['ean'],
				'enable' => $PopulateData['enable'],
				'delivelercode' => $PopulateData['delivelercode'],
				'producerid' => $PopulateData['producerid'],
				'collectionid' => $PopulateData['collectionid'],
				'delivererid' => $PopulateData['delivererid']
			),
			'meta_data' => Array(
				'language_data' => $PopulateData['language']
			),
			'category_pane' => Array(
				'category' => $PopulateData['category']
			),
			'price_pane' => Array(
				'vatid' => $PopulateData['vatid'],
				'buyprice' => $PopulateData['buyprice'],
				'buycurrencyid' => $PopulateData['buycurrencyid'],
				'sellcurrencyid' => $PopulateData['sellcurrencyid'],
				'standard_price' => Array(
					'sellprice' => $PopulateData['sellprice'],
					'promotion' => $PopulateData['promotion'],
					'discountprice' => $PopulateData['discountprice'],
					'promotionstart' => $PopulateData['promotionstart'],
					'promotionend' => $PopulateData['promotionend']
				)
			),
			'weight_pane' => Array(
				'weight' => $PopulateData['weight'],
				'width' => $PopulateData['width'],
				'height' => $PopulateData['height'],
				'deepth' => $PopulateData['deepth'],
				'unit' => $PopulateData['unit'],
				'packagesize' => $PopulateData['packagesize']
			),
			'stock_pane' => Array(
				'stock' => $PopulateData['standardstock'],
				'trackstock' => $PopulateData['trackstock'],
				'availablityid' => $PopulateData['availablityid'],
				'disableatstockenabled' => $PopulateData['disableatstockenabled'],
				'disableatstock' => $PopulateData['disableatstock']
			),
			'description_pane' => Array(
				'language_data' => $PopulateData['language']
			),
			'technical_data_pane' => Array(
				'technical_data' => App::getModel('TechnicalData')->GetValuesForProduct((int) $this->id, $PopulateData['technicaldatasetid']),
                'technicaldatasetid' => $PopulateData['technicaldatasetid']
			),
            'crosssell_products' => Array(
                'crosssell' => App::getModel('crosssell')->getProductsDataGrid((int) $this->id)
            ),
            'upsell_products' => Array(
                'upsell' => App::getModel('upsell')->getProductsDataGrid((int) $this->id)
            ),
            'similar_products' => Array(
                'similar' => App::getModel('similarproduct')->getProductsDataGrid((int) $this->id)
            ),
			'photos_pane' => Array(
				'photo' => $PopulateData['photo']
			),
			'files_pane' => Array(
				'file' => $PopulateData['file']
			),
			'statusproduct_pane' => Array(
				'productstatuses' => $PopulateData['productstatuses'],
				'newactive' => $PopulateData['productnew']['newactive'],
				'new_data' => Array(
					'startnew' => $PopulateData['productnew']['startnew'],
					'endnew' => $PopulateData['productnew']['endnew']
				)
			),
			'variants_pane' => Array(
				'variants' => $PopulateData['variants']
			)
		);
		
		foreach ($groupPrice as $key => $val){
			$CurrentViewData['price_pane'][$key] = $val;
		}
		
		$this->formModel->setPopulateData($CurrentViewData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewProduct($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			App::redirect(__ADMINPANE__ . '/product');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render(),
			'message' => $this->trans('TXT_PRODUCT_DUPLICATED')
		));
	}
}