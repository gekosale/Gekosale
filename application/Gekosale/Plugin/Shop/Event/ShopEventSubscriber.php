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
namespace Gekosale\Plugin\Shop\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;

/**
 * Class ShopEventSubscriber
 *
 * @package Gekosale\Plugin\Shop\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopEventSubscriber implements EventSubscriberInterface
{
    public function onAdminMenuInitAction(Event $event)
    {

    }

    public function onKernelController(Event $event)
    {
        if ($event->getRequestType() == HttpKernelInterface::SUB_REQUEST) {
            return;
        }

        $container = $event->getDispatcher()->getContainer();
        $request   = $event->getRequest();
        $session   = $container->get('session');

        if (!$session->has('shop.settings')) {
            $shop = $container->get('shop.repository')->getShopByHost();
            $container->get('session')->set('shop.settings', $shop);
        }

        $templateVars                  = $request->attributes->get('_template_vars');
        $templateVars['shop_settings'] = $container->get('session')->get('shop.settings');
        $templateVars['shops']         = $container->get('shop.repository')->getAllShopToSelect();

        $request->attributes->set('_template_vars', $templateVars);

    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER                  => [
                'onKernelController',
                -256
            ],
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction',
        ];
    }
}