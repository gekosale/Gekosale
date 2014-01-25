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
 * $Id: productbuyalsobox.php 576 2011-10-22 08:23:55Z gekosale $
 */
namespace Gekosale\Component\Productbuyalso\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend\Box;

class ProductBuyAlso extends Box
{

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $this->productid = App::getModel('product')->getProductIdBySeo($this->getParam());
        $this->_boxAttributes['productsCount'] = 10;
        $this->_boxAttributes['pagination'] = 1;
        $this->_boxAttributes['view'] = 'list';
        $this->_boxAttributes['orderBy'] = 'name';
        $this->_boxAttributes['orderDir'] = 'asc';
        $dataset = App::getModel('productbuyalsobox')->getDataset();
        if ($this->_boxAttributes['productsCount'] > 0){
            $dataset->setPagination($this->_boxAttributes['productsCount']);
        }
        else{
            $dataset->setPagination(PHP_INT_MAX);
        }
        $dataset->setSQLParams(Array(
            'ids' => App::getModel('product')->getAlsoProduct($this->productid),
            'productid' => (int) $this->productid
        ));
        $dataset->setOrderBy($this->_boxAttributes['orderBy'], $this->_boxAttributes['orderBy']);
        $dataset->setOrderDir($this->_boxAttributes['orderDir'], $this->_boxAttributes['orderDir']);
        $dataset->setCurrentPage(1);
        $products = App::getModel('productbuyalsobox')->getProductDataset();
        $this->dataset = $products;
    }

    public function index ()
    {
        $this->registry->template->assign('view', $this->_boxAttributes['view']);
        $this->registry->template->assign('pagination', $this->_boxAttributes['pagination']);
        $this->registry->template->assign('dataset', $this->dataset);
        return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
    }

    public function boxVisible ()
    {
        return ($this->dataset['total'] > 0) ? true : false;
    }

    public function getBoxTypeClassname ()
    {
        if ($this->dataset['total'] > 0){
            return 'layout-box-type-product-list';
        }
    }
}