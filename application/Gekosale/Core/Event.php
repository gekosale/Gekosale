<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core;

use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends ContainerAwareEventDispatcher
{

    protected $container;


    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($container);
    }

    public function addSubscribers ()
    {
        $this->addSubscriber(new RouterListener($this->container->get('router')->getMatcher()));
        
        $this->addSubscriber(new Event\Subscriber\TemplateSubscriber());
        
//         $this->addSubscriber(new Event\Subscriber\VatSubscriber());
    }
}