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
namespace Gekosale\Plugin\PluginManager\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class PluginManagerController
 *
 * @package Gekosale\Plugin\PluginManager\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PluginManagerController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('plugin_manager.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('plugin_manager.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('plugin_manager.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.plugin_manager.index';
    }
}
