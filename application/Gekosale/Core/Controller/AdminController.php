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
namespace Gekosale\Core\Controller;

use Gekosale\Core\Controller;

/**
 * Class AdminController
 *
 * @package Gekosale\Core\Controller
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class AdminController extends Controller
{
    abstract protected function getRepository();

    abstract protected function getDataGrid();

    abstract protected function getForm();
}