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

use Illuminate\Database\Eloquent\Collection;

/**
 * Class CustomCollection
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CustomCollection extends Collection
{
    /**
     * Flatten Collection to key-value pairs used in forms
     *
     * @param            $idKey
     * @param            $translationPath
     * @param            $language
     *
     * @return array
     */
    public function toSelect($idKey, $translationPath, $language)
    {
        $items           = $this;
        $select          = [];
        $translationPath = explode('.', $translationPath);

        if (count($translationPath) == 2) {
            $translationNode = $translationPath[0];
            $translationKey  = $translationPath[1];
        } else {
            $translationNode = null;
            $translationKey  = $translationPath[0];
        }

        $items->each(function ($item) use (&$select, $idKey, $translationNode, $translationKey, $items, $language) {

            if (null === $translationNode) {
                $select[$item->$idKey] = $item->$translationKey;
            } else {
                foreach ($item->$translationNode as $translation) {
                    if ($translation->language_id === $language) {
                        $select[$item->$idKey] = $translation->$translationKey;
                    }
                }
            }
        });

        return $select;
    }
}
