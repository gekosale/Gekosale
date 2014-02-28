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
        $form = $this->addForm([
            'name' => 'vat'
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Basic settings')
        ]));

        $languageData = $requiredData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Language settings'),
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
                $this->addRuleUnique($this->trans('Tax rate already exists'),
                    [
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

        $requiredData->addChild($this->addTextField([
            'name'    => 'value',
            'label'   => $this->trans('Tax value'),
            'comment' => $this->trans('Tax value given in %'),
            //            'rules'   => [
            //                new FormEngine\Rules\Required($this->trans('Tax value is required')),
            //                new FormEngine\Rules\Unique($this->trans('Tax value already exists'), 'vat', 'value', null, [
            //                    'column' => 'id',
            //                    'values' => $this->getParam('id')
            //                ])
            //            ],
            //            'suffix'  => '%',
            //            'filters' => [
            //                new FormEngine\Filters\CommaToDotChanger()
            //            ]
        ]));

        $form->AddFilter($this->addFilterNoCode());
        $form->AddFilter($this->addFilterTrim());
        $form->AddFilter($this->addFilterSecure());

        $event = new VatFormEvent($form, $vatData);

        $this->getDispatcher()->dispatch(VatFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
