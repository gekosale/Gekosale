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

class ModelEvent extends Event
{

    protected $submittedData;

    protected $id;

    public function __construct ($submittedData, $id)
    {
        $this->submittedData = $submittedData;
        $this->id = $id;
    }

    public function getSubmittedData ()
    {
        return $this->submittedData;
    }

    public function getId ()
    {
        return $this->id;
    }
}