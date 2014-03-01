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

use Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;
use Gekosale\Plugin\Language\Event\LanguageFormEvent;

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

    public function onFormInitAction(Event $event)
    {
//        $form               = $event->getForm();
//        $formHelper         = $event->getDispatcher()->getContainer()->get('form_helper');
//        $translationService = $event->getDispatcher()->getContainer()->get('translation');
//
//        $layerData = $form->addChild($formHelper->addFieldset([
//            'name'  => 'shop_data',
//            'label' => 'Shop settings'
//        ]));
//
//        $layerData->addChild($formHelper->addShopSelector([
//            'name'  => 'shops',
//            'label' => $translationService->trans('Shops')
//        ]));
    }

    public static function getSubscribedEvents()
    {
        return array(
            AdminMenuInitEvent::ADMIN_MENU_INIT_EVENT => 'onAdminMenuInitAction',
            LanguageFormEvent::FORM_INIT_EVENT        => 'onFormInitAction',
        );
    }
}