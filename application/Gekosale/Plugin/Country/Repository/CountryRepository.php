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
namespace Gekosale\Plugin\Country\Repository;

use Gekosale\Core\Repository;
use Symfony\Component\Intl\Intl;

/**
 * Class CountryRepository
 *
 * @package Gekosale\Plugin\Country\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CountryRepository extends Repository
{

    /**
     * Returns all country names for given locale
     *
     * @param string $locale
     *
     * @return \string[]
     */
    public function all($locale = 'en')
    {
        return Intl::getRegionBundle()->getCountryNames($locale);
    }
}