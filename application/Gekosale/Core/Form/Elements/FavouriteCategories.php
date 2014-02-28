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
 * Class FavouriteCategories
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class FavouriteCategories extends Tree implements ElementInterface
{
    protected $_jsGetSelectedInfo;

    public function __construct($attributes)
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

    public function getSelectedInfo($request)
    {
        $rows = Array();
        if (!is_array($request['id'])) {
            $request['id'] = Array(
                $request['id']
            );
        }
        foreach ($request['id'] as $rowId) {
            $path     = call_user_func($this->_attributes['load_selected_info'], $rowId);
            $pathSize = count($path);
            if ($pathSize === 0) {
                $path = array();
            } else {
                $path[$pathSize - 1] = '<strong>' . $path[$pathSize - 1] . '</strong>';
                if ($pathSize > 5) {
                    $path = array_slice($path, $pathSize - 5);
                    array_unshift($path, '...');
                }
            }

            $rows[] = Array(
                'id'     => $rowId,
                'values' => Array(
                    implode(' / ', $path)
                )
            );
        }

        return Array(
            'rows' => $rows
        );
    }

    public function prepareAttributesJs()
    {
        $attributes = parent::prepareAttributesJs();
        $attributes[]
                      = $this->formatAttributeJs('get_selected_info', 'fGetSelectedInfo', ElementInterface::TYPE_FUNCTION);
        $attributes[] = $this->formatAttributeJs('columns', 'aoColumns', ElementInterface::TYPE_OBJECT);

        return $attributes;
    }
}
