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

use Gekosale\Core\Model\Country;
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
     * Returns all countries
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Intl::getRegionBundle()->getCountryNames();
    }
}