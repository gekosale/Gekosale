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
use Gekosale\Core\Form\Element\Form;

class FormInitEvent extends Event
{

    protected $form;

    protected $populateData;

    public function __construct (Form $form, $populateData)
    {
        $this->form = $form;
        $this->populateData = $populateData;
    }

    public function getForm ()
    {
        return $this->form;
    }

    public function getPopulateData ()
    {
        return $this->populateData;
    }

    public function setPopulateData ($Data)
    {
        $this->populateData = array_merge($this->populateData, $Data);
    }
}