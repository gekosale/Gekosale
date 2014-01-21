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
 * @package    Image_Captcha
 * @subpackage Background
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Abstrakcyjny model generowania tła
 * 
 * @category   Image
 * @package    Image_Captcha
 * @subpackage Background
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
abstract class Image_Captcha_Background
{  
    /**
     * Kolor
     *
     * @access protected
     * @var string
     */
    protected $color = '#fff';
    
    /**
     *  Komunikaty błędów
     */
    const   ERROR_COLOR = 'Wrong color';
    
    /**
     * Konstruktor
     *
     * @access public
     * @param  string|array $color kolor
     * @return void
     */
    public function __construct ($color = '#000')
    {
        $this->color  = $color;
    }
    
    /**
     * Zwraca identyfikator koloru
     *
     * @access public
     * @param  string   $color kolor
     * @param  resource $image strumień obrazu 
     * @return integer
     * @throws Image_Captcha_Exception
     */
    protected function _color ($color, $image)
    {
        $color = ($color{0} == '#') ? substr($color, 1) : $color;

        if((strlen($color) != 3 && strlen($color) != 6) || ((strlen($color) == 3) && $color{0} != $color{1} && $color{0} != $color{2})) {
            throw new Image_Captcha_Exception (self::ERROR_COLOR);
        }

        if(strlen($color) == 3) {
            $color .= $color;
        }

        $red   = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue  = hexdec(substr($color, 4, 2));

        return imagecolorallocate($image, $red, $green, $blue);
    }
}