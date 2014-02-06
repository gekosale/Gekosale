<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Company
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Company\Controller\Admin;

use Gekosale\Core\Controller\Admin;

class Company extends Admin
{

    /**
     * @Gekosale\Core\Model(model="Gekosale\Plugin\Company\Model\Company")
     */
    public function index ()
    {
        $this->model->initDatagrid();
        
        $this->registerXajaxMethod('doDeleteCompany', $this->model);
        $this->registerXajaxMethod('doLoadCompany', $this->model);
        
        return Array(
            'datagrid_filter' => $this->model->getFilterData()
        );
    }

    /**
     * @Gekosale\Core\Model (model="Gekosale\Plugin\Company\Model\Company")
     * @Gekosale\Core\Form  (form="Gekosale\Plugin\Company\Form\Company")
     */
    public function add ()
    {
        $form = $this->form->init();
        
        if ($form->Validate()) {
            
            $this->model->save($form->getSubmitValues());
            
            return $this->redirect($this->generateUrl('admin.company.index'));
        }
        
        return Array(
            'form' => $form
        );
    }

    /**
     * @Gekosale\Core\Model (model="Gekosale\Plugin\Company\Model\Company")
     * @Gekosale\Core\Form  (form="Gekosale\Plugin\Company\Form\Company")
     */
    public function edit ($id)
    {
        $form = $this->form->init($id, $this->model->getPopulateData($id));
        
        if ($form->Validate()) {
            
            $this->model->save($form->getSubmitValues(), $id);
            
            return $this->redirect($this->generateUrl('admin.company.index'));
        }
        
        return Array(
            'form' => $form
        );
    }
}
