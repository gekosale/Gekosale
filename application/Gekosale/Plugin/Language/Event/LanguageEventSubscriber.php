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
namespace Gekosale\Plugin\Language\Event;

use Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\HttpKernel\KernelEvents,
    Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;

/**
 * Class LanguageEventSubscriber
 *
 * @package Gekosale\Plugin\Language\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageEventSubscriber implements EventSubscriberInterface
{
    /**
     * Resolves language id from Request using current locale
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
//        echo $request->getHttpHost();
//        echo $request->getLocale();
    }

    /**
     * Appends items to admin menu
     *
     * @param Event $event
     */
    public function onAdminMenuInitAction(Event $event)
    {

    }

    /**
     * Return array containing all subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST                     => 'onKernelRequest',
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction'
        );
    }
}