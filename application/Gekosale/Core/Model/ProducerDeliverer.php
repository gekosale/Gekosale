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
namespace Gekosale\Core\Model;

use Gekosale\Core\Model;

/**
 * Class ProducerDeliverer
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerDeliverer extends Model
{

    protected $table = 'producer_deliverer';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['producer_id', 'deliverer_id'];
}