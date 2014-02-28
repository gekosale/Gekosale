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
        $form = $this->addForm([
            'name' => 'language',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Basic settings')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required'))
            ]
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'translation',
            'label' => $this->trans('Translation'),
            'rules' => [
                $this->addRuleRequired($this->trans('Translation is required'))
            ]
        ]));

        $requiredData->addChild($this->addSelect([
            'name'    => 'locale',
            'label'   => $this->trans('Preferred locale'),
            'options' => $this->makeOptions($this->get('language.repository')->getAllLocaleToSelect())
        ]));

        $currencyData = $form->addChild($this->addFieldset([
            'name'  => 'currency_data',
            'label' => $this->trans('Currency settings')
        ]));

        $currencyData->addChild($this->addSelect([
            'name'    => 'currency_id',
            'label'   => $this->trans('Default currency'),
            'options' => $this->makeOptions($this->get('currency.repository')->getAllCurrencyToSelect())
        ]));

        $form->AddFilter($this->addFilterNoCode());
        $form->AddFilter($this->addFilterSecure());

        $event = new LanguageFormEvent($form, $languageData);

        $this->getDispatcher()->dispatch(LanguageFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
