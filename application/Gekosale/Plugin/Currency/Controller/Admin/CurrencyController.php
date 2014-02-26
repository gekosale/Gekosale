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
namespace Gekosale\Plugin\Currency\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class CurrencyController
 *
 * @package Gekosale\Plugin\Currency\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CurrencyController extends AdminController
{
    /**
     * Get currency datagrid
     *
     * @return \Gekosale\Core\DataGrid
     */
    protected function getDataGrid()
    {
        return $this->get('currency.datagrid');
    }

    /**
     * Get currency repository
     *
     * @return \Gekosale\Core\Repository
     */
    protected function getRepository()
    {
        return $this->get('currency.repository');
    }

    /**
     * Get currency form
     *
     * @return \Gekosale\Core\Form
     */
    protected function getForm()
    {
        return $this->get('currency.form');
    }

    /**
     * Get alias for plugin
     *
     * @return string
     */
    protected function getPluginAlias()
    {
        return 'admin.vat';
    }
}
