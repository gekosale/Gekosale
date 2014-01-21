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
 * $Revision: 616 $
 * $Author: gekosale $
 * $Date: 2011-12-05 10:13:27 +0100 (Pn, 05 gru 2011) $
 * $Id: productsearchlistbox.php 616 2011-12-05 09:13:27Z gekosale $
 */
namespace Gekosale;

class ProductSearchListBoxController extends Component\Controller\Box
{

    protected $_currentParams = Array();

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
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
            'action' => 'index',
            'param' => $this->getParam(),
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
        
        $this->searchPhrase = str_replace('_', '', App::getModel('formprotection')->cropDangerousCode($this->getParam()));
        $this->getProductsTemplate();
        
        $this->registry->template->assign('searchPhrase', $this->searchPhrase);
        $this->registry->template->assign('phrase', $this->searchPhrase);
        $this->registry->template->assign('showpagination', $this->_boxAttributes['pagination']);
        $this->registry->template->assign('sorting', $this->createSorting());
        $this->registry->template->assign('viewSwitcher', $this->createViewSwitcher());
        $this->registry->template->assign('view', $this->view);
        $this->registry->template->assign('orderBy', $this->orderBy);
        $this->registry->template->assign('orderDir', $this->orderDir);
        $this->registry->template->assign('producers', App::getModel('product')->getProducerAll());
        $this->registry->template->assign('dataset', $this->dataset);
        $this->registry->template->assign('pagination', $this->_boxAttributes['pagination']);
        $this->registry->template->assign('paginationLinks', $this->createPaginationLinks());
        
        if ($this->dataset['total'] == 0){
            App::redirectUrl($this->registry->router->generate('frontend.productsearch', true, Array(
                'action' => 'noresults',
                'param' => $this->searchPhrase
            )));
        }
        
        return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
    }

    public function getBoxTypeClassname ()
    {
        return 'layout-box-type-product-list';
    }

    protected function getProductsTemplate ()
    {
        $producer = (strlen($this->producers) > 0) ? array_filter(array_values(explode('_', $this->producers))) : Array();
        $attributes = array_filter((strlen($this->attributes) > 0) ? array_filter(array_values(explode('_', $this->attributes))) : Array());
        $technicaldata = array_filter((strlen($this->technicaldata) > 0) ? array_filter(array_values(explode('_', $this->technicaldata))) : Array());
        
        $Products = App::getModel('layerednavigationbox')->getProductsForAttributes(0, $attributes, $technicaldata);
        $dataset = App::getModel('productsearch')->getDataset();
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
            'categoryid' => (int) 0,
            'clientid' => App::getContainer()->get('session')->getActiveClientid(),
            'producer' => $producer,
            'filterbyproducer' => (! empty($producer)) ? 1 : 0,
            'pricefrom' => (float) $this->priceFrom,
            'priceto' => (float) $this->priceTo,
            'name' => '%' . str_replace(' ', '%', $this->searchPhrase) . '%',
            'enablelayer' => (! empty($Products) && (count($attributes) > 0 || count($technicaldata) > 0)) ? 1 : 0,
            'products' => $Products
        ));
        $products = App::getModel('productsearch')->getProductDataset();
        $this->dataset = $products;
    }

    protected function createPaginationLinks ()
    {
        $currentParams = $this->_currentParams;
        
        $paginationLinks = Array();
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage - 1;
            
            $paginationLinks['previous'] = Array(
                'link' => ($this->currentPage > 1) ? $this->registry->router->generate('frontend.productsearch', true, $currentParams) : '',
                'class' => ($this->currentPage > 1) ? 'previous' : 'previous disabled',
                'label' => $this->trans('TXT_PREVIOUS')
            );
        }
        
        foreach ($this->dataset['totalPages'] as $page){
            
            $currentParams['currentPage'] = $page;
            
            $paginationLinks[$page] = Array(
                'link' => $this->registry->router->generate('frontend.productsearch', true, $currentParams),
                'class' => ($this->currentPage == $page) ? 'active' : '',
                'label' => $page
            );
        }
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage + 1;
            
            $paginationLinks['next'] = Array(
                'link' => ($this->currentPage < end($this->dataset['totalPages'])) ? $this->registry->router->generate('frontend.productsearch', true, $currentParams) : '',
                'class' => ($this->currentPage < end($this->dataset['totalPages'])) ? 'next' : 'next disabled',
                'label' => $this->trans('TXT_NEXT')
            );
        }
        
        return $paginationLinks;
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
            'link' => $this->registry->router->generate('frontend.productsearch', true, $currentParams),
            'label' => $this->trans('TXT_DEFAULT'),
            'active' => ($this->orderBy == 'default' && $this->orderDir == 'asc') ? true : false
        );
        
        foreach ($columns as $orderBy => $orderByLabel){
            foreach ($directions as $orderDir => $orderDirLabel){
                
                $currentParams['orderBy'] = $orderBy;
                $currentParams['orderDir'] = $orderDir;
                
                $sorting[] = Array(
                    'link' => $this->registry->router->generate('frontend.productsearch', true, $currentParams),
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
                'link' => $this->registry->router->generate('frontend.productsearch', true, $currentParams),
                'label' => $label,
                'type' => $view,
                'active' => ($this->view == $view) ? true : false
            );
        }
        
        return $switcher;
    }
}
