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

        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset([
            'name'  => 'required_data',
            'label' => $this->trans('TXT_MAIN_DATA')
        ]));

        $requiredData->AddChild(new FormEngine\Elements\TextField([
            'name'    => 'name',
            'label'   => $this->trans('TXT_NAME'),
            'comment' => $this->trans('TXT_EXAMPLE') . ': en_EN',
            'rules'   => [
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME'))
            ]
        ]));

        $requiredData->AddChild(new FormEngine\Elements\TextField([
            'name'    => 'translation',
            'label'   => $this->trans('TXT_TRANSLATION'),
            'comment' => $this->trans('TXT_EXAMPLE') . ': TXT_ENGLISH',
            'rules'   => [
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TRANSLATION'))
            ]
        ]));

        $currencyData = $form->AddChild(new FormEngine\Elements\Fieldset([
            'name'  => 'currency_data',
            'label' => $this->trans('TXT_CURRENCY_DATA')
        ]));

        $currencyData->AddChild(new FormEngine\Elements\Select([
            'name'    => 'currency_id',
            'label'   => $this->trans('TXT_DEFAULT_LANGUAGE_CURRENCY'),
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
