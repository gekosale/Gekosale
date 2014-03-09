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

use Gekosale\Core\Model\TranslatableModelInterface;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class Model
 *
 * Extends base Eloquent model and provides additional methods
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Model extends BaseModel
{
    /**
     * Translatable attributes in model
     *
     * @var array
     */
    protected $translatable = [];

    /**
     * Boots Illuminate\Database\Eloquent\Model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Returns all model translatable attributes
     *
     * @return array
     */
    protected function getTranslatableAttributes()
    {
        return $this->translatable;
    }

    /**
     * Adds new translatable attribute
     *
     * @param $attribute
     */
    public function addTranslatableAttribute($attribute)
    {
        $this->translatable[] = $attribute;
    }

    /**
     * Checks if attribute is translatable
     *
     * @param $key
     *
     * @return bool
     */
    protected function isTranslatableAttribute($key)
    {
        return array_key_exists($key, array_flip($this->translatable));
    }

    /**
     * Shortcut to get PropertyAccessor
     *
     * @return \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected function getPropertyAccessor()
    {
        return PropertyAccess::createPropertyAccessor();
    }

    /**
     * Sets translatable attributes in model
     *
     * @param array $Data
     * @param       $language
     */
    public function setTranslationData(array $Data, $language)
    {
        $accessor = $this->getPropertyAccessor();

        foreach ($this->getTranslatableAttributes() as $attribute) {
            if ($this->isTranslatableAttribute($attribute)
                && isset($Data[$attribute])
                && is_array($Data[$attribute])
                && isset($Data[$attribute][$language])
            ) {
                $accessor->setValue($this, $attribute, $Data[$attribute][$language]);
            }
        }
    }

    /**
     * Returns translation data
     *
     * @return array
     * @throws \LogicException
     */
    public function getTranslationData()
    {
        if (!$this instanceof TranslatableModelInterface) {
            throw new \LogicException('Model must implement TranslatableModelInterface to get translations from it.');
        }

        $collection   = $this->translation;
        $languageData = [];

        foreach ($collection as $item) {
            foreach ($item->translatable as $attribute) {
                $languageData[$item->language_id][$attribute] = $item->$attribute;
            }
        }

        return $languageData;
    }
}