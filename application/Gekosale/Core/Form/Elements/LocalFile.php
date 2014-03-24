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
 * Class LocalFile
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LocalFile extends File implements ElementInterface
{

    public function __construct($attributes, ContainerInterface $container)
    {
        parent::__construct($attributes, $container);
        if (!isset($this->attributes['traversable'])) {
            $this->attributes['traversable'] = true;
        }
        if (!isset($this->attributes['file_types'])) {
            $this->attributes['file_types'] = ['jpg', 'png', 'gif', 'swf'];
        }

        $this->attributes['load_handler']   = 'xajax_loadFiles_' . $this->_id;
        $this->attributes['delete_handler'] = 'xajax_deleteFile_' . $this->_id;

        $this->container->get('xajax_manager')->registerFunction([
            'loadFiles_' . $this->_id,
            $this,
            'loadFiles'
        ]);

        $this->container->get('xajax_manager')->registerFunction([
            'deleteFile_' . $this->_id,
            $this,
            'deleteFile'
        ]);

        $designPath      = $container->getParameter('application.design_path');
        $this->iconsPath = sprintf('%s/%s', $designPath, '_images_panel/icons/filetypes');

        $this->attributes['type_icons'] = [
            'cdup'      => $this->getIcon('cdup.png'),
            'unknown'   => $this->getIcon('unknown.png'),
            'directory' => $this->getIcon('directory.png'),
            'gif'       => $this->getIcon('image.png'),
            'png'       => $this->getIcon('image.png'),
            'jpg'       => $this->getIcon('image.png'),
            'bmp'       => $this->getIcon('image.png'),
            'txt'       => $this->getIcon('text.png'),
            'doc'       => $this->getIcon('text.png'),
            'rtf'       => $this->getIcon('text.png'),
            'odt'       => $this->getIcon('text.png'),
            'htm'       => $this->getIcon('document.png'),
            'html'      => $this->getIcon('document.png'),
            'php'       => $this->getIcon('document.png')
        ];
    }

    private function getIcon($icon)
    {
        return sprintf('%s/%s', $this->iconsPath, $icon);
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
            $this->formatAttributeJs('load_handler', 'fLoadFiles', ElementInterface::TYPE_FUNCTION),
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
