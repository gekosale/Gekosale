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
 * Tekst podstawowy
 * 
 * @category   Image
 * @package    Image_Text
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Text_Basic extends Image_Text
{
    /**
     * Czcionka
     *
     * @access protected
     * @var string
     */
    protected $font = null;
    
    /**
     * Kolor czcionki
     *
     * @access protected
     * @var string
     */
    protected $fontColor = '#fff';
    
    /**
     * Rozmiar czcionki
     *
     * @access protected
     * @var integer
     */
    protected $fontSize = 12;
            
    /**
     *  Komunikaty błędów
     */
     const FONT_ERROR = 'Wrong font format, only ttf is valid';
     
    /**
     * Wybranie czcionki
     *
     * @access public
     * @param  string $name nazwa czcionki
     * @return Image_Text_Basic
     * @throws Image_Text_Exception
     */
    public function setFont ($name = null)
    {
        $font = $this->getFont ($name);
        
        if(strtolower(substr($font, (strrpos($font, '.')+1))) == 'ttf') {
            $this->font = $font;
        } elseif(!$this->quiet) {
            throw new Image_Text_Exception (self::FONT_ERROR);
        }
        
        return $this;
    }
    
    /**
     * Definiuje kolor czcionki
     *
     * @access public
     * @param  string  $color kolor czcionki
     * @return Image_Text_Basic
     */
    public function setFontColor ($color = null)
    {
        $this->fontColor = $color;
        return $this;
    }
    
    /**
     * Definiuje rozmiar czcionki
     *
     * @access public
     * @param  integer $size rozmiar czcionki
     * @return Image_Text_Basic
     */
    public function setFontSize ($size = null)
    {
        $this->fontSize = (int)$size;
        return $this;
    }
            
    /**
     * Wstawienie tekstu
     *
     * @access public
     * @param  string   $text tekst
     * @param  integer  $place pozycja tekstu
     * @param  integer  $fixedX przesunięcie w zględem osi x
     * @param  integer  $fixedY przesunięcie w zględem osi y
     * @param  integer  $angle stopień obrotu tekstu
     * @param  boolean  $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Image_Text_Exception
     */
    public function write ($text = null, $place = self::CENTERED, $fixedX = 0, $fixedY = 0, $angle = 0, $asCopy = false)
    {
        if($this->image->isOpen()) {
            
            $textbox = imageftbbox($this->fontSize, $angle, $this->font, $text);

            $width  = abs($textbox[0]) + abs($textbox[2]);
            $height = abs($textbox[1]) + abs($textbox[5]);
       
            switch($place) {
                case self::FIXED:    
                    $x = $fixedX;
                    $y = $fixedY + $height;
                break;
    
                case self::TOP + self::LEFT:
                    $x = $fixedX;
                    $y = $height + $fixedY;
                break;
    
                case self::TOP + self::CENTER:
                    $x = round(($this->image->imageWidth() / 2) - ($width / 2) + $fixedX);
                    $y = $height + $fixedY;
                break;
    
                case self::TOP + self::RIGHT:
                    $x = $this->image->imageWidth() - $width + $fixedX;
                    $y = $height + $fixedY;
                break;
                               
                case self::MIDDLE + self::LEFT:
                    $x = $fixedX;
                    $y = round(($this->image->imageHeight() / 2) + ($height / 2) + $fixedY);
                break;
    
                case self::MIDDLE + self::CENTER:
                    $x = round(($this->image->imageWidth() / 2) - ($width / 2) + $fixedX);
                    $y = round(($this->image->imageHeight() / 2) + ($height / 2) + $fixedY);
                break;
                
                case self::CENTERED:
                    $x = round(($width / 2) - ($this->image->imageWidth() / 2) + $fixedX);
                    $y = round(($this->image->imageHeight() / 2) + ($height / 2) + $fixedY);
                break;
    
                case self::MIDDLE + self::RIGHT:
                    $x = $this->image->imageWidth() - $width + $fixedX;
                    $y = round(($this->image->imageHeight() / 2) + ($height / 2) + $fixedY);
                break;
    
                case self::BOTTOM:
                    $x = $fixedX;
                    $y = $this->image->imageHeight() + $fixedY;
                break;
    
                case self::BOTTOM + self::LEFT:
                    $x = $fixedX;
                    $y = $this->image->imageHeight() + $fixedY;
                break;
    
                case self::BOTTOM + self::CENTER:
                    $x = round(($this->image->imageWidth() / 2) - ($width / 2) + $fixedX);
                    $y = $this->image->imageHeight() + $fixedY;
                break;
    
                case self::BOTTOM + self::RIGHT:
                    $x = $this->image->imageWidth()  - $width + $fixedX;
                    $y = $this->image->imageHeight() + $fixedY;
                break;
                
                default:
                    $x = $fixedX;
                    $y = $height + $fixedY;
            }
                
            if($asCopy) {
                $image = imagecreatetruecolor($this->image->imageWidth(), $this->image->imageHeight());
        
                if(!imagefttext($image, $this->fontSize, $angle, $x, $y, $this->_color($this->fontColor, $image), $this->font, $text)) {
                    throw new Image_Text_Exception (self::ERROR_TEXT);
                }
                $obj = clone $this;
                $obj->setImageResource($image);
                
                return $obj;
            } else {
                if(!imagefttext($this->image->imageResource(), $this->fontSize, $angle, $x, $y, $this->_color($this->fontColor, $this->image->imageResource()), $this->font, $text)) {
                    throw new Image_Text_Exception (self::ERROR_TEXT);
                }
            }
        }
        
        return $this->image;
    }
}