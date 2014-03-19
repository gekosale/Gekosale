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
namespace Gekosale\Plugin\Product\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class ProductController
 *
 * @package Gekosale\Plugin\Product\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGrid()
    {
        return $this->get('product.datagrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('product.repository');
    }

    /**
     * {@inheritdoc}
     */
    protected function getForm()
    {
        return $this->get('product.form');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultRoute()
    {
        return 'admin.product.index';
    }
}
