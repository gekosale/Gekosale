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

class Form extends \Twig_Extension
{

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions ()
    {
        return array(
            new \Twig_SimpleFunction('form', array(
                $this,
                'Render'
            ), array(
                'is_safe' => Array(
                    'html'
                )
            ))
        );
    }

    public function Render ($form)
    {
        return $form->Render();
    }

    public function getName ()
    {
        return 'form';
    }
}
