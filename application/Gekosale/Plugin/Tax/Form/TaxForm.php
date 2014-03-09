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
namespace Gekosale\Plugin\Tax\Form;

use Gekosale\Core\Form,
    Gekosale\Plugin\Tax\Event\TaxFormEvent;

/**
 * Class TaxForm
 *
 * @package Gekosale\Plugin\Tax\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class TaxForm extends Form
{

    /**
     * Initializes TaxForm
     *
     * @param array $taxData
     *
     * @return Form\Elements\Form
     */
    public function init($taxData = [])
    {
        $form = $this->addForm([
            'name' => 'tax'
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
                        'table'   => 'tax_translation',
                        'column'  => 'name',
                        'exclude' => [
                            'column' => 'tax_id',
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
            'rules'   => [
                $this->addRuleRequired($this->trans('Tax value is required')),
                $this->addRuleUnique($this->trans('Tax value already exists'),
                    [
                        'table'   => 'tax',
                        'column'  => 'value',
                        'exclude' => [
                            'column' => 'id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ],
            'suffix'  => '%',
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ]
        ]));

        $form->addFilter($this->addFilterNoCode());
        $form->addFilter($this->addFilterTrim());
        $form->addFilter($this->addFilterSecure());

        $event = new TaxFormEvent($form, $taxData);

        $this->getDispatcher()->dispatch(TaxFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
