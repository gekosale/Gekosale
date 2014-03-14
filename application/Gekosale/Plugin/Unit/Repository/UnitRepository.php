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
namespace Gekosale\Plugin\Unit\Repository;

use Gekosale\Core\Repository,
    Gekosale\Core\Model\Unit,
    Gekosale\Core\Model\UnitTranslation;

/**
 * Class UnitRepository
 *
 * @package Gekosale\Plugin\Unit\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class UnitRepository extends Repository
{

    /**
     * Returns Unit Collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Unit::all();
    }

    /**
     * Returns a Unit Model
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Unit::with('translation')->findOrFail($id);
    }

    /**
     * Deletes Unit Model
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return Unit::destroy($id);
        });

    }

    /**
     * Saves Unit
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {

            $unit = Unit::firstOrNew([
                'id' => $id
            ]);

            $unit->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = UnitTranslation::firstOrNew([
                    'unit_id'     => $unit->id,
                    'language_id' => $language
                ]);

                $translation->setTranslationData($Data, $language);
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
        $unitData = $this->find($id);
        $populateData     = [];
        $accessor         = $this->getPropertyAccessor();
        $languageData     = $unitData->getTranslationData();

        $accessor->setValue($populateData, '[required_data]', [
            'language_data' => $languageData,
        ]);

        return $populateData;
    }
}