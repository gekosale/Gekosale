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
     * Returns data required for populating a form
     *
     * @param $id
     *
     * @return array
     */
    public function getPopulateData($id)
    {
        $currencyData = $this->find($id)->toArray();

        $populateData = Array(
            'required_data' => Array(
                'name'               => $currencyData['name'],
                'symbol'             => $currencyData['symbol'],
                'decimal_separator'  => $currencyData['decimal_separator'],
                'decimal_count'      => $currencyData['decimal_count'],
                'thousand_separator' => $currencyData['thousand_separator'],
                'positive_prefix'    => $currencyData['positive_prefix'],
                'positive_sufix'     => $currencyData['positive_sufix'],
                'negative_prefix'    => $currencyData['negative_prefix'],
                'negative_sufix'     => $currencyData['negative_sufix']
            )
        );

        return $populateData;
    }

    /**
     * Returns all valid currency symbols as key-value pairs
     *
     * @return array
     */
    public function getCurrencySymbols()
    {
        $currencies = Intl::getCurrencyBundle()->getCurrencyNames();

        ksort($currencies);

        $Data = Array();

        foreach ($currencies as $currencySymbol => $currencyName) {
            $Data[$currencySymbol] = sprintf('%s (%s)', $currencySymbol, $currencyName);
        }

        return $Data;
    }
}