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
namespace Gekosale\Plugin\PluginManager\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\PluginManager\Event\PluginManagerFormEvent;

/**
 * Class PluginManagerForm
 *
 * @package Gekosale\Plugin\PluginManager\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PluginManagerForm extends Form
{
    /**
     * Initializes PluginManagerForm
     *
     * @param array $plugin_managerData
     *
     * @return Form\Elements\Form
     */
    public function init($plugin_managerData = [])
    {
        $form = $this->addForm([
            'name' => 'plugin_manager',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'plugin_data',
            'label' => $this->trans('Required data')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
            ]
        ]));

        $form->addFilter($this->addFilterNoCode());

        $form->addFilter($this->addFilterTrim());

        $form->addFilter($this->addFilterSecure());

        $event = new PluginManagerFormEvent($form, $plugin_managerData);

        $this->getDispatcher()->dispatch(PluginManagerFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
