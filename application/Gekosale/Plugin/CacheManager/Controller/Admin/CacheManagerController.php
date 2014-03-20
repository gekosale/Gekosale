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
namespace Gekosale\Plugin\CacheManager\Controller\Admin;

use Gekosale\Core\Controller\AdminController;
use Assetic\Extension\Twig\TwigResource;

/**
 * Class CacheManagerController
 *
 * @package Gekosale\Plugin\CacheManager\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CacheManagerController extends AdminController
{
    public function deleteAction()
    {
        $am        = $this->get('assetic_manager');
        $loader    = $this->get('twig.loader.admin');
        $writer    = $this->get('assetic_writer');
        $templates = ['layout.twig'];

        foreach ($templates as $template) {
            $resource = new TwigResource($loader, $template);
            $am->addResource($resource, 'twig');
        }

        $writer->writeManagerAssets($am);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('cache_manager.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('cache_manager.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('cache_manager.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.cache_manager.index';
    }
}
