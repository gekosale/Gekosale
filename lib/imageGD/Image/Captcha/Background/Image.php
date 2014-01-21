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
 * Generowanie tła, na podstawie obiektu obrazu
 * 
 * @category   Image
 * @package    Image_Captcha
 * @subpackage Background
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Captcha_Background_Image implements Image_Captcha_Background_Interface
{
    /**
     * Obiekt obrazu
     *
     * @access protected
     * @var Image|boolean
     */
    protected $image = false;
    
    /**
     * Konstruktor
     *
     * @access public
     * @param  Image        $image obiekt obrazu
     * @return void
     */
    public function __construct (Image $image)
    {
        $this->image = $image;
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
        $image->compose ($this->image);
    }

}