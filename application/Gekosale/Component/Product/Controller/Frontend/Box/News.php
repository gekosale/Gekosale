<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
//  * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: productnewsbox.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale\Component\Productnews\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

class ProductNews extends Box
{

    protected $dataset;

    protected $products;

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $this->currentPage = $this->getParam('currentPage', 1);
        $this->_currentParams = Array(
            'currentPage' => $this->currentPage
        );
        if (is_numeric($this->currentPage)){
            $dataset = App::getModel('productnews')->getDataset();
            if ($this->_boxAttributes['productsCount'] > 0){
                $dataset->setPagination($this->_boxAttributes['productsCount']);
            }
            else{
                $dataset->setPagination(PHP_INT_MAX);
            }
            $dataset->setOrderBy($this->_boxAttributes['orderBy'], $this->_boxAttributes['orderBy']);
            $dataset->setOrderDir($this->_boxAttributes['orderDir'], $this->_boxAttributes['orderDir']);
            $dataset->setCurrentPage($this->currentPage);
            $this->dataset = App::getModel('productnews')->getProductDataset();
        }
    }

    public function index ()
    {
        if ($this->registry->router->getCurrentController() != 'productnews'){
            $this->_boxAttributes['pagination'] = 0;
        }
        
        $this->registry->template->assign('view', $this->_boxAttributes['view']);
        $this->registry->template->assign('pagination', $this->_boxAttributes['pagination']);
        $this->registry->template->assign('dataset', $this->dataset);
        $this->registry->template->assign('paginationLinks', $this->createPaginationLinks());
        return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
    }

    protected function createPaginationLinks ()
    {
        $currentParams = $this->_currentParams;
        
        $paginationLinks = Array();
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage - 1;
            
            $paginationLinks['previous'] = Array(
                'link' => ($this->currentPage > 1) ? $this->registry->router->generate('frontend.productnews', true, $currentParams) : '',
                'class' => ($this->currentPage > 1) ? 'previous' : 'previous disabled',
                'label' => $this->trans('TXT_PREVIOUS')
            );
        }
        
        foreach ($this->dataset['totalPages'] as $page){
            
            $currentParams['currentPage'] = $page;
            
            $paginationLinks[$page] = Array(
                'link' => $this->registry->router->generate('frontend.productnews', true, $currentParams),
                'class' => ($this->currentPage == $page) ? 'active' : '',
                'label' => $page
            );
        }
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage + 1;
            
            $paginationLinks['next'] = Array(
                'link' => ($this->currentPage < end($this->dataset['totalPages'])) ? $this->registry->router->generate('frontend.productnews', true, $currentParams) : '',
                'class' => ($this->currentPage < end($this->dataset['totalPages'])) ? 'next' : 'next disabled',
                'label' => $this->trans('TXT_NEXT')
            );
        }
        
        return $paginationLinks;
    }

    public function getBoxTypeClassname ()
    {
        return 'layout-box-type-product-list';
    }

    public function boxVisible ()
    {
        if ($this->registry->router->getCurrentController() == 'productnews'){
            return true;
        }
        return ($this->dataset['total'] > 0) ? true : false;
    }
}