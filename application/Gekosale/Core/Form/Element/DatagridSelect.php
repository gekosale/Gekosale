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

class DatagridSelect extends Select
{

    public $datagrid;

    protected $_jsFunction;

    const SORT_DIR_ASC = 1;

    const SORT_DIR_DESC = 2;

    const ALIGN_LEFT = 1;

    const ALIGN_CENTER = 2;

    const ALIGN_RIGHT = 3;

    const FILTER_NONE = 0;

    const FILTER_INPUT = 1;

    const FILTER_BETWEEN = 2;

    const FILTER_SELECT = 3;

    const FILTER_AUTOSUGGEST = 4;

    const WIDTH_AUTO = 0;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        if (! isset($this->_attributes['key'])) {
            throw new Exception("Datagrid key (attribute: key) not set for field '{$this->_attributes['name']}'.");
        }
        if (! isset($this->_attributes['columns'])) {
            throw new Exception("Datagrid columns (attribute: columns) not set for field '{$this->_attributes['name']}'.");
        }
        if (! isset($this->_attributes['datagrid_init_function']) || ! is_callable($this->_attributes['datagrid_init_function'])) {
            throw new Exception("Datagrid initialization function not set (attribute: datagrid_init_function) for field '{$this->_attributes['name']}'. Hint: check whether the method you have specified is public.");
        }
        $this->_jsFunction = 'LoadRecords_' . $this->_id;
        $this->_attributes['jsfunction'] = 'xajax_' . $this->_jsFunction;
        App::getRegistry()->xajax->registerFunction(array(
            $this->_jsFunction,
            $this,
            'loadRecords_' . $this->_id
        ));
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('jsfunction', 'fLoadRecords', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('key', 'sKey'),
            $this->formatAttributeJavascript('columns', 'aoColumns', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('selected_columns', 'aoSelectedColumns', FE::TYPE_OBJECT),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }

    public function loadRecords ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function getDatagrid ()
    {
        if ($this->datagrid == NULL) {
            $this->datagrid = App::getModel('datagrid/datagrid');
            call_user_func($this->_attributes['datagrid_init_function'], $this->datagrid);
        }
        return $this->datagrid;
    }

    public function __call ($name, $args)
    {
        if (substr($name, 0, 11) == 'loadRecords') {
            return call_user_func(Array(
                $this,
                'loadRecords'
            ), $args[0], $args[1]);
        }
    }
}
