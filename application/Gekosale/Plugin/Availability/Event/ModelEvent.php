<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Component
 * @subpackage  Gekosale\Plugin\Availability
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Availability\Event;

use Gekosale\Core\Event\ModelSaveEvent;

/**
 * Class ModelEvent
 *
 * @package Gekosale\Plugin\Availability\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ModelEvent extends ModelSaveEvent
{

	const MODEL_SAVE_EVENT = 'availability.model.save';
}