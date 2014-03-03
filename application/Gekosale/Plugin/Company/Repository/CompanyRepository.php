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
use Gekosale\Core\Model\Shop;

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
        return Company::with('shop', 'shop.translation')->get();
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
        $this->transaction(function () use ($id) {
            return Company::destroy($id);
        });
    }

    /**
     * Saves company
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {
            $company = Company::firstOrNew([
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
        $companyData  = $this->find($id);
        $populateData = [];
        $accessor     = $this->getPropertyAccessor();

        $accessor->setValue($populateData, '[required_data]', [
            'name'       => $companyData->name,
            'short_name' => $companyData->short_name
        ]);

        $accessor->setValue($populateData, '[address_data]', [
            'street'   => $companyData->street,
            'streetno' => $companyData->streetno,
            'flatno'   => $companyData->flatno,
            'province' => $companyData->province,
            'postcode' => $companyData->postcode,
            'city'     => $companyData->city,
            'country'  => $companyData->country
        ]);

        return $populateData;
    }

    /**
     * Gets all companies and returns them as key-value pairs
     *
     * @return array
     */
    public function getAllCompanyToSelect()
    {
        $companies = $this->all();
        $Data      = Array();
        foreach ($companies as $company) {
            $Data[$company->id] = $company->name;
        }

        return $Data;
    }

    /**
     * Returns a tree containing all companies and related shops
     *
     * @return array
     */
    public function getShopsTree()
    {
        $tree      = [];
        $companies = $this->all();

        foreach ($companies as $company) {
            $tree[$company->id] = [
                'id'       => $company->id,
                'name'     => $company->name,
                'parent'   => null,
                'children' => []
            ];

            foreach ($company->shop as $shop) {

                $translation = $shop->translation()->hasLanguageId($this->getCurrentLanguage());

                $tree[$company->id]['children'][$shop->id] = [
                    'id'   => $shop->id,
                    'name' => $translation->name,
                ];
            }
        }

        return $tree;
    }
}