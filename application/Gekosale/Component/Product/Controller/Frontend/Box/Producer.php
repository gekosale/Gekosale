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
 * $Revision: 250 $
 * $Author: gekosale $
 * $Date: 2011-07-07 20:59:21 +0200 (Cz, 07 lip 2011) $
 * $Id: productsincategorybox.php 250 2011-07-07 18:59:21Z gekosale $
 */
namespace Gekosale\Component\Producerlist\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

class ProducerList extends Box
{

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $this->producer = App::getModel('producerlistbox')->getProducerBySeo($this->getParam());
        $this->dataset = Array();
    }

    public function index ()
    {
        if (strlen($this->getParam()) > 0 && empty($this->producer)){
            App::redirectSeo($this->registry->router->generate('frontend.producerlist', true));
        }
        
        if (! empty($this->producer)){
            $this->orderBy = $this->getParam('orderBy', 'default');
            $this->orderDir = $this->getParam('orderDir', 'asc');
            $this->currentPage = $this->getParam('currentPage', 1);
            $this->view = $this->getParam('viewType', $this->_boxAttributes['view']);
            $this->collection = $this->getParam('collection', 0);
            
            $this->producers = $this->getParam('producers');
            $this->attributes = $this->getParam('attributes');
            
            $this->priceFrom = $this->getParam('priceFrom', 0);
            $this->priceTo = $this->getParam('priceTo', Core::PRICE_MAX);
            
            $this->_currentParams = Array(
                'param' => $this->producer['seo'],
                'collection' => $this->collection,
                'currentPage' => $this->currentPage,
                'viewType' => $this->view,
                'priceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo,
                'producers' => $this->producers,
                'orderBy' => $this->orderBy,
                'orderDir' => $this->orderDir
            );
            
            $this->collectionData = App::getModel('collection')->getCollectionBySeo($this->collection);
            
            $this->getProductsTemplate();
            
            $this->registry->template->assign('view', (int) $this->view);
            $this->registry->template->assign('currentPage', $this->currentPage);
            $this->registry->template->assign('collection', $this->collection);
            $this->registry->template->assign('collectionData', $this->collectionData);
            $this->registry->template->assign('orderBy', $this->orderBy);
            $this->registry->template->assign('orderDir', $this->orderDir);
            $this->registry->template->assign('sorting', $this->createSorting());
            $this->registry->template->assign('viewSwitcher', $this->createViewSwitcher());
            $this->registry->template->assign('dataset', $this->dataset);
            $this->registry->template->assign('pagination', $this->_boxAttributes['pagination']);
            $this->registry->template->assign('paginationLinks', $this->createPaginationLinks());
            $this->registry->template->assign('producer', $this->producer);
            return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
        }
        else{
            $this->registry->template->assign('producers', App::getModel('producerlist')->getProducerAll());
            return $this->registry->template->fetch($this->loadTemplate('list.tpl'));
        }
    }

    public function getBoxHeading ()
    {
        if (! empty($this->producer)){
            return $this->producer['name'];
        }
        else{
            return $this->trans('TXT_PRODUCER_LIST');
        }
    }

    public function getBoxTypeClassname ()
    {
        return 'layout-box-type-product-list';
    }

    protected function getProductsTemplate ()
    {
        $dataset = App::getModel('producerlistbox')->getDataset();
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
            'clientid' => App::getContainer()->get('session')->getActiveClientid(),
            'producer' => $this->producer['id'],
            'collection' => isset($this->collectionData['id']) ? $this->collectionData['id'] : 0,
            'pricefrom' => (float) $this->priceFrom,
            'priceto' => (float) $this->priceTo
        ));
        
        $products = App::getModel('producerlistbox')->getProductDataset();
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
            'link' => $this->registry->router->generate('frontend.producerlist', true, $currentParams),
            'label' => $this->trans('TXT_DEFAULT'),
            'active' => ($this->orderBy == 'default' && $this->orderDir == 'asc') ? true : false
        );
        
        foreach ($columns as $orderBy => $orderByLabel){
            foreach ($directions as $orderDir => $orderDirLabel){
                
                $currentParams['orderBy'] = $orderBy;
                $currentParams['orderDir'] = $orderDir;
                
                $sorting[] = Array(
                    'link' => $this->registry->router->generate('frontend.producerlist', true, $currentParams),
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
                'link' => $this->registry->router->generate('frontend.producerlist', true, $currentParams),
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
                'link' => ($this->currentPage > 1) ? $this->registry->router->generate('frontend.producerlist', true, $currentParams) : '',
                'class' => ($this->currentPage > 1) ? 'previous' : 'previous disabled',
                'label' => $this->trans('TXT_PREVIOUS')
            );
        }
        
        foreach ($this->dataset['totalPages'] as $page){
            
            $currentParams['currentPage'] = $page;
            
            $paginationLinks[$page] = Array(
                'link' => $this->registry->router->generate('frontend.producerlist', true, $currentParams),
                'class' => ($this->currentPage == $page) ? 'active' : '',
                'label' => $page
            );
        }
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage + 1;
            
            $paginationLinks['next'] = Array(
                'link' => ($this->currentPage < end($this->dataset['totalPages'])) ? $this->registry->router->generate('frontend.producerlist', true, $currentParams) : '',
                'class' => ($this->currentPage < end($this->dataset['totalPages'])) ? 'next' : 'next disabled',
                'label' => $this->trans('TXT_NEXT')
            );
        }
        
        return $paginationLinks;
    }
}