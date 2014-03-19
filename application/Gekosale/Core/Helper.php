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

use Illuminate\Database\Eloquent\Collection;

/**
 * Class Helper
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Helper extends Component
{
    /**
     * Returns current translation from translation Collection
     *
     * @param Collection $collection
     */
    public function getCurrentTranslation(Collection $collection)
    {
        $language = $this->getCurrentLanguage();

        $data = $collection->each(function ($item) use ($collection, $language) {
            if ($item->language_id == $language) {
                return $item;
            }
        });

        return $data;
    }

    /**
     * Replaces commas with dots
     *
     * @param $value
     *
     * @return string
     */
    public static function changeCommaToDot($value)
    {
        return str_replace(',', '.', $value);
    }
}