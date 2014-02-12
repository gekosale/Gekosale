<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @package     Gekosale\Core\Template
 * @subpackage  Gekosale\Core\Template\Extension
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Template\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Asset extends \Twig_Extension
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('asset', array(
                                                   $this,
                                                   'getAsset'
                                              ), array(
                                                      'is_safe' => Array(
                                                          'html'
                                                      )
                                                 ))
        );
    }

    public function getAsset($path)
    {
        return sprintf('%s/%s', $this->container->get('request')->getSchemeAndHttpHost(), $path);
    }

    public function getName()
    {
        return 'asset';
    }
}
