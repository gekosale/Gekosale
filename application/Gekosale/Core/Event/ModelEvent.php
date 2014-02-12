<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ModelEvent
 *
 * @package Gekosale\Core\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ModelEvent extends Event
{

	/**
	 * @var array
	 */
	protected $data = Array();

	/**
	 * @var null
	 */
	protected $id = NULL;

	public function __construct($data, $id)
	{
		$this->data = $data;
		$this->id   = $id;
	}

	public function getSubmittedData()
	{
		return $this->data;
	}

	public function getId()
	{
		return $this->id;
	}
}