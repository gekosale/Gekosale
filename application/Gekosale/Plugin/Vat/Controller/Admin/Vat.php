<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Vat
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Vat\Controller\Admin;

use Gekosale\Core\Controller\Admin;

/**
 * @Gekosale\Core\Model(model="Gekosale\Plugin\Vat\Model\Vat")
 */
class Vat extends Admin
{

    public function index ()
    {
        $this->model->initDatagrid();
        
        $this->registerXajaxMethod('doDeleteVat', $this->model);
        $this->registerXajaxMethod('doLoadVat', $this->model);
        
        return Array(
            'datagrid_filter' => $this->model->getFilterData()
        );
    }

    public function add ()
    {
        $form = $this->getForm('Gekosale\Plugin\Vat\Form\Vat')->init();
        
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
        $form = $this->getForm('Gekosale\Plugin\Vat\Form\Vat')->init($id, $this->model->getPopulateData($id));
        
        if ($form->Validate()) {
            
            $this->model->save($form->getSubmitValues(), $id);
            
            return $this->redirect($this->generateUrl('admin.vat.index'));
        }
        
        return Array(
            'form' => $form
        );
    }
}
