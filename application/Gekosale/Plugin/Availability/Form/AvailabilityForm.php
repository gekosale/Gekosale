<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Component
 * @subpackage  Gekosale\Plugin\Availability
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Availability\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Availability\Event\AvailabilityFormEvent;

class AvailabilityForm extends Form
{

    public function init($availabilityData = [])
    {
        $form = $this->addForm(Array(
            'name'   => 'availability',
            'action' => '',
            'method' => 'post'
        ));

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

        $form->addFilter($this->addFilterNoCode());

        $form->addFilter($this->addFilterTrim());

        $form->addFilter($this->addFilterSecure());

        $event = new AvailabilityFormEvent($form, $availabilityData);

        $this->getDispatcher()->dispatch(AvailabilityFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
