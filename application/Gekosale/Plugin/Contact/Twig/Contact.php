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
namespace Gekosale\Plugin\Contact\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;

/**
 * Class Contact
 *
 * @package Gekosale\Plugin\Contact\Twig
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Contact extends Twig_Extension
{

    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Register extension functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('contact_form', array(
                $this,
                'renderContactForm'
            ), array(
                'is_safe' => Array(
                    'html'
                )
            ))
        );
    }

    /**
     * Renders fully-functional contact form
     *
     * @return mixed
     */
    public function renderContactForm()
    {
        $form = $this->container->get('contact.form')->init();

        return $form->renderStatic();
    }

    public function getName()
    {
        return 'contact';
    }
}
