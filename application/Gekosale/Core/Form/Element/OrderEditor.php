<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form
 * @subpackage  Gekosale\Core\Form\Element
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Element;

class OrderEditor extends Select
{

    public $datagrid;

    protected $_jsFunction;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_jsFunction = 'LoadProducts_' . $this->_id;
        $this->_attributes['jsfunction'] = 'xajax_' . $this->_jsFunction;
        App::getRegistry()->xajax->registerFunction(array(
            $this->_jsFunction,
            $this,
            'loadProducts'
        ));
        $this->_attributes['load_category_children'] = App::getRegistry()->xajaxInterface->registerFunction(array(
            'LoadCategoryChildren_' . $this->_id,
            $this,
            'loadCategoryChildren'
        ));
        $this->_attributes['datagrid_filter'] = $this->getDatagridFilterData();
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('on_change', 'fOnChange', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('on_before_change', 'fOnBeforeChange', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('jsfunction', 'fLoadProducts', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('datagrid_filter', 'oFilterData', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('load_category_children', 'fLoadCategoryChildren', FE::TYPE_FUNCTION),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }

    public function loadCategoryChildren ($request)
    {
        return Array(
            'aoItems' => $this->getCategories($request['parentId'])
        );
    }

    protected function getCategories ($parent = 0)
    {
        $categories = App::getModel('category')->getChildCategories($parent);
        usort($categories, Array(
            $this,
            'sortCategories'
        ));
        return $categories;
    }

    protected function sortCategories ($a, $b)
    {
        return $a['weight'] - $b['weight'];
    }

    public function loadProducts ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function getDatagrid ()
    {
        if (($this->datagrid == NULL) || ! ($this->datagrid instanceof DatagridModel)) {
            $this->datagrid = App::getModel(get_class($this) . '/datagrid');
            $this->initDatagrid($this->datagrid);
        }
        return $this->datagrid;
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function processVariants ($productId)
    {
        if (! isset($this->_attributes['clientgroupid'])) {
            $this->_attributes['clientgroupid'] = 0;
        }
        if (! isset($this->_attributes['currencyid'])) {
            $this->_attributes['currencyid'] = 0;
        }
        $rawVariants = (App::getModel('product/product')->getAttributeCombinationsForProduct($productId, $this->_attributes['clientgroupid'], $this->_attributes['currencyid']));
        $variants = Array();
        
        $variants[] = Array(
            'id' => '',
            'caption' => $this->trans('TXT_CHOOSE_VARIANT'),
            'price' => ''
        );
        foreach ($rawVariants as $variant) {
            $caption = Array();
            foreach ($variant['attributes'] as $attribute) {
                $caption[] = str_replace('"', '\'', $attribute['name']);
            }
            $variants[] = Array(
                'id' => $variant['id'],
                'caption' => implode(', ', $caption),
                'options' => Array(
                    'price' => $variant['price'],
                    'stock' => $variant['qty'],
                    'weight' => $variant['weight'],
                    'ean' => $variant['symbol'],
                    'thumb' => App::getModel('product')->getThumbPathForId($variant['photoid'])
                )
            );
        }
        return json_encode($variants);
    }

    public function processSellprice ($sellprice)
    {
        return $sellprice;
    }

    protected function initDatagrid ($datagrid)
    {
        $datagrid->setTableData('product', Array(
            'idproduct' => Array(
                'source' => 'P.idproduct'
            ),
            'name' => Array(
                'source' => 'PT.name',
                'prepareForAutosuggest' => true
            ),
            'categoryname' => Array(
                'source' => 'CT.name'
            ),
            'ean' => Array(
                'source' => 'P.ean'
            ),
            'categoryid' => Array(
                'source' => 'PC.categoryid',
                'prepareForTree' => true,
                'first_level' => $this->getCategories()
            ),
            'ancestorcategoryid' => Array(
                'source' => 'CP.ancestorcategoryid'
            ),
            'categoriesname' => Array(
                'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
                'filter' => 'having'
            ),
            'sellprice' => Array(
                'source' => 'ROUND(P.sellprice, 4)'
            ),
            'sellprice_gross' => Array(
                'source' => 'ROUND(P.sellprice * (1 + V.value / 100), 2)'
            ),
            'weight' => Array(
                'source' => 'P.weight'
            ),
            'barcode' => Array(
                'source' => 'P.barcode',
                'prepareForAutosuggest' => true
            ),
            'buyprice' => Array(
                'source' => 'P.buyprice'
            ),
            'producer' => Array(
                'source' => 'PRT.name',
                'prepareForSelect' => true
            ),
            'vat' => Array(
                'source' => 'CONCAT(V.value, \'%\')',
                'prepareForSelect' => true
            ),
            'stock' => Array(
                'source' => 'stock'
            ),
            'trackstock' => Array(
                'source' => 'P.trackstock'
            ),
            'variant__options' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'processVariants'
                )
            ),
            'thumb' => Array(
                'source' => 'PP.photoid',
                'processFunction' => Array(
                    App::getModel('product'),
                    'getThumbPathForId'
                )
            )
        ));
        $datagrid->setFrom('
			product P
			LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = P.idproduct
			LEFT JOIN productphoto PP ON PP.productid = P.idproduct AND PP.mainphoto = 1
			LEFT JOIN viewcategory VC ON VC.categoryid = PC.categoryid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
			LEFT JOIN `producer` R ON P.producerid = R.idproducer
			LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
			LEFT JOIN `vat` V ON P.vatid = V.idvat
		');
        
        $datagrid->setGroupBy('
			P.idproduct
		');
        
        if (isset($this->_attributes['viewid'])) {
            $datagrid->setAdditionalWhere("
				IF(PC.categoryid IS NOT NULL, VC.viewid = :view, 1)
			");
            
            $datagrid->setSQLParams(Array(
                'view' => $this->_attributes['viewid']
            ));
        }
    }
}