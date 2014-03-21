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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Image
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Image extends File implements ElementInterface
{

    public function __construct($attributes, ContainerInterface $container)
    {
        parent::__construct($attributes, $container);

        $this->_attributes['file_types'] = [
            'jpg',
            'jpeg',
            'png',
            'gif'
        ];
    }
}
