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

    public function init($vatData = Array())
    {
        $form = new $this->addForm(Array(
            'name'   => 'vat',
        ));

        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name'  => 'required_data',
            'label' => _('TXT_MAIN_DATA')
        )));

        $languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
            'name'  => 'language_data',
            'label' => _('TXT_LANGUAGE_DATA')
        )));

        $languageData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'name',
            'label' => _('TXT_NAME'),
            'rules' => Array(
                new FormEngine\Rules\Required(_('ERR_EMPTY_NAME')),
                new FormEngine\Rules\Unique(_('ERR_VAT_ALREADY_EXISTS'), 'vattranslation', 'name', null, Array(
                    'column' => 'vatid',
                    'values' => (int)$this->registry->core->getParam()
                ))
            )
        )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name'    => 'value',
            'label'   => _('TXT_VALUE'),
            'comment' => _('TXT_VALUE_IN_PERCENT'),
            'rules'   => Array(
                new FormEngine\Rules\Required(_('ERR_EMPTY_VALUE')),
                new FormEngine\Rules\Unique(_('ERR_VALUE_ALREADY_EXISTS'), 'vat', 'value', null, Array(
                    'column' => 'idvat',
                    'values' => (int)$this->registry->core->getParam()
                ))
            ),
            'suffix'  => '%',
            'filters' => Array(
                new FormEngine\Filters\CommaToDotChanger()
            )
        )));

        $form->AddFilter($this->AddFilterNoCode());
        $form->AddFilter($this->AddFilterTrim());
        $form->AddFilter($this->AddFilterSecure());

        $event = new VatFormEvent($form, $vatData);

        $this->getDispatcher()->dispatch(VatFormEvent::FORM_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
