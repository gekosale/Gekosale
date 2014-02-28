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
namespace Gekosale\Plugin\Language\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Language\Event\LanguageFormEvent;
use FormEngine;

/**
 * Class LanguageForm
 *
 * @package Gekosale\Plugin\Language\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageForm extends Form
{

    public function init($languageData = [])
    {
        $form = new FormEngine\Elements\Form([
            'name' => 'language',
        ]);

        $requiredData = $form->addChild(new FormEngine\Elements\Fieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Basic settings')
        ]));

        $requiredData->addChild(new FormEngine\Elements\TextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                new FormEngine\Rules\Required($this->trans('Name is required'))
            ]
        ]));

        $requiredData->addChild(new FormEngine\Elements\TextField([
            'name'  => 'translation',
            'label' => $this->trans('Translation'),
            'rules' => [
                new FormEngine\Rules\Required($this->trans('Translation is required'))
            ]
        ]));

        $currencyData = $form->addChild(new FormEngine\Elements\Fieldset([
            'name'  => 'currency_data',
            'label' => $this->trans('Currency settings')
        ]));

        $currencyData->addChild(new FormEngine\Elements\Select([
            'name'    => 'currency_id',
            'label'   => $this->trans('Default currency'),
            'options' => FormEngine\Option::Make($this->get('currency.repository')->getAllCurrencyToSelect())
        ]));

        $form->AddFilter(new FormEngine\Filters\NoCode());
        $form->AddFilter(new FormEngine\Filters\Secure());

        $event = new LanguageFormEvent($form, $languageData);

        $this->getDispatcher()->dispatch(LanguageFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
