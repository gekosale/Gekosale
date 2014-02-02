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

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('comment', 'sComment'),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('set', 'sSetId'),
            $this->_FormatAttribute_JS('attributes', 'aoAttributes', FE::TYPE_OBJECT),
            $this->_FormatAttribute_JS('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('deleteAttributeFunction', 'fDeleteAttribute', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('renameAttributeFunction', 'fRenameAttribute', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('renameValueFunction', 'fRenameValue', FE::TYPE_FUNCTION),
            $this->_FormatRepeatable_JS(),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
