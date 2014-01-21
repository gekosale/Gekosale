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
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Generowanie tokenów Captcha
 * 
 * @category   Image
 * @package    Image_Captcha
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Captcha extends Image
{    
    /**
     * Katalog z czcionkami
     *
     * @access protected
     * @var array
     */
    protected $fontDirs = array();

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
    protected $fontSize = 16;
    
    /**
     * Różnica losowej wielkości czcionek
     * Losowanie z pośród zakresu (rozmiar-roznica, rozmiar+roznica)
     *
     * @access protected
     * @var integer
     */
    protected $fontSizeDiff = 4;
    
    /**
     * Obracanie się liter
     * Losowe obracanie z zakresu (-zakres, zakres)
     *
     * @access protected
     * @var integer
     */
    protected $fontWave = 12;
    
    /**
     *  Komunikaty błędów
     */
    const   ERROR_CREATE        = 'Error create';
    const   FONT_ERROR          = 'Wrong font format, only ttf is valid';
    const   FONT_FILE_NO_EXISTS = 'Font file not exists';
    const   FONT_DIR_NO_EXISTS  = 'Font directory not exists';
    const   ERROR_TEXT          = 'Error writing text';
     
    /**
     * Konstruktor
     *
     * @access public
     * @param  integer $width szerokość
     * @param  integer $height wysokość
     * @param  boolean $quiet tryb cichy
     * @return void
     * @throws Image_Captcha_Exception
     */
    public function __construct ($width = null, $height = null, $quiet = false)
    {
        $this->imageWidth  = (int)$width;
        $this->imageHeight = (int)$height;
        
        if($this->imageWidth > 0 && $this->imageHeight > 0) {
            $this->imageResource = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
            $this->file      = realpath(__FILE__);
            $this->quiet     = $quiet;        
            $this->fileName  = 'captcha';
            $this->imageType = IMAGETYPE_PNG;
            $this->imageExts = 'png'; 
        }  elseif(!$this->quiet) {
            throw new Image_Captcha_Exception (self::ERROR_CREATE);
        }
    }

    /**
     * Definuje katalog z plikami czcionek
     *
     * @access public
     * @param  string     $dir katalog z czcionkami
     * @param  string     $extension rozszeżenie plików
     * @return Image_Captcha
     * @throws Image_Captcha_Exception
     */
    public function setFontDir ($dir = null, $extension = null)
    {
        $dir = realpath($dir);
        if(file_exists($dir)) {
            $this->fontDirs[$dir] = $extension;
        } elseif(!$this->quiet) {
            throw new Image_Captcha_Exception (self::FONT_DIR_NO_EXISTS);
        }
        
    }

    /**
     * Wybiera czcionkę
     *
     * @access public
     * @param  string $name nazwa czcionki
     * @return string
     * @throws Image_Captcha_Exception
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
            throw new Image_Captcha_Exception (self::FONT_FILE_NO_EXISTS);
        }
        
        return false;
    }

     
    /**
     * Wybranie czcionki
     *
     * @access public
     * @param  string $name nazwa czcionki
     * @return Image_Captcha
     * @throws Image_Captcha_Exception
     */
    public function setFont ($name = null)
    {
        $font = $this->getFont ($name);
        
        if(strtolower(substr($font, (strrpos($font, '.')+1))) == 'ttf') {
            $this->font = $font;
        } elseif(!$this->quiet) {
            throw new Image_Captcha_Exception (self::FONT_ERROR);
        }
        
        return $this;
    }
    
    /**
     * Definiuje kolor czcionki
     *
     * @access public
     * @param  string|array  $color kolor czcionki
     * @return Image_Captcha
     */
    public function setFontColor ($color = null)
    {
        $this->fontColor = $color;
        return $this;
    }
    
    /**
     * Definiuje odchylenie czcionki
     *
     * @access public
     * @param  integer $wave odchylenie
     * @return Image_Captcha
     */
    public function setFontWave ($wave = 10)
    {
        $this->fontWave = (int)$wave;
        return $this;
    }
    
    /**
     * Definiuje rozmiar czcionki
     *
     * @access public
     * @param  integer $size rozmiar czcionki
     * @return Image_Captcha
     */
    public function setFontSize ($size = null)
    {
        $this->fontSize = (int)$size;
        return $this;
    }
    
    /**
     * Definiuje różnicę rozmiarów czcionki
     *
     * @access public
     * @param  integer $diff różnica rozmiarów
     * @return Image_Captcha
     */
    public function setFontSizeDiff ($diff = 5)
    {
        $this->fontSizeDiff = (int)$diff;
        return $this;
    }
    
    /**
     * Wypełnienie kolorem
     *
     * @access public
     * @param  string  $color kolor wypełnienia
     * @return Image_Captcha
     * @throws Image_Captcha_Exception
     */
    public function fillColor ($color = '#fff')
    {
        if($this->isOpen ()) {
            $color = $this->_color($color, $this->imageResource);
            imagefill($this->imageResource, 0, 0, $color);
        }
        
        return $this;
    }
    
    /**
     * Obramowanie
     *
     * @access public
     * @param  string  $color kolor obramowania
     * @return Image_Captcha
     */
    public function setBorder ($color = '#fff')
    {
        if($this->isOpen ()) {
            $color = $this->_color($color, $this->imageResource);
            
            imageline  ($this->imageResource, 0, 0, $this->imageWidth, 0, $color);
            imageline  ($this->imageResource, 0, $this->imageHeight-1, $this->imageWidth, $this->imageHeight-1, $color);
            imageline  ($this->imageResource, 0, 0, 0, $this->imageHeight, $color);
            imageline  ($this->imageResource, $this->imageWidth-1, 0, $this->imageWidth-1, $this->imageHeight, $color);
        }
        
        return $this;
    }
    
    /**
     * Dodanie tła
     *
     * @access public
     * @param  Image_Captcha_Background_Interface $background obiekt tła
     * @return Image_Captcha
     */
    public function addBackground (Image_Captcha_Background_Interface $background)
    {
        if($this->isOpen ()) {
            $background->render($this);
        }
        
        return $this;
    }
     
    /**
     * Wstawienie tekstu
     *
     * @access public
     * @param  string $text tekst
     * @return Image_Captcha
     */
    public function write ($text = null)
    {
        if($this->isOpen()) {
            $width  = 0;
            
            $heighta = 0;
            $heightb = 0;
            
            $textWrite = array();
            
            for($i = 0; $i < strlen($text); $i++) {
                $fsize = rand($this->fontSize-$this->fontSizeDiff, $this->fontSize+$this->fontSizeDiff);
                $angle = rand(-$this->fontWave, $this->fontWave);
                $textbox = imageftbbox($fsize, $angle, $this->font, $text{$i});

                $w = abs($textbox[0]) + abs($textbox[2]);
                $h = abs($textbox[1]) + abs($textbox[5]);
                
                $sx = $w + 3 + rand(0, 4);
                $sy = $h + rand(-5, 5);
                
                $ox    = $width + $sx;
               
                $width = $ox;
                $heighta = ($heighta > 0 && $sy < $heighta) ? $sy : (($heighta == 0 ) ? $sy : $heighta);
                $heightb = ($sx > $heightb) ? $sx : $heightb;
                
                $textWrite[] = array('fsize' => $fsize, 'angle' => $angle, 'letter' => $text{$i}, 'sx' => $sx, 'sy' => $sy, 'ox' => $ox);
            }
            
            $x = round(($this->imageWidth / 2) - ($width / 2));
            $y = round(($this->imageHeight / 2)/2);

            foreach ($textWrite as $i => $data) {
                $fcolor = (is_array($this->fontColor)) ? $this->fontColor[array_rand($this->fontColor)] : $this->fontColor;
                $fcolor = $this->_color($fcolor, $this->imageResource);

                imagefttext($this->imageResource, $data['fsize'], $data['angle'], $x, rand($data['sy']+$y, $this->imageHeight -$y)  , $fcolor, $this->font, $data['letter']);
                
                $x += $data['sx'];
            }
        }
        
        return $this;
    }
}