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
namespace Gekosale\Plugin\Layout\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Layout\Event\LayoutThemeFormEvent;

/**
 * Class LayoutThemeForm
 *
 * @package Gekosale\Plugin\LayoutTheme\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayoutThemeForm extends Form
{
    /**
     * Initializes layout_theme Form
     *
     * @param array $layout_themeData
     *
     * @return Form\Elements\Form
     */
    public function init($layout_themeData = [])
    {
        $form = $this->addForm([
            'name' => 'layout_theme',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
        ]));

        $requiredData->addChild($this->addLocalFile([
            'name'        => 'theme',
            'label'       => $this->trans('Theme package'),
            'file_source' => 'themes/',
            'upload_url'  => $this->generateUrl('admin.layout_theme.add'),
            'traversable' => false,
            'file_types'  => [
                'zip'
            ]
        ]));

        $form->addFilters([
            $this->addFilterNoCode(),
            $this->addFilterTrim(),
            $this->addFilterSecure()
        ]);

        $event = new LayoutThemeFormEvent($form, $layout_themeData);

        $this->getDispatcher()->dispatch(LayoutThemeFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
