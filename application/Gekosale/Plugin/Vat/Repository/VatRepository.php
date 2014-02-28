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
namespace Gekosale\Plugin\Vat\Repository;

use Gekosale\Core\Model\Vat;
use Gekosale\Core\Repository;
use Gekosale\Core\Model\VatTranslation;

/**
 * Class VatRepository
 *
 * @package Gekosale\Plugin\Vat\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class VatRepository extends Repository
{

    /**
     * Returns all tax rates
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Vat::all();
    }

    /**
     * Returns a single tax rate
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Vat::with('translation')->findOrFail($id);
    }

    /**
     * Deletes tax rate by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return Vat::destroy($id);
    }

    /**
     * Saves tax rate
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $vat = Vat::firstOrCreate([
            'id' => $id
        ]);

        $vat->value = $Data['required_data']['value'];

        $translations = $Data['required_data']['language_data']['name'];

        foreach ($translations as $languageId => $name) {

            $translation = VatTranslation::firstOrCreate([
                'vat_id'      => $vat->id,
                'language_id' => $languageId
            ]);

            $translation->name = $name;

            $translation->save();
        }

        $vat->save();
    }

    /**
     * Returns data required for populating the form
     *
     * @param $id
     *
     * @return array
     */
    public function getPopulateData($id)
    {
        $vatData = $this->find($id)->toArray();

        if (empty($vatData)) {
            throw new \InvalidArgumentException('Vat with such ID does not exists');
        }

        $languageData = [];
        foreach ($vatData['translation'] as $translation) {
            $languageData['name'][$translation['language_id']] = $translation['name'];
        }

        $populateData = [
            'required_data' => [
                'value'         => $vatData['value'],
                'language_data' => $languageData
            ]
        ];

        return $populateData;
    }
}