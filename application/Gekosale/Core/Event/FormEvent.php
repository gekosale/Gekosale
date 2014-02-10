<?php

/**
 * Gekosale Open-Source E-Commerce Platform
 * 
 * This file is part of the Gekosale package.
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Event;

use Symfony\Component\EventDispatcher\Event;
use Gekosale\Core\Form\Element\Form;

class FormEvent extends Event
{

    protected $form;

    protected $populateData = Array();

    /**
     * Constructor
     * 
     * @param Form $form
     */
    public function __construct (Form $form)
    {
        $this->form = $form;
    }

    /**
     * Returns a form instance
     * 
     * @return Form
     * 
     */
    public function getForm ()
    {
        return $this->form;
    }

    /**
     * Returns an array containing all data fetched from bound form events
     * 
     * @return array 
     */
    public function getPopulateData ()
    {
        return $this->populateData;
    }
    
    /**
     * Feeds an array containing data needed to populate the form
     * 
     * @param array $Data
     * 
     * @return void
     */
    public function setPopulateData (array $Data)
    {
        $this->populateData = array_merge_recursive($this->populateData, $Data);
    }
}