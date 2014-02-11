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

class RelatedCategories extends FavouriteCategories
{

    protected $_jsGetSelectedInfo;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_jsGetSelectedInfo = 'GetSelectedInfo_' . $this->_id;
        if (isset($this->_attributes['load_selected_info']) && is_callable($this->_attributes['load_selected_info'])) {
            $this->_attributes['get_selected_info'] = 'xajax_' . $this->_jsGetSelectedInfo;
            App::getRegistry()->xajaxInterface->registerFunction(array(
                $this->_jsGetSelectedInfo,
                $this,
                'getSelectedInfo'
            ));
        }
    }

    public function getSelectedInfo ($request)
    {
        $rows = Array();
        if (! is_array($request['id'])) {
            $request['id'] = Array(
                $request['id']
            );
        }
        if (! is_array($request['shop_id'])) {
            $request['shop_id'] = Array(
                $request['shop_id']
            );
        }
        foreach ($request['id'] as $i => $rowId) {
            $paths = call_user_func($this->_attributes['load_selected_info'], $rowId, $request['shop_id'][$i]);
            $allegroPath = $paths['allegro'];
            $shopPath = (array) $paths['shop'];
            $allegroPathSize = count($allegroPath);
            $shopPathSize = count($shopPath);
            $allegroPath[$allegroPathSize - 1] = '<strong>' . $allegroPath[$allegroPathSize - 1] . '</strong>';
            $shopPath[$shopPathSize - 1] = '<strong>' . $shopPath[$shopPathSize - 1] . '</strong>';
            if ($allegroPathSize > 3) {
                $allegroPath = array_slice($allegroPath, $allegroPathSize - 3);
                array_unshift($allegroPath, '...');
            }
            if ($shopPathSize > 3) {
                $shopPath = array_slice($shopPath, $shopPathSize - 3);
                array_unshift($shopPath, '...');
            }
            $rows[] = Array(
                'id' => $rowId,
                'values' => Array(
                    implode(' / ', $allegroPath),
                    implode(' / ', $shopPath)
                )
            );
        }
        return Array(
            'rows' => $rows
        );
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = parent::prepareAttributesJavascript();
        $attributes[] = $this->formatAttributeJavascript('shop_categories', 'aoShopCategories', FE::TYPE_OBJECT);
        return $attributes;
    }
}
