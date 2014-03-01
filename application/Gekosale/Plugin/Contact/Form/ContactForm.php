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
namespace Gekosale\Plugin\Contact\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Contact\Event\ContactFormEvent;

/**
 * Class ContactForm
 *
 * @package Gekosale\Plugin\Contact\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ContactForm extends Form
{
    /**
     * Initializes contact Form
     *
     * @param array $contactData
     *
     * @return Form\Elements\Form
     */
    public function init($contactData = [])
    {
        $form = $this->addForm(Array(
            'name'   => 'contact',
            'action' => '',
            'method' => 'post'
        ));

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
        ]));

        $requiredData->addChild($this->addCheckBox([
            'name'  => 'is_enabled',
            'label' => $this->trans('Enabled'),
        ]));

        $translationData = $form->addChild($this->addFieldset([
            'name'  => 'translation_data',
            'label' => $this->trans('Translations')
        ]));

        $languageData = $translationData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Translations'),
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
            ]
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'email',
            'label' => $this->trans('E-mail'),
            'rules' => [
                $this->addRuleRequired($this->trans('E-mail is required')),
                $this->addRuleEmail('E-mail is not valid')
            ]
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'phone',
            'label' => $this->trans('Phone'),
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'street',
            'label' => $this->trans('Street'),
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'streetno',
            'label' => $this->trans('Street number'),
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'flatno',
            'label' => $this->trans('Flat number'),
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'province',
            'label' => $this->trans('Province'),
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'postcode',
            'label' => $this->trans('Post code'),
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'city',
            'label' => $this->trans('City'),
        ]));

        $languageData->addChild($this->addSelect([
            'name'    => 'country',
            'label'   => $this->trans('Country'),
            'options' => $this->makeOptions($this->get('country.repository')->all())
        ]));

        $form->addFilter($this->addFilterNoCode());

        $form->addFilter($this->addFilterTrim());

        $form->addFilter($this->addFilterSecure());

        $event = new ContactFormEvent($form, $contactData);

        $this->getDispatcher()->dispatch(ContactFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
