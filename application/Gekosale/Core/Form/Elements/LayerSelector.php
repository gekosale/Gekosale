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

namespace Gekosale\Core\Form\Elements;

/**
 * Class LayerSelector
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayerSelector extends Field implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);

        $storesArray = Array();
        $storesRaw   = App::getModel('stores')->getStoresAll();

        foreach ($storesRaw as $storeRaw) {
            $storesArray['s' . $storeRaw['id']]['id']     = $storeRaw['id'];
            $storesArray['s' . $storeRaw['id']]['name']   = $storeRaw['name'];
            $storesArray['s' . $storeRaw['id']]['label']  = 's' . $storeRaw['id'];
            $storesArray['s' . $storeRaw['id']]['parent'] = null;
            $storesArray['s' . $storeRaw['id']]['weight'] = $storeRaw['id'];
            $storesArray['s' . $storeRaw['id']]['type']   = 'store';
        }

        $viewsRaw = App::getModel('stores')->getViewsAll();

        foreach ($viewsRaw as $viewRaw) {
            $storesArray['v' . $viewRaw['id']]['id']     = $viewRaw['id'];
            $storesArray['v' . $viewRaw['id']]['name']   = $viewRaw['name'];
            $storesArray['v' . $viewRaw['id']]['label']  = 'v' . $viewRaw['id'];
            $storesArray['v' . $viewRaw['id']]['parent'] = 's' . $viewRaw['parent'];
            $storesArray['v' . $viewRaw['id']]['weight'] = $viewRaw['id'];
            $storesArray['v' . $viewRaw['id']]['type']   = 'view';
        }
        $this->_attributes['stores'] = $storesArray;
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('stores', 'oStores', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('active', 'sActive'),
            $this->formatAttributeJs('set', 'sSet'),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

}
