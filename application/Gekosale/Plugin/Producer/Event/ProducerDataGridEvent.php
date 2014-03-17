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

use Gekosale\Core\Event\DataGridEvent;

/**
 * Class ProducerDataGridEvent
 *
 * @package Gekosale\Plugin\Producer\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
final class ProducerDataGridEvent extends DataGridEvent
{

    const DATAGRID_INIT_EVENT = 'producer.datagrid.init';
}