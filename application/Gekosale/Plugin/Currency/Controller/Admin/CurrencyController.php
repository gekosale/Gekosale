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
        $this->getFlashBag()->add('notice', 'Profile updated');
        
        print_r($this->getFlashBag()->get('notice'));die();
        
        foreach ($this->getFlashBag()->get('notice') as $message) {
            echo "<div class='flash-notice'>$message</div>";
        }
        
//         if (! $this->getDb()->schema()->hasTable('users')) {
//             $this->getDb()->schema()->create('users', function  ($table)
//             {
//                 $table->increments('id');
//                 $table->string('email')->unique();
//                 $table->timestamps();
//             });
//         }
        
//         die();
        
        return Array(
//             'datagrid_filter' => $this->model->getFilterData()
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
