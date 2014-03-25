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
namespace Gekosale\Plugin\Category\Repository;

use Gekosale\Core\Helper;
use Gekosale\Core\Model\Category;
use Gekosale\Core\Model\CategoryTranslation;
use Gekosale\Core\Repository;

/**
 * Class CategoryRepository
 *
 * @package Gekosale\Plugin\Category\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CategoryRepository extends Repository
{

    /**
     * Returns all currencies
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Category::with('translation')->get();
    }

    /**
     * Returns a single category data
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Deletes category by ID
     *
     * @param $id
     */
    public function delete($request)
    {
        $this->transaction(function () use ($request) {
            return Category::destroy($request['id']);
        });
    }

    /**
     * Saves category
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $category = Category::firstOrCreate([
            'id' => $id
        ]);

        $requiredData = $Data['required_data'];

        $category->name               = $requiredData['name'];
        $category->symbol             = $requiredData['symbol'];
        $category->decimal_separator  = $requiredData['decimal_separator'];
        $category->decimal_count      = $requiredData['decimal_count'];
        $category->thousand_separator = $requiredData['thousand_separator'];
        $category->positive_prefix    = $requiredData['positive_prefix'];
        $category->positive_suffix    = $requiredData['positive_suffix'];
        $category->negative_prefix    = $requiredData['negative_prefix'];
        $category->negative_suffix    = $requiredData['negative_suffix'];

        $category->save();
    }

    public function quickAddCategory($request)
    {
        $id = $this->transaction(function () use ($request) {
            $category            = new Category();
            $category->parent_id = isset($request['parent']) ? $request['parent'] : null;
            $category->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = CategoryTranslation::firstOrNew([
                    'category_id' => $category->id,
                    'language_id' => $language
                ]);

                $translation->name = $request['name'];
                $translation->slug = Helper::makeSlug($request['name']);
                $translation->save();
            }

            return $category->id;
        });

        return [
            'id' => $id
        ];
    }

    /**
     * Returns data required for populating a form
     *
     * @param $id
     *
     * @return array
     */
    public function getPopulateData($id)
    {
        $categoryData = $this->find($id);

        $populateData = [
            'required_data' => [
                'name'               => $categoryData->name,
                'symbol'             => $categoryData->symbol,
                'decimal_separator'  => $categoryData->decimal_separator,
                'decimal_count'      => $categoryData->decimal_count,
                'thousand_separator' => $categoryData->thousand_separator,
                'positive_prefix'    => $categoryData->positive_prefix,
                'positive_suffix'    => $categoryData->positive_suffix,
                'negative_prefix'    => $categoryData->negative_prefix,
                'negative_suffix'    => $categoryData->negative_suffix
            ]
        ];

        return $populateData;
    }

    public function changeCategoryOrder($request)
    {
        $this->transaction(function () use ($request) {
            foreach ($request['items'] as $item) {
                $category            = Category::findOrFail($item['id']);
                $category->parent_id = $item['parent'];
                $category->hierarchy = $item['weight'];
                $category->save();
            }
        });

        return [
            'status' => $this->trans('Category order saved successfully.')
        ];
    }

    /**
     * Returns categories tree
     *
     * @return array
     */
    public function getCategoriesTree()
    {
        $categories     = $this->all();
        $categoriesTree = [];

        foreach ($categories as $category) {

            $children     = Category::children($category->id)->get();
            $languageData = $category->translation->getCurrentTranslation($this->getCurrentLanguage());

            $categoriesTree[$category->id] = [
                'id'          => $category->id,
                'name'        => $languageData->name,
                'hasChildren' => count($children),
                'parent'      => $category->parent_id,
                'weight'      => $category->hierarchy
            ];
        }

        return $categoriesTree;
    }
}