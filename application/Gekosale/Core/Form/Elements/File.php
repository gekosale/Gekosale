<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace FormEngine\Elements;

use Gekosale\App as App;
use Gekosale\CoreException as CoreException;
use FormEngine\FE as FE;

class File extends Field
{

    public $datagrid;

    protected static $_filesLoadHandlerSet = false;
    protected $_jsFunction;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['session_name'] = session_name();
        $this->_attributes['session_id']   = session_id();
        $this->_jsFunction                 = 'LoadFiles_' . $this->_id;
        $this->_attributes['load_handler'] = 'xajax_' . $this->_jsFunction;
        App::getRegistry()->xajax->registerFunction(array(
            $this->_jsFunction,
            $this,
            'doLoadFilesForDatagrid_' . $this->_id
        ));

    }

    public function __call($function, $arguments)
    {
        if (substr($function, 0, strlen('doLoadFilesForDatagrid_')) == 'doLoadFilesForDatagrid_') {
            return call_user_func_array(Array(
                $this,
                'doLoadFilesForDatagrid'
            ), $arguments);
        }
        throw new CoreException('Tried to call a method that doesn\'t exist: ' . $function);
    }

    public function doLoadFilesForDatagrid($request, $processFunction)
    {
        if (isset($this->_attributes['file_types']) && is_array($this->_attributes['file_types']) && count($this->_attributes['file_types'])) {
            if (!isset($request['where']) || !is_array($request['where'])) {
                $request['where'] = Array();
            }
            $request['where'][] = Array(
                'operator' => 'IN',
                'column'   => 'fileextension',
                'value'    => $this->_attributes['file_types']
            );
            $request['limit']   = !empty($this->_attributes['limit']) ? $this->_attributes['limit'] : 10;
        }
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function getDatagrid()
    {
        if ($this->datagrid == null) {
            $this->datagrid = App::getModel(get_class($this) . '/datagrid');
            $this->initDatagrid($this->datagrid);
        }
        return $this->datagrid;
    }

    public function getThumbForId($id)
    {
        try {
            $image = App::getModel('gallery')->getSmallImageById($id);
        } catch (Exception $e) {
            $image = Array(
                'path' => ''
            );
        }
        return $image['path'];
    }

    protected function initDatagrid($datagrid)
    {
        $datagrid->setTableData('file', Array(
            'idfile'        => Array(
                'source' => 'F.idfile'
            ),
            'filename'      => Array(
                'source'                => 'F.name',
                'prepareForAutosuggest' => true
            ),
            'fileextension' => Array(
                'source'           => 'FE.name',
                'prepareForSelect' => true
            ),
            'filetype'      => Array(
                'source'           => 'FT.name',
                'prepareForSelect' => true
            ),
            'adddate'       => Array(
                'source' => 'F.adddate'
            ),
            'thumb'         => Array(
                'source'          => 'F.idfile',
                'processFunction' => Array(
                    $this,
                    'getThumbForId'
                )
            )
        ));
        $datagrid->setFrom('
			`file` F
			INNER JOIN `filetype` FT ON FT.idfiletype = F.filetypeid
			INNER JOIN `fileextension` FE ON FE.idfileextension = F.fileextensionid
		');

        $datagrid->setGroupBy('
			F.idfile
		');

        if (isset($this->_attributes['ids']) && count($this->_attributes['ids'] > 0)) {
            $datagrid->setAdditionalWhere('F.idfile IN (' . implode(',', $this->_attributes['ids']) . ')');
        } else {
            $datagrid->setAdditionalWhere("F.idfile IS NOT NULL");
        }

    }

    protected function prepareAttributesJavascript()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('main_id', 'sMainId'),
            $this->formatAttributeJavascript('visibility_change', 'bVisibilityChangeable'),
            $this->formatAttributeJavascript('upload_url', 'sUploadUrl'),
            $this->formatAttributeJavascript('session_name', 'sSessionName'),
            $this->formatAttributeJavascript('session_id', 'sSessionId'),
            $this->formatAttributeJavascript('file_types', 'asFileTypes'),
            $this->formatAttributeJavascript('file_types_description', 'sFileTypesDescription'),
            $this->formatAttributeJavascript('delete_handler', 'fDeleteHandler', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('load_handler', 'fLoadFiles', FE::TYPE_FUNCTION),
            $this->formatRepeatableJavascript(),
            $this->formatRulesJavascript(),
            $this->formatDependencyJavascript(),
            $this->formatDefaultsJavascript()
        );
        return $attributes;
    }

}
