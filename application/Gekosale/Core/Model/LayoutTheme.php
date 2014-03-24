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

use Gekosale\Core\Helper;
use Gekosale\Core\Model;

/**
 * Class LayoutTheme
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayoutTheme extends Model
{

    protected $table = 'layout_theme';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id'];
}