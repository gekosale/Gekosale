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
namespace Gekosale\Plugin\Currency\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Currency\Event\CurrencyFormEvent;
use FormEngine;

/**
 * Class CurrencyForm
 *
 * @package Gekosale\Plugin\Currency\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CurrencyForm extends Form
{

    public function init ()
    {
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'currency',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('Basic information')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'name',
            'label' => $this->trans('Name'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Name is required'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'symbol',
            'label' => $this->trans('Symbol'),
            'options' => FormEngine\Option::Make($this->get('currency.repository')->getCurrencySymbols()),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Symbol is required'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'decimalseparator',
            'label' => $this->trans('Decimal separator'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Decimal separator is required'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'decimalcount',
            'label' => $this->trans('Decimal count'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Decimal count is required'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'thousandseparator',
            'label' => $this->trans('Thousands separator')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'positiveprefix',
            'label' => $this->trans('Positive prefix')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'positivesufix',
            'label' => $this->trans('Positive sufix')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'negativeprefix',
            'label' => $this->trans('Negative prefix')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'negativesufix',
            'label' => $this->trans('Negative sufix')
        )));
        
        $exchangeData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'exchange_data',
            'label' => $this->trans('TXT_CURRENCY_EXCHANGE')
        )));
        
        $form->AddFilter(new FormEngine\Filters\NoCode());
        
        $form->AddFilter(new FormEngine\Filters\Secure());
        
        $event = new CurrencyFormEvent($form);
        
        $this->getDispatcher()->dispatch(CurrencyFormEvent::FORM_INIT_EVENT, $event);
        
        $form->Populate($event->getPopulateData());
        
        return $form;
    }
}
