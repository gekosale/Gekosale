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
namespace Gekosale\Plugin\Shop\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class ShopController
 *
 * @package Gekosale\Plugin\Shop\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('shop.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('shop.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('shop.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.shop.index';
    }
}
