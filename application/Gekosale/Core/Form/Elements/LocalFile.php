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
 * Class LocalFile
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LocalFile extends File implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (!isset($this->attributes['traversable'])) {
            $this->attributes['traversable'] = true;
        }
        if (!isset($this->attributes['file_types'])) {
            $this->attributes['file_types'] = Array(
                'jpg',
                'png',
                'gif',
                'swf'
            );
        }
        $this->attributes['file_types_description'] = Translation::get('TXT_FILE_TYPES_IMAGE');
        $this->attributes['upload_url']
                                                     = App::getURLAdressWithAdminPane() . 'files/add/' . base64_encode($this->attributes['file_source']);
        $this->attributes['load_handler']           = 'xajax_loadFiles_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'loadFiles_' . $this->_id,
            $this,
            'loadFiles'
        ));
        $this->attributes['delete_handler'] = 'xajax_deleteFile_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            'deleteFile_' . $this->_id,
            $this,
            'deleteFile'
        ));
        $this->attributes['type_icons'] = Array(
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
            $this->formatAttributeJs('file_source', 'sFilePath'),
            $this->formatAttributeJs('upload_url', 'sUploadUrl'),
            $this->formatAttributeJs('session_name', 'sSessionName'),
            $this->formatAttributeJs('session_id', 'sSessionId'),
            $this->formatAttributeJs('file_types', 'asFileTypes'),
            $this->formatAttributeJs('type_icons', 'oTypeIcons', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('file_types_description', 'sFileTypesDescription'),
            $this->formatAttributeJs('delete_handler', 'fDeleteFile', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('load_handler', 'fLoadFiles', ElementInterface::TYPE_FUNCTIONN),
            $this->formatRepeatableJs(),
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
        if (substr($request['file'], 0, strlen($this->attributes['file_source'])) != $this->attributes['file_source']) {
            throw new Exception('The requested path "' . $request['file'] . '" is outside of permitted sandbox.');
        }
        if (!unlink($request['file'])) {
            throw new Exception('Deletion of file "' . $request['file'] . '" unsuccessful.');
        }

        return Array();
    }

    public function loadFiles($request)
    {
        $inRoot = false;
        if (substr($request['path'], 0, strlen($this->attributes['file_source'])) != $this->attributes['file_source']) {
            $request['path'] = $this->attributes['file_source'];
        }
        if ($request['path'] == $this->attributes['file_source']) {
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
            if ($file != '.svn') {
                if (is_dir($filepath) && $this->attributes['traversable']) {
                    $dirs[] = Array(
                        'dir'   => true,
                        'name'  => $file,
                        'path'  => $request['path'] . $file,
                        'size'  => '',
                        'owner' => '' . fileowner($filepath),
                        'mtime' => date('Y-m-d H:i:s', filemtime($filepath))
                    );
                } else {

                    if (in_array(pathinfo($request['path'] . $file, PATHINFO_EXTENSION), $this->attributes['file_types'])) {
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
            }
        }
        closedir($dir);

        return Array(
            'files' => array_merge($dirs, $files),
            'cwd'   => $request['path']
        );
    }

}
