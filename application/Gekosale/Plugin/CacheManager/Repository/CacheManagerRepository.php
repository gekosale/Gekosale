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
namespace Gekosale\Plugin\CacheManager\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\CacheManager;
use Gekosale\Core\Model\CacheManagerTranslation;

/**
 * Class CacheManagerRepository
 *
 * @package Gekosale\Plugin\CacheManager\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CacheManagerRepository extends Repository
{

    /**
     * Returns all tax rates
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return CacheManager::all();
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
        return CacheManager::with('translation')->findOrFail($id);
    }

    /**
     * Deletes cache_manager record by ID
     *
     * @param int $id cache_manager ID to delete
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return CacheManager::destroy($id);
        });
    }

    /**
     * Saves cache_manager
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {

            $cache_manager = CacheManager::firstOrNew([
                'id' => $id
            ]);

            $cache_manager->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = CacheManagerTranslation::firstOrNew([
                    'cache_manager_id' => $cache_manager->id,
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
        $cache_managerData = $this->find($id);
        $populateData     = [];
        $accessor         = $this->getPropertyAccessor();
        $languageData     = $cache_managerData->getTranslationData();

        $accessor->setValue($populateData, '[required_data]', [
            'language_data' => $languageData,
        ]);

        return $populateData;
    }
}