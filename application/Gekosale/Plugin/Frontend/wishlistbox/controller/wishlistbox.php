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
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: wishlistbox.php 576 2011-10-22 08:23:55Z gekosale $
 */
namespace Gekosale\Plugin;

class WishlistBoxController extends Component\Controller\Box
{

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $dataset = App::getModel('wishlist')->getDataset();
        $this->_boxAttributes['productsCount'] = 12;
        $this->_boxAttributes['pagination'] = 1;
        $this->_boxAttributes['view'] = 'grid';
        $this->_boxAttributes['orderBy'] = 'name';
        $this->_boxAttributes['orderDir'] = 'asc';
        $this->currentPage = $this->getParam('currentPage', 1);
        if ($this->_boxAttributes['productsCount'] > 0){
            $dataset->setPagination($this->_boxAttributes['productsCount']);
        }
        else{
            $dataset->setPagination(PHP_INT_MAX);
        }
        $dataset->setOrderBy($this->_boxAttributes['orderBy'], $this->_boxAttributes['orderBy']);
        $dataset->setOrderDir($this->_boxAttributes['orderDir'], $this->_boxAttributes['orderDir']);
        $dataset->setCurrentPage($this->currentPage);
        $this->dataset = App::getModel('wishlist')->getProductDataset();
    }

    public function index ()
    {
        $this->registry->xajax->registerFunction(array(
            'deleteProductFromWishList',
            App::getModel('wishlist'),
            'deleteAJAXProductFromWishList'
        ));
        
        $this->registry->template->assign('view', $this->_boxAttributes['view']);
        $this->registry->template->assign('pagination', $this->_boxAttributes['pagination']);
        $this->registry->template->assign('dataset', $this->dataset);
        $this->registry->template->assign('paginationLinks', $this->createPaginationLinks());
        
        if ($message = App::getContainer()->get('session')->getVolatileWishlistMessage()){
            $this->registry->template->assign('message', reset($message));
        }
        
        if ($error = App::getContainer()->get('session')->getVolatileWishlistError()){
            $this->registry->template->assign('message_error', reset($message));
        }
        
        return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
    }

    public function getBoxTypeClassname ()
    {
        if ($this->dataset['total'] > 0){
            return 'layout-box-type-product-list';
        }
    }

    protected function createPaginationLinks ()
    {
        $currentParams = array();
        $currentParams['currentPage'] = $this->currentPage;
        
        $paginationLinks = Array();
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage - 1;
            
            $paginationLinks['previous'] = Array(
                'link' => ($this->currentPage > 1) ? $this->registry->router->generate('frontend.wishlist', true, $currentParams) : '',
                'class' => ($this->currentPage > 1) ? 'previous' : 'previous disabled',
                'label' => $this->trans('TXT_PREVIOUS')
            );
        }
        
        foreach ($this->dataset['totalPages'] as $page){
            
            $currentParams['currentPage'] = $page;
            
            $paginationLinks[$page] = Array(
                'link' => $this->registry->router->generate('frontend.wishlist', true, $currentParams),
                'class' => ($this->currentPage == $page) ? 'active' : '',
                'label' => $page
            );
        }
        
        if ($this->dataset['totalPages'] > 1){
            
            $currentParams['currentPage'] = $this->currentPage + 1;
            
            $paginationLinks['next'] = Array(
                'link' => ($this->currentPage < end($this->dataset['totalPages'])) ? $this->registry->router->generate('frontend.wishlist', true, $currentParams) : '',
                'class' => ($this->currentPage < end($this->dataset['totalPages'])) ? 'next' : 'next disabled',
                'label' => $this->trans('TXT_NEXT')
            );
        }
        
        return $paginationLinks;
    }
}