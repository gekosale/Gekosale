<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Component
 * @subpackage  Gekosale\Component\Configuration\Vat
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Component\Configuration\Vat\Form;

use Gekosale\Core\Form;
use Gekosale\Component\Configuration\Vat\Event\FormEvent;

class Vat extends Form
{

    public function init ($id = 0, $Data = Array())
    {
        $form = $this->AddForm(Array(
            'name' => 'vat',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $form->AddFieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('TXT_MAIN_DATA')
        ));
        
        $languageData = $requiredData->AddFieldsetLanguage(Array(
            'name' => 'language_data',
            'label' => $this->trans('TXT_LANGUAGE_DATA')
        ), $this->container);
        
        $languageData->AddTextField(Array(
            'name' => 'name',
            'label' => $this->trans('TXT_NAME'),
            'rules' => Array(
                $this->AddRuleRequired($this->trans('ERR_EMPTY_NAME'))
            )
        ));
        
        $requiredData->AddTextField(Array(
            'name' => 'value',
            'label' => $this->trans('TXT_VALUE'),
            'comment' => $this->trans('TXT_VALUE_IN_PERCENT'),
            'rules' => Array(
                $this->AddRuleRequired($this->trans('ERR_EMPTY_VALUE')),
                $this->AddRuleUnique($this->trans('ERR_VALUE_ALREADY_EXISTS'), 'vat', 'value', null, Array(
                    'column' => 'id',
                    'values' => $id
                ))
            ),
            'suffix' => '%',
            'filters' => Array(
                $this->AddFilterCommaToDotChanger()
            )
        ));
        
        $form->AddFilter($this->AddFilterNoCode());
        $form->AddFilter($this->AddFilterTrim());
        $form->AddFilter($this->AddFilterSecure());
        
        $event = new FormEvent($form, $Data);
        
        $this->getDispatcher()->dispatch(FormEvent::FORM_INIT_EVENT, $event);
        
        $form->Populate($event->getPopulateData());
        
        return $form;
    }
}
