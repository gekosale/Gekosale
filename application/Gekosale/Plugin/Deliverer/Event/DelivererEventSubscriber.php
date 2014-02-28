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
namespace Gekosale\Plugin\Deliverer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;

/**
 * Class DelivererEventSubscriber
 *
 * @package Gekosale\Plugin\Deliverer\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class DelivererEventSubscriber implements EventSubscriberInterface
{

    public function onAdminMenuInitAction(Event $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction'
        );
    }
}