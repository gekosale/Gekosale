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
namespace Gekosale\Plugin\PaymentMethod\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class PaymentMethodController
 *
 * @package Gekosale\Plugin\PaymentMethod\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PaymentMethodController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('payment_method.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('payment_method.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('payment_method.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.payment_method.index';
    }
}
