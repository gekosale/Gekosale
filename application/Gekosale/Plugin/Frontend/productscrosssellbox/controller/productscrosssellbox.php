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
 * $Revision: 602 $
 * $Author: gekosale $
 * $Date: 2011-11-07 22:45:33 +0100 (Pn, 07 lis 2011) $
 * $Id: productscrosssellbox.php 602 2011-11-07 21:45:33Z gekosale $
 */
namespace Gekosale\Plugin;

class ProductsCrossSellBoxController extends Component\Controller\Box
{

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $dataset = App::getModel('productscrosssellbox')->getDataset();
        if ($this->_boxAttributes['productsCount'] > 0){
            $dataset->setPagination($this->_boxAttributes['productsCount']);
        }
        else{
            $dataset->setPagination(PHP_INT_MAX);
        }
        $dataset->setOrderBy($this->_boxAttributes['orderBy'], $this->_boxAttributes['orderBy']);
        $dataset->setOrderDir($this->_boxAttributes['orderDir'], $this->_boxAttributes['orderDir']);
        $dataset->setCurrentPage(1);
        $products = App::getModel('productscrosssellbox')->getProductDataset();
        $this->dataset = $products;
    }

    public function index ()
    {
        $this->registry->template->assign('view', $this->_boxAttributes['view']);
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