<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Component
 * @subpackage  Gekosale\Plugin\CurrencyForm
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Currency\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Currency\Event\FormEvent;

class CurrencyForm extends Form
{

    public function init ()
    {
        $form = $this->AddForm(Array(
            'name' => 'currency',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $form->AddFieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('Main data')
        ));
        
        $languageData = $requiredData->AddFieldsetLanguage(Array(
            'name' => 'language_data',
            'label' => $this->trans('Translation data')
        ), $this->container);
        
        $languageData->AddTextField(Array(
            'name' => 'name',
            'label' => $this->trans('Name'),
            'rules' => Array(
                $this->AddRuleRequired($this->trans('Name cannot be empty'))
            )
        ));
        
        $requiredData->AddTextField(Array(
            'name' => 'value',
            'label' => $this->trans('Value'),
            'comment' => $this->trans('Value in %'),
            'rules' => Array(
                $this->AddRuleRequired($this->trans('Value cannot be empty')),
//                 $this->AddRuleUnique($this->trans('Value is not unique'), 'currency', 'value', null, Array(
//                     'column' => 'id',
//                     'values' => $id
//                 ))
            ),
            'suffix' => '%',
            'filters' => Array(
                $this->AddFilterCommaToDotChanger()
            )
        ));
        
        $form->AddFilter($this->AddFilterNoCode());
        $form->AddFilter($this->AddFilterTrim());
        $form->AddFilter($this->AddFilterSecure());
        
        $event = new FormEvent($form);
        
        $this->getDispatcher()->dispatch(FormEvent::FORM_INIT_EVENT, $event);
        
        print_r($form);
        die();
        
        $form->Populate($event->getPopulateData());
        
        return $form;
    }
}
