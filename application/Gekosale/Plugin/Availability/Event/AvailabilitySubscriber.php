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
namespace Gekosale\Plugin\Availability\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;

/**
 * Class AvailabilitySubscriber
 *
 * @package Gekosale\Plugin\Availability\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilitySubscriber implements EventSubscriberInterface
{

    /**
     * $form = $event->getForm();
        
//         $form->fields['required_data']->addTextField(Array(
//             'name' => 'value1222',
//             'label' => 'Test1',
//             'suffix' => '%'
//         ));
        
//         $repository = $event->getDispatcher()->getContainer()->get('currency.repository');
        
//         $event->setPopulateData(Array(
//             'required_data2' => Array(
//                 'value2' => 'required_data2'
//             )
//         ));
     * @param Event $event
     */
    
    public function onFormInitAction (Event $event)
    {
    }

    public function onAdminMenuInitAction (Event $event)
    {
        $event->setMenuData(Array(
            'menu' => Array(
                'availability'
            )
        ));
    }

    public static function getSubscribedEvents ()
    {
        return array(
            'currency.form.init' => 'onFormInitAction',
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction'
        );
    }
}