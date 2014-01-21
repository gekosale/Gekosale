<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace FormEngine\Elements;
use Gekosale\Translation as Translation;

class Downloader extends File
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['file_types'] = Array(
            'octet-stream',
            'jpg',
            'jpeg',
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
        );
        $this->_attributes['file_types_description'] = $this->trans('TXT_FILE_TYPES_IMAGE');
    }
}
