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
 * Generowanie tła, kółeczka
 * 
 * @category   Image
 * @package    Image_Captcha
 * @subpackage Background
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Captcha_Background_Dots extends Image_Captcha_Background implements Image_Captcha_Background_Interface
{
    /**
     * Generowanie tła
     *
     * @access public
     * @param  Image_Captcha $image obiekt obrazu
     * @return void
     */
    public function render (Image_Captcha $image)
    {
        $pts = array();
        
        for($i = 0; $i < round($image->imageWidth() / 1.5); $i++)    {    
            $x = rand(0, $image->imageWidth());    
            $y = rand(0, $image->imageHeight());       
            
            if(!in_array($x.'_'.$y, $pts ) ){    
                imageellipse($image->imageResource(), $x, $y, rand(2, 7), rand(3, 6), $this->_color(((is_array($this->color)) ? $this->color[array_rand($this->color)] : $this->color), $image->imageResource()));
                $pts[] = $x.'_'.$y;    
            } else {
                $i--;    
            }
        }
    }

}