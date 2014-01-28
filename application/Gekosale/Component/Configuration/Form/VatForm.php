<?php

namespace Gekosale\Component\Configuration\Form;
use FormEngine;
use FormEngine\Elements\Form;

class Vat extends Form
{

    public function __construct ($container)
    {
        parent::__construct($container, Array(
            'name' => 'vat',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $this->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('TXT_MAIN_DATA')
        )));
        
        $languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage($container, Array(
            'name' => 'language_data',
            'label' => $this->trans('TXT_LANGUAGE_DATA')
        )));
        
        $languageData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'name',
            'label' => $this->trans('TXT_NAME'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
//                 new FormEngine\Rules\Unique($this->trans('ERR_VAT_ALREADY_EXISTS'), 'vattranslation', 'name', null, Array(
//                     'column' => 'vatid',
//                     'values' => $this->container->get('request')->attributes->get('id')
//                 ))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'value',
            'label' => $this->trans('TXT_VALUE'),
            'comment' => $this->trans('TXT_VALUE_IN_PERCENT'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE')),
//                 new FormEngine\Rules\Unique($this->trans('ERR_VALUE_ALREADY_EXISTS'), 'vat', 'value', null, Array(
//                     'column' => 'idvat',
//                     'values' => $this->container->get('request')->attributes->get('id')
//                 ))
            ),
            'suffix' => '%',
            'filters' => Array(
                new FormEngine\Filters\CommaToDotChanger()
            )
        )));
        
        //         $Data = Event::dispatch($this, 'admin.vat.initForm', Array(
        //             'form' => $form,
        //             'id' => (int) $this->registry->core->getParam(),
        //             'data' => $this->populateData
        //         ));
        

        $this->AddFilter(new FormEngine\Filters\NoCode());
        $this->AddFilter(new FormEngine\Filters\Trim());
        $this->AddFilter(new FormEngine\Filters\Secure());
        
        return $this;
    }
}