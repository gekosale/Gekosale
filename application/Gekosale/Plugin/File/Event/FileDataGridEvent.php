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
namespace Gekosale\Plugin\File\Event;

use Gekosale\Core\Event\DataGridEvent;

/**
 * Class FileDataGridEvent
 *
 * @package Gekosale\Plugin\File\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
final class FileDataGridEvent extends DataGridEvent
{

    const DATAGRID_INIT_EVENT = 'file.datagrid.init';
}