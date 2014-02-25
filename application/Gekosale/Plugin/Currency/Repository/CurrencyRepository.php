<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\CurrencyRepository
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Currency\Repository;

use Gekosale\Core\Repository;
use Symfony\Component\Intl\Intl;

class CurrencyRepository extends Repository
{

    public function all ()
    {
        return Currency::all();
    }

    public function getCurrencySymbols ()
    {
        $currencies = Intl::getCurrencyBundle()->getCurrencyNames();
        
        ksort($currencies);
        
        $Data = Array();
        
        foreach ($currencies as $currencySymbol => $currencyName){
            $Data[$currencySymbol] = sprintf('%s (%s)', $currencySymbol, $currencyName);
        }
        
        return $Data;
    }
}