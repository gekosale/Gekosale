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
namespace Gekosale\Core\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Category extends Model
{
    protected $table = 'category';
    public $timestamps = true;
    protected $softDelete = false;
    protected $fillable = array('id');

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query, $parent)
    {
        return $query->where('parent_id', '=', $parent);
    }
}