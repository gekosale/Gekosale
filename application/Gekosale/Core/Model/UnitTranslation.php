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
 * Class UnitTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class UnitTranslation extends Model
{

    protected $table = 'unit_translation';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['unit_id', 'language_id'];

    public function unit()
    {
        return $this->belongsTo('Gekosale\Core\Model\Unit');
    }

    public function language()
    {
        return $this->belongsTo('Gekosale\Core\Model\Language');
    }
}