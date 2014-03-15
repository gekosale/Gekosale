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
namespace Gekosale\Plugin\HomePage\Controller\Frontend;

use Gekosale\Core\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomePageController
 *
 * @package Gekosale\Plugin\HomePage\Controller\Frontend
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class FooterController extends FrontendController
{

    public function indexAction()
    {
        return new Response('aaa');
    }

}
