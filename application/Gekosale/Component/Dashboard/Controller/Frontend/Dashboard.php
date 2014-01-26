<?php
/**
 * Gekosale
 *
 * @copyright  Copyright (c) 2014 Gekosale Spółka z o.o. (http://www.gekosale.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Gekosale\Component\Dashboard\Controller\Frontend;

use Gekosale\Core\Component\Controller\Frontend;

class Dashboard extends Frontend
{

    public function index()
    {
        return Array(
            'foo' => 'bar2'
        );
    }
}