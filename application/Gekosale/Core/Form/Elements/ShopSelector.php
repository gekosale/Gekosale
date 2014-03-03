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

namespace Gekosale\Core\Form\Elements;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ShopSelector
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopSelector extends Field implements ElementInterface
{
    public function __construct($attributes)
    {
        $attributes['stores'] = $this->prepareShopsTree($attributes['stores']);
        parent::__construct($attributes);
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('stores', 'oStores', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('active', 'sActive'),
            $this->formatAttributeJs('set', 'sSet'),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

    /**
     * Prepares stores attribute for JS
     *
     * @param $tree
     *
     * @return array
     */
    protected function prepareShopsTree($tree)
    {
        $stores = [];

        foreach ($tree as $companyId => $companyData) {
            $stores['s' . $companyId] = [
                'name'   => $companyData['name'],
                'label'  => 's' . $companyId,
                'parent' => null,
                'weight' => $companyId,
                'type'   => 'store',
            ];

            foreach ($companyData['children'] as $shopId => $shopData) {
                $stores['v' . $shopId] = [
                    'name'   => $shopData['name'],
                    'label'  => 'v' . $shopId,
                    'parent' => 's' . $companyId,
                    'weight' => $shopId,
                    'type'   => 'view',
                ];
            }
        }

        return $stores;
    }

}
