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

namespace Gekosale\Plugin\PayPal\Processor;


class PayPalProcessor extends PaymentProcessor implements PaymentProcessorInterface
{
    public function getName()
    {
        return 'PayPal';
    }

    public function getAlias()
    {
        return 'paypal';
    }

    public function getConfigurationFields($form)
    {

    }

    public function onPreSaveOrderAction()
    {

    }

    public function onPostSaveOrderAction()
    {

    }
}