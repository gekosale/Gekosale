<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Plugin\Currency\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

class CurrencyController extends AdminController
{

    public function index ()
    {
        $this->datagrid = $this->get('currency.datagrid')->init();
        
        return Array(
            'datagrid' => $this->datagrid,
            'datagrid_filter' => Array()
        );
    }

    public function add ()
    {
        $form = $this->get('currency.form')->init();
        
        if ($form->Validate()){
            
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
        
        if ($form->Validate()){
            
            $this->model->save($form->getSubmitValues(), $id);
            
            return $this->redirect($this->generateUrl('admin.vat.index'));
        }
        
        return Array(
            'form' => $form
        );
    }
}
