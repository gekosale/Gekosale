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
namespace Gekosale\Plugin\Product\Controller\Frontend;

use Gekosale\Core\Controller\FrontendController;

/**
 * Class ProductController
 *
 * @package Gekosale\Plugin\Contact\Controller\Frontend
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductController extends FrontendController
{

    public function indexAction()
    {
        $content = $this->forward('Gekosale\Plugin\Product\Controller\Frontend\ProductBoxController');

        print_r($content->getContent());die();
    }
}
