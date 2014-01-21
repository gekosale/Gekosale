<?php
/**
 * Image
 *
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * @link       http://code.google.com/p/nweb-image
 *
 * @category   Image
 * @package    Image_Validator
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Walidator stosunku długości boków obrazu
 * 
 * @category   Image
 * @package    Image_Validator
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Validator_Proportion extends Image_Validator_Abstract
{
    /**
     * Długość boku
     *
     * @access protected
     * @var integer|null
     */
    protected $x = null;
    
    /**
     * Długość boku
     *
     * @access protected
     * @var integer|null
     */
    protected $y = null;
    
    /**
     * Zwraca prawdę lub fałsz
     * @access public
     * @param  integer $x szerokość
     * @param  integer $y wysokość
     * @return void
     */
    public function __construct ($x = null, $y = null) 
    {
        $this->x    = $x;
        $this->y    = $y;
        
        $this->message = 'stosunek długości boków pliku "%s" jest inny niż ' . $this->x . ':' . $this->y;
    }
    
    /**
     * Zwraca prawdę lub fałsz
     *
     * @access public
     * @return boolean
     */
    public function isValid () 
    {
        return ($this->x/$this->y) == ($this->image->imageWidth()/$this->image->imageHeight());
    }
}