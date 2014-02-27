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
namespace Gekosale\Core;

/**
 * Class Form
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Form extends Component
{
    public function addForm(array $options)
    {
        return new Form\Elements\Form($options);
    }

    public function addFieldset(array $options)
    {
        return new Form\Elements\Fieldset($options);
    }

    public function addTextField(array $options)
    {
        return new Form\Elements\TextField($options);
    }
}