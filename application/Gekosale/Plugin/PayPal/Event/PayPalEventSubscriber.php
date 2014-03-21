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
namespace Gekosale\Plugin\PayPal\Event;

use Gekosale\Plugin\PaymentMethod\Event\PaymentMethodFormEvent;
use Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Gekosale\Plugin\AdminMenu\Event\AdminMenuInitEvent;

/**
 * Class PayPalEventSubscriber
 *
 * @package Gekosale\Plugin\PayPal\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PayPalEventSubscriber implements EventSubscriberInterface
{

    public function onPaymentMethodFormInitAction(Event $event)
    {

    }

    public static function getSubscribedEvents()
    {
        return array(
            PaymentMethodFormEvent::FORM_INIT_EVENT => 'onPaymentMethodFormInitAction'
        );
    }
}