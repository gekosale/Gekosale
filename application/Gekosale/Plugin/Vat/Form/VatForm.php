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
namespace Gekosale\Plugin\Vat\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Vat\Event\VatFormEvent;
use FormEngine;

/**
 * Class VatForm
 *
 * @package Gekosale\Plugin\Vat\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class VatForm extends Form
{

    public function init($vatData = [])
    {
        $form = new FormEngine\Elements\Form([
            'name' => 'vat',
        ]);

        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Basic settings')
        ]));

        $languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Language settings'),
            'languages' => $this->getLanguages()
        ]));

        $languageData->AddChild(new FormEngine\Elements\TextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                new FormEngine\Rules\Required($this->trans('Name is required')),
                new FormEngine\Rules\Unique(
                    $this->container,
                    [
                        'message' => $this->trans('Tax rate already exists'),
                        'table'   => 'vat_translation',
                        'column'  => 'name',
                        'exclude' => [
                            'column' => 'vat_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ]
        ]));

        $requiredData->AddChild(new FormEngine\Elements\TextField([
            'name'    => 'value',
            'label'   => $this->trans('Tax value'),
            'comment' => $this->trans('Tax value given in %'),
            'rules'   => [
                new FormEngine\Rules\Required($this->trans('Tax value is required')),
                new FormEngine\Rules\Unique($this->trans('Tax value already exists'), 'vat', 'value', null, [
                    'column' => 'id',
                    'values' => $this->getParam('id')
                ])
            ],
            'suffix'  => '%',
            'filters' => [
                new FormEngine\Filters\CommaToDotChanger()
            ]
        ]));

        $form->AddFilter($this->AddFilterNoCode());
        $form->AddFilter($this->AddFilterTrim());
        $form->AddFilter($this->AddFilterSecure());

        $event = new VatFormEvent($form, $vatData);

        $this->getDispatcher()->dispatch(VatFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
