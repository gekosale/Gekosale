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
        $form = $this->addForm([
            'name' => 'currency',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Basic information')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required'))
            ]
        ]));

        $requiredData->addChild($this->addSelect([
            'name'    => 'symbol',
            'label'   => $this->trans('Symbol'),
            'options' => $this->makeOptions($this->get('currency.repository')->getCurrencySymbols()),
            'rules'   => [
                $this->addRuleRequired($this->trans('Symbol is required'))
            ]
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'decimal_separator',
            'label' => $this->trans('Decimal separator'),
            'rules' => [
                $this->addRuleRequired($this->trans('Decimal separator is required'))
            ]
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'decimal_count',
            'label' => $this->trans('Decimal count'),
            'rules' => [
                $this->addRuleRequired($this->trans('Decimal count is required'))
            ]
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'thousand_separator',
            'label' => $this->trans('Thousands separator')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'positive_prefix',
            'label' => $this->trans('Positive prefix')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'positive_suffix',
            'label' => $this->trans('Positive suffix')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'negative_prefix',
            'label' => $this->trans('Negative prefix')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'negative_suffix',
            'label' => $this->trans('Negative suffix')
        ]));

        $form->AddFilter($this->addFilterNoCode());
        $form->AddFilter($this->addFilterSecure());

        $event = new CurrencyFormEvent($form, $currencyData);

        $this->getDispatcher()->dispatch(CurrencyFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
