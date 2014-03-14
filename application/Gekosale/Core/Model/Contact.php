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
 * Class Contact
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Contact extends Model implements TranslatableModelInterface
{

    protected $table = 'contact';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id'];

    /**
     * Relation with translation table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasMany('Gekosale\Core\Model\ContactTranslation');
    }

    /**
     * Mutator for is_enabled attribute
     *
     * @param $value
     */
    public function setIsEnabledAttribute($value)
    {
        $this->attributes['is_enabled'] = (int)$value;
    }

    /**
     * Accessor for is_enabled attribute
     *
     * @param $value
     *
     * @return int
     */
    public function getIsEnabledAttribute($value)
    {
        return (int)$value;
    }
}