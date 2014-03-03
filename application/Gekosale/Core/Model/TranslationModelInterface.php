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
 * Interface TranslationModelInterface
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
interface TranslationModelInterface
{

    /**
     * Relation with language table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language();

    /**
     * Returns translations for current language
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeHasLanguageId($query, $language);
}