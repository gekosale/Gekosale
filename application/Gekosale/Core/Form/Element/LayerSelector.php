<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form
 * @subpackage  Gekosale\Core\Form\Element
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Element;

class LayerSelector extends Field
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        
        $storesArray = Array();
        $storesRaw = App::getModel('stores')->getStoresAll();
        
        foreach ($storesRaw as $storeRaw) {
            $storesArray['s' . $storeRaw['id']]['id'] = $storeRaw['id'];
            $storesArray['s' . $storeRaw['id']]['name'] = $storeRaw['name'];
            $storesArray['s' . $storeRaw['id']]['label'] = 's' . $storeRaw['id'];
            $storesArray['s' . $storeRaw['id']]['parent'] = null;
            $storesArray['s' . $storeRaw['id']]['weight'] = $storeRaw['id'];
            $storesArray['s' . $storeRaw['id']]['type'] = 'store';
        }
        
        $viewsRaw = App::getModel('stores')->getViewsAll();
        
        foreach ($viewsRaw as $viewRaw) {
            $storesArray['v' . $viewRaw['id']]['id'] = $viewRaw['id'];
            $storesArray['v' . $viewRaw['id']]['name'] = $viewRaw['name'];
            $storesArray['v' . $viewRaw['id']]['label'] = 'v' . $viewRaw['id'];
            $storesArray['v' . $viewRaw['id']]['parent'] = 's' . $viewRaw['parent'];
            $storesArray['v' . $viewRaw['id']]['weight'] = $viewRaw['id'];
            $storesArray['v' . $viewRaw['id']]['type'] = 'view';
        }
        $this->_attributes['stores'] = $storesArray;
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('stores', 'oStores', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('active', 'sActive'),
            $this->formatAttributeJavascript('set', 'sSet'),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
