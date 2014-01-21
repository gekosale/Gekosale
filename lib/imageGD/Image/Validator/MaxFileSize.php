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
 * @package    Image_Validator
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt  GNU Lesser General Public
 * @version    2.1
 */

/**
 * Walidator do określenia maksymalnej wielkości pliku
 * 
 * @category   Image
 * @package    Image_Validator
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
class Image_Validator_MaxFileSize extends Image_Validator_Abstract
{
    /**
     * Informacja o błędzie
     *
     * @access protected
     * @var integer|null
     */
    protected $size = null;
    
    /**
     * Zwraca prawdę lub fałsz
     * @access public
     * @param  integer $size wielkość w bajtach
     * @return void
     */
    public function __construct ($size = null) 
    {
        $this->size    = $size;
        $this->message = 'rozmiar pliku "%s" przekracza ' . round(($size/1024), 2) . 'KB';
    }
    
    /**
     * Zwraca prawdę lub fałsz
     *
     * @access public
     * @return boolean
     */
    public function isValid () 
    {
        return $this->image->filesize() <= $this->size;
    }
}