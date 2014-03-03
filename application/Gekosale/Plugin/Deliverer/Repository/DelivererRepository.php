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
namespace Gekosale\Plugin\Deliverer\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\Deliverer;
use Gekosale\Core\Model\DelivererTranslation;
use Gekosale\Core\Helper;

/**
 * Class DelivererRepository
 *
 * @package Gekosale\Plugin\Deliverer\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class DelivererRepository extends Repository
{

    /**
     * Returns all tax rates
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Deliverer::with('translation')->get();
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
        return Deliverer::with('translation')->findOrFail($id);
    }

    /**
     * Deletes tax rate by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return Deliverer::destroy($id);
        });
    }

    /**
     * Saves deliverer
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {
            $deliverer = Deliverer::firstOrNew([
                'id' => $id
            ]);

            $deliverer->save();

            foreach ($Data['name'] as $languageId => $name) {

                $translation = DelivererTranslation::firstOrCreate([
                    'deliverer_id' => $deliverer->id,
                    'language_id'  => $languageId
                ]);

                $translation->name = $name;

                $translation->save();
            }
        });
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param $id
     *
     * @return array
     */
    public function getPopulateData($id)
    {
        $delivererData = $this->find($id);

        return [
            'required_data' => [
                'language_data' => $delivererData->getLanguageData()
            ]
        ];
    }

    /**
     * Returns Collection as ke-value pairs ready to use in selects
     *
     * @return mixed
     */
    public function getAllDelivererToSelect()
    {
        return $this->getHelper()->flattenCollection($this->all(), 'id', 'translation.name');
    }
}