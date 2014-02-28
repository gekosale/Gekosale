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
 * Class ProgressIndicator
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProgressIndicator extends Field implements ElementInterface
{

    public $datagrid;

    protected static $_filesLoadHandlerSet = false;
    protected $_jsFunction;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['load']
            = App::getRegistry()->xajaxInterface->registerFunction(array(
            'ProgressIndicator_OnLoad_' . $this->_id,
            $this->_attributes['load'][0],
            $this->_attributes['load'][1]
        ));
        $this->_attributes['process']
            = App::getRegistry()->xajaxInterface->registerFunction(array(
            'ProgressIndicator_OnProcess_' . $this->_id,
            $this->_attributes['process'][0],
            $this->_attributes['process'][1]
        ));
        $this->_attributes['success']
            = App::getRegistry()->xajaxInterface->registerFunction(array(
            'ProgressIndicator_OnSuccess_' . $this->_id,
            $this->_attributes['success'][0],
            $this->_attributes['success'][1]
        ));
    }


    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('chunks', 'iChunks'),
            $this->formatAttributeJs('load', 'fLoadRecords', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('process', 'fProcessRecords', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('success', 'fSuccessRecords', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('preventSubmit', 'bPreventSubmit', ElementInterface::TYPE_BOOLEAN),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

}
