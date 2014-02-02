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

class ProgressIndicator extends Field
{

    public $datagrid;

    protected static $_filesLoadHandlerSet = false;

    protected $_jsFunction;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['load'] = App::getRegistry()->xajaxInterface->registerFunction(array(
            'ProgressIndicator_OnLoad_' . $this->_id,
            $this->_attributes['load'][0],
            $this->_attributes['load'][1]
        ));
        $this->_attributes['process'] = App::getRegistry()->xajaxInterface->registerFunction(array(
            'ProgressIndicator_OnProcess_' . $this->_id,
            $this->_attributes['process'][0],
            $this->_attributes['process'][1]
        ));
        $this->_attributes['success'] = App::getRegistry()->xajaxInterface->registerFunction(array(
            'ProgressIndicator_OnSuccess_' . $this->_id,
            $this->_attributes['success'][0],
            $this->_attributes['success'][1]
        ));
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('comment', 'sComment'),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('chunks', 'iChunks'),
            $this->_FormatAttribute_JS('load', 'fLoadRecords', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('process', 'fProcessRecords', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('success', 'fSuccessRecords', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('preventSubmit', 'bPreventSubmit', FE::TYPE_BOOLEAN),
            $this->_FormatRepeatable_JS(),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
