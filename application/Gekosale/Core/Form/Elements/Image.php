<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core\Form\Elements;

class Image extends File
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['file_types'] = Array(
            'jpg',
            'jpeg',
            'png',
            'gif',
            'swf'
        );

        $this->_attributes['file_types_description'] = Translation::get('TXT_FILE_TYPES_IMAGE');
    }

}
