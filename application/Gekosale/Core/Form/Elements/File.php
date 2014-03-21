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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class File
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class File extends Field implements ElementInterface
{

    public $datagrid;

    protected static $_filesLoadHandlerSet = false;
    protected $jsFunction;

    public function __construct($attributes, ContainerInterface $container)
    {
        parent::__construct($attributes);

        $this->container                   = $container;
        $this->datagrid                    = $container->get('file.datagrid');
        $this->_attributes['session_name'] = session_name();
        $this->_attributes['session_id']   = session_id();
        $this->jsFunction                  = 'LoadFiles_' . $this->_id;
        $this->_attributes['load_handler'] = 'xajax_' . $this->jsFunction;

        $this->container->get('xajax_manager')->registerFunction([
            $this->jsFunction,
            $this,
            'doLoadFilesForDatagrid_' . $this->_id
        ]);

        $this->datagrid->init();
    }

    public function __call($function, $arguments)
    {
        if (substr($function, 0, strlen('doLoadFilesForDatagrid_')) == 'doLoadFilesForDatagrid_') {
            return call_user_func_array([
                $this,
                'doLoadFilesForDatagrid'
            ], $arguments);
        }
    }

    public function doLoadFilesForDatagrid($request)
    {
        if (isset($this->_attributes['file_types']) && is_array($this->_attributes['file_types']) && count($this->_attributes['file_types'])) {
            if (!isset($request['where']) || !is_array($request['where'])) {
                $request['where'] = [];
            }
            $request['where'][] = Array(
                'operator' => 'IN',
                'column'   => 'extension',
                'value'    => $this->_attributes['file_types']
            );
            $request['limit']   = !empty($this->_attributes['limit']) ? $this->_attributes['limit'] : 10;
        }

        return $this->datagrid->loadData($request);
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('main_id', 'sMainId'),
            $this->formatAttributeJs('visibility_change', 'bVisibilityChangeable'),
            $this->formatAttributeJs('upload_url', 'sUploadUrl'),
            $this->formatAttributeJs('session_name', 'sSessionName'),
            $this->formatAttributeJs('session_id', 'sSessionId'),
            $this->formatAttributeJs('file_types', 'asFileTypes'),
            $this->formatAttributeJs('file_types_description', 'sFileTypesDescription'),
            $this->formatAttributeJs('delete_handler', 'fDeleteHandler', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('load_handler', 'fLoadFiles', ElementInterface::TYPE_FUNCTION),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

}
