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

    public function init($currencyData = Array())
    {
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'currency',
        ));

        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name'  => 'required_data',
            'label' => $this->trans('Basic information')
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Name is required'))
            )
        )));

        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name'    => 'symbol',
            'label'   => $this->trans('Symbol'),
            'options' => FormEngine\Option::Make($this->get('currency.repository')->getCurrencySymbols()),
            'rules'   => Array(
                new FormEngine\Rules\Required($this->trans('Symbol is required'))
            )
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'decimal_separator',
            'label' => $this->trans('Decimal separator'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Decimal separator is required'))
            )
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'decimal_count',
            'label' => $this->trans('Decimal count'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('Decimal count is required'))
            )
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'thousand_separator',
            'label' => $this->trans('Thousands separator')
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'positive_prefix',
            'label' => $this->trans('Positive prefix')
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'positive_sufix',
            'label' => $this->trans('Positive sufix')
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'negative_prefix',
            'label' => $this->trans('Negative prefix')
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'negative_sufix',
            'label' => $this->trans('Negative sufix')
        )));

        $form->AddFilter(new FormEngine\Filters\NoCode());

        $form->AddFilter(new FormEngine\Filters\Secure());

        $event = new CurrencyFormEvent($form, $currencyData);

        $this->getDispatcher()->dispatch(CurrencyFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
