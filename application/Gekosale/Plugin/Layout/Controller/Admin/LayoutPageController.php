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
 * Class LayoutPageController
 *
 * @package Gekosale\Plugin\LayoutPage\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayoutPageController extends AdminController
{
    public function indexAction()
    {
        $tree = $this->getTree()->init();

        return Array(
            'tree' => $tree
        );
    }

    public function editAction($id)
    {
        $tree = $this->getTree()->init();

        return Array(
            'tree' => $tree
        );
    }

    /**
     * Get Tree
     */
    protected function getTree()
    {
        return $this->get('layout_page.tree');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('layout_page.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('layout_page.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.layout_page.index';
    }
}
