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
 * Class ColourSchemePicker
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ColourSchemePicker extends TextField implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['session_name'] = session_name();
        $this->_attributes['session_id']   = session_id();
        $this->_attributes['file_types']   = Array(
            'jpg',
            'png',
            'gif',
            'swf'
        );
        if (!isset($this->_attributes['file_source'])) {
            $this->_attributes['file_source'] = 'upload/';
        }
        $this->_attributes['file_types_description'] = \Gekosale\Translation::get('TXT_FILE_TYPES_IMAGE');
        $this->_attributes['upload_url']
                                                     = App::getURLAdressWithAdminPane() . 'files/add/' . base64_encode($this->_attributes['file_source']);
        $this->_attributes['load_handler']           = 'xajax_LoadFiles_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'LoadFiles_' . $this->_id,
            $this,
            'LoadFiles'
        ));
        $this->_attributes['delete_handler'] = 'xajax_DeleteFile_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'deleteFile_' . $this->_id,
            $this,
            'deleteFile'
        ));
        $this->_attributes['type_icons'] = Array(
            'cdup'      => DESIGNPATH . '_images_panel/icons/filetypes/cdup.png',
            'unknown'   => DESIGNPATH . '_images_panel/icons/filetypes/unknown.png',
            'directory' => DESIGNPATH . '_images_panel/icons/filetypes/directory.png',
            'gif'       => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
            'png'       => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
            'jpg'       => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
            'bmp'       => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
            'txt'       => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
            'doc'       => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
            'rtf'       => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
            'odt'       => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
            'htm'       => DESIGNPATH . '_images_panel/icons/filetypes/document.png',
            'html'      => DESIGNPATH . '_images_panel/icons/filetypes/document.png',
            'php'       => DESIGNPATH . '_images_panel/icons/filetypes/document.png'
        );
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('selector', 'sSelector'),
            $this->formatAttributeJs('gradient_height', 'iGradientHeight'),
            $this->formatAttributeJs('type_colour', 'bAllowColour', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('type_gradient', 'bAllowGradient', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('type_image', 'bAllowImage', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('file_source', 'sFilePath'),
            $this->formatAttributeJs('upload_url', 'sUploadUrl'),
            $this->formatAttributeJs('session_name', 'sSessionName'),
            $this->formatAttributeJs('session_id', 'sSessionId'),
            $this->formatAttributeJs('file_types', 'asFileTypes'),
            $this->formatAttributeJs('type_icons', 'oTypeIcons', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('file_types_description', 'sFileTypesDescription'),
            $this->formatAttributeJs('delete_handler', 'fDeleteFile', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('load_handler', 'fLoadFiles', ElementInterface::TYPE_FUNCTION),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

    public function deleteFile($request)
    {
        if (!isset($request['file'])) {
            throw new Exception('No file specified.');
        }
        if (substr($request['file'], 0, strlen($this->_attributes['file_source'])) != $this->_attributes['file_source']) {
            throw new Exception('The requested path "' . $request['file'] . '" is outside of permitted sandbox.');
        }
        if (!unlink($request['file'])) {
            throw new Exception('Deletion of file "' . $request['file'] . '" unsuccessful.');
        }

        return Array();
    }

    public function LoadFiles($request)
    {
        $inRoot = false;
        if (substr($request['path'], 0, strlen($this->_attributes['file_source'])) != $this->_attributes['file_source']) {
            $request['path'] = $this->_attributes['file_source'];
        }
        if ($request['path'] == $this->_attributes['file_source']) {
            $inRoot = true;
        }
        $path  = ROOTPATH . $request['path'];
        $files = Array();
        $dirs  = Array();
        if (($dir = opendir($path)) === false) {
            throw new Exception('Directory "' + $path + '" cannot be listed.');
        }
        while (($file = readdir($dir)) !== false) {
            if ($file == '.') {
                continue;
            }
            if ($inRoot && ($file == '..')) {
                continue;
            }
            $filepath = $path . $file;
            if (is_dir($filepath)) {
                $dirs[] = Array(
                    'dir'   => true,
                    'name'  => $file,
                    'path'  => $request['path'] . $file,
                    'size'  => '',
                    'owner' => '' . fileowner($filepath),
                    'mtime' => date('Y-m-d H:i:s', filemtime($filepath))
                );
            } else {
                $files[] = Array(
                    'dir'   => false,
                    'name'  => $file,
                    'path'  => $request['path'] . $file,
                    'size'  => '' . filesize($filepath),
                    'owner' => '' . fileowner($filepath),
                    'mtime' => date('Y-m-d H:i:s', filemtime($filepath))
                );
            }
        }
        closedir($dir);

        return Array(
            'files' => array_merge($dirs, $files),
            'cwd'   => $request['path']
        );
    }
}
