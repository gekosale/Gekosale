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

class Helper extends Component
{
    /**
     * Flatten Collection to key-value pairs
     *
     * @param Collection $collection
     * @param            $idKey
     * @param            $translationPath
     *
     * @return array
     */
    public function flattenCollection(Collection $collection, $idKey, $translationPath)
    {
        $language        = $this->getCurrentLanguage();
        $select          = [];
        $translationPath = explode('.', $translationPath);

        if (count($translationPath) == 2) {
            $translationNode = $translationPath[0];
            $translationKey  = $translationPath[1];
        } else {
            $translationNode = null;
            $translationKey  = $translationPath[0];
        }

        $collection->each(function ($item) use (&$select, $idKey, $translationNode, $translationKey, $collection, $language) {

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
}