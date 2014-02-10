<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Vat
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Vat\Model;

use Gekosale\Plugin\Vat\Event\ModelEvent;
use Gekosale\Core\Model;
use Gekosale\Core\Datagrid;
use Gekosale\Plugin\Vat\Model\ORM\VatQuery;
use Gekosale\Plugin\Vat\Model\ORM\VatTranslationQuery;

class VatI18n extends Model
{

    protected $table = 'vat_i18n';
}
