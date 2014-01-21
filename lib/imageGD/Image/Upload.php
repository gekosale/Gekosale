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
 * @package    Image_Upload
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Upload obrazków
 * 
 * @category   Image
 * @package    Image_Upload
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Upload
{    
    /**
     * Zwraca prawdę lub fałsz czy metoda upload została wykonana
     *
     * @access protected
     * @var boolead
     */
    protected $isUpload = false;
    
    /**
     * Tablica obiektów obrazu
     *
     * @access protected
     * @var array
     */
    protected $images = array();
    
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
     * Format zmiany nazwy
     *
     * @access protected
     * @var string
     */
    protected $nameFormat = '%fname';

    /**
     * Liczba rozpoczynająca auto numerowanie
     *
     * @access protected
     * @var integer
     */
    protected $autoNumber = 0;

    /**
     * Licznik autonumerowania
     *
     * @access protected
     * @var integer
     */
    protected $counter = 0;
    
    /**
     *  Komunikaty błędów
     */
    const DIR_NO_EXISTS    = 'Directory not exists';
    const DIR_NO_WRITEABLE = 'Directory not writable';
    const NO_UPLOAD_SET    = 'First call upload function';
    const NO_NAMES         = 'You have to set names of form inputs';
     
    /**
     * Dodanie obiektu akcji
     *
     * @access public
     * @param  Image_Action_Interface $action obiekt akcji
     * @return Image_Upload
     */
    public function addAction (Image_Action_Interface $action)
    {
        $this->actions[] = $action;
        return $this;
    }
    
    /**
     * Dodanie obiektu walidatora
     *
     * @access public
     * @param  Image_Validator_Abstract $action obiekt walidatora
     * @return Image_Upload
     */
    public function addValidator (Image_Validator_Abstract $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }
        
    /**
     * Definuje format zmiany nazwy
     *
     *  %fname - oryginalna nazwa pliku
     *  #####  - autonumerowanie przykład: ## - 01; ### - 001
     *
     * @access public
     * @param  string $format format nazwy
     * @return Image_Upload
     */
    public function setNameFormat ($format)
    {
        $this->nameFormat = $format;
        return $this;
    }
        
    /**
     * Definuje liczbę rozpoczynająca auto numerowanie
     *
     * @access public
     * @param  integer $number liczba całkowita
     * @return Image_Upload
     */
    public function setAutonumberStart ($number = 0)
    {
        $this->autoNumber = (int)$number;
        $this->counter    = 0;
        return $this;
    }
    
    /**
     * Zwraca tablicę dwuwymiarową wiadomości zwróconych przez walidator
     *
     * @access public
     * @return array
     */
    public function getMessages ()
    {
        return $this->messages;
    }
    
    /**
     * Zwraca tablicę instancji obiektów obrazu
     *
     * @access public
     * @return array
     * @throws Image_Upload_Exception
     */
    public function getImages ()
    {
        if(!empty($this->images)) {
            return $this->images;
        } elseif(!$this->isUpload && !$this->quiet) {
            throw new Image_Upload_Exception (self::NO_UPLOAD_SET);
        }
        
        return array();
    }
    
    /**
     * Utworzenie instancji obiektów, załadowanie plików z tablicy $_FILES
     *
     * @access public
     * @param  string|array $name nazwy indeksów z tablicy $_FILES
     * @param  boolean      $onlyIsValid dodaje tylko te obrazy które są prawidłowe
     * @return boolean
     * @throws Image_Upload_Exception
     */
    public function upload ($name = null, $onlyIsValid = true)
    {
        $this->isUpload = true;
        $name = (array)$name;
        
        if(!empty($name)) {
            if(isset($_FILES) && !empty($_FILES)) {
                foreach($name as $index) {
                    if(isset($_FILES[$index]) && !empty($_FILES[$index])) {
                        if(is_array($_FILES[$index]['tmp_name'])) {              
                            for($i=0; $i < count($_FILES[$index]['tmp_name']); $i++) {
                                if(!empty($_FILES[$index]['tmp_name'][$i]) && !empty($_FILES[$index]['name'][$i])) {
                                    $image = new Image ($_FILES[$index]['tmp_name'][$i], $this->quiet);
                                    $image->setImageName($_FILES[$index]['name'][$i]);
                                    
                                    foreach($this->validators as $validator) {
                                        $image->addValidator($validator);
                                    }

                                    if(($onlyIsValid && $image->isValid()) || !$onlyIsValid) {
                                        foreach($this->actions as $action) {
                                            $image->addAction($action);
                                        }                                  
                                        $this->images[] = $image;
                                    } else {
                                        $this->messages[] = array('filename' => $image->imageName(), 'messages' => $image->getMessages()); 
                                        $image->imageCleanup();
                                    }
         
                                }
                            }
                        } elseif(!empty($_FILES[$index]['tmp_name']) && !empty($_FILES[$index]['name'])) {
                            $image = new Image ($_FILES[$index]['tmp_name'], $this->quiet);
                            $image->setImageName($_FILES[$index]['name']);
                            
                            foreach($this->validators as $validator) {
                                $image->addValidator($validator);
                            }
                            
                            if(($onlyIsValid && $image->isValid()) || !$onlyIsValid) {
                                foreach($this->actions as $action) {
                                    $image->addAction($action);
                                }
                                $this->images[] = $image;
                            } else {
                                $this->messages[] = array('filename' => $image->imageName(), 'messages' => $image->getMessages()); 
                                $image->imageCleanup();
                            }
                        }
                    }
                }
                return true;
            }
        } elseif(!$this->quiet) {
            throw new Image_Upload_Exception (self::NO_NAMES);
        }
        return false;
    }

    /**
     * Zapisanie wszystkich plików obrazu
     *
     * @access public
     * @param  null|string $dir katalog docelowy
     * @param  integer     $quality jakość obrazu
     * @return Image_Upload
     * @throws Image_Upload_Exception
     */
    public function saveAll ($dir = null, $quality = 100)
    {
        $dir = (is_null($dir)) ? basename(__FILE__) : realpath($dir);
    
        if(!$this->quiet) {
            if(!file_exists($dir)) {
                throw new Image_Upload_Exception(self::DIR_NO_EXISTS);
            } elseif(!is_writable($dir)) {
                throw new Image_Upload_Exception(self::DIR_NO_WRITEABLE);
            }
        }
        
        if(!empty($this->images)) {
            $this->counter = $this->autoNumber;
            foreach($this->images as $image) {
                $image->setImageName($this->formatName($image->imageName()));
                $image->execActions();
                $image->save(null, $quality, $dir);
            }
        }
        
        return $this;
    }

    /**
     * Zwalnia pamięć i resetuje uploadowane pliki
     *
     * @access public
     * @return Image_Upload
     */
    public function uploadCleanup ()
    {
        if(!empty($this->images)) {
            foreach($this->images as $image) {
                $image->imageCleanup();
            }
            $this->images = array();
        }
        
        return $this;
    }

    /**
     * Formatowanie nazwy pliku
     *
     * @access protected
     * @param  string $filename oryginalna nazwa pliku
     * @return string
     */
    protected function formatName ($filename)
    {
        $name = $this->nameFormat;
        
        $numberCount = substr_count($name, '#');
        
        if($numberCount) {
            $number = str_pad ((string)$this->counter, $numberCount, '0', STR_PAD_LEFT);
            $name   = str_replace(str_repeat('#', $numberCount), $number, $name);
            ++$this->counter;
        }
        
        if(substr_count($this->nameFormat, '%fname')) {
            $name   = str_replace('%fname', $filename, $name);
        }
        
        return $name;
    }
}