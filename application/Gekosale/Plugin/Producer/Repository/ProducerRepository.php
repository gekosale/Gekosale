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
namespace Gekosale\Plugin\Producer\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\Producer;
use Gekosale\Core\Model\ProducerTranslation;

/**
 * Class ProducerRepository
 *
 * @package Gekosale\Plugin\Producer\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerRepository extends Repository
{
    /**
     * Returns producer collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Producer::all();
    }

    /**
     * Returns single producer model
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Producer::with('translation')->findOrFail($id);
    }

    /**
     * Deletes producer
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return Producer::destroy($id);
        });
    }

    /**
     * Saves producer
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {
            $producer = Producer::firstOrNew([
                'id' => $id
            ]);

            $producer->save();

            foreach ($Data['name'] as $languageId => $name) {

                $translation = ProducerTranslation::firstOrCreate([
                    'producer_id' => $producer->id,
                    'language_id' => $languageId
                ]);

                $translation->name = $name;

                $translation->save();
            }
        });
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param int $id Producer ID
     *
     * @return array Populate data
     */
    public function getPopulateData($id)
    {
        $producerData = $this->find($id);

        return [
            'required_data' => [
                'language_data' => $producerData->getLanguageData()
            ]
        ];
    }
}