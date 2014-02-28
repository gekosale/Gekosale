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
namespace Gekosale\Plugin\File\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\File;

/**
 * Class FileRepository
 *
 * @package Gekosale\Plugin\File\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class FileRepository extends Repository
{
    /**
     * Returns all files
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return File::all();
    }

    /**
     * Returns single file by ID
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return File::findOrFail($id);
    }
}