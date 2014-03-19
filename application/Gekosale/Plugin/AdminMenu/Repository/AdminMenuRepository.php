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
     * Admin menu sorting function
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function sortMenu(&$a, &$b)
    {
        if (!empty($a['children'])) {
            usort($a['children'], [$this, 'sortMenu']);
        }
        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return $a['sort_order'] > $b['sort_order'] ? 1 : -1;
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
        $tree               = $this->parseMenuTree();

        usort($tree, [$this, 'sortMenu']);

        return $tree;
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

            $menuItems[] = [
                'id'         => $menu['id'],
                'icon'       => $menu['icon'],
                'name'       => $this->trans($menu['name']),
                'sort_order' => $menu['sort_order'],
                'link'       => (null !== $menu['route']) ? $this->generateUrl($menu['route']) : '',
                'active'     => (bool)($this->currentRoute == $menu['route']),
                'children'   => $this->parseMenuTree($menu['id']),
            ];
        }

        return $menuItems;
    }
}