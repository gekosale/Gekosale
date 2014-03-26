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
namespace Gekosale\Plugin\Product\Event;

use Gekosale\Plugin\Layout\Event\LayoutPageFormEvent;
use Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ProductEventSubscriber
 *
 * @package Gekosale\Plugin\Product\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductEventSubscriber implements EventSubscriberInterface
{

    public function onLayoutPageTreeInitAction(GenericEvent $event)
    {
        $event->setArgument('product', 'Product');
    }

    public function onAdminMenuInitAction(Event $event)
    {
    }

    public function onProductDataGridInitAction(Event $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction',
            ProductDataGridEvent::DATAGRID_INIT_EVENT => 'onProductDataGridInitAction',
            LayoutPageFormEvent::TREE_INIT_EVENT      => 'onLayoutPageTreeInitAction'
        );
    }
}