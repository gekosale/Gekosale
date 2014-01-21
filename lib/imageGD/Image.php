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
 * @package    Image
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Obsługa plików obrazu
 * 
 * @category   Image
 * @package    Image
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image
{
    /**
     * Ścieżka i nazwa pliku
     *
     * @access protected
     * @var string
     */
    protected $file     = false;

    /**
     * Oryginalna nazwa pliku
     *
     * @access protected
     * @var string
     */
    protected $fileName     = false;

    /**
     * Zasób image
     *
     * @access protected
     * @var resource
     */
    protected $imageResource = false;

    /**
     * Szerokosć zdjęcia
     *
     * @access protected
     * @var integer
     */
    protected $imageWidth    = null;

    /**
     * Wysokość zdjęcia
     *
     * @access protected
     * @var integer
     */
    protected $imageHeight   = null;

    /**
     * Typ pliku
     *
     * @access protected
     * @var string
     */
    protected $imageType     = null;

    /**
     * Rozszeżenie zdjęcia (gif/jpg/png)
     *
     * @access protected
     * @var string
     */
    protected $imageExts     = null;

    /**
     * Tryb cichy, bez wyrzucania wyjątków
     *
     * @access protected
     * @var boolean
     */
    protected $quiet = false;

    /**
     * Tablica obiektów akcji
     *
     * @access protected
     * @var array
     */
    protected $actions = array();

    /**
     * Tablica obiektów walidatorów
     *
     * @access protected
     * @var array
     */
    protected $validators = array();

    /**
     * Tablica wiadomości walidatorów
     *
     * @access protected
     * @var array
     */
    protected $messages = array();

    /**
     * Uprawnienia pliku
     *
     * @access protected
     * @var integer
     */
    protected $_default_chmod = 0644;

    /**
     *  Komunikaty błędów
     */
    const   NO_EXISTS            = 'Image file no exists';
    const   ERROR_OPEN           = 'Image file no opened';
    const   ERROR_COMPOSE_OPEN   = 'Image to compose is no opened';
    const   ERROR_FORMAT         = 'Wrong file format';
    const   ERROR_CROP           = 'Error croping image';
    const   ERROR_RESIZE         = 'Error resizing image';
    const   ERROR_AUTO_RESIZE    = 'Error auto resizing image';
    const   ERROR_RESIZE_CANVAS  = 'Error croping image';
    const   ERROR_COMPOSE        = 'Error compose image';
    const   ERROR_WATERNARK      = 'Error watermark image';
    const   ERROR_GRAY_SCALE     = 'Error filter grayscale';
    const   ERROR_BRIGHTNESS     = 'Error filter brightness';
    const   ERROR_NEGATIVE       = 'Error filter negative';
    const   ERROR_CONTRAST       = 'Error filter contrast';
    const   ERROR_COLORIZE       = 'Error filter colorize';
    const   ERROR_SAVE           = 'Error saving image';
    const   ERROR_DISPLAY        = 'Headers already sent';
    const   ERROR_FIXED          = 'You have to specify fixed position x and y';
    const   ERROR_COLOR          = 'Wrong color';
    const   DIR_NO_EXISTS        = 'Directory not exists';
    const   DIR_NO_WRITEABLE     = 'Directory not writable';

    /**
     * Pozycja zdjęcia
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
     * Pozycja odwrócenia
     */
    const    FLIP_HORIZONTAL = 11;
    const    FLIP_VERTICAL   = 12;
    const    FLIP_BOTH       = 23;

    /**
     * Konstruktor
     *
     * @access public
     * @param  string  $file ścieżka do pliku
     * @param  boolean $quiet tryb cichy
     * @return void
     * @throws Exception
     */
    public function __construct ($file = null, $quiet = false)
    {
        $this->file     = realpath($file);
        $this->quiet    = $quiet;        
        $this->fileName = basename($this->file);
        
        if(file_exists($this->file)) {
            $image = @getimagesize($this->file);
            
            if(!$image && !$this->quiet) {
                throw new Exception(self::ERROR_FORMAT);
            } else {
                $this->imageWidth  = $image[0];
                $this->imageHeight = $image[1];
                $this->imageType   = $image[2];
        
                switch($this->imageType) {
                    case IMAGETYPE_JPEG: 
                        $this->imageResource = imageCreateFromJpeg($this->file); 
                        $this->imageExts = 'jpg'; 
                    break;
                    case IMAGETYPE_PNG:  
                        $this->imageResource = imageCreateFromPng($this->file);  
                        imagealphablending($this->imageResource, false);
                        imagesavealpha($this->imageResource, true);
                        $this->imageExts = 'png'; 
                    break;
                    case IMAGETYPE_GIF:  
                        $this->imageResource = imageCreateFromGif($this->file);  
                        $this->imageExts = 'gif'; 
                    break;
        
                    default:
                        if(!$this->quiet) throw new Exception(self::ERROR_FORMAT);
                }
            }
        } elseif(!$this->quiet) {
            throw new Exception (self::NO_EXISTS.':'.$file);
        }
    }

    /**
     * Utworzenie instancji obiektu z uploadowanego pliku, przesyłaną metodą POST
     *
     * @access public
     * @param  string  $index indeks tablicy pliku
     * @param  boolean $quiet tryb cichy
     * @return Image|boolean
     */
    public static function upload ($index = null, $quiet = false)
    {
        if(!empty($index)) {
            if(isset($_FILES[$index]) && !empty($_FILES[$index])) {
                if(!is_array($_FILES[$index]['tmp_name'])) {
                    $obj = new self($_FILES[$index]['tmp_name'], $quiet);
                    return $obj->setImageName($_FILES[$index]['name']);
                }
            }
        }
        
        return false;
    }

    /**
     * Dodanie obiektu akcji
     *
     * @access public
     * @param  Image_Action_Interface $action obiekt akcji
     * @return Image
     */
    public function addAction (Image_Action_Interface $action)
    {
        $this->actions[] = $action;
        
        return $this;
    }

    /**
     * Wykonuje dodane akcje
     *
     * @access public
     * @return Image
     */
    public function execActions ()
    {
        foreach($this->actions as $action) {
            $action->action($this);
        }
        
        return $this;
    }

    /**
     * Dodanie obiektu walidatora
     *
     * @access public
     * @param  Image_Validator_Abstract $action obiekt walidatora
     * @return Image
     */
    public function addValidator (Image_Validator_Abstract $validator)
    {
        $this->validators[] = $validator;
        
        return $this;
    }

    /**
     * Zwraca prawdę lub fałsz czy walidacja przebiegła pomyślnie
     *
     * @access public
     * @return boolean
     */
    public function isValid ()
    {
        $isValid = true;
        
        foreach($this->validators as $validator) {
            $validator->init(clone $this);
            if(!$validator->isValid()) {
                $isValid = false;
                $this->messages[] = $validator->getMessage();
            }
    
        }
        
        return $isValid;
    }

    /**
     * Zwraca tablicę wiadomości zwrócone przez walidator
     *
     * @access public
     * @return array
     */
    public function getMessages ()
    {
        return $this->messages;
    }

    /**
     * Zwraca prawdę lub fałsz czy plik obrazu został otworzony
     *
     * @access public
     * @return boolean
     * @throws Exception
     */
    public function isOpen ()
    {
        if(!is_resource($this->imageResource) && !$this->quiet) {
            if($this->quiet) {
                return false;
            } else {
                throw new Exception(self::ERROR_OPEN);
            }
        } else {
            return true;
        }
    }
    
    /**
     * Zwraca pełną ścieżkę pliku
     *
     * @access public
     * @return string
     */
    public function imageFile ()
    {
        return $this->file;
    }
    
    /**
     * Zwraca rozmiar pliku
     *
     * @access public
     * @return integer
     */
    public function filesize ()
    {
        return filesize($this->file);
    }
    
    /**
     * Zmienia uprawnienia pliku
     *
     * @access public
     * @param  integer $chmod uprawnienia
     * @return boolean
     */
    public function chmod ($chmod = null)
    {
        if(is_null($chmod)) {
            $chmod = $this->_default_chmod;
        }
        return chmod($this->_file, $chmod);
    }

    /**
     * Zwraca nazwę pliku
     *
     * @access public
     * @return string
     */
    public function imageName ()
    {
        return $this->fileName;
    }

    /**
     * Definiuje nazwę pliku
     *
     * @access public
     * @param  string $name nazwa pliku
     * @return Image
     */
    public function setImageName ($name = null)
    {
        $this->fileName = basename($name);
        return $this;
    }

    /**
     * Zwraca katalog ze zdjęciem
     *
     * @access public
     * @return string
     */
    public function imageDir ()
    {
        return dirname ($this->file);
    }
    
    /**
     * Zwraca strumień obrazu
     *
     * @access public
     * @return resource
     */
    public function imageResource ()
    {
        return ($this->isOpen ()) ? $this->imageResource : false;
    }
    
    /**
     * Definiuje strumień obrazu
     *
     * @access public
     * @param  resource $resource strumień obrazu
     * @return Image
     */
    public function setImageResource ($resource, $destroy = false)
    {
        if(is_resource($resource)) {            
            if($destroy && is_resource($this->imageResource)) {
                imagedestroy($this->imageResource);
            }
            
            $this->imageResource = $resource;
            $this->imageWidth    = imagesx($resource);
            $this->imageHeight   = imagesy($resource);
        }
        
        return $this;
    }

    /**
     * Zwraca szerokość zdjęcia
     *
     * @access public
     * @return integer
     */
    public function imageWidth ()
    {
        return ($this->isOpen ()) ? $this->imageWidth : 0;
    }

    /**
     * Zwraca wysokość zdjęcia
     *
     * @access public
     * @return integer
     */
    public function imageHeight ()
    {
        return ($this->isOpen()) ? $this->imageHeight : 0;
    }

    /**
     * Zwraca rozszeżenie pliku
     *
     * @access public
     * @return string
     */
    public function imageExtension ()
    {
        return ($this->isOpen()) ? $this->imageExts : null;
    }

    /**
     * Zwalnia pamięć przydzieloną dla zdjęcia
     *
     * @access public
     * @return boolean
     */
    public function imageCleanup ()
    {
        if($this->isOpen ()) {
            return imagedestroy($this->imageResource);
        }
        
        return false;
    }

    /**
     * Wycięcie zdjęcia
     *
     * @access public
     * @param  integer $left   przesunięcie od lewej w px
     * @param  integer $top    przesunięcie od góry w px
     * @param  integer $width  szerokość wycięcia w px
     * @param  integer $height wysokość wycięcia w px
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function imageCrop ($left, $top, $width, $height, $asCopy = false)
    {
        if($this->isOpen ()) {
            $image_x = $this->imageWidth;
            $image_y = $this->imageHeight;
    
            if(($left == 0 && $width == $image_x) && ($top == 0 && $height == $image_y)) {
                return ($asCopy) ? clone $this : $this;
            }
    
            if($image_x < ($width + $left) || $image_y < ($height + $top)) {
                throw new Exception(self::ERROR_CROP);
            }
    
            $image = imagecreatetruecolor($width, $height);
            
            if($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
    
            if(!imagecopy($image, $this->imageResource, 0, 0, $left, $top, $width, $height)) {
                throw new Exception(self::ERROR_CROP);
            }
            
            if($asCopy) {
                $obj = clone $this;
                $obj->setImageResource($image);
                return $obj;
            } else {                
                $this->setImageResource($image, true);
            }
        }

        return $this;
    }

    /**
     * Zmiana rozmiaru zdjęcia
     *
     * @access public
     * @param  integer $width  szerokość w px
     * @param  integer $height wysokość w px
     * @param  boolean $asCopy  zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function imageResize ($width, $height, $asCopy = false)
    {
        if($this->isOpen ()) {
            $image_x = $this->imageWidth;
            $image_y = $this->imageHeight;
    
            if($width == $image_x &&  $height == $image_y) {
                return ($asCopy) ? clone $this : $this;
            } 
    
            $image = imagecreatetruecolor($width, $height);
            
            if($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            if(!imagecopyresampled($image, $this->imageResource, 0, 0, 0, 0, $width, $height, $this->imageWidth, $this->imageHeight)) {
                throw new Exception(self::ERROR_RESIZE);
            }
            
            if($asCopy) {
                $obj = clone $this;
                $obj->setImageResource($image);
                return $obj;
            } else {
                $this->setImageResource($image, true);
            }
        }
        
        return $this;
    }

    /**
     * Zmiana rozmiaru zdjęcia z automatycznym wycięciem zbędnego fragmentu
     *
     * @access public
     * @param  integer $width  szerokość w px
     * @param  integer $height wysokość w px
     * @param  boolean $asCopy  zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function imageAutoResize ($width, $height, $asCopy = false)
    {
        if($this->isOpen ()) {
            $x = $this->imageWidth;
            $y = $this->imageHeight;
    
            if($width == $x && $height == $y) {
                return $this;
            }
    
            if($width > $x || $height > $y) {
                throw new Exception(self::ERROR_AUTO_RESIZE);
            }
    
            $x1 = 0;
            $y1 = 0;
    
            /* Nowa Szerokość */
            $xm1 = round(($y * $width) / $height);
    
            /* Nowa Wysokość */
            $ym1 = round(($height * $x) / $width);
    
            /* Jeśli nowa szerokość jest mniejsza od starej */
            if($xm1 < $x) {
                /* Przesunięcie od lewej (wycentrowanie) */
                $x1 = floor(($x-$xm1)/2);
                /* Nowa szerokość */
                $x  = $xm1;
            /* Jeśli nowa wysokość jest mniejsza od starej */
            } elseif ($ym1 < $y) {
                /* Przesunięcie z góry (centrowanie w pionie) */
                $y1 = floor(($y - $ym1)/2);
                /* Nowa wysokość */
                $y  = $ym1;
            }
    
            $obj = &$this->imageCrop($x1, $y1, $x, $y, $asCopy);
            $obj->imageResize($width, $height);
            
            return $obj;
        }

        return $this;
    }

    /**
     * Zmiana rozmiaru obszaru roboczego
     *
     * @access public
     * @param  integer $width      szerokość w px
     * @param  integer $height     wysokość w px
     * @param  integer $place      pozycja
     * @param  string  $background kolor tła
     * @param  integer $fixedX     przesunięcie w poziomie w px
     * @param  integer $fixedY     przesunięcie w pionie w px
     * @param  boolean $asCopy     zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function resizeCanvas ($width, $height, $place = self::CENTERED, $background = '#fff', $fixedX = null, $fixedY = null, $asCopy = false)
    {
        if($this->isOpen ()) {
            $image = imagecreatetruecolor($width, $height);
            
            if(!empty($background)) {
                $color = $this->_color($background, $image);
                imagefill($image, 0, 0, $color);
            } elseif($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            $position = $this->_position($width, $height, $this->imageWidth, $this->imageHeight, $place, $fixedX, $fixedY);
    
            if(!imagecopy($image, $this->imageResource, $position[0], $position[1], 0, 0, $this->imageWidth, $this->imageHeight)) {
                throw new Exception(self::ERROR_RESIZE_CANVAS);
            }
            
            if($asCopy) {
                $obj = clone $this;
                $obj->setImageResource($image);
                return $obj;
            } else {
                $this->setImageResource($image, true);
            }
        }
        
        return $this;
    }

    /**
     * Zmiana szerokości i wysokości (uwzględniając proporcje)
     *
     * @access public
     * @param  integer $width  szerokość w px
     * @param  integer $height wysokość w px
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     */
    public function resizeToWidthHeight ($width, $height, $asCopy = false)
    {
        if($this->isOpen ()) {
            if ($width < $this->imageWidth) {
               return $this->resizeToWidth($width, $asCopy);
            } elseif($height < $this->imageHeight) {
                return $this->resizeToHeight($height, $asCopy);
            }
        }
        
        return $this;
    }

    /**
     * Zmiana szerokości obrazu (uwzględniając proporcje - zmienia się również wysokość)
     *
     * @access public
     * @param  integer $width  szerokość w px
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     */
    public function resizeToWidth ($width, $asCopy = false)
    {
        if($this->isOpen ()) {
            $height = ($width * $this->imageHeight) / $this->imageWidth;
            return $this->imageResize($width, $height, $asCopy);
        }
        
        return $this;
    }

    /**
     * Zmiana wysokości obrazu (uwzględniając proporcje - zmienia się również szerokość)
     *
     * @access public
     * @param  integer $height wysokość w px
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     */
    public function resizeToHeight ($height, $asCopy = false)
    {
        if($this->isOpen ()) {
            $width = ($this->imageWidth * $height) / $this->imageHeight;
            return $this->imageResize($width, $height, $asCopy);
        }
        
        return $this;
    }

    /**
     * Obraca obraz
     *
     * @access public
     * @param  integer $degrees    stopień obrotu
     * @param  string  $background kolor tła
     * @param  boolean $asCopy     zwrócenie kopii obiektu
     * @return Image
     */
    public function rotate ($degrees = 90, $background = null, $asCopy = false)
    {
        if($this->isOpen ()) {
            $background = (!is_null($background)) ? $this->_color($background, $this->imageResource) : -1;
            $image = imagerotate($this->imageResource, $degrees, $background);
            
            if($asCopy) {
                $obj = clone $this;
                $obj->setImageResource($image);
                return $obj;
            } else {
                $this->setImageResource($image, true);
            }
        }
        
        return $this;
    }

    /**
     * Odwraca obraz
     *
     * @access public
     * @param  integer $direction  kierunek
     * @param  boolean $asCopy     zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function filp ($direction = self::FLIP_BOTH, $asCopy = false)
    {
        if($this->isOpen ()) {
            $src_x      = 0;
            $src_y      = 0;
            $src_width  = $this->imageWidth;
            $src_height = $this->imageHeight;

            switch ((int)$direction) {
                case self::FLIP_HORIZONTAL:
                    $src_y                =    $this->imageHeight;
                    $src_height           =    -$this->imageHeight;
                break;
        
                case self::FLIP_VERTICAL:
                    $src_x                =    $this->imageWidth;
                    $src_width            =    -$this->imageWidth;
                break;
        
                case self::FLIP_BOTH:
                    $src_x                =    $this->imageWidth;
                    $src_y                =    $this->imageHeight;
                    $src_width            =    -$this->imageWidth;
                    $src_height           =    -$this->imageHeight;
                break;
        
                default:
                    return $this;
            }

            $image = imagecreatetruecolor ($this->imageWidth, $this->imageHeight);
            
            if($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            if (!imagecopyresampled ($image, $this->imageResource, 0, 0, $src_x, $src_y, $this->imageWidth, $this->imageHeight, $src_width, $src_height ) ){
                throw new Exception(self::ERROR_FLIP);
            }
            
            if($asCopy) {
                $obj = clone $this;
                $obj->setImageResource($image);
                return $obj;
            } else {
                $this->setImageResource($image, true);
            }
        }
        
        return $this;
    }

    /**
     * Komponuje ze sobą dwa pliki obrazu, nie zachowuje przeźroczystości zdjęcia
     *
     * @access public
     * @param  Image   $image   obiekt obrazu
     * @param  integer $place   pozycja
     * @param  integer $opacity nieprzezroczystość, zakres 0 - 100
     * @param  integer $fixedX  przesunięcie w poziomie w px
     * @param  integer $fixedY  przesunięcie w pionie w px
     * @param  boolean $asCopy  zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function compose (Image $image, $place = self::CENTERED, $opacity = 100, $fixedX = 0, $fixedY = 0, $asCopy = false)
    {
        if($this->isOpen () && $image->isOpen ()) {
            $opacity = round((int) $opacity);
            $opacity = ($opacity > 100) ? 100 : (($opacity < 0) ? 0 : $opacity);
    
            $position = $this->_position($this->imageWidth, $this->imageHeight, $image->imageWidth(), $image->imageHeight(), $place, $fixedX, $fixedY);
            
            if($asCopy) {
                $rimage = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($rimage, false);
                    imagesavealpha($rimage, true);
                }
                
                if(!imagecopymerge($rimage, $image->imageResource(), $position[0], $position[1], 0, 0, $image->imageWidth(), $image->imageHeight(), $opacity)) {
                    throw new Exception(self::ERROR_COMPOSE);
                }
            
                $obj = clone $this;
                $obj->setImageResource($rimage);
                
                return $obj;
            } else {
                if(!imagecopymerge($this->imageResource, $image->imageResource(), $position[0], $position[1], 0, 0, $image->imageWidth(), $image->imageHeight(), $opacity)) {
                    throw new Exception(self::ERROR_COMPOSE);
                }
            }
        }
        
        return $this;
    }

    /**
     * Wstawia plik obrazu zachowując przeźroczystość obrazka
     *
     * @access public
     * @param  Image   $image   obiekt obrazu
     * @param  integer $place   pozycja
     * @param  integer $fixedX  przesunięcie w poziomie w px
     * @param  integer $fixedY  przesunięcie w pionie w px
     * @param  boolean $asCopy  zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function watermark (Image $image, $place = self::CENTERED, $fixedX = 0, $fixedY = 0, $asCopy = false)
    {
        if($this->isOpen () && $image->isOpen ()) {    
            $position = $this->_position($this->imageWidth, $this->imageHeight, $image->imageWidth(), $image->imageHeight(), $place, $fixedX, $fixedY);

            if($asCopy) {
                $rimage = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($rimage, false);
                    imagesavealpha($rimage, true);
                }
            
                if(!imagecopy($rimage, $this->imageResource, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight)) {
                    throw new Exception(self::ERROR_WATERMARK);
                }
                
                if(!imagecopy($rimage, $image->imageResource(), $position[0], $position[1], 0, 0, $image->imageWidth(), $image->imageHeight())) {
                    throw new Exception(self::ERROR_WATERMARK);
                }

                $obj = clone $this;
                $obj->setImageResource($rimage);
                
                return $obj;
            } else {
                if(!imagecopy($this->imageResource, $image->imageResource(), $position[0], $position[1], 0, 0, $image->imageWidth(), $image->imageHeight())) {
                    throw new Exception(self::ERROR_WATERMARK);
                }
            }
        }
        
        return $this;
    }

    /**
     * Fala
     *
     * @access public
     * @param  integer $amplitude amplituda
     * @param  integer $period    cykl
     * @param  boolean $asCopy    zwrócenie kopii obiektu
     * @return Image
     */
    public function wave ($amplitude = 15, $period = 30, $asCopy = false)
    {
        if($this->isOpen ()) {
            $image = imagecreatetruecolor($this->imageWidth * 2, $this->imageHeight * 2);
        
            if($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
            
            imagecopyresampled ($image,$this->imageResource, 0, 0, 0, 0, $this->imageWidth * 2,$this->imageHeight * 2,$this->imageWidth, $this->imageHeight);

            for ($i = 0; $i < ($this->imageWidth * 2); $i += 2){
               imagecopy($image, $image,0 + $i - 2,0 + sin($i / $period) * $amplitude, 0 + $i,0,2,($this->imageHeight * 2));
            }
           
            $rimage = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
            
        
            if($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($rimage, false);
                imagesavealpha($rimage, true);
            }
            
            imagecopyresampled ($rimage, $image, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight,$this->imageWidth*2,$this->imageHeight*2);
            imagedestroy($image);    
            
            if($asCopy) {
                $obj = clone $this;
                $obj->setImageResource($rimage);
                return $obj;
            } else {
                $this->setImageResource($rimage, true);
            }            
        }
        
        return $this;
    }
    
    /**
     * Filtr: Skala szarości
     *
     * @access public
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function grayScale ($asCopy = false)
    {
        if($this->isOpen ()) {
            if($asCopy) {
                $image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
        
                if(!imagecopy($image, $this->imageResource, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight)) {
                    throw new Exception(self::ERROR_GRAY_SCALE);
                }
                if(!imagefilter($image, IMG_FILTER_GRAYSCALE)) {
                    throw new Exception(self::ERROR_GRAY_SCALE);
                }
            
                $obj = clone $this;
                $obj->setImageResource($image);
                
                return $obj;
            } else {
                if(!imagefilter($this->imageResource, IMG_FILTER_GRAYSCALE)) {
                    throw new Exception(self::ERROR_GRAY_SCALE);
                }
            }
        }
        return $this;
    }

    /**
     * Filtr: Jasność
     *
     * @access public
     * @param  integer $level, zakres (-255) - 255
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function brightness ($level = 0, $asCopy = false)
    {
        if($this->isOpen ()) {
            $level = round((int)$level);
            $level = ($level < -255 ) ? -255 : (($level > 255) ? 255 : $level);
            
            if($asCopy) {
                $image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
        
                if(!imagecopy($image, $this->imageResource, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight)) {
                    throw new Exception(self::ERROR_BRIGHTNESS);
                }
                if(!imagefilter($image, IMG_FILTER_BRIGHTNESS, $level)) {
                    throw new Exception(self::ERROR_BRIGHTNESS);
                }
            
                $obj = clone $this;
                $obj->setImageResource($image);
                
                return $obj;
            } else {
                if(!imagefilter($this->imageResource, IMG_FILTER_BRIGHTNESS, $level)) {
                    throw new Exception(self::ERROR_BRIGHTNESS);
                }
            }
        }
        
        return $this;
    }

    /**
     * Filtr: Negatyw
     *
     * @access public
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function negative ($asCopy = false)
    {
        if($this->isOpen ()) {
            if($asCopy) {
                $image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
        
                if(!imagecopy($image, $this->imageResource, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight)) {
                    throw new Exception(self::ERROR_NEGATIVE);
                }
                if(!imagefilter($image, IMG_FILTER_NEGATE)) {
                    throw new Exception(self::ERROR_NEGATIVE);
                }
            
                $obj = clone $this;
                $obj->setImageResource($image);
                
                return $obj;
            } else {
                if(!imagefilter($this->imageResource, IMG_FILTER_NEGATE)) {
                    throw new Exception(self::ERROR_NEGATIVE);
                }
            }
        }
        
        return $this;
    }

    /**
     * Filtr: Kontrast
     *
     * @access public
     * @param  integer $level  poziom, zakres (-100) - 100
     * @param  boolean $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function contrast ($level = 0, $asCopy = false)
    {
        if($this->isOpen ()) {
            $level = round((int)$level);
            $level = ($level < -100 ) ? -100 : (($level > 100) ? 100 : $level);
            
            if($asCopy) {
                $image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
        
                if(!imagecopy($image, $this->imageResource, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight)) {
                    throw new Exception(self::ERROR_CONTRAST);
                }
                if(!imagefilter($image, IMG_FILTER_CONTRAST, $level)) {
                    throw new Exception(self::ERROR_CONTRAST);
                }
            
                $obj = clone $this;
                $obj->setImageResource($image);
                
                return $obj;
            } else {
                if(!imagefilter($this->imageResource, IMG_FILTER_CONTRAST, $level)) {
                    throw new Exception(self::ERROR_CONTRAST);
                }
            }
        }
        
        return $this;
    }

    /**
     * Filtr: Koloryzacja
     *
     * @access public
     * @param  integer $red    czerwienie, zakres (-255) - 255
     * @param  integer $green  zielenie, zakres (-255) - 255
     * @param  integer $blue   niebieskość, zakres (-255) - 255
     * @param  boolean $asCopy zwrócenie kopii obiektu, zakres (-255) - 255
     * @return Image
     * @throws Exception
     */
    public function colorize ($red = 0, $green = 0, $blue = 0, $asCopy = false)
    {
        if($this->isOpen ()) {
            $red   = (int)$red;
            $green = (int)$green;
            $blue  = (int)$blue;
    
            $red   = ($red  < -255 ) ? -255 : (($red  > 255) ? 255 : $red);
            $green = ($green < -255 ) ? -255 : (($green  > 255) ? 255 : $green);
            $red   = ($blue  < -255 ) ? -255 : (($blue  > 255) ? 255 : $blue);
            
            if($asCopy) {
                $image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
                
                if($this->imageType == IMAGETYPE_PNG) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
        
                if(!imagecopy($image, $this->imageResource, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight)) {
                    throw new Exception(self::ERROR_COLORIZE);
                }
                if(!imagefilter($image, IMG_FILTER_COLORIZE, $red, $green, $blue)) {
                    throw new Exception(self::ERROR_COLORIZE);
                }
            
                $obj = clone $this;
                $obj->setImageResource($image);
                
                return $obj;
            } else {
                if(!imagefilter($this->imageResource, IMG_FILTER_COLORIZE, $red, $green, $blue)) {
                    throw new Exception(self::ERROR_COLORIZE);
                }
            }
        }
        
        return $this;
    }

    /**
     * Zapisanie zmian (lub zapisanie do innego pliku)
     *
     * @access public
     * @param  string|null $name nazwa pliku
     * @param  integer     $quality jakość obrazu
     * @param  string      $dir katalog w którym plik ma zostać zapisany
     * @param  boolean     $asCopy zwrócenie kopii obiektu
     * @return Image
     * @throws Exception
     */
    public function save ($name = null, $quality = 100, $dir = null, $asCopy = false)
    {
        if($this->isOpen ()) {
            $dir = (is_null($dir)) ? dirname($this->file) : realpath($dir);
            
            if(!$this->quiet) {
                if(!file_exists($dir)) {
                    throw new Exception(self::DIR_NO_EXISTS);
                } elseif(!is_writable($dir)) {
                    throw new Exception(self::DIR_NO_WRITEABLE);
                }
            }
            
            if(!is_null($name)) {
                $name      = basename($name);
                $nameClear = (strtolower(substr($this->fileName, (strrpos($this->fileName, '.')+1))) == $this->imageExts) ? substr($this->fileName, 0, (strrpos($this->fileName, '.'))) : $this->fileName;
                $name      = str_replace('*', $nameClear, $name);
            } else {
                $name = $this->fileName;
            }
            
            $ext = strrpos($name, '.');
            
            if($ext == false || ($ext == true && (strtolower(substr($name, ($ext+1))) != $this->imageExts))) {
                $name = $name . '.' . $this->imageExts;
            }
            
            switch($this->imageType) {
                case IMAGETYPE_JPEG:
                    $quality = round((int)$quality);
                    $quality = ($quality > 100) ? 100 : (($quality < 0) ? 0 : $quality);
                    
                    if(!imagejpeg($this->imageResource, $dir . '/' . $name , $quality) && !$this->quiet) {
                        throw new Exception(self::ERROR_SAVE);
                    }
                break;
                
                case IMAGETYPE_PNG:
                    if(!imagepng($this->imageResource, $dir . '/' . $name) && !$this->quiet) {
                        throw new Exception(self::ERROR_SAVE);
                    }
                break;
                
                case IMAGETYPE_GIF:
                    if(!imagegif($this->imageResource, $dir . '/' . $name) && !$this->quiet) {
                        throw new Exception(self::ERROR_SAVE);
                    }
                break;
            }
            
            chmod($dir . '/' . $name, $this->_default_chmod);
            
            if($asCopy) {
                return new self($dir . '/' . $name);
            } else {
                $this->file = $dir . '/' . $name;
                return $this;
            }
        }
        
        return $this;
    }

    /**
     * Wyświetlenie zdjęcia
     *
     * @access public
     * @param  integer $quality jakość obrazu
     * @return void
     * @throws Exception
     */
    public function display ($quality = 100)
    {
        if($this->isOpen()) {
            if(!headers_sent()) {
                switch ($this->imageType) {
                    case IMAGETYPE_JPEG:
                        $quality = round((int)$quality);
                        $quality = ($quality > 100) ? 100 : (($quality < 0) ? 0 : $quality);
                        header('Content-Type: image/jpeg');
                        imagejpeg($this->imageResource, null, $quality);
                    break;
                    
                    case IMAGETYPE_PNG:
                        header('Content-Type: image/png');
                        imagepng($this->imageResource);
                    break;
                    
                    case IMAGETYPE_GIF:
                        header('Content-Type: image/gif');
                        imagegif($this->imageResource);
                    break;
                }
            } elseif (!$this->quiet) {
                throw new Exception(self::ERROR_DISPLAY);
            }
        }
        
        return $this;
    }

    /**
     * Zwraca identyfikator koloru
     *
     * @access public
     * @param  string  $color  kolor
     * @param  resource $image strumień obrazu 
     * @return integer
     * @throws Exception
     */
    protected function _color ($color, $image)
    {
        $color = ($color{0} == '#') ? substr($color, 1) : $color;

        if((strlen($color) != 3 && strlen($color) != 6) || ((strlen($color) == 3) && $color{0} != $color{1} && $color{0} != $color{2})) {
            throw new Exception(self::ERROR_COLOR);
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
     * @param integer $width  szerokość
     * @param integer $height wysokość
     * @param integer $place  pozycja
     * @param integer $fixedX x1
     * @param integer $fixedY y1
     * @return array
     */
    protected function _position ($width, $height, $imageWidth, $imageHeight, $place, $fixedX = 0, $fixedY = 0)
    {
        $startX = 0;
        $startY = 0;

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