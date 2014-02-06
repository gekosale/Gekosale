<?php

namespace Gekosale\Core\Event\Subscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Gekosale\Plugin\Vat\Event\FormEvent;

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
        
        $event->setPopulateData(Array(
            'required_data' => Array(
                'value1' => 'required_data1'
            )
        ));
    }

    public static function getSubscribedEvents ()
    {
        return array(
            FormEvent::FORM_INIT_EVENT => 'onFormInitAction',
        );
    }
}