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
use FormEngine;

class CurrencyForm extends Form
{

    public function init ()
    {
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'currencieslist',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('TXT_MAIN_DATA')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'name',
            'label' => $this->trans('TXT_CURRENCY_NAME'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'symbol',
            'label' => $this->trans('TXT_CURRENCY_SYMBOL'),
            'options' => FormEngine\Option::Make(App::getModel('currencieslist')->getCurrenciesALLToSelect()),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CURRENCY_SYMBOL'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'decimalseparator',
            'label' => $this->trans('TXT_CURRENCY_DECIMAL_SEPARATOR'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CURRENCY_DECIMAL_SEPARATOR'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'decimalcount',
            'label' => $this->trans('TXT_CURRENCY_DECIMAL_COUNT'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CURRENCY_DECIMAL_COUNT'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'thousandseparator',
            'label' => $this->trans('TXT_CURRENCY_THOUSAND_SEPARATOR')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'positivepreffix',
            'label' => $this->trans('TXT_CURRENCY_POSITIVE_PREFFIX')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'positivesuffix',
            'label' => $this->trans('TXT_CURRENCY_POSITIVE_SUFFIX')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'negativepreffix',
            'label' => $this->trans('TXT_CURRENCY_NEGATIVE_PREFFIX')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'negativesuffix',
            'label' => $this->trans('TXT_CURRENCY_NEGATIVE_SUFFIX')
        )));
        
        $exchangeData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'exchange_data',
            'label' => $this->trans('TXT_CURRENCY_EXCHANGE')
        )));
        
        $currencies = App::getModel('currencieslist')->getCurrencies();
        
        foreach ($currencies as $key => $currency){
            
            $exchangeData->AddChild(new FormEngine\Elements\TextField(Array(
                'name' => 'currency_' . $currency['idcurrency'],
                'label' => $currency['currencysymbol'],
                'filters' => Array(
                    new FormEngine\Filters\CommaToDotChanger()
                ),
                'rules' => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_EXCHANGE_DATA'))
                )
            )));
        }
        
        $layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'view_data',
            'label' => $this->trans('TXT_STORES')
        )));
        
        $layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
            'name' => 'view',
            'label' => $this->trans('TXT_VIEW'),
            'default' => Helper::getViewIdsDefault()
        )));
        
        $form->AddFilter(new FormEngine\Filters\NoCode());
        
        $form->AddFilter(new FormEngine\Filters\Secure());
        
        $event = new FormEvent($form);
        
        $this->getDispatcher()->dispatch(FormEvent::FORM_INIT_EVENT, $event);
        
        $form->Populate($event->getPopulateData());
        
        return $form;
    }
}
