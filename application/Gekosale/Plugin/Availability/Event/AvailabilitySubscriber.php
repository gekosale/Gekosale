<?php

namespace Gekosale\Plugin\Availability\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AvailabilitySubscriber implements EventSubscriberInterface
{

    public function onFormInitAction (Event $event)
    {
        $form = $event->getForm();

        $form->fields['required_data']->addTextField(Array(
            'name' => 'value1222',
            'label' => 'Test1',
            'suffix' => '%'
        ));

        $repository = $event->getDispatcher()->getContainer()->get('currency.repository');
        
        $event->setPopulateData(Array(
            'required_data2' => Array(
                'value2' => 'required_data2'
            )
        ));
    }

    public static function getSubscribedEvents ()
    {
        return array(
            'currency.form.init' => 'onFormInitAction'
        );
    }
}