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
 * Abstrakcyjny obiekt dla klas walidatorów
 * 
 * @category   Image
 * @package    Image_Validator
 * @author     Krzysztof Kardasz <krzysztof.kardasz@gmail.com>
 * @copyright  Copyright (c) 2008 Krzysztof Kardasz
 */
abstract class Image_Validator_Abstract
{
    /**
     * Informacja o błędzie
     *
     * @access protected
     * @var string
     */
    protected $message = 'File image %s is not valid';
    
    /**
     * Obiekt Image
     *
     * @access protected
     * @var Image
     */
    protected $image = false;
    
    /**
     * Zwraca wiadomość o błędzie
     *
     * @return void
     */
    final public function init (Image $image) 
    {
        $this->image = $image;
    }
            
    /**
     * Zwraca wiadomość o błędzie
     *
     * @return string
     */
    public function getMessage () 
    {
        return sprintf($this->message, $this->image->imageName ());
    }
    
    /**
     * Zwraca prawdę lub fałsz
     *
     * @return boolean
     */
    abstract public function isValid ();
}