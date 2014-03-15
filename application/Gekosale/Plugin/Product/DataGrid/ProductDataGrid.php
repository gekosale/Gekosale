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
namespace Gekosale\Plugin\Product\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class ProductDataGrid
 *
 * @package Gekosale\Plugin\Product\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {

        $this->setTableData([
            'id'                    => [
                'source' => 'P.id'
            ],
            'name'                  => [
                'source' => 'PT.name',
            ],
            'seo'                   => [
                'source'          => 'PT.seo',
                'processFunction' => function ($seo) {
                        return $seo
                    }
            ],
            'delivelercode'         => [
                'source' => 'P.delivelercode'
            ],
            'hierarchy'             => [
                'source' => 'P.hierarchy'
            ],
            'ean'                   => [
                'source' => 'P.ean'
            ],
            'categoryname'          => [
                'source' => 'CT.name'
            ],
            'categoryid'            => [
                'source'         => 'PC.categoryid',
                'prepareForTree' => true,
                'first_level'    => $this->getCategories()
            ],
            'ancestorcategoryid'    => [
                'source' => 'CP.ancestorcategoryid'
            ],
            'categoriesname'        => [
                'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
                'filter' => 'having'
            ],
            'sellprice'             => [
                'source' => 'P.sellprice'
            ],
            'sellprice_gross'       => [
                'source' => 'ROUND(P.sellprice * (1 + V.value / 100), 2)'
            ],
            'barcode'               => [
                'source'                => 'P.barcode',
                'prepareForAutosuggest' => true
            ],
            'buyprice'              => [
                'source' => 'P.buyprice'
            ],
            'buyprice_gross'        => [
                'source' => 'ROUND(P.buyprice * (1 + V.value / 100), 2)'
            ],
            'producer'              => [
                'source'           => 'PRT.name',
                'prepareForSelect' => true
            ],
            'deliverer'             => [
                'source'           => 'DT.name',
                'prepareForSelect' => true
            ],
            'status'                => [
                'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', PS.name), 1) SEPARATOR \'<br />\')',
                'filter' => 'having'
            ],
            'vat'                   => [
                'source'           => 'CONCAT(V.value, \'%\')',
                'prepareForSelect' => true
            ],
            'stock'                 => [
                'source' => 'P.stock'
            ],
            'enable'                => [
                'source' => 'P.enable'
            ],
            'weight'                => [
                'source' => 'P.weight'
            ],
            'adddate'               => [
                'source' => 'P.adddate'
            ],
            'thumb'                 => [
                'source'          => 'PP.photoid',
                'processFunction' => [
                    $this,
                    'getThumbPathForId'
                ]
            ],
            'attributes'            => [
                'source' => 'PAS.idattributeset'
            ],
            'trackstock'            => [
                'source' => 'P.trackstock'
            ],
            'disableatstockenabled' => [
                'source' => 'P.disableatstockenabled'
            ]
        ]);

        $datagrid->setFrom('
			product P
			LEFT JOIN producttranslation PT ON P.id = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = P.id
			LEFT JOIN productphoto PP ON PP.productid = P.id AND PP.mainphoto = 1
			LEFT JOIN productstatuses PSS ON PSS.productid = P.id
			LEFT JOIN productstatus PS ON PS.idstatus = PSS.productstatusid
			LEFT JOIN productattributeset PAS ON PAS.productid = P.id
			LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
			LEFT JOIN productdeliverer PD ON PD.productid = P.id
			LEFT JOIN deliverertranslation DT ON PD.delivererid = DT.delivererid AND DT.languageid = :languageid
			LEFT JOIN vat V ON P.vatid = V.idvat
		');

        $datagrid->setGroupBy('
			P.id
		');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getProductForAjax' => [$this, 'getData'],
            'doDeleteProduct'   => [$this, 'delete']
        ]);
    }
}