<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form
 * @subpackage  Gekosale\Core\Form\Element
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Element;

class Image extends File
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['file_types'] = Array(
            'jpg',
            'jpeg',
            'png',
            'gif',
            'swf'
        );
        $this->_attributes['file_types_description'] = $this->trans('TXT_FILE_TYPES_IMAGE');
    }
}
