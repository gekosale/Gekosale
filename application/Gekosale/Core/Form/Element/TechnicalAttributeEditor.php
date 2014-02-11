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

class TechnicalAttributeEditor extends Field
{

    public function __construct ($attributes)
    {
        $attributes['attributes'] = App::getModel('technicaldata')->getTechnicalDataFull();
        parent::__construct($attributes);
        
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'DeleteAttribute',
            $this,
            'deleteAttribute'
        ));
        
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'RenameAttribute',
            $this,
            'renameAttribute'
        ));
        
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'RenameValue',
            $this,
            'renameValue'
        ));
        
        $this->_attributes['deleteAttributeFunction'] = 'xajax_DeleteAttribute';
        $this->_attributes['renameAttributeFunction'] = 'xajax_RenameAttribute';
        $this->_attributes['renameValueFunction'] = 'xajax_RenameValue';
    }

    public function renameAttribute ($request)
    {
        App::getModel('technicaldata')->RenameAttribute($request['id'], $request['name'], $request['languageid']);
        return $request;
    }

    public function renameValue ($request)
    {
        App::getModel('technicaldata')->RenameValue($request['id'], $request['name'], $request['languageid']);
        return $request;
    }

    public function deleteAttribute ($request)
    {
        App::getModel('technicaldata')->DeleteDataGroup($request['id'], $request['set_id']);
        
        return Array(
            'status' => true
        );
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('set', 'sSetId'),
            $this->formatAttributeJavascript('attributes', 'aoAttributes', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('deleteAttributeFunction', 'fDeleteAttribute', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('renameAttributeFunction', 'fRenameAttribute', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('renameValueFunction', 'fRenameValue', FE::TYPE_FUNCTION),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
