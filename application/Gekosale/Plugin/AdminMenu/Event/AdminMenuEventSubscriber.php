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
namespace Gekosale\Plugin\AdminMenu\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;
use Gekosale\Core\Model\AdminMenu;

class AdminMenuEventSubscriber implements EventSubscriberInterface
{

    public function onKernelController (FilterControllerEvent $event)
    {
        $container = $event->getDispatcher()->getContainer();
        
        if (! $container->get('session')->has('admin.menu')){
            
            $menuData = Array(
                'menu' => Array(
                    'sales',
                    'crm'
                )
            );
            
            $eventData = new AdminMenuInitEvent($menuData);
            
            $event->getDispatcher()->dispatch(AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT, $eventData);
            
            $adminMenuData = $eventData->getMenuData();
            
            $container->get('session')->set('admin.menu', $eventData->getMenuData());
        }
        
        $adminMenuData = $container->get('session')->get('admin.menu');
        
        $templateVars = $event->getRequest()->attributes->get('_template_vars');

        print_r($adminMenuData);
        die();

        $event->getRequest()->attributes->set('_template_vars', array_merge($templateVars, $adminMenuData));
    }

    public static function getSubscribedEvents ()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                'onKernelController',
                - 256
            )
        );
    }
}