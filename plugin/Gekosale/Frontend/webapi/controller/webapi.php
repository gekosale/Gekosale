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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: staticcontent.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;

class WebApiController extends Component\Controller\Frontend
{

    public function index ()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] != 'application/json'){
            $methods = Array(
                'getProduct', //done
                'getProducts', //done
                'addProduct',
                'updateProduct',
                'deleteProduct',
                'getCategory',
                'getCategories',
                'getCategoriesTree',
                'addCategory', //done
                'updateCategory', //done
                'deleteCategory', //done
                'getProducer', //done
                'getProducers', //done
                'addProducer', //done
                'updateProducer',
                'deleteProducer',
                'getLanguages',
                'getCurrencies',
                'getOrder',
                'getOrders',
                'getClient',
                'getClients'
            );
            
            foreach ($methods as $method){
                if (! is_file(ROOTPATH . 'design' . DS . 'frontend' . DS . 'webapi' . DS . 'blocks' . DS . $method . '.tpl')){
                    file_put_contents(ROOTPATH . 'design' . DS . 'frontend' . DS . 'webapi' . DS . 'blocks' . DS . $method . '.tpl', '');
                }
            }
            $this->registry->template->assign('methods', $methods);
            $this->registry->template->display($this->loadTemplate('layout.tpl'));
        }
        else{
            $this->server = new Server($this->registry);
            $this->server->handle(App::getModel('webapi')) or print 'no request';
        }
    }
}