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
namespace Gekosale\Core;

/**
 * Class LayoutManager
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayoutManager extends Component
{
    public function renderLayout($layout)
    {
        $content = $this->forward('Gekosale\Plugin\HomePage\Controller\Frontend\FooterController')->getContent();
        return $content;
    }

}