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
namespace Gekosale\Plugin\HomePage\Event;

use Gekosale\Plugin\Layout\Event\LayoutPageFormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class HomePageEventSubscriber
 *
 * @package Gekosale\Plugin\HomePage\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class HomePageEventSubscriber implements EventSubscriberInterface
{

    public function onLayoutPageTreeInitAction(GenericEvent $event)
    {
        $event->setArgument('home_page', 'Home Page');
    }

    public static function getSubscribedEvents()
    {
        return array(
            LayoutPageFormEvent::TREE_INIT_EVENT => 'onLayoutPageTreeInitAction'
        );
    }
}