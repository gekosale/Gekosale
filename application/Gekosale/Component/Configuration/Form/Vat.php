<?php

namespace Gekosale\Component\Configuration\Form;
use Gekosale\Core\Component\Form;
use FormEngine;

class Vat extends Form
{

    protected $populateData;

    public function setPopulateData ($Data)
    {
        $this->populateData = $Data;
    }

    public function initForm ()
    {
        $form = new FormEngine\Elements\Form(Array(
                                                  'name' => 'vat',
                                                  'action' => '',
                                                  'method' => 'post'
                                             ));

        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                                                                              'name' => 'required_data',
                                                                              'label' => $this->trans('TXT_MAIN_DATA')
                                                                         )));

        $languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
                                                                                              'name' => 'language_data',
                                                                                              'label' => $this->trans('TXT_LANGUAGE_DATA')
                                                                                         )));

        $languageData->AddChild(new FormEngine\Elements\TextField(Array(
                                                                       'name' => 'name',
                                                                       'label' => $this->trans('TXT_NAME'),
                                                                       'rules' => Array(
                                                                           new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
                                                                           new FormEngine\Rules\Unique($this->trans('ERR_VAT_ALREADY_EXISTS'), 'vattranslation', 'name', null, Array(
                                                                                                                                                                                    'column' => 'vatid',
                                                                                                                                                                                    'values' => (int) $this->registry->core->getParam()
                                                                                                                                                                               ))
                                                                       )
                                                                  )));

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
                                                                       'name' => 'value',
                                                                       'label' => $this->trans('TXT_VALUE'),
                                                                       'comment' => $this->trans('TXT_VALUE_IN_PERCENT'),
                                                                       'rules' => Array(
                                                                           new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE')),
                                                                           new FormEngine\Rules\Unique($this->trans('ERR_VALUE_ALREADY_EXISTS'), 'vat', 'value', null, Array(
                                                                                                                                                                            'column' => 'idvat',
                                                                                                                                                                            'values' => (int) $this->registry->core->getParam()
                                                                                                                                                                       ))
                                                                       ),
                                                                       'suffix' => '%',
                                                                       'filters' => Array(
                                                                           new FormEngine\Filters\CommaToDotChanger()
                                                                       )
                                                                  )));

        $Data = Event::dispatch($this, 'admin.vat.initForm', Array(
                                                                  'form' => $form,
                                                                  'id' => (int) $this->registry->core->getParam(),
                                                                  'data' => $this->populateData
                                                             ));

        if (! empty($Data)){
            $form->Populate($Data);
        }

        $form->AddFilter(new FormEngine\Filters\NoCode());
        $form->AddFilter(new FormEngine\Filters\Trim());
        $form->AddFilter(new FormEngine\Filters\Secure());

        return $form;
    }

}