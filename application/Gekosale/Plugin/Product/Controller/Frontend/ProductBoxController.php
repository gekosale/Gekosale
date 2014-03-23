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
use Symfony\Component\HttpFoundation\Response;

class ProductBoxController extends FrontendController
{

    public function indexAction($slug)
    {
        return new Response($slug);
    }
} 