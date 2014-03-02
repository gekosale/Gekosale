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
namespace Gekosale\Plugin\Contact\Controller\Frontend;

use Gekosale\Core\Controller\FrontendController;

/**
 * Class ContactController
 *
 * @package Gekosale\Plugin\Contact\Controller\Frontend
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ContactController extends FrontendController
{

    public function indexAction()
    {
        echo $this->getRequest()->getLocale();
        return [];
    }
}
