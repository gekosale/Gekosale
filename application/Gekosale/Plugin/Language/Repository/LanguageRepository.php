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
namespace Gekosale\Plugin\Language\Repository;

use Gekosale\Core\Model\Language,
    Gekosale\Core\Repository;

use Symfony\Component\Intl\Intl;

/**
 * Class LanguageRepository
 *
 * @package Gekosale\Plugin\Language\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageRepository extends Repository
{

    /**
     * Returns all currencies
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Language::all();
    }

    /**
     * Returns a single language data
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Language::findOrFail($id);
    }

    /**
     * Deletes language model by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return Language::destroy($id);
    }

    /**
     * Saves language data
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $language = Language::firstOrNew([
            'id' => $id
        ]);

        $language->name               = $Data['required_data']['name'];
        $language->symbol             = $Data['required_data']['symbol'];
        $language->decimal_separator  = $Data['required_data']['decimal_separator'];
        $language->decimal_count      = $Data['required_data']['decimal_count'];
        $language->thousand_separator = $Data['required_data']['thousand_separator'];
        $language->positive_prefix    = $Data['required_data']['positive_prefix'];
        $language->positive_sufix     = $Data['required_data']['positive_sufix'];
        $language->negative_prefix    = $Data['required_data']['negative_prefix'];
        $language->negative_sufix     = $Data['required_data']['negative_sufix'];

        $language->save();
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
        $languageData = $this->find($id)->toArray();

        if (empty($languageData)) {
            throw new \InvalidArgumentException('Language with such ID does not exists');
        }

        $populateData = [
            'required_data' => [
                'name'               => $languageData['name'],
                'symbol'             => $languageData['symbol'],
                'decimal_separator'  => $languageData['decimal_separator'],
                'decimal_count'      => $languageData['decimal_count'],
                'thousand_separator' => $languageData['thousand_separator'],
                'positive_prefix'    => $languageData['positive_prefix'],
                'positive_sufix'     => $languageData['positive_sufix'],
                'negative_prefix'    => $languageData['negative_prefix'],
                'negative_sufix'     => $languageData['negative_sufix']
            ]
        ];

        return $populateData;
    }
}