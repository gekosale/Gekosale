<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: updater.php 619 2011-12-19 21:09:00Z gekosale $ 
 */

namespace Gekosale;

class ContextmenuModel extends Component\Model
{
    protected $items = array();
    protected $title = null;
    
    public function __construct(){
        $this->title = $this->trans('TXT_RELATED_TOOLS');
        
    }
    
    public function setTitle($title){
        $this->title = $title;
    }
    
    /**
     * Add option to context menu in admin panel
     * @param string $label
     * @param string $link 
     * @param string $section
     */
    public function add($label, $link, $section = null) {
        $this->items[] = array('label' => $label, 'link' => $link);
    }
    
    /**
     * Bind context menu to current page
     * @param mixed $event
     * @param Request $request 
     */
	public function bind($event, $request) {  
        $event->setReturnValues(array(
           
            'contextmenu_items' => $this->items,
            'contextmenu_title' => $this->title
        ));
    }
}