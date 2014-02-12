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

use Gekosale\Core\Controller\AdminController;
use Gekosale\Plugin\Vat\Repository\Vat as VatRepository;

class Vat extends AdminController
{

    public function index ()
    {
        $this->repository = new VatRepository();
        
        $this->model->initDatagrid();
        
        return Array(
            'datagrid_filter' => $this->model->getFilterData()
        );
    }

    public function add ()
    {
        $form = $this->form->init();
        
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
        $form = $this->form->init($id, $this->model->getPopulateData($id));
        
        if ($form->Validate()) {
            
            $this->model->save($form->getSubmitValues(), $id);
            
            return $this->redirect($this->generateUrl('admin.vat.index'));
        }
        
        return Array(
            'form' => $form
        );
    }
}
