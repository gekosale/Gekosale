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

/**
 * Class ProductSelectRelated
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductSelectRelated extends Select implements ElementInterface
{
    public $datagrid;
    protected $_jsFunction;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_jsFunction               = 'LoadProducts_' . $this->_id;
        $this->_attributes['jsfunction'] = 'xajax_' . $this->_jsFunction;
        App::getRegistry()->xajax->registerFunction(array(
            $this->_jsFunction,
            $this,
            'loadProducts_' . $this->_id
        ));
        $this->_attributes['load_category_children'] = App::getRegistry()->xajaxInterface->registerFunction(array(
            'LoadCategoryChildren_' . $this->_id,
            $this,
            'loadCategoryChildren'
        ));
        if (isset($this->_attributes['exclude_from'])) {
            $this->_attributes['exclude_from_field'] = $this->_attributes['exclude_from']->GetName();
        }
        if (!isset($this->_attributes['exclude'])) {
            $this->_attributes['exclude'] = Array(
                0
            );
        }
        $this->_attributes['datagrid_filter'] = $this->getDatagridFilterData();
    }

    public function __call($function, $arguments)
    {
        if ($function == 'loadProducts_' . $this->_id) {
            return call_user_func_array(Array(
                $this,
                'loadProducts'
            ), $arguments);
        }
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('exclude_from_field', 'sExcludeFrom'),
            $this->formatAttributeJs('jsfunction', 'fLoadProducts', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('datagrid_filter', 'oFilterData', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('load_category_children', 'fLoadCategoryChildren', ElementInterface::TYPE_FUNCTION),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

    public function loadCategoryChildren($request)
    {
        return Array(
            'aoItems' => $this->getCategories($request['parentId'])
        );
    }

    protected function getCategories($parent = 0)
    {
        $categories = App::getModel('category')->getChildCategories($parent);
        usort($categories, Array(
            $this,
            'sortCategories'
        ));

        return $categories;
    }

    protected function sortCategories($a, $b)
    {
        return $a['weight'] - $b['weight'];
    }

    public function loadProducts($request, $processFunction)
    {
        if (isset($request['dynamic_exclude']) and is_array($request['dynamic_exclude'])) {
            $this->_attributes['exclude'] = array_merge($this->_attributes['exclude'], $request['dynamic_exclude']);
        } else {
            $this->_attributes['exclude'] = Array(
                0
            );
        }
        $this->getDatagrid()->setAdditionalWhere('
			P.idproduct NOT IN (' . implode(',', $this->_attributes['exclude']) . ')
		');

        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function getDatagrid()
    {
        if (($this->datagrid == null)) {
            $this->datagrid = App::getModel('datagrid/datagrid');
            $this->initDatagrid($this->datagrid);
        }

        return $this->datagrid;
    }

    public function getDatagridFilterData()
    {
        return $this->getDatagrid()->getFilterData();
    }

    protected function initDatagrid($datagrid)
    {
        $datagrid->setTableData('product', Array(
            'idproduct'          => Array(
                'source' => 'P.idproduct'
            ),
            'name'               => Array(
                'source'                => 'PT.name',
                'prepareForAutosuggest' => true
            ),
            'categoryname'       => Array(
                'source' => 'CT.name'
            ),
            'categoryid'         => Array(
                'source'         => 'PC.categoryid',
                'prepareForTree' => true,
                'first_level'    => $this->getCategories()
            ),
            'ancestorcategoryid' => Array(
                'source' => 'CP.ancestorcategoryid'
            ),
            'categoriesname'     => Array(
                'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
                'filter' => 'having'
            ),
            'sellprice'          => Array(
                'source' => 'P.sellprice'
            ),
            'sellprice_gross'    => Array(
                'source' => 'ROUND(P.sellprice * (1 + V.value / 100), 2)'
            ),
            'barcode'            => Array(
                'source'                => 'P.barcode',
                'prepareForAutosuggest' => true
            ),
            'buyprice'           => Array(
                'source' => 'P.buyprice'
            ),
            'buyprice_gross'     => Array(
                'source' => 'ROUND(P.buyprice * (1 + V.value / 100), 2)'
            ),
            'producer'           => Array(
                'source'           => 'PRT.name',
                'prepareForSelect' => true
            ),
            'vat'                => Array(
                'source'           => 'CONCAT(V.value, \'%\')',
                'prepareForSelect' => true
            ),
            'stock'              => Array(
                'source' => 'stock'
            ),
            'thumb'              => Array(
                'source'          => 'PP.photoid',
                'processFunction' => Array(
                    $this,
                    'getThumbPathForId'
                )
            )
        ));
        $datagrid->setFrom('
			product P
			LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = P.idproduct
			LEFT JOIN productphoto PP ON PP.productid = P.idproduct AND PP.mainphoto = 1
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
			LEFT JOIN producer R ON P.producerid = R.idproducer
			LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
			LEFT JOIN `vat` V ON P.vatid = V.idvat
		');

        $datagrid->setGroupBy('
			P.idproduct
		');

        if (isset($this->_attributes['additional_rows'])) {
            $datagrid->setAdditionalRows($this->_attributes['additional_rows']);
        }
    }

    public function getThumbPathForId($id)
    {
        if ($id > 1) {
            try {
                $image = App::getModel('gallery')->getSmallImageById($id);
            } catch (Exception $e) {
                $image = Array(
                    'path' => ''
                );
            }

            return $image['path'];
        } else {
            return '';
        }
    }
}