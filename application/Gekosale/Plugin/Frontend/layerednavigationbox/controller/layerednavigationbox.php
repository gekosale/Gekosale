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
 */
namespace Gekosale\Plugin;

class LayeredNavigationBoxController extends Component\Controller\Box
{

	public function index ()
	{
		switch ($this->registry->router->getCurrentController()) {
			case 'categorylist':
				
				$this->category = App::getModel('categorylist')->getCurrentCategory();
				
				$this->orderBy = $this->getParam('orderBy', 'default');
				$this->orderDir = $this->getParam('orderDir', 'asc');
				$this->currentPage = $this->getParam('currentPage', 1);
				$this->view = $this->getParam('viewType', 0);
				$this->priceFrom = $this->getParam('priceFrom', 0);
				$this->priceTo = $this->getParam('priceTo', Core::PRICE_MAX);
				$this->producers = $this->getParam('producers', 0);
				$this->attributes = $this->getParam('attributes', 0);
				$this->technicaldata = $this->getParam('technicaldata', 0);
				
				if (isset($_POST['layered_submitted']) && $_POST['layered_submitted'] == 1){
					App::redirectUrl($this->generateRedirectUrl());
				}
				
				$Data = App::getModel('layerednavigationbox')->getLayeredAttributesForCategory($this->category['id']);
				
				$paramAttributes = (strlen($this->attributes) > 0) ? array_filter(array_values(explode('_', $this->attributes))) : Array();
				
				foreach ($Data as $groupId => $groupData){
					foreach ($groupData['attributes'] as $attributeId => $attributeData){
						
						if (! empty($paramAttributes)){
							$attr = array_merge($paramAttributes, (array) $attributeId);
						}
						else{
							$attr = (array) $attributeId;
						}
						
						$url = $this->registry->router->generate('frontend.categorylist', true, Array(
							'param' => $this->category['seo'],
							'orderBy' => $this->orderBy,
							'orderDir' => $this->orderDir,
							'currentPage' => $this->currentPage,
							'viewType' => $this->view,
							'priceFrom' => $this->priceFrom,
							'priceTo' => $this->priceTo,
							'producers' => $this->producers,
							'attributes' => implode('_', array_unique($attr)),
							'technicaldata' => $this->technicaldata
						));
						
						$Data[$groupId]['attributes'][$attributeId]['link'] = $url;
						$Data[$groupId]['attributes'][$attributeId]['active'] = in_array($attributeId, $paramAttributes);
					}
				}
				
				$TechnicalDataAttributes = App::getModel('layerednavigationbox')->getTechnicalAttributes($this->category['id']);
				
				$paramTechnicalDataAttributes = (strlen($this->technicaldata) > 0) ? array_filter(array_values(explode('_', $this->technicaldata))) : Array();
				
				foreach ($TechnicalDataAttributes as $groupName => $attributes){
					foreach ($attributes as $attributeId => $attributeData){
						
						if (! empty($paramTechnicalDataAttributes)){
							$attr = array_merge($paramTechnicalDataAttributes, (array) $attributeData['id']);
						}
						else{
							$attr = (array) $attributeData['id'];
						}
						
						$url = $this->registry->router->generate('frontend.categorylist', true, Array(
							'param' => $this->category['seo'],
							'orderBy' => $this->orderBy,
							'orderDir' => $this->orderDir,
							'currentPage' => $this->currentPage,
							'viewType' => $this->view,
							'priceFrom' => $this->priceFrom,
							'priceTo' => $this->priceTo,
							'producers' => $this->producers,
							'attributes' => $this->attributes,
							'technicaldata' => implode('_', array_unique($attr))
						));
						
						$TechnicalDataAttributes[$groupName][$attributeId]['link'] = $url;
						$TechnicalDataAttributes[$groupName][$attributeId]['active'] = in_array($attributeData['id'], $paramTechnicalDataAttributes);
					}
				}
				
				$producers = App::getModel('product')->getProducerAll(Array(
					$this->category['id']
				));
				
				$paramProducers = (strlen($this->producers) > 0) ? array_filter(array_values(explode('_', $this->producers))) : Array();
				
				foreach ($producers as $key => $producer){
					
					if (! empty($paramProducers)){
						$prod = array_merge($paramProducers, (array) $producer['id']);
					}
					else{
						$prod = (array) $producer['id'];
					}
					
					$url = $this->registry->router->generate('frontend.categorylist', true, Array(
						'param' => $this->category['seo'],
						'orderBy' => $this->orderBy,
						'orderDir' => $this->orderDir,
						'currentPage' => $this->currentPage,
						'viewType' => $this->view,
						'priceFrom' => $this->priceFrom,
						'priceTo' => $this->priceTo,
						'producers' => implode('_', array_unique($prod)),
						'attributes' => $this->attributes,
						'technicaldata' => $this->technicaldata
					));
					
					$producers[$key]['link'] = $url;
					$producers[$key]['active'] = in_array($producer['id'], $paramProducers);
				}
				$this->registry->template->assign('priceFrom', $this->priceFrom);
				$this->registry->template->assign('priceTo', $this->priceTo);
				$this->registry->template->assign('producers', $producers);
				$this->registry->template->assign('currentCategory', $this->category);
				$this->registry->template->assign('groups', $Data);
				$this->registry->template->assign('technicaldatagroups', $TechnicalDataAttributes);
				$this->registry->template->assign('current', (int) $this->registry->core->getParam());
				
				break;
			case 'productsearch':
				
				$this->orderBy = $this->getParam('orderBy', 'default');
				$this->orderDir = $this->getParam('orderDir', 'asc');
				$this->currentPage = $this->getParam('currentPage', 1);
				$this->view = $this->getParam('viewType', 0);
				$this->priceFrom = $this->getParam('priceFrom', 0);
				$this->priceTo = $this->getParam('priceTo', Core::PRICE_MAX);
				$this->producers = $this->getParam('producers', 0);
				$this->attributes = $this->getParam('attributes', 0);
				$this->technicaldata = $this->getParam('technicaldata', 0);
				
				if (isset($_POST['layered_submitted']) && $_POST['layered_submitted'] == 1){
					App::redirectUrl($this->generateRedirectUrl());
				}
				
				$this->searchPhrase = str_replace('_', '', App::getModel('formprotection')->cropDangerousCode($this->getParam()));
				
				$dataset = App::getModel('searchresults')->getDataset();
				$dataset->setPagination(5);
				$dataset->setCurrentPage(1);
				$dataset->setOrderBy('name', 'name');
				$dataset->setOrderDir('desc', 'desc');
				$dataset->setSQLParams(Array(
					'name' => '%' . $this->searchPhrase . '%'
				));
				$products = App::getModel('searchresults')->getProductDataset();
				$productIds = Array(
					0
				);
				foreach ($products['rows'] as $key => $product){
					$productIds[] = $product['id'];
				}
				
				$Data = App::getModel('layerednavigationbox')->getLayeredAttributesByProductIds($productIds);
				
				$paramAttributes = (strlen($this->attributes) > 0) ? array_filter(array_values(explode('_', $this->attributes))) : Array();
				
				foreach ($Data as $groupId => $groupData){
					foreach ($groupData['attributes'] as $attributeId => $attributeData){
						
						if (! empty($paramAttributes)){
							$attr = array_merge($paramAttributes, (array) $attributeId);
						}
						else{
							$attr = (array) $attributeId;
						}
						
						$url = $this->registry->router->generate('frontend.productsearch', true, Array(
							'action' => 'index',
							'param' => $this->getParam(),
							'orderBy' => $this->orderBy,
							'orderDir' => $this->orderDir,
							'currentPage' => $this->currentPage,
							'viewType' => $this->view,
							'priceFrom' => $this->priceFrom,
							'priceTo' => $this->priceTo,
							'producers' => $this->producers,
							'attributes' => implode('_', array_unique($attr)),
							'technicaldata' => $this->technicaldata
						));
						
						$Data[$groupId]['attributes'][$attributeId]['link'] = $url;
						$Data[$groupId]['attributes'][$attributeId]['active'] = in_array($attributeId, $paramAttributes);
					}
				}
				
				$TechnicalDataAttributes = App::getModel('layerednavigationbox')->getTechnicalAttributesByProductIds($productIds);
				
				$paramTechnicalDataAttributes = (strlen($this->technicaldata) > 0) ? array_filter(array_values(explode('_', $this->technicaldata))) : Array();
				
				foreach ($TechnicalDataAttributes as $groupName => $attributes){
					foreach ($attributes as $attributeId => $attributeData){
						
						if (! empty($paramTechnicalDataAttributes)){
							$attr = array_merge($paramTechnicalDataAttributes, (array) $attributeData['id']);
						}
						else{
							$attr = (array) $attributeData['id'];
						}
						
						$url = $this->registry->router->generate('frontend.productsearch', true, Array(
							'action' => 'index',
							'param' => $this->getParam(),
							'orderBy' => $this->orderBy,
							'orderDir' => $this->orderDir,
							'currentPage' => $this->currentPage,
							'viewType' => $this->view,
							'priceFrom' => $this->priceFrom,
							'priceTo' => $this->priceTo,
							'producers' => $this->producers,
							'attributes' => $this->attributes,
							'technicaldata' => implode('_', array_unique($attr))
						));
						
						$TechnicalDataAttributes[$groupName][$attributeId]['link'] = $url;
						$TechnicalDataAttributes[$groupName][$attributeId]['active'] = in_array($attributeData['id'], $paramTechnicalDataAttributes);
					}
				}
				
				$producers = App::getModel('product')->getProducerAllByProducts($productIds);
				
				$paramProducers = (strlen($this->producers) > 0) ? array_filter(array_values(explode('_', $this->producers))) : Array();
				
				foreach ($producers as $key => $producer){
					
					if (! empty($paramProducers)){
						$prod = array_merge($paramProducers, (array) $producer['id']);
					}
					else{
						$prod = (array) $producer['id'];
					}
					
					$url = $this->registry->router->generate('frontend.productsearch', true, Array(
						'action' => 'index',
						'param' => $this->getParam(),
						'orderBy' => $this->orderBy,
						'orderDir' => $this->orderDir,
						'currentPage' => $this->currentPage,
						'viewType' => $this->view,
						'priceFrom' => $this->priceFrom,
						'priceTo' => $this->priceTo,
						'producers' => implode('_', array_unique($prod)),
						'attributes' => $this->attributes,
						'technicaldata' => $this->technicaldata
					));
					
					$producers[$key]['link'] = $url;
					$producers[$key]['active'] = in_array($producer['id'], $paramProducers);
				}
				$this->registry->template->assign('priceFrom', $this->priceFrom);
				$this->registry->template->assign('priceTo', $this->priceTo);
				$this->registry->template->assign('groups', $Data);
				$this->registry->template->assign('technicaldatagroups', $TechnicalDataAttributes);
				$this->registry->template->assign('producers', $producers);
				break;
		}
		
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	protected function generateRedirectUrl ()
	{
		$priceFrom = App::getModel('formprotection')->cropDangerousCode($_POST['priceFrom']);
		$priceTo = App::getModel('formprotection')->cropDangerousCode($_POST['priceTo']);
		
		$producer = (! empty($_POST['producer'])) ? App::getModel('formprotection')->filterArray($_POST['producer']) : Array(
			0
		);
		
		$attribute = (! empty($_POST['attribute'])) ? App::getModel('formprotection')->filterArray($_POST['attribute']) : Array(
			0
		);
		
		$technicaldata = (! empty($_POST['technicaldata'])) ? App::getModel('formprotection')->filterArray($_POST['technicaldata']) : Array(
			0
		);
		
		switch ($this->registry->router->getCurrentController()) {
			case 'categorylist':
				$url = $this->registry->router->generate('frontend.categorylist', true, Array(
					'param' => $this->category['seo'],
					'orderBy' => $this->orderBy,
					'orderDir' => $this->orderDir,
					'currentPage' => $this->currentPage,
					'viewType' => $this->view,
					'priceFrom' => ($priceFrom > 0) ? $priceFrom : 0,
					'priceTo' => ($priceTo > 0) ? $priceTo : Core::PRICE_MAX,
					'producers' => implode('_', array_unique($producer)),
					'attributes' => implode('_', array_unique($attribute)),
					'technicaldata' => implode('_', array_unique($technicaldata))
				));
				break;
			case 'productsearch':
				$url = $this->registry->router->generate('frontend.productsearch', true, Array(
					'action' => 'index',
					'param' => $this->getParam(),
					'orderBy' => $this->orderBy,
					'orderDir' => $this->orderDir,
					'currentPage' => $this->currentPage,
					'viewType' => $this->view,
					'priceFrom' => ($priceFrom > 0) ? $priceFrom : 0,
					'priceTo' => ($priceTo > 0) ? $priceTo : Core::PRICE_MAX,
					'producers' => implode('_', array_unique($producer)),
					'attributes' => implode('_', array_unique($attribute)),
					'technicaldata' => implode('_', array_unique($technicaldata))
				));
				break;
		}
		
		return $url;
	}

	public function getBoxTypeClassname ()
	{
		return 'layout-box-type-layered-navigation';
	}
}