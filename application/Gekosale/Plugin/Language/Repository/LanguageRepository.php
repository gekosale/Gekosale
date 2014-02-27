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
     * Returns a language record
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Language::with('currency')->findOrFail($id);
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

        $language->name        = $Data['required_data']['name'];
        $language->translation = $Data['required_data']['translation'];
        $language->currency_id = $Data['currency_data']['currency_id'];

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
                'name'        => $languageData['name'],
                'translation' => $languageData['translation'],
            ],
            'currency_data' => [
                'currency_id' => $languageData['currency_id']
            ]
        ];

        return $populateData;
    }
}