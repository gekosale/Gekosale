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

class AllegroProductSelect extends ProductSelect
{

    public $datagrid;

    protected $_jsFunctionLoad;

    protected $_jsFunctionProcessTitles;

    protected $_jsFunctionPrepareRow;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_jsFunctionLoad = 'LoadProducts_' . $this->_id;
        $this->_attributes['jsfunction_load'] = 'xajax_' . $this->_jsFunctionLoad;
        App::getRegistry()->xajax->registerFunction(array(
            $this->_jsFunctionLoad,
            $this,
            'loadProducts'
        ));
        $this->_jsFunctionProcessTitles = 'ProcessTitles_' . $this->_id;
        $this->_attributes['jsfunction_process_titles'] = 'xajax_' . $this->_jsFunctionProcessTitles;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunctionProcessTitles,
            $this,
            'processTitles'
        ));
        $this->_jsFunctionPrepareRow = 'PrepareRow_' . $this->_id;
        $this->_attributes['jsfunction_prepare_row'] = 'xajax_' . $this->_jsFunctionPrepareRow;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunctionPrepareRow,
            $this,
            'prepareRow'
        ));
        $this->_attributes['advanced_editor'] = true;
        $this->_attributes['repeat_min'] = 1;
        $this->_attributes['repeat_max'] = FE::INFINITE;
        $this->_attributes['favourite_categories'] = Array();
        $favouriteCategories = App::getModel('allegro/allegrocategories')->getAllegroFavouriteCategoriesALLToSelect();
        foreach ($favouriteCategories as $favouriteCategoryId => $favouriteCategoryCaption) {
            $this->_attributes['favourite_categories'][] = Array(
                'id' => $favouriteCategoryId,
                'caption' => $favouriteCategoryCaption,
                'path' => implode(' > ', App::getModel('allegro/allegrocategories')->getCategoryPath($favouriteCategoryId)) . ' > <strong>' . $favouriteCategoryCaption . '</strong>'
            );
        }
        $this->_jsGetChildren = 'GetChildren_' . $this->_id;
        if (isset($this->_attributes['load_allegro_category_children']) && is_callable($this->_attributes['load_allegro_category_children'])) {
            $this->_attributes['get_children'] = 'xajax_' . $this->_jsGetChildren;
            App::getRegistry()->xajaxInterface->registerFunction(array(
                $this->_jsGetChildren,
                $this,
                'getChildren'
            ));
        }
        $this->_attributes['datagrid_filter'] = $this->getDatagridFilterData();
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function getChildren ($request)
    {
        $children = call_user_func($this->_attributes['load_allegro_category_children'], $request['parent']);
        if (! is_array($children)) {
            $children = Array();
        }
        return Array(
            'children' => $children
        );
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('help', 'sHelp'),
            $this->_FormatAttribute_JS('comment', 'sComment'),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('default_title_format', 'sDefaultTitleFormat'),
            $this->_FormatAttribute_JS('default_description_format', 'sDefaultDescriptionFormat'),
            $this->_FormatAttribute_JS('favourite_categories', 'aoFavouriteCategories', FE::TYPE_OBJECT),
            $this->_FormatAttribute_JS('jsfunction_load', 'fLoadProducts', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('jsfunction_process_titles', 'fProcessTitles', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('jsfunction_prepare_row', 'fPrepareRow', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('get_children', 'fGetChildren', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('advanced_editor', 'bAdvancedEditor', FE::TYPE_BOOLEAN),
            $this->_FormatAttribute_JS('datagrid_filter', 'oFilterData', FE::TYPE_OBJECT),
            $this->_FormatAttribute_JS('main_allegro_categories', 'oItems', FE::TYPE_OBJECT),
            $this->_FormatFactor_JS('min_price_factor', 'oMinPriceFactor'),
            $this->_FormatFactor_JS('start_price_factor', 'oStartPriceFactor'),
            $this->_FormatFactor_JS('buy_price_factor', 'oBuyPriceFactor'),
            $this->_FormatRepeatable_JS(),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }

    public function loadProducts ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function processTitles ($request)
    {
        $data = $request['data'];
        if (! is_array($data) && ! empty($data)) {
            $data = Array(
                $data
            );
        }
        foreach ($data as &$row) {
            $tags = Array();
            $values = Array();
            foreach ($this->_attributes['tags_translation_table'] as $tag => $column) {
                $tags[] = $tag;
                if ($column{0} == '$') {
                    $column = substr($column, 1);
                    $values[$tag] = isset($row[$column]) ? $row[$column] : '';
                }
                else {
                    $values[$tag] = $column;
                }
            }
            $row[$request['field']] = App::getModel('allegro/tagtranslator')->Translate($request['format'], $tags, $values);
        }
        return Array(
            'data' => $data
        );
    }

    public function prepareRow ($request)
    {
        $translated = $this->processTitles(Array(
            'format' => $request['title_format'],
            'data' => Array(
                $request['row']
            ),
            'field' => 'title'
        ));
        
        $translated = $this->processTitles(Array(
            'format' => $request['description_format'],
            'data' => $translated['data'],
            'field' => 'description__value'
        ));
        
        $translated['data'][0]['title'] = substr(str_replace(Array(
            '\\',
            '"',
            '<',
            '>',
            '&'
        ), '', $translated['data'][0]['title']), 0, 50);
        
        return Array(
            'row' => $translated['data'][0]
        );
    }

    public function getDatagrid ()
    {
        if (($this->datagrid == NULL) || ! ($this->datagrid instanceof DatagridModel)) {
            $this->datagrid = App::getModel('datagrid/datagrid');
            $this->initDatagrid($this->datagrid);
        }
        return $this->datagrid;
    }

    public function processAllegroCategories ($productId)
    {
        $categories = Array();
        $defaultCategories = App::getModel('allegro/allegrocategories')->getDefaultAllegroCategoriesForProduct($productId);
        foreach ($defaultCategories as $key => $category) {
            $ids = array_keys($category);
            $id = $ids[0];
            $categories[] = Array(
                'id' => $id,
                'caption' => $category[$id],
                'path' => implode(' / ', App::getModel('allegro/allegrocategories')->getCategoryPath($id))
            );
        }
        return json_encode($categories);
    }

    public function processVariants ($productId)
    {
        $rawVariants = (App::getModel('product/product')->getAttributeCombinationsForProduct($productId));
        $variants = Array();
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

    public function processVariantData ($productId)
    {
        $rawVariants = (App::getModel('product/product')->getAttributeCombinationsForProduct($productId));
        $variants = Array();
        foreach ($rawVariants as $variant) {
            $caption = Array();
            foreach ($variant['attributes'] as $attribute) {
                $caption[] = $attribute['name'];
            }
            $variants[$variant['id']] = Array(
                'sellprice' => $variant['price'],
                'sellprice_gross' => $variant['price_gross'],
                'stock' => $variant['qty']
            );
        }
        return json_encode($variants);
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
                'source' => 'P.sellprice'
            ),
            'sellprice_gross' => Array(
                'source' => 'ROUND(P.sellprice * (1 + V.value / 100), 2)'
            ),
            'barcode' => Array(
                'source' => 'P.barcode',
                'prepareForAutosuggest' => true
            ),
            'buyprice' => Array(
                'source' => 'P.buyprice'
            ),
            'buyprice_gross' => Array(
                'source' => 'ROUND(P.buyprice * (1 + V.value / 100), 2)'
            ),
            'producer' => Array(
                'source' => 'RT.name',
                'prepareForSelect' => true
            ),
            'vat' => Array(
                'source' => 'CONCAT(V.value, \'%\')',
                'prepareForSelect' => true
            ),
            'stock' => Array(
                'source' => 'P.stock'
            ),
            'allegro_category__options' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'processAllegroCategories'
                )
            ),
            'variant__options' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'processVariants'
                )
            ),
            'variant__data' => Array(
                'source' => 'P.idproduct',
                'processFunction' => Array(
                    $this,
                    'processVariantData'
                )
            )
        ));
        $datagrid->setFrom('
			product P
			LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = P.idproduct
			LEFT JOIN category C ON C.idcategory = PC.categoryid
        	LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON (C.idcategory = CT.categoryid AND CT.languageid = :languageid)
			LEFT JOIN `producer` R ON P.producerid = R.idproducer
			LEFT JOIN `producertranslation` RT ON RT.producerid = R.idproducer
			LEFT JOIN `vat` V ON P.vatid = V.idvat
		');
        $datagrid->setGroupBy('
			P.idproduct
		');
    }
}
