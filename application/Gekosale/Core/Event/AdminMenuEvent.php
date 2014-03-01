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
namespace Gekosale\Core\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FormEvent
 *
 * @package Gekosale\Core\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AdminMenuEvent extends Event
{

    protected $menuData = Array();

    /**
     * Constructor
     *
     * @param Form $form
     */
    public function __construct ($menuData)
    {
        $this->menuData = $menuData;
    }

    /**
     * Returns an array containing current menu data
     *
     * @return array
     */
    public function getMenuData ()
    {
        return $this->menuData;
    }

    /**
     * Appends data to menu
     *
     * @param array $Data
     *
     * @return void
     */
    public function setMenuData (array $Data)
    {
        $this->menuData = array_merge_recursive($this->menuData, $Data);
    }
}