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
namespace Gekosale\Plugin\PluginManager\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\PluginManager;

/**
 * Class PluginManagerRepository
 *
 * @package Gekosale\Plugin\PluginManager\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PluginManagerRepository extends Repository
{

    /**
     * Returns a plugin_manager collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return PluginManager::all();
    }

    /**
     * Returns the plugin_manager model
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return PluginManager::findOrFail($id);
    }

    /**
     * Deletes plugin_manager by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return PluginManager::destroy($id);
    }

    /**
     * Saves plugin_manager
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $plugin_manager = PluginManager::firstOrCreate([
            'id' => $id
        ]);

        $plugin_manager->save();
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
        $plugin_managerData = $this->find($id);

        return [
            'required_data' => [
                'name'       => $plugin_managerData->name,
            ],
        ];
    }
}