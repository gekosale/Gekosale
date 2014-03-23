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

use Gekosale\Core\Helper;
use Gekosale\Core\Model;

/**
 * Class ClientGroup
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ClientGroup extends Model implements TranslatableModelInterface
{

    protected $table = 'client_group';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id'];

    /**
     * {@inheritdoc}
     */
    public function translation()
    {
        return $this->hasMany('Gekosale\Core\Model\ClientGroupTranslation');
    }

    /**
     * Mutator for discount attribute
     *
     * @param $value
     */
    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = Helper::changeCommaToDot($value);
    }
}