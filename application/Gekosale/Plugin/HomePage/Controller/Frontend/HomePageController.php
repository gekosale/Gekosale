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

/**
 * Class HomePageController
 *
 * @package Gekosale\Plugin\HomePage\Controller\Frontend
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class HomePageController extends FrontendController
{

    public function indexAction()
    {
        return [
            'layout' => $this->getLayoutManager()->renderLayout($this->getLayout())
        ];
    }

    private function getLayout()
    {
        return 'HomePage';
    }

}
