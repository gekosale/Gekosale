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
namespace Gekosale\Plugin\ClientGroup\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\ClientGroup\Event\ClientGroupFormEvent;

/**
 * Class ClientGroupForm
 *
 * @package Gekosale\Plugin\ClientGroup\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ClientGroupForm extends Form
{
    /**
     * Initializes client_group Form
     *
     * @param array $client_groupData
     *
     * @return Form\Elements\Form
     */
    public function init($client_groupData = [])
    {
        $form = $this->addForm([
            'name' => 'client_group',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'    => 'discount',
            'label'   => $this->trans('Discount'),
            'comment' => $this->trans('Discount for particular client group'),
            'suffix'  => '%',
            'rules'   => [
                $this->addRuleCustom($this->trans('Discount must be between 0-100'), function ($value) {
                    return ($value >= 0 && $value <= 100);
                })
            ],
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ],
        ]));

        $languageData = $requiredData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Translations'),
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
                $this->addRuleLanguageUnique($this->trans('Name already exists'),
                    [
                        'table'   => 'client_group_translation',
                        'column'  => 'name',
                        'exclude' => [
                            'column' => 'client_group_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ]
        ]));

        $form->addFilters([
            $this->addFilterNoCode(),
            $this->addFilterTrim(),
            $this->addFilterSecure()
        ]);

        $event = new ClientGroupFormEvent($form, $client_groupData);

        $this->getDispatcher()->dispatch(ClientGroupFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
