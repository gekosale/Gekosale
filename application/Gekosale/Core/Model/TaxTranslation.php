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

use Gekosale\Core\Model;

/**
 * Class TaxTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class TaxTranslation extends Model
{

    protected $table = 'tax_translation';

    public $timestamps = true;

    protected $softDelete = false;
    
    protected $fillable = ['tax_id', 'language_id', 'name'];

    public function tax()
    {
        return $this->belongsTo('Gekosale\Core\Model\Tax');
    }

    public function language()
    {
        return $this->belongsTo('Gekosale\Core\Model\Language');
    }
}