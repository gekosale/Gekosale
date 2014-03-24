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
namespace Gekosale\Plugin\Layout\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class LayoutThemeController
 *
 * @package Gekosale\Plugin\LayoutTheme\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayoutThemeController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('layout_theme.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('layout_theme.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('layout_theme.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.layout_theme.index';
    }
}
