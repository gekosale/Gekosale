<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: virtualproductfiles.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;
use sfEvent;

class VirtualProductFilesModel extends FileUploader
{

    protected $_path;

    public function __construct ($registry)
    {
        parent::__construct($registry);
        $this->load();
    }

    public function load ()
    {
        $this->_path = ROOTPATH . 'design' . DS . '_virtualproduct' . DS;
        $this->loadAllowedType(Array(
            'image',
            self::MIME
        ));
        $this->loadAllowedExtensions(Array(
            'octet-stream',
            'jpg',
            'png',
            'gif',
            'psd',
            'doc',
            'docx',
            'csv',
            'xls',
            'tgz',
            'rar',
            'zip',
            'pdf',
            'avi',
            'mov',
            'mpg',
            'mpeg',
            'mp3'
        ));
        return $this;
    }

    public function insert ($file)
    {
        try{
            $id = $this->insertVirtualProductFile($file);
            $this->AddToFilesystem($file, $id);
            return $id;
        }
        catch (Exception $e){
            throw new CoreException($e);
        }
    }

    public function AddToFilesystem ($file, $id)
    {
        $filepath = $this->_path . $id . '.' . $this->getFileExtension($file['name']);
        if (! move_uploaded_file($file['tmp_name'], $filepath)){
            throw new Exception('File upload unsuccessful.');
        }
    }

    public function insertVirtualProductFile ($file)
    {
        try{
            return $this->insertFile($file['name']);
        }
        catch (Exception $e){
            throw $e;
        }
    }
}
