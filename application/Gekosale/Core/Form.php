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
    /**
     * Shortcut for adding Form
     *
     * @param array $options
     *
     * @return Form\Elements\Form
     */
    public function addForm(array $options)
    {
        return new Form\Elements\Form($options);
    }

    /**
     * Shortcut for adding Fieldset
     *
     * @param array $options
     *
     * @return Form\Elements\Fieldset
     */
    public function addFieldset(array $options)
    {
        return new Form\Elements\Fieldset($options);
    }

    /**
     * Shortcut for adding FieldsetLanguage
     *
     * @param array $options
     *
     * @return Form\Elements\FieldsetLanguage
     */
    public function addFieldsetLanguage(array $options)
    {
        return new Form\Elements\FieldsetLanguage($this->container, $options);
    }

    /**
     * Shortcut for adding TextField
     *
     * @param array $options
     *
     * @return Form\Elements\TextField
     */
    public function addTextField(array $options)
    {
        return new Form\Elements\TextField($options);
    }

    /**
     * Shortcut for adding filter CommaToDotChanger
     *
     * @return Form\Filters\CommaToDotChanger
     */
    public function addFilterCommaToDotChanger()
    {
        return new Form\Filters\CommaToDotChanger();
    }

    /**
     * Shortcut for adding filter NoCode
     *
     * @return Form\Filters\NoCode
     */
    public function addFilterNoCode()
    {
        return new Form\Filters\NoCode();
    }

    /**
     * Shortcut for adding filter Trim
     *
     * @return Form\Filters\Trim
     */
    public function addFilterTrim()
    {
        return new Form\Filters\Trim();
    }

    /**
     * Shortcut for adding filter Secure
     *
     * @return Form\Filters\Secure
     */
    public function addFilterSecure()
    {
        return new Form\Filters\Secure();
    }
}