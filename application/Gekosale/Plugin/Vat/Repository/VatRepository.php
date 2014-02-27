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

use Gekosale\Core\Model\Vat,
    Gekosale\Core\Repository;

/**
 * Class VatRepository
 *
 * @package Gekosale\Plugin\Vat\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class VatRepository extends Repository
{

    /**
     * Returns all currencies
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Vat::all();
    }

    /**
     * Returns a single vat data
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Vat::findOrFail($id);
    }

    /**
     * Deletes vat model by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return Vat::destroy($id);
    }

    /**
     * Saves vat data
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $vat = Vat::firstOrNew([
            'id' => $id
        ]);

        $vat->name               = $Data['required_data']['name'];
        $vat->symbol             = $Data['required_data']['symbol'];
        $vat->decimal_separator  = $Data['required_data']['decimal_separator'];
        $vat->decimal_count      = $Data['required_data']['decimal_count'];
        $vat->thousand_separator = $Data['required_data']['thousand_separator'];
        $vat->positive_prefix    = $Data['required_data']['positive_prefix'];
        $vat->positive_sufix     = $Data['required_data']['positive_sufix'];
        $vat->negative_prefix    = $Data['required_data']['negative_prefix'];
        $vat->negative_sufix     = $Data['required_data']['negative_sufix'];

        $vat->save();
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
        $vatData = $this->find($id)->toArray();

        if (empty($vatData)) {
            throw new \InvalidArgumentException('Vat with such ID does not exists');
        }

        $populateData = [
            'required_data' => [
                'value' => $vatData['value'],
            ]
        ];

        return $populateData;
    }
}