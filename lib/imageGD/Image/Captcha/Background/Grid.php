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
 * Generowanie tła, kratka
 * 
 * @category   Image
 * @package    Image_Captcha
 * @subpackage Background
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Captcha_Background_Grid extends Image_Captcha_Background implements Image_Captcha_Background_Interface
{
    /**
     * Odstępy
     *
     * @access protected
     * @var integer
     */
    protected $step = 5;
    
    /**
     * Konstruktor
     *
     * @access public
     * @param  string|array $color kolor
     * @param  integer      $step  odstępy
     * @return void
     */
    public function __construct ($color = '#000', $step = 5)
    {
        parent::__construct ($color);
        $this->step = $step;
    }
    
    /**
     * Generowanie tła
     *
     * @access public
     * @param  Image_Captcha $image obiekt obrazu
     * @return void
     */
    public function render (Image_Captcha $image)
    {
        for($i = 0; $i <= $image->imageWidth(); $i+= $this->step) {    
            imageline( $image->imageResource(), 0, $i, $image->imageWidth(), $i, $this->_color(((is_array($this->color)) ? $this->color[array_rand($this->color)] : $this->color), $image->imageResource()));
            imageline( $image->imageResource(), $i, 0, $i, $image->imageHeight(), $this->_color(((is_array($this->color)) ? $this->color[array_rand($this->color)] : $this->color), $image->imageResource()));
        }
        
        imageline  ($image->imageResource(), 0, $image->imageHeight()-1, $image->imageWidth(), $image->imageHeight()-1, $this->_color(((is_array($this->color)) ? $this->color[array_rand($this->color)] : $this->color), $image->imageResource()));
        imageline  ($image->imageResource(), $image->imageWidth()-1, 0, $image->imageWidth()-1, $image->imageHeight(), $this->_color(((is_array($this->color)) ? $this->color[array_rand($this->color)] : $this->color), $image->imageResource()));
    }
}