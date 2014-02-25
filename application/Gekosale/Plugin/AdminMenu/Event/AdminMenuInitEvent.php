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

use Gekosale\Core\Event\AdminMenuEvent;

/**
 * Class AdminMenuInitEvent
 *
 * @package Gekosale\Plugin\Currency\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
final class AdminMenuInitEvent extends AdminMenuEvent
{

    const ADMIN_MENU_INIT_EVENT = 'admin_menu.init';
}