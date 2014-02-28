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
namespace Gekosale\Plugin\Currency\Repository;

use Gekosale\Core\Model\Currency,
    Gekosale\Core\Repository;

use Symfony\Component\Intl\Intl;

/**
 * Class CurrencyRepository
 *
 * @package Gekosale\Plugin\Currency\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CurrencyRepository extends Repository
{

    /**
     * Returns all currencies
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Currency::all();
    }

    /**
     * Returns a single currency data
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Currency::findOrFail($id);
    }

    /**
     * Deletes currency model by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return Currency::destroy($id);
    }

    /**
     * Saves currency data
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $currency = Currency::firstOrCreate([
            'id' => $id
        ]);

        $currency->name               = $Data['required_data']['name'];
        $currency->symbol             = $Data['required_data']['symbol'];
        $currency->decimal_separator  = $Data['required_data']['decimal_separator'];
        $currency->decimal_count      = $Data['required_data']['decimal_count'];
        $currency->thousand_separator = $Data['required_data']['thousand_separator'];
        $currency->positive_prefix    = $Data['required_data']['positive_prefix'];
        $currency->positive_suffix    = $Data['required_data']['positive_suffix'];
        $currency->negative_prefix    = $Data['required_data']['negative_prefix'];
        $currency->negative_suffix    = $Data['required_data']['negative_suffix'];

        $currency->save();
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
        $currencyData = $this->find($id);

        $populateData = [
            'required_data' => [
                'name'               => $currencyData->name,
                'symbol'             => $currencyData->symbol,
                'decimal_separator'  => $currencyData->decimal_separator,
                'decimal_count'      => $currencyData->decimal_count,
                'thousand_separator' => $currencyData->thousand_separator,
                'positive_prefix'    => $currencyData->positive_prefix,
                'positive_suffix'    => $currencyData->positive_suffix,
                'negative_prefix'    => $currencyData->negative_prefix,
                'negative_suffix'    => $currencyData->negative_suffix
            ]
        ];

        return $populateData;
    }

    /**
     * Retrieves all valid currency symbols as key-value pairs
     *
     * @return array
     */
    public function getCurrencySymbols()
    {
        $currencies = Intl::getCurrencyBundle()->getCurrencyNames();

        ksort($currencies);

        $Data = [];

        foreach ($currencies as $currencySymbol => $currencyName) {
            $Data[$currencySymbol] = sprintf('%s (%s)', $currencySymbol, $currencyName);
        }

        return $Data;
    }

    /**
     * Gets all currencies and returns them as key-value pair
     *
     * @return array
     */
    public function getAllCurrencyToSelect()
    {
        $currencies = $this->all();
        $Data       = Array();
        foreach ($currencies as $currency) {
            $Data[$currency->id] = $currency->symbol;
        }

        return $Data;
    }
}