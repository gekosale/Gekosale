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
namespace Gekosale\Core;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * Class Uploader
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Uploader extends Component
{
    protected $name;

    protected $path;

    /**
     * Returns files as array from FileBag
     *
     * @param FileBag $bag
     *
     * @return array
     */
    public function getFiles(FileBag $bag)
    {
        $files         = [];
        $fileBag       = $bag->all();
        $arrayIterator = new RecursiveArrayIterator($fileBag);
        $fileIterator  = new RecursiveIteratorIterator($arrayIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($fileIterator as $file) {
            if (is_array($file)) {
                continue;
            }

            $files[] = $file;
        }

        return $files;
    }

    public function setPaths($paths)
    {
        $this->rootpath     = $this->container->getParameter('application.root_path');
        $this->originalPath = sprintf('%s/%s', $this->rootpath, $paths['original']);
    }

    public function upload(UploadedFile $file, $name)
    {
        $file->move($this->originalPath, $name);
    }
}