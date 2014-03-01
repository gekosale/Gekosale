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
namespace Gekosale\Plugin\Contact\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class ContactController
 *
 * @package Gekosale\Plugin\Contact\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ContactController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('contact.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('contact.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('contact.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.contact.index';
    }
}
