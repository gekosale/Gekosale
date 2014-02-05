<?php

namespace Gekosale\Core\Event\Subscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Gekosale\Component\Configuration\Vat\Event\FormEvent;

class VatSubscriber implements EventSubscriberInterface
{

    public function onFormInitAction (Event $event)
    {
        $form = $event->getForm();
        
        $form->fields['required_data']->AddTextField(Array(
            'name' => 'value1',
            'label' => 'Test1',
            'suffix' => '%'
        ));
        
        $data = $event->getPopulateData();
        
        $event->setPopulateData(Array(
            'required_data' => Array(
                'value1' => 'required_data1'
            )
        ));
    }

    public function onFormSaveAction (Event $event)
    {
        $data = $event->getSubmittedData();
        $id = $event->getId();
    }

    public static function getSubscribedEvents ()
    {
        return array(
            FormEvent::FORM_INIT_EVENT => 'onFormInitAction',
            'vat.form.save' => 'onFormSaveAction'
        );
    }
}