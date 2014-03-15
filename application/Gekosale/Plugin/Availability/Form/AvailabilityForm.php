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
namespace Gekosale\Plugin\Availability\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Availability\Event\AvailabilityFormEvent;

/**
 * Class AvailabilityForm
 *
 * @package Gekosale\Plugin\Availability\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilityForm extends Form
{
    /**
     * Initializes availability Form
     *
     * @param array $availabilityData
     *
     * @return Form\Elements\Form
     */
    public function init($availabilityData = [])
    {
        $form = $this->addForm([
            'name'   => 'availability',
            'action' => '',
            'method' => 'post'
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
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
                        'table'   => 'availability_translation',
                        'column'  => 'name',
                        'exclude' => [
                            'column' => 'availability_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ]
        ]));

        $languageData->addChild($this->addTextArea([
            'name'  => 'description',
            'label' => $this->trans('Description')
        ]));

        $form->addFilters([
            $this->addFilterNoCode(),
            $this->addFilterTrim(),
            $this->addFilterSecure()
        ]);

        $event = new AvailabilityFormEvent($form, $availabilityData);

        $this->getDispatcher()->dispatch(AvailabilityFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
