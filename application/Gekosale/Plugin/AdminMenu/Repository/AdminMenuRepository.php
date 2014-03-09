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
namespace Gekosale\Plugin\AdminMenu\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\AdminMenu;

/**
 * Class AdminMenuRepository
 *
 * @package Gekosale\Plugin\AdminMenu\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AdminMenuRepository extends Repository
{

    /**
     * Returns a admin menu collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return AdminMenu::all();
    }

    /**
     * Returns menu
     *
     * @return array
     */
    public function getMenuData()
    {
        $this->menuData     = $this->all();
        $this->currentRoute = $this->getRequest()->attributes->get('_route');

        return $this->parseMenuTree();
    }

    /**
     * Parses and returns menu tree
     *
     * @param null $parent
     *
     * @return array
     */
    protected function parseMenuTree($parent = null)
    {
        $menuItems = Array();
        foreach ($this->menuData as $menu) {
            if ($parent == null) {
                if ($menu['parent_id'] != '') {
                    continue;
                }
            } elseif ($menu['parent_id'] != $parent) {
                continue;
            }

            $menuItems[] = Array(
                'id'       => $menu['id'],
                'icon'     => $menu['icon'],
                'name'     => $this->trans($menu['name']),
                'link'     => (null !== $menu['route']) ? $this->generateUrl($menu['route']) : '',
                'active'   => (bool)($this->currentRoute == $menu['route']),
                'children' => $this->parseMenuTree($menu['id']),
            );
        }

        return $menuItems;
    }
}