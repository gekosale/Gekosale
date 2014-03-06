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

/**
 * Interface TranslatableModelInterface
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
interface TranslatableModelInterface
{

    /**
     * Relation with translation table
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function translation();
}