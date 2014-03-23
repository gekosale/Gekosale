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
namespace Gekosale\Plugin\ClientGroup\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\ClientGroup;
use Gekosale\Core\Model\ClientGroupTranslation;

/**
 * Class ClientGroupRepository
 *
 * @package Gekosale\Plugin\ClientGroup\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ClientGroupRepository extends Repository
{

    /**
     * Returns all tax rates
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return ClientGroup::all();
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
        return ClientGroup::with('translation')->findOrFail($id);
    }

    /**
     * Deletes client_group record by ID
     *
     * @param int $id client_group ID to delete
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return ClientGroup::destroy($id);
        });
    }

    /**
     * Saves client_group
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {

            $client_group = ClientGroup::firstOrNew([
                'id' => $id
            ]);

            $client_group->discount = $Data['discount'];
            $client_group->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = ClientGroupTranslation::firstOrNew([
                    'client_group_id' => $client_group->id,
                    'language_id'     => $language
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
        $client_groupData = $this->find($id);
        $populateData     = [];
        $accessor         = $this->getPropertyAccessor();
        $languageData     = $client_groupData->getTranslationData();

        $accessor->setValue($populateData, '[required_data]', [
            'discount'      => $client_groupData->discount,
            'language_data' => $languageData,
        ]);

        return $populateData;
    }
}