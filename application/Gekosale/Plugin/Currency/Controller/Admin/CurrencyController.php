<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\CurrencyController
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Currency\Controller\Admin;

use Gekosale\Core\Controller\AdminController;
use Illuminate\Database\Schema;

class CurrencyController extends AdminController
{

    public function index ()
    {
//         $this->getAjaxManager()->registerFunction('LoadAllCurrencieslist', array(
//             $this,
//             'getCurrencieslistForAjax'
//         ));
        
//         $this->getAjaxManager()->process();
        
        //         Phery::instance()->set(array(
        //             'alias-for-function' => function  ($ajax_data)
        //             {
        //                 ob_start();
        //                 var_dump($ajax_data);
        //                 $data = ob_get_clean();
        

        //                 return PheryResponse::factory('#result')->html($data)
        //                     ->process();
        //             }
        //         ))
        //             ->process();
        
        $this->get('currency.datagrid')->init();
        
        print_r($this->get('currency.datagrid'));
        die();
        
        return Array(
            'datagrid_filter' => Array()
        );
    }

    public function add ()
    {
        $form = $this->get('currency.form');
        
        if ($form->Validate()) {
            
            $this->model->save($form->getSubmitValues());
            
            return $this->redirect($this->generateUrl('admin.vat.index'));
        }
        
        return Array(
            'form' => $form
        );
    }

    public function edit ($id)
    {
        $form = $this->get('currency.form');
        
        if ($form->Validate()) {
            
            $this->model->save($form->getSubmitValues(), $id);
            
            return $this->redirect($this->generateUrl('admin.vat.index'));
        }
        
        return Array(
            'form' => $form
        );
    }
}
