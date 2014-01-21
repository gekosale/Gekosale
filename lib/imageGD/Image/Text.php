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
 * @package    Image_Text
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Abstrakcyjny obiekt tekstu
 * 
 * @category   Image
 * @package    Image_Text
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
abstract class Image_Text
{
    /**
     * Obiekt Image
     *
     * @access protected
     * @var Image|boolean
     */
    protected $image = false;
    
    /**
     * Katalog z czcionkami
     *
     * @access protected
     * @var array
     */
    protected $fontDirs = array();

    /**
     * Tryb cichy, bez wyrzucania wyjątków
     *
     * @access protected
     * @var boolean
     */
    protected $quiet = false;

    /**
     * Pozycja
     */
    const    TOP      = 0;
    const    MIDDLE   = 1;
    const    BOTTOM   = 2;
    const    LEFT     = 4;
    const    CENTER   = 8;
    const    RIGHT    = 16;
    const    CENTERED = 9;
    const    FIXED    = 92;

    /**
     * Dodatkowe teksty
     */
    const    RESOLUTION = 25;
    const    SIZE       = 52;
    
    /**
     *  Komunikaty błędów
     */
     const   FONT_FILE_NO_EXISTS = 'Font file not exists';
     const   FONT_DIR_NO_EXISTS  = 'Font directory not exists';
     const   ERROR_TEXT          = 'Error writing text';
     const   ERROR_COLOR         = 'Wrong color';
     
    /**
     * Konstruktor
     *
     * @access public
     * @param  Image   $image obiekt obrazu
     * @param  boolean $quiet tryb cichy
     * @return void
     */
    public function __construct (Image $image, $quiet = false)
    {
        $this->image  = $image;
        $this->quiet  = $quiet;        
    }

    /**
     * Definuje katalog z plikami czcionek
     *
     * @access public
     * @param  string     $dir katalog z czcionkami
     * @param  string     $extension rozszeżenie plików
     * @return Image_Text
     * @throws Image_Text_Exception
     */
    public function setFontDir ($dir = null, $extension = null)
    {
        $dir = realpath($dir);
        if(file_exists($dir)) {
            $this->fontDirs[$dir] = $extension;
        } elseif(!$this->quiet) {
            throw new Image_Text_Exception (self::FONT_DIR_NO_EXISTS);
        }
        
    }

    /**
     * Wybiera czcionkę
     *
     * @access public
     * @param  string $name nazwa czcionki
     * @return string
     * @throws Image_Text_Exception
     */
    protected function getFont ($name = null)
    {
        $name = basename($name);
        
        foreach ($this->fontDirs as $dir => $extension) {
            $file = $dir . DIRECTORY_SEPARATOR . $name . ((!is_null($extension)) ? ('.' . $extension) : null);
            if(file_exists($file)) {
                return $file;
            }
        }
        
        if(!$this->quiet) {
            throw new Image_Text_Exception (self::FONT_FILE_NO_EXISTS);
        }
        
        return false;
    }

    /**
     * Zwraca identyfikator koloru
     *
     * @access public
     * @param  string   $color kolor
     * @param  resource $image strumień obrazu
     * @return integer
     */
    protected function _color ($color, $image)
    {
        $color = ($color{0} == '#') ? substr($color, 1) : $color;

        if((strlen($color) != 3 && strlen($color) != 6) || ((strlen($color) == 3) && $color{0} != $color{1} && $color{0} != $color{2})) {
            throw new Image_Text_Exception (self::ERROR_COLOR);
        }

        if(strlen($color) == 3) {
            $color .= $color;
        }

        $red   = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue  = hexdec(substr($color, 4, 2));

        return imagecolorallocate($image, $red, $green, $blue);
    }
    
    /**
     * Zwraca tablicę pozycji zdjęcia
     *
     * @access public
     * @param integer $width
     * @param integer $height
     * @param integer $place
     * @param integer $fixedX
     * @param integer $fixedY
     * @return array
     */
    protected function _position ($width, $height, $imageWidth, $imageHeight, $place, $fixedX, $fixedY)
    {
        switch($place) {
            case self::FIXED:
                $startX = $fixedX;
                $startY = $fixedY;
            break;

            case self::TOP + self::LEFT:
                $startX = $fixedX;
                $startY = $fixedY;
            break;

            case self::TOP + self::CENTER:
                $startX = round(($width / 2) - ($imageWidth / 2) + $fixedX);
                $startY = $fixedY;
            break;

            case self::TOP + self::RIGHT:
                $startX = $width - $imageWidth + $fixedX;
                $startY = $fixedY;
            break;

            case self::MIDDLE + self::LEFT:
                $startX = $fixedX;
                $startY = round(($height / 2) - ($imageHeight / 2) + $fixedY);
            break;

            case self::CENTERED:
                $startX = round(($width / 2) - ($imageWidth / 2)+ $fixedX);
                $startY = round(($height / 2) - ($imageHeight / 2) + $fixedY);
            break;

            case self::MIDDLE + self::RIGHT:
                $startX = $width - $imageWidth+ $fixedX;
                $startY = round(($height / 2) - ($imageHeight / 2) + $fixedY);
            break;

            case self::BOTTOM + self::LEFT:
                $startX = $fixedX;
                $startY = $height - $imageHeight;
            break;

            case self::BOTTOM + self::CENTER:
                $startX = round(($width / 2) - ($imageWidth / 2)+ $fixedX);
                $startY = $height - $imageHeight + $fixedY;
            break;

            case self::BOTTOM + self::RIGHT:
                $startX = $width - $imageWidth + $fixedX;
                $startY = $height - $imageHeight + $fixedY;
            break;
        }

        return array($startX, $startY);
    }
}