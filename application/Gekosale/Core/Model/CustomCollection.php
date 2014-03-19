<?php

namespace Gekosale\Core\Model;

use Illuminate\Database\Eloquent\Collection;

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
