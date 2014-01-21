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
 * Tekst w ramce
 * 
 * @category   Image
 * @package    Image_Text
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Text_Box extends Image_Text
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
     * Margines tekstu
     *
     * @access protected
     * @var integer
     */
    protected $textMargin = 3;
    
    /**
     * Pozycja tekstu
     *
     * @access protected
     * @var integer
     */
    protected $textPosition = self::BOTTOM;
    
    /**
     * Obramowanie
     *
     * @access protected
     * @var integer
     */
    protected $border = 1;
    
    /**
     * Kolor tła
     *
     * @access protected
     * @var string
     */
    protected $backgroundColor = '#000';
    
    /**
     *  Komunikaty błędów
     */
     const FONT_ERROR = 'Wrong font format, only gdf is valid';
     
    /**
     * Wybranie czcionki
     *
     * @access public
     * @param  string $name nazwa czcionki
     * @return Image_Text_Box
     * @throws Image_Text_Exception
     */
    public function setFont ($name = null)
    {
        $font = $this->getFont ($name);
        
        if(strtolower(substr($font, (strrpos($font, '.')+1))) == 'gdf') {
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
     * @return Image_Text_Box
     */
    public function setFontColor ($color = null)
    {
        $this->fontColor = $color;
        return $this;
    }
    
    /**
     * Definiuje kolor tła
     *
     * @access public
     * @param  string  $color kolor tła
     * @return Image_Text_Box
     */
    public function setBackgroundColor ($color = null)
    {
        $this->backgroundColor = $color;
        return $this;
    }
    
    /**
     * Definiuje pozycję tekstu
     *
     * @access public
     * @param  integer $position pozycja tekstu
     * @return Image_Text_Box
     */
    public function setTextPosition ($position = null)
    {
        $this->textPosition = $position;
        return $this;
    }
    
    /**
     * Definiuje margines tekstu
     *
     * @access public
     * @param  integer $margin margines tekstu w px
     * @return Image_Text_Box
     */
    public function setTextMargin ($margin = null)
    {
        $this->textMargin = $margin;
        return $this;
    }
    
    /**
     * Definiuje rozmiar obramowania
     *
     * @access public
     * @param  integer $border rozmiar obramowania w px
     * @return Image_Text_Box
     */
    public function setBorder ($border = null)
    {
        $this->border = $border;
        return $this;
    }
    
    /**
     * Wstawienie tekstu
     *
     * @access public
     * @param  string|array $text tekst
     * @param  boolean      $asCopy zwrócenie kopii obiektu
     * @return Image
     */
    public function write ($text = null, $asCopy = false)
    {
        if($this->image->isOpen()) {
            $heightAdd = 0;
            $widthAdd  = $this->border * 2;
            $widthNew  = $this->image->imageWidth() + $widthAdd;
    
            $stringWidth = $widthNew - ($this->textMargin * 2);
           
            $font        = imageloadfont($this->font);
            $fontHeight  = imagefontheight($font);
            $fontWidth   = imagefontwidth($font);
            $stringBreak = floor($stringWidth / $fontWidth);
    
            if(is_array($text)) {
                $textArray = array();
                foreach ($text as $value) {
                    $value       = wordwrap($value, $stringBreak, "\n");
                    $_tarray     = explode("\n", $value);
                    $textArray   = array_merge($textArray, (array) $_tarray);
                    $heightAdd  += ($fontHeight + $this->textMargin) * count((array)$_tarray);
                }
            } else {
                $text       = wordwrap($text, $stringBreak, "\n");
                $textArray  = explode("\n", $text);
                $heightAdd  += ($fontHeight + $this->textMargin) * count($textArray);
            }
    
            $heightNew  = $this->image->imageHeight() + $heightAdd + $this->textMargin;
    
            switch($this->textPosition) {
                case self::BOTTOM:
                    $spaceY = $this->border;
                    $spaceT = $this->image->imageHeight() + $this->textMargin + $this->border;
                break;
    
                case self::TOP:
                    $spaceY = $heightAdd + $this->textMargin - $this->border;
                    $spaceT = $this->textMargin + $this->border;
                break;
            }
    
            $imageNew = imagecreatetruecolor($widthNew, $heightNew);
            $background = $this->_color($this->backgroundColor, $imageNew);
            imagefill($imageNew, 0, 0, $background);
            imagerectangle($imageNew, 0, 0, $this->image->imageWidth(), $this->image->imageHeight(), $background);
            imagecopyresampled($imageNew, $this->image->imageResource(), $this->border, $spaceY, 0, 0, $this->image->imageWidth(), $this->image->imageHeight(), $this->image->imageWidth(), $this->image->imageHeight());
    
            $fontColor  = $this->_color($this->fontColor, $imageNew);

            foreach ($textArray as $text) {
                while (strlen($text) * imagefontwidth($font) > imagesx($imageNew)) {
                    if ($font > 1) {
                        $font--;
                    } else { break; }
                }
                imagestring($imageNew, $font, imagesx($imageNew) / 2 - strlen($text) * imagefontwidth($font) / 2, $spaceT, $text, $fontColor);
                $spaceT += $this->textMargin + $fontHeight;
            }
    
            if($imageNew) {
                if($asCopy) {
                    $obj = clone $this->image;
                    $obj->setImageResource ($imageNew);
                    return $obj;
                } else {
                    $this->image->setImageResource ($imageNew, true);
                    return $this->image;
                }
            } else {
                throw new Image_Text_Exception (self::ERROR_TEXT);
            } 
        }
        
        return $this->image;
    }
}