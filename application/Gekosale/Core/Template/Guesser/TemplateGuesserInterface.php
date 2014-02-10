<?php

/**
 * Gekosale Open-Source E-Commerce Platform
 * 
 * This file is part of the Gekosale package.
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Template\Guesser;

interface TemplateGuesserInterface
{

    /**
     * Application uses Twig Engine
     * 
     * @var string
     */
    const TEMPLATING_ENGINE = 'twig';

    /**
     * Guesses template name for action in controller
     * 
     * @param string $controller
     * @param string $action
     * 
     * @return string Template name
     */
    public function guess ($controller, $action);

    /**
     * Checks controller type 
     * 
     * @param string $controller
     * 
     * @throws \InvalidArgumentException if controller doesn't match pattern
     * 
     * @return array Controller name parts
     */
    public function check ($controller);
}