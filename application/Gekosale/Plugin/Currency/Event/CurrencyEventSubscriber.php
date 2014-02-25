<?php

/**
 * Gekosale Open-Source E-Commerce Platform
 * 
 * This file is part of the Gekosale package.
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Currency\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;

class CurrencyEventSubscriber implements EventSubscriberInterface
{

    public function onFormInitAction (Event $event)
    {
        $repository = $event->getDispatcher()->getContainer()->get('currency.repository');
        
        $event->setPopulateData(Array(
            'required_data' => Array(
                'value1' => 'required_data1'
            )
        ));
    }

    public function onAdminMenuInitAction (Event $event)
    {
        $event->setMenuData(Array(
            'menu' => Array(
                'configuration'
            )
        ));
    }

    public static function getSubscribedEvents ()
    {
        return array(
            CurrencyFormEvent::FORM_INIT_EVENT => 'onFormInitAction',
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction'
        );
    }
}