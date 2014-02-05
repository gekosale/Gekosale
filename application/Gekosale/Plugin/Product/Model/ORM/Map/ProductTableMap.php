<?php

namespace Gekosale\Plugin\Product\Model\ORM\Map;

use Gekosale\Plugin\Attribute\Model\ORM\Map\ProductAttributeTableMap;
use Gekosale\Plugin\Crosssell\Model\ORM\Map\CrosssellTableMap;
use Gekosale\Plugin\Deliverer\Model\ORM\Map\DelivererProductTableMap;
use Gekosale\Plugin\MissingCart\Model\ORM\Map\MissingCartProductTableMap;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderProductTableMap;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\Map\ProductGroupPriceTableMap;
use Gekosale\Plugin\ProductNew\Model\ORM\Map\ProductNewTableMap;
use Gekosale\Plugin\Product\Model\ORM\Product;
use Gekosale\Plugin\Product\Model\ORM\ProductQuery;
use Gekosale\Plugin\Similar\Model\ORM\Map\SimilarTableMap;
use Gekosale\Plugin\TechnicalData\Model\ORM\Map\ProductTechnicalDataGroupTableMap;
use Gekosale\Plugin\Upsell\Model\ORM\Map\UpsellTableMap;
use Gekosale\Plugin\Wishlist\Model\ORM\Map\WishlistTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'product' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ProductTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Product.Model.ORM.Map.ProductTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'product';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Product.Model.ORM.Product';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 30;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 30;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'product.ID';

    /**
     * the column name for the DELIVELER_CODE field
     */
    const COL_DELIVELER_CODE = 'product.DELIVELER_CODE';

    /**
     * the column name for the EAN field
     */
    const COL_EAN = 'product.EAN';

    /**
     * the column name for the BARCODE field
     */
    const COL_BARCODE = 'product.BARCODE';

    /**
     * the column name for the BUY_PRICE field
     */
    const COL_BUY_PRICE = 'product.BUY_PRICE';

    /**
     * the column name for the SELL_PRICE field
     */
    const COL_SELL_PRICE = 'product.SELL_PRICE';

    /**
     * the column name for the PRODUCER_ID field
     */
    const COL_PRODUCER_ID = 'product.PRODUCER_ID';

    /**
     * the column name for the VAT_ID field
     */
    const COL_VAT_ID = 'product.VAT_ID';

    /**
     * the column name for the STOCK field
     */
    const COL_STOCK = 'product.STOCK';

    /**
     * the column name for the ADD_DATE field
     */
    const COL_ADD_DATE = 'product.ADD_DATE';

    /**
     * the column name for the WEIGHT field
     */
    const COL_WEIGHT = 'product.WEIGHT';

    /**
     * the column name for the BUY_CURRENCY_ID field
     */
    const COL_BUY_CURRENCY_ID = 'product.BUY_CURRENCY_ID';

    /**
     * the column name for the SELL_CURRENCY_ID field
     */
    const COL_SELL_CURRENCY_ID = 'product.SELL_CURRENCY_ID';

    /**
     * the column name for the TECHNICAL_DATA_SET_ID field
     */
    const COL_TECHNICAL_DATA_SET_ID = 'product.TECHNICAL_DATA_SET_ID';

    /**
     * the column name for the TRACK_STOCK field
     */
    const COL_TRACK_STOCK = 'product.TRACK_STOCK';

    /**
     * the column name for the ENABLE field
     */
    const COL_ENABLE = 'product.ENABLE';

    /**
     * the column name for the PROMOTION field
     */
    const COL_PROMOTION = 'product.PROMOTION';

    /**
     * the column name for the DISCOUNT_PRICE field
     */
    const COL_DISCOUNT_PRICE = 'product.DISCOUNT_PRICE';

    /**
     * the column name for the PROMOTION_START field
     */
    const COL_PROMOTION_START = 'product.PROMOTION_START';

    /**
     * the column name for the PROMOTION_END field
     */
    const COL_PROMOTION_END = 'product.PROMOTION_END';

    /**
     * the column name for the SHOPED field
     */
    const COL_SHOPED = 'product.SHOPED';

    /**
     * the column name for the WIDTH field
     */
    const COL_WIDTH = 'product.WIDTH';

    /**
     * the column name for the HEIGHT field
     */
    const COL_HEIGHT = 'product.HEIGHT';

    /**
     * the column name for the DEEPTH field
     */
    const COL_DEEPTH = 'product.DEEPTH';

    /**
     * the column name for the UNIT_MEASURE_ID field
     */
    const COL_UNIT_MEASURE_ID = 'product.UNIT_MEASURE_ID';

    /**
     * the column name for the DISABLE_AT_STOCK field
     */
    const COL_DISABLE_AT_STOCK = 'product.DISABLE_AT_STOCK';

    /**
     * the column name for the AVAILABILITY_ID field
     */
    const COL_AVAILABILITY_ID = 'product.AVAILABILITY_ID';

    /**
     * the column name for the HIERARCHY field
     */
    const COL_HIERARCHY = 'product.HIERARCHY';

    /**
     * the column name for the PACKAGE_SIZE field
     */
    const COL_PACKAGE_SIZE = 'product.PACKAGE_SIZE';

    /**
     * the column name for the DISABLE_AT_STOCK_ENABLED field
     */
    const COL_DISABLE_AT_STOCK_ENABLED = 'product.DISABLE_AT_STOCK_ENABLED';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'DelivelerCode', 'Ean', 'Barcode', 'BuyPrice', 'SellPrice', 'ProducerId', 'VatId', 'Stock', 'AddDate', 'Weight', 'BuyCurrencyId', 'SellCurrencyId', 'TechnicalDataSetId', 'TrackStock', 'Enable', 'Promotion', 'DiscountPrice', 'PromotionStart', 'PromotionEnd', 'Shoped', 'Width', 'Height', 'Deepth', 'UnitMeasureId', 'DisableAtStock', 'AvailabilityId', 'Hierarchy', 'PackageSize', 'DisableAtStockEnabled', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'delivelerCode', 'ean', 'barcode', 'buyPrice', 'sellPrice', 'producerId', 'vatId', 'stock', 'addDate', 'weight', 'buyCurrencyId', 'sellCurrencyId', 'technicalDataSetId', 'trackStock', 'enable', 'promotion', 'discountPrice', 'promotionStart', 'promotionEnd', 'shoped', 'width', 'height', 'deepth', 'unitMeasureId', 'disableAtStock', 'availabilityId', 'hierarchy', 'packageSize', 'disableAtStockEnabled', ),
        self::TYPE_COLNAME       => array(ProductTableMap::COL_ID, ProductTableMap::COL_DELIVELER_CODE, ProductTableMap::COL_EAN, ProductTableMap::COL_BARCODE, ProductTableMap::COL_BUY_PRICE, ProductTableMap::COL_SELL_PRICE, ProductTableMap::COL_PRODUCER_ID, ProductTableMap::COL_VAT_ID, ProductTableMap::COL_STOCK, ProductTableMap::COL_ADD_DATE, ProductTableMap::COL_WEIGHT, ProductTableMap::COL_BUY_CURRENCY_ID, ProductTableMap::COL_SELL_CURRENCY_ID, ProductTableMap::COL_TECHNICAL_DATA_SET_ID, ProductTableMap::COL_TRACK_STOCK, ProductTableMap::COL_ENABLE, ProductTableMap::COL_PROMOTION, ProductTableMap::COL_DISCOUNT_PRICE, ProductTableMap::COL_PROMOTION_START, ProductTableMap::COL_PROMOTION_END, ProductTableMap::COL_SHOPED, ProductTableMap::COL_WIDTH, ProductTableMap::COL_HEIGHT, ProductTableMap::COL_DEEPTH, ProductTableMap::COL_UNIT_MEASURE_ID, ProductTableMap::COL_DISABLE_AT_STOCK, ProductTableMap::COL_AVAILABILITY_ID, ProductTableMap::COL_HIERARCHY, ProductTableMap::COL_PACKAGE_SIZE, ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_DELIVELER_CODE', 'COL_EAN', 'COL_BARCODE', 'COL_BUY_PRICE', 'COL_SELL_PRICE', 'COL_PRODUCER_ID', 'COL_VAT_ID', 'COL_STOCK', 'COL_ADD_DATE', 'COL_WEIGHT', 'COL_BUY_CURRENCY_ID', 'COL_SELL_CURRENCY_ID', 'COL_TECHNICAL_DATA_SET_ID', 'COL_TRACK_STOCK', 'COL_ENABLE', 'COL_PROMOTION', 'COL_DISCOUNT_PRICE', 'COL_PROMOTION_START', 'COL_PROMOTION_END', 'COL_SHOPED', 'COL_WIDTH', 'COL_HEIGHT', 'COL_DEEPTH', 'COL_UNIT_MEASURE_ID', 'COL_DISABLE_AT_STOCK', 'COL_AVAILABILITY_ID', 'COL_HIERARCHY', 'COL_PACKAGE_SIZE', 'COL_DISABLE_AT_STOCK_ENABLED', ),
        self::TYPE_FIELDNAME     => array('id', 'deliveler_code', 'ean', 'barcode', 'buy_price', 'sell_price', 'producer_id', 'vat_id', 'stock', 'add_date', 'weight', 'buy_currency_id', 'sell_currency_id', 'technical_data_set_id', 'track_stock', 'enable', 'promotion', 'discount_price', 'promotion_start', 'promotion_end', 'shoped', 'width', 'height', 'deepth', 'unit_measure_id', 'disable_at_stock', 'availability_id', 'hierarchy', 'package_size', 'disable_at_stock_enabled', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'DelivelerCode' => 1, 'Ean' => 2, 'Barcode' => 3, 'BuyPrice' => 4, 'SellPrice' => 5, 'ProducerId' => 6, 'VatId' => 7, 'Stock' => 8, 'AddDate' => 9, 'Weight' => 10, 'BuyCurrencyId' => 11, 'SellCurrencyId' => 12, 'TechnicalDataSetId' => 13, 'TrackStock' => 14, 'Enable' => 15, 'Promotion' => 16, 'DiscountPrice' => 17, 'PromotionStart' => 18, 'PromotionEnd' => 19, 'Shoped' => 20, 'Width' => 21, 'Height' => 22, 'Deepth' => 23, 'UnitMeasureId' => 24, 'DisableAtStock' => 25, 'AvailabilityId' => 26, 'Hierarchy' => 27, 'PackageSize' => 28, 'DisableAtStockEnabled' => 29, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'delivelerCode' => 1, 'ean' => 2, 'barcode' => 3, 'buyPrice' => 4, 'sellPrice' => 5, 'producerId' => 6, 'vatId' => 7, 'stock' => 8, 'addDate' => 9, 'weight' => 10, 'buyCurrencyId' => 11, 'sellCurrencyId' => 12, 'technicalDataSetId' => 13, 'trackStock' => 14, 'enable' => 15, 'promotion' => 16, 'discountPrice' => 17, 'promotionStart' => 18, 'promotionEnd' => 19, 'shoped' => 20, 'width' => 21, 'height' => 22, 'deepth' => 23, 'unitMeasureId' => 24, 'disableAtStock' => 25, 'availabilityId' => 26, 'hierarchy' => 27, 'packageSize' => 28, 'disableAtStockEnabled' => 29, ),
        self::TYPE_COLNAME       => array(ProductTableMap::COL_ID => 0, ProductTableMap::COL_DELIVELER_CODE => 1, ProductTableMap::COL_EAN => 2, ProductTableMap::COL_BARCODE => 3, ProductTableMap::COL_BUY_PRICE => 4, ProductTableMap::COL_SELL_PRICE => 5, ProductTableMap::COL_PRODUCER_ID => 6, ProductTableMap::COL_VAT_ID => 7, ProductTableMap::COL_STOCK => 8, ProductTableMap::COL_ADD_DATE => 9, ProductTableMap::COL_WEIGHT => 10, ProductTableMap::COL_BUY_CURRENCY_ID => 11, ProductTableMap::COL_SELL_CURRENCY_ID => 12, ProductTableMap::COL_TECHNICAL_DATA_SET_ID => 13, ProductTableMap::COL_TRACK_STOCK => 14, ProductTableMap::COL_ENABLE => 15, ProductTableMap::COL_PROMOTION => 16, ProductTableMap::COL_DISCOUNT_PRICE => 17, ProductTableMap::COL_PROMOTION_START => 18, ProductTableMap::COL_PROMOTION_END => 19, ProductTableMap::COL_SHOPED => 20, ProductTableMap::COL_WIDTH => 21, ProductTableMap::COL_HEIGHT => 22, ProductTableMap::COL_DEEPTH => 23, ProductTableMap::COL_UNIT_MEASURE_ID => 24, ProductTableMap::COL_DISABLE_AT_STOCK => 25, ProductTableMap::COL_AVAILABILITY_ID => 26, ProductTableMap::COL_HIERARCHY => 27, ProductTableMap::COL_PACKAGE_SIZE => 28, ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED => 29, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_DELIVELER_CODE' => 1, 'COL_EAN' => 2, 'COL_BARCODE' => 3, 'COL_BUY_PRICE' => 4, 'COL_SELL_PRICE' => 5, 'COL_PRODUCER_ID' => 6, 'COL_VAT_ID' => 7, 'COL_STOCK' => 8, 'COL_ADD_DATE' => 9, 'COL_WEIGHT' => 10, 'COL_BUY_CURRENCY_ID' => 11, 'COL_SELL_CURRENCY_ID' => 12, 'COL_TECHNICAL_DATA_SET_ID' => 13, 'COL_TRACK_STOCK' => 14, 'COL_ENABLE' => 15, 'COL_PROMOTION' => 16, 'COL_DISCOUNT_PRICE' => 17, 'COL_PROMOTION_START' => 18, 'COL_PROMOTION_END' => 19, 'COL_SHOPED' => 20, 'COL_WIDTH' => 21, 'COL_HEIGHT' => 22, 'COL_DEEPTH' => 23, 'COL_UNIT_MEASURE_ID' => 24, 'COL_DISABLE_AT_STOCK' => 25, 'COL_AVAILABILITY_ID' => 26, 'COL_HIERARCHY' => 27, 'COL_PACKAGE_SIZE' => 28, 'COL_DISABLE_AT_STOCK_ENABLED' => 29, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'deliveler_code' => 1, 'ean' => 2, 'barcode' => 3, 'buy_price' => 4, 'sell_price' => 5, 'producer_id' => 6, 'vat_id' => 7, 'stock' => 8, 'add_date' => 9, 'weight' => 10, 'buy_currency_id' => 11, 'sell_currency_id' => 12, 'technical_data_set_id' => 13, 'track_stock' => 14, 'enable' => 15, 'promotion' => 16, 'discount_price' => 17, 'promotion_start' => 18, 'promotion_end' => 19, 'shoped' => 20, 'width' => 21, 'height' => 22, 'deepth' => 23, 'unit_measure_id' => 24, 'disable_at_stock' => 25, 'availability_id' => 26, 'hierarchy' => 27, 'package_size' => 28, 'disable_at_stock_enabled' => 29, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('product');
        $this->setPhpName('Product');
        $this->setClassName('\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product');
        $this->setPackage('Gekosale.Plugin.Product.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('DELIVELER_CODE', 'DelivelerCode', 'VARCHAR', false, 45, null);
        $this->addColumn('EAN', 'Ean', 'VARCHAR', false, 45, null);
        $this->addColumn('BARCODE', 'Barcode', 'VARCHAR', false, 32, null);
        $this->addColumn('BUY_PRICE', 'BuyPrice', 'DECIMAL', true, 16, 0);
        $this->addColumn('SELL_PRICE', 'SellPrice', 'DECIMAL', true, 16, 0);
        $this->addForeignKey('PRODUCER_ID', 'ProducerId', 'INTEGER', 'producer', 'ID', false, 10, null);
        $this->addForeignKey('VAT_ID', 'VatId', 'INTEGER', 'vat', 'ID', true, 10, null);
        $this->addColumn('STOCK', 'Stock', 'INTEGER', false, 10, null);
        $this->addColumn('ADD_DATE', 'AddDate', 'TIMESTAMP', true, null, 'CURRENT_TIMESTAMP');
        $this->addColumn('WEIGHT', 'Weight', 'DECIMAL', false, 16, null);
        $this->addForeignKey('BUY_CURRENCY_ID', 'BuyCurrencyId', 'INTEGER', 'currency', 'ID', false, 10, null);
        $this->addForeignKey('SELL_CURRENCY_ID', 'SellCurrencyId', 'INTEGER', 'currency', 'ID', false, 10, null);
        $this->addForeignKey('TECHNICAL_DATA_SET_ID', 'TechnicalDataSetId', 'INTEGER', 'technical_data_set', 'ID', false, 10, null);
        $this->addColumn('TRACK_STOCK', 'TrackStock', 'INTEGER', false, 10, 1);
        $this->addColumn('ENABLE', 'Enable', 'INTEGER', true, 10, 1);
        $this->addColumn('PROMOTION', 'Promotion', 'INTEGER', false, 10, 0);
        $this->addColumn('DISCOUNT_PRICE', 'DiscountPrice', 'DECIMAL', false, 15, 0);
        $this->addColumn('PROMOTION_START', 'PromotionStart', 'DATE', false, null, null);
        $this->addColumn('PROMOTION_END', 'PromotionEnd', 'DATE', false, null, null);
        $this->addColumn('SHOPED', 'Shoped', 'INTEGER', false, null, 0);
        $this->addColumn('WIDTH', 'Width', 'DECIMAL', false, 15, null);
        $this->addColumn('HEIGHT', 'Height', 'DECIMAL', false, 15, null);
        $this->addColumn('DEEPTH', 'Deepth', 'DECIMAL', false, 15, null);
        $this->addForeignKey('UNIT_MEASURE_ID', 'UnitMeasureId', 'INTEGER', 'unit_measure', 'ID', false, null, null);
        $this->addColumn('DISABLE_AT_STOCK', 'DisableAtStock', 'INTEGER', false, null, 0);
        $this->addForeignKey('AVAILABILITY_ID', 'AvailabilityId', 'INTEGER', 'availability', 'ID', false, null, null);
        $this->addColumn('HIERARCHY', 'Hierarchy', 'INTEGER', true, null, 0);
        $this->addColumn('PACKAGE_SIZE', 'PackageSize', 'DECIMAL', true, 15, 1);
        $this->addColumn('DISABLE_AT_STOCK_ENABLED', 'DisableAtStockEnabled', 'INTEGER', true, null, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Availability', '\\Gekosale\\Plugin\\Availability\\Model\\ORM\\Availability', RelationMap::MANY_TO_ONE, array('availability_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CurrencyRelatedByBuyCurrencyId', '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Currency', RelationMap::MANY_TO_ONE, array('buy_currency_id' => 'id', ), null, null);
        $this->addRelation('Producer', '\\Gekosale\\Plugin\\Producer\\Model\\ORM\\Producer', RelationMap::MANY_TO_ONE, array('producer_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CurrencyRelatedBySellCurrencyId', '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Currency', RelationMap::MANY_TO_ONE, array('sell_currency_id' => 'id', ), null, null);
        $this->addRelation('UnitMeasure', '\\Gekosale\\Plugin\\UnitMeasure\\Model\\ORM\\UnitMeasure', RelationMap::MANY_TO_ONE, array('unit_measure_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('Vat', '\\Gekosale\\Plugin\\Vat\\Model\\ORM\\Vat', RelationMap::MANY_TO_ONE, array('vat_id' => 'id', ), null, null);
        $this->addRelation('TechnicalDataSet', '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSet', RelationMap::MANY_TO_ONE, array('technical_data_set_id' => 'id', ), 'SET NULL', 'CASCADE');
        $this->addRelation('MissingCartProduct', '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCartProduct', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'MissingCartProducts');
        $this->addRelation('OrderProduct', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderProduct', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'SET NULL', null, 'OrderProducts');
        $this->addRelation('ProductAttribute', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttribute', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', 'CASCADE', 'ProductAttributes');
        $this->addRelation('ProductCategory', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\ProductCategory', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'ProductCategories');
        $this->addRelation('DelivererProduct', '\\Gekosale\\Plugin\\Deliverer\\Model\\ORM\\DelivererProduct', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', 'CASCADE', 'DelivererProducts');
        $this->addRelation('ProductFile', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\ProductFile', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', 'CASCADE', 'ProductFiles');
        $this->addRelation('ProductGroupPrice', '\\Gekosale\\Plugin\\ProductGroupPrice\\Model\\ORM\\ProductGroupPrice', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'ProductGroupPrices');
        $this->addRelation('ProductNew', '\\Gekosale\\Plugin\\ProductNew\\Model\\ORM\\ProductNew', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', 'CASCADE', 'ProductNews');
        $this->addRelation('ProductPhoto', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\ProductPhoto', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', 'CASCADE', 'ProductPhotos');
        $this->addRelation('CrosssellRelatedByProductId', '\\Gekosale\\Plugin\\Crosssell\\Model\\ORM\\Crosssell', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'CrosssellsRelatedByProductId');
        $this->addRelation('CrosssellRelatedByRelatedProductId', '\\Gekosale\\Plugin\\Crosssell\\Model\\ORM\\Crosssell', RelationMap::ONE_TO_MANY, array('id' => 'related_product_id', ), 'CASCADE', null, 'CrosssellsRelatedByRelatedProductId');
        $this->addRelation('SimilarRelatedByProductId', '\\Gekosale\\Plugin\\Similar\\Model\\ORM\\Similar', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'SimilarsRelatedByProductId');
        $this->addRelation('SimilarRelatedByRelatedProductId', '\\Gekosale\\Plugin\\Similar\\Model\\ORM\\Similar', RelationMap::ONE_TO_MANY, array('id' => 'related_product_id', ), 'CASCADE', null, 'SimilarsRelatedByRelatedProductId');
        $this->addRelation('UpsellRelatedByProductId', '\\Gekosale\\Plugin\\Upsell\\Model\\ORM\\Upsell', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'UpsellsRelatedByProductId');
        $this->addRelation('UpsellRelatedByRelatedProductId', '\\Gekosale\\Plugin\\Upsell\\Model\\ORM\\Upsell', RelationMap::ONE_TO_MANY, array('id' => 'related_product_id', ), 'CASCADE', null, 'UpsellsRelatedByRelatedProductId');
        $this->addRelation('ProductTechnicalDataGroup', '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\ProductTechnicalDataGroup', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', 'CASCADE', 'ProductTechnicalDataGroups');
        $this->addRelation('Wishlist', '\\Gekosale\\Plugin\\Wishlist\\Model\\ORM\\Wishlist', RelationMap::ONE_TO_MANY, array('id' => 'product_id', ), 'CASCADE', null, 'Wishlists');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to product     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                MissingCartProductTableMap::clearInstancePool();
                OrderProductTableMap::clearInstancePool();
                ProductAttributeTableMap::clearInstancePool();
                ProductCategoryTableMap::clearInstancePool();
                DelivererProductTableMap::clearInstancePool();
                ProductFileTableMap::clearInstancePool();
                ProductGroupPriceTableMap::clearInstancePool();
                ProductNewTableMap::clearInstancePool();
                ProductPhotoTableMap::clearInstancePool();
                CrosssellTableMap::clearInstancePool();
                SimilarTableMap::clearInstancePool();
                UpsellTableMap::clearInstancePool();
                ProductTechnicalDataGroupTableMap::clearInstancePool();
                WishlistTableMap::clearInstancePool();
            }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }
    
    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? ProductTableMap::CLASS_DEFAULT : ProductTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (Product object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ProductTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ProductTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ProductTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ProductTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ProductTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ProductTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ProductTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ProductTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ProductTableMap::COL_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_DELIVELER_CODE);
            $criteria->addSelectColumn(ProductTableMap::COL_EAN);
            $criteria->addSelectColumn(ProductTableMap::COL_BARCODE);
            $criteria->addSelectColumn(ProductTableMap::COL_BUY_PRICE);
            $criteria->addSelectColumn(ProductTableMap::COL_SELL_PRICE);
            $criteria->addSelectColumn(ProductTableMap::COL_PRODUCER_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_VAT_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_STOCK);
            $criteria->addSelectColumn(ProductTableMap::COL_ADD_DATE);
            $criteria->addSelectColumn(ProductTableMap::COL_WEIGHT);
            $criteria->addSelectColumn(ProductTableMap::COL_BUY_CURRENCY_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_SELL_CURRENCY_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_TECHNICAL_DATA_SET_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_TRACK_STOCK);
            $criteria->addSelectColumn(ProductTableMap::COL_ENABLE);
            $criteria->addSelectColumn(ProductTableMap::COL_PROMOTION);
            $criteria->addSelectColumn(ProductTableMap::COL_DISCOUNT_PRICE);
            $criteria->addSelectColumn(ProductTableMap::COL_PROMOTION_START);
            $criteria->addSelectColumn(ProductTableMap::COL_PROMOTION_END);
            $criteria->addSelectColumn(ProductTableMap::COL_SHOPED);
            $criteria->addSelectColumn(ProductTableMap::COL_WIDTH);
            $criteria->addSelectColumn(ProductTableMap::COL_HEIGHT);
            $criteria->addSelectColumn(ProductTableMap::COL_DEEPTH);
            $criteria->addSelectColumn(ProductTableMap::COL_UNIT_MEASURE_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_DISABLE_AT_STOCK);
            $criteria->addSelectColumn(ProductTableMap::COL_AVAILABILITY_ID);
            $criteria->addSelectColumn(ProductTableMap::COL_HIERARCHY);
            $criteria->addSelectColumn(ProductTableMap::COL_PACKAGE_SIZE);
            $criteria->addSelectColumn(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.DELIVELER_CODE');
            $criteria->addSelectColumn($alias . '.EAN');
            $criteria->addSelectColumn($alias . '.BARCODE');
            $criteria->addSelectColumn($alias . '.BUY_PRICE');
            $criteria->addSelectColumn($alias . '.SELL_PRICE');
            $criteria->addSelectColumn($alias . '.PRODUCER_ID');
            $criteria->addSelectColumn($alias . '.VAT_ID');
            $criteria->addSelectColumn($alias . '.STOCK');
            $criteria->addSelectColumn($alias . '.ADD_DATE');
            $criteria->addSelectColumn($alias . '.WEIGHT');
            $criteria->addSelectColumn($alias . '.BUY_CURRENCY_ID');
            $criteria->addSelectColumn($alias . '.SELL_CURRENCY_ID');
            $criteria->addSelectColumn($alias . '.TECHNICAL_DATA_SET_ID');
            $criteria->addSelectColumn($alias . '.TRACK_STOCK');
            $criteria->addSelectColumn($alias . '.ENABLE');
            $criteria->addSelectColumn($alias . '.PROMOTION');
            $criteria->addSelectColumn($alias . '.DISCOUNT_PRICE');
            $criteria->addSelectColumn($alias . '.PROMOTION_START');
            $criteria->addSelectColumn($alias . '.PROMOTION_END');
            $criteria->addSelectColumn($alias . '.SHOPED');
            $criteria->addSelectColumn($alias . '.WIDTH');
            $criteria->addSelectColumn($alias . '.HEIGHT');
            $criteria->addSelectColumn($alias . '.DEEPTH');
            $criteria->addSelectColumn($alias . '.UNIT_MEASURE_ID');
            $criteria->addSelectColumn($alias . '.DISABLE_AT_STOCK');
            $criteria->addSelectColumn($alias . '.AVAILABILITY_ID');
            $criteria->addSelectColumn($alias . '.HIERARCHY');
            $criteria->addSelectColumn($alias . '.PACKAGE_SIZE');
            $criteria->addSelectColumn($alias . '.DISABLE_AT_STOCK_ENABLED');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(ProductTableMap::DATABASE_NAME)->getTable(ProductTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ProductTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ProductTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ProductTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Product or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Product object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Product\Model\ORM\Product) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ProductTableMap::DATABASE_NAME);
            $criteria->add(ProductTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ProductQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ProductTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ProductTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ProductQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Product or Criteria object.
     *
     * @param mixed               $criteria Criteria or Product object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Product object
        }

        if ($criteria->containsKey(ProductTableMap::COL_ID) && $criteria->keyContainsValue(ProductTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ProductTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ProductQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // ProductTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ProductTableMap::buildTableMap();
