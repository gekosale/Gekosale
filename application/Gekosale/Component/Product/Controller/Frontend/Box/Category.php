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
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: productsincategorybox.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale\Component\Productsincategory\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

class ProductsInCategory extends Box
{

    protected $_currentParams = Array();

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $this->category = App::getModel('categorylist')->getCurrentCategory();
        $this->dataset = Array();
    }

    public function index ()
    {
        $this->orderBy = $this->getParam('orderBy', 'default');
        $this->orderDir = $this->getParam('orderDir', 'asc');
        $this->currentPage = $this->getParam('currentPage', 1);
        $this->view = $this->getParam('viewType', $this->_boxAttributes['view']);
        
        $this->producers = $this->getParam('producers', 0);
        $this->attributes = $this->getParam('attributes', 0);
        $this->technicaldata = $this->getParam('technicaldata', 0);
        
        $this->priceFrom = $this->getParam('priceFrom', 0);
        $this->priceTo = $this->getParam('priceTo', Core::PRICE_MAX);
        
        $this->_currentParams = Array(
            'param' => $this->category['seo'],
            'currentPage' => $this->currentPage,
            'viewType' => $this->view,
            'priceFrom' => $this->priceFrom,
            'priceTo' => $this->priceTo,
            'producers' => $this->producers,
            'orderBy' => $this->orderBy,
            'orderDir' => $this->orderDir,
            'attributes' => $this->attributes,
            'technicaldata' => $this->technicaldata
        );
        
        $this->getProductsTemplate();
        
        $subcategories = App::getModel('categorylist')->getCategoryMenuTop($this->category['id']);
        
        if ($this->dataset['total'] > 0 || count($subcategories) > 0){
            $this->registry->template->assign('subcategories', array_chunk($subcategories, 3));
            $this->registry->template->assign('currentCategory', $this->category);
            $this->registry->template->assign('view', (int) $this->view);
            $this->registry->template->assign('currentPage', $this->currentPage);
            $this->registry->template->assign('orderBy', $this->orderBy);
            $this->registry->template->assign('orderDir', $this->orderDir);
            $this->registry->template->assign('currentProducers', $this->producers);
            $this->registry->template->assign('currentAttributes', $this->attributes);
            $this->registry->template->assign('currentTechnicalData', $this->technicaldata);
            $this->registry->template->assign('sorting', $this->createSorting());
            $this->registry->template->assign('viewSwitcher', $this->createViewSwitcher());
            $this->registry->template->assign('dataset', $this->dataset);
            $this->registry->template->assign('pagination', $this->_boxAttributes['pagination']);
            $this->registry->template->assign('paginationLinks', $this->createPaginationLinks());
            $this->registry->template->assign('categoryPromotions', $this->getCategoryPromotions());
            $this->registry->template->assign('categoryNews', $this->getCategoryNews());
            return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
        }
        else{
            $this->registry->template->assign('currentCategory', $this->category);
            $this->registry->template->assign('view', 0);
            $this->registry->template->assign('dataset', App::getModel('recommendations')->getPromotions(4));
            return $this->registry->template->fetch($this->loadTemplate('index_no_products.tpl'));
        }
    }

    public function getBoxHeading ()
    {
        return $this->category['name'];
    }

    public function getBoxTypeClassname ()
    {
        return 'layout-box-type-product-list';
    }

    protected function getCategoryPromotions ()
    {
        $dataset = App::getModel('productpromotion')->getDataset();
        $dataset->setPagination(4);
        $dataset->setOrderBy('random', 'random');
        $dataset->setCurrentPage(1);
        return App::getModel('productpromotion')->getProductDataset();
    }

    protected function getCategoryNews ()
    {
        $dataset = App::getModel('productnews')->getDataset();
        $dataset->setPagination(4);
        $dataset->setOrderBy('random', 'random');
        $dataset->setCurrentPage(1);
        return App::getModel('productnews')->getProductDataset();
    }

    protected function getProductsTemplate ()
    {
        $producer = (strlen($this->producers) > 0) ? array_filter(array_values(explode('_', $this->producers))) : Array();
        
        $attributes = array_filter((strlen($this->attributes) > 0) ? array_filter(array_values(explode('_', $this->attributes))) : Array());
        
        $technicaldata = array_filter((strlen($this->technicaldata) > 0) ? array_filter(array_values(explode('_', $this->technicaldata))) : Array());
        
        $Products = App::getModel('layerednavigationbox')->getProductsForAttributes((int) $this->category['id'], $attributes, $technicaldata);
        
        $dataset = App::getModel('product')->getDataset();
        if ($this->_boxAttributes['productsCount'] > 0){
            $dataset->setPagination($this->_boxAttributes['productsCount']);
        }
        else{
            $dataset->setPagination(PHP_INT_MAX);
        }
        $dataset->setCurrentPage($this->currentPage);
        $dataset->setOrderBy('name', $this->orderBy);
        $dataset->setOrderDir('asc', $this->orderDir);
        $dataset->setSQLParams(Array(
            'categoryid' => (int) $this->category['id'],
            'clientid' => App::getContainer()->get('session')->getActiveClientid(),
            'producer' => $producer,
            'pricefrom' => (float) $this->priceFrom,
            'priceto' => (float) $this->priceTo,
            'filterbyproducer' => (! empty($producer)) ? 1 : 0,
            'enablelayer' => (! empty($Products) && (count($attributes) > 0 || count($technicaldata) > 0)) ? 1 : 0,
            'products' => $Products
        ));
        $products = App::getModel('product')->getProductDataset();
        $this->dataset = $products;
        $this->registry->template->assign('items', $products['rows']);
        $this->registry->template->assign('view', $this->view);
    }

    protected function createSorting ()
    {
        $columns = Array(
            'name' => $this->trans('TXT_NAME'),
            'price' => $this->trans('TXT_PRICE'),
            'rating' => $this->trans('TXT_AVERAGE_OPINION'),
            'opinions' => $this->trans('TXT_OPINIONS_QTY'),
            'adddate' => $this->trans('TXT_ADDDATE')
        );
        
        $directions = Array(
            'asc' => $this->trans('TXT_ASC'),
            'desc' => $this->trans('TXT_DESC')
        );
        
        $sorting = Array();
        
        $currentParams = $this->_currentParams;
        
        $currentParams['orderBy'] = 'default';
        $currentParams['orderDir'] = 'asc';
        
        $sorting[] = Array(
            'link' => $this->registry->router->generate('frontend.categorylist', true, $currentParams),
            'label' => $this->trans('TXT_DEFAULT'),
            'active' => ($this->orderBy == 'default' && $this->orderDir == 'asc') ? true : false
        );
        
        foreach ($columns as $orderBy => $orderByLabel){
            foreach ($directions as $orderDir => $orderDirLabel){
                
                $currentParams['orderBy'] = $orderBy;
                $currentParams['orderDir'] = $orderDir;
                
                $sorting[] = Array(
                    'link' => $this->registry->router->generate('frontend.categorylist', true, $currentParams),
                    'label' => $orderByLabel . ' - ' . $orderDirLabel,
                    'active' => ($this->orderBy == $orderBy && $this->orderDir == $orderDir) ? true : false
                );
            }
        }
        
        return $sorting;
    }

    protected function createViewSwitcher ()
    {
        $viewTypes = Array(
            0 => $this->trans('TXT_VIEW_GRID'),
            1 => $this->trans('TXT_VIEW_LIST')
        );
        
        $switcher = Array();
        
        $currentParams = $this->_currentParams;
        
        foreach ($viewTypes as $view => $label){
            
            $currentParams['viewType'] = $view;
            
            $switcher[] = Array(
                'link' => $this->registry->router->generate('frontend.categorylist', true, $currentParams),
                'label' => $label,
                'type' => $view,
                'active' => ($this->view == $view) ? true : false
            );
        }
        
        return $switcher;
    }

    protected function createPaginationLinks ()
    {
        $currentParams = $this->_currentParams;
        
        $paginationLinks = Array();
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage - 1;
            
            $paginationLinks['previous'] = Array(
                'link' => ($this->currentPage > 1) ? $this->registry->router->generate('frontend.categorylist', true, $currentParams) : '',
                'class' => ($this->currentPage > 1) ? 'previous' : 'previous disabled',
                'label' => $this->trans('TXT_PREVIOUS')
            );
        }
        
        foreach ($this->dataset['totalPages'] as $page){
            
            $currentParams['currentPage'] = $page;
            
            $paginationLinks[$page] = Array(
                'link' => $this->registry->router->generate('frontend.categorylist', true, $currentParams),
                'class' => ($this->currentPage == $page) ? 'active' : '',
                'label' => $page
            );
        }
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage + 1;
            
            $paginationLinks['next'] = Array(
                'link' => ($this->currentPage < end($this->dataset['totalPages'])) ? $this->registry->router->generate('frontend.categorylist', true, $currentParams) : '',
                'class' => ($this->currentPage < end($this->dataset['totalPages'])) ? 'next' : 'next disabled',
                'label' => $this->trans('TXT_NEXT')
            );
        }
        
        return $paginationLinks;
    }
}