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
namespace Gekosale\Plugin\Producer\Event;

use Gekosale\Core\Event\FormEvent;

/**
 * Class ProducerFormEvent
 *
 * @package Gekosale\Plugin\Producer\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
final class ProducerFormEvent extends FormEvent
{

    const FORM_INIT_EVENT = 'producer.form.init';
}