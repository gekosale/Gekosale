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
namespace Gekosale\Plugin\Company\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\Company;

/**
 * Class CompanyRepository
 *
 * @package Gekosale\Plugin\Company\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CompanyRepository extends Repository
{

    /**
     * Returns a company collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Company::all();
    }

    /**
     * Returns the company model
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Company::findOrFail($id);
    }

    /**
     * Deletes company by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return Company::destroy($id);
    }

    /**
     * Saves company
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $company = Company::firstOrCreate([
            'id' => $id
        ]);

        $company->name       = $Data['name'];
        $company->short_name = $Data['short_name'];
        $company->street     = $Data['street'];
        $company->streetno   = $Data['streetno'];
        $company->flatno     = $Data['flatno'];
        $company->province   = $Data['province'];
        $company->postcode   = $Data['postcode'];
        $company->city       = $Data['city'];
        $company->country    = $Data['country'];

        $company->save();
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
        $companyData = $this->find($id);

        return [
            'required_data' => [
                'name'       => $companyData->name,
                'short_name' => $companyData->short_name,
            ],
            'address_data'  => [
                'street'   => $companyData->street,
                'streetno' => $companyData->streetno,
                'flatno'   => $companyData->flatno,
                'province' => $companyData->province,
                'postcode' => $companyData->postcode,
                'city'     => $companyData->city,
                'country'  => $companyData->country
            ]
        ];
    }
}