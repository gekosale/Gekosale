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
use Gekosale\Plugin\Availability\Event\FormEvent;

class Availability extends Form
{

    public function init ($id = 0, $Data = Array())
    {
        $form = $this->AddForm(Array(
            'name' => 'availability',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $form->AddFieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('TXT_MAIN_DATA')
        ));
        
        $languageData = $requiredData->AddFieldsetLanguage(Array(
            'name' => 'language_data',
            'label' => $this->trans('TXT_LANGUAGE_DATA')
        ), $this->container);
        
        $languageData->addTextField(Array(
            'name' => 'name',
            'label' => $this->trans('TXT_NAME'),
            'rules' => Array(
                $this->AddRuleRequired($this->trans('ERR_EMPTY_NAME'))
            )
        ));
        
        $form->AddFilter($this->AddFilterNoCode());
        $form->AddFilter($this->AddFilterTrim());
        $form->AddFilter($this->AddFilterSecure());
        
        $event = new FormEvent($form, $Data);
        
        $this->getDispatcher()->dispatch(FormEvent::FORM_INIT_EVENT, $event);
        
        $form->Populate($event->getPopulateData());
        
        return $form;
    }
}
