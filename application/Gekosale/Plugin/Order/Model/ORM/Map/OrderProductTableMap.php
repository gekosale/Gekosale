<?php

namespace Gekosale\Plugin\Order\Model\ORM\Map;

use Gekosale\Plugin\Order\Model\ORM\OrderProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProductQuery;
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
 * This class defines the structure of the 'order_product' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class OrderProductTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Order.Model.ORM.Map.OrderProductTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'order_product';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderProduct';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Order.Model.ORM.OrderProduct';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 15;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 15;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'order_product.ID';

    /**
     * the column name for the NAME field
     */
    const COL_NAME = 'order_product.NAME';

    /**
     * the column name for the PRICE field
     */
    const COL_PRICE = 'order_product.PRICE';

    /**
     * the column name for the QUANTITY field
     */
    const COL_QUANTITY = 'order_product.QUANTITY';

    /**
     * the column name for the QUANTITY_PRICE field
     */
    const COL_QUANTITY_PRICE = 'order_product.QUANTITY_PRICE';

    /**
     * the column name for the ORDER_ID field
     */
    const COL_ORDER_ID = 'order_product.ORDER_ID';

    /**
     * the column name for the PRODUCT_ID field
     */
    const COL_PRODUCT_ID = 'order_product.PRODUCT_ID';

    /**
     * the column name for the PRODUCT_ATTRIBUTE_ID field
     */
    const COL_PRODUCT_ATTRIBUTE_ID = 'order_product.PRODUCT_ATTRIBUTE_ID';

    /**
     * the column name for the VARIANT field
     */
    const COL_VARIANT = 'order_product.VARIANT';

    /**
     * the column name for the VAT field
     */
    const COL_VAT = 'order_product.VAT';

    /**
     * the column name for the PRICE_NETTO field
     */
    const COL_PRICE_NETTO = 'order_product.PRICE_NETTO';

    /**
     * the column name for the DISCOUNT_PRICE field
     */
    const COL_DISCOUNT_PRICE = 'order_product.DISCOUNT_PRICE';

    /**
     * the column name for the DISCOUNT_PRICE_NETTO field
     */
    const COL_DISCOUNT_PRICE_NETTO = 'order_product.DISCOUNT_PRICE_NETTO';

    /**
     * the column name for the EAN field
     */
    const COL_EAN = 'order_product.EAN';

    /**
     * the column name for the PHOTO_ID field
     */
    const COL_PHOTO_ID = 'order_product.PHOTO_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'Price', 'Quantity', 'QuantityPrice', 'OrderId', 'ProductId', 'ProductAttributeId', 'Variant', 'Vat', 'PriceNetto', 'DiscountPrice', 'DiscountPriceNetto', 'Ean', 'PhotoId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'price', 'quantity', 'quantityPrice', 'orderId', 'productId', 'productAttributeId', 'variant', 'vat', 'priceNetto', 'discountPrice', 'discountPriceNetto', 'ean', 'photoId', ),
        self::TYPE_COLNAME       => array(OrderProductTableMap::COL_ID, OrderProductTableMap::COL_NAME, OrderProductTableMap::COL_PRICE, OrderProductTableMap::COL_QUANTITY, OrderProductTableMap::COL_QUANTITY_PRICE, OrderProductTableMap::COL_ORDER_ID, OrderProductTableMap::COL_PRODUCT_ID, OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, OrderProductTableMap::COL_VARIANT, OrderProductTableMap::COL_VAT, OrderProductTableMap::COL_PRICE_NETTO, OrderProductTableMap::COL_DISCOUNT_PRICE, OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO, OrderProductTableMap::COL_EAN, OrderProductTableMap::COL_PHOTO_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_NAME', 'COL_PRICE', 'COL_QUANTITY', 'COL_QUANTITY_PRICE', 'COL_ORDER_ID', 'COL_PRODUCT_ID', 'COL_PRODUCT_ATTRIBUTE_ID', 'COL_VARIANT', 'COL_VAT', 'COL_PRICE_NETTO', 'COL_DISCOUNT_PRICE', 'COL_DISCOUNT_PRICE_NETTO', 'COL_EAN', 'COL_PHOTO_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'price', 'quantity', 'quantity_price', 'order_id', 'product_id', 'product_attribute_id', 'variant', 'vat', 'price_netto', 'discount_price', 'discount_price_netto', 'ean', 'photo_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'Price' => 2, 'Quantity' => 3, 'QuantityPrice' => 4, 'OrderId' => 5, 'ProductId' => 6, 'ProductAttributeId' => 7, 'Variant' => 8, 'Vat' => 9, 'PriceNetto' => 10, 'DiscountPrice' => 11, 'DiscountPriceNetto' => 12, 'Ean' => 13, 'PhotoId' => 14, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'price' => 2, 'quantity' => 3, 'quantityPrice' => 4, 'orderId' => 5, 'productId' => 6, 'productAttributeId' => 7, 'variant' => 8, 'vat' => 9, 'priceNetto' => 10, 'discountPrice' => 11, 'discountPriceNetto' => 12, 'ean' => 13, 'photoId' => 14, ),
        self::TYPE_COLNAME       => array(OrderProductTableMap::COL_ID => 0, OrderProductTableMap::COL_NAME => 1, OrderProductTableMap::COL_PRICE => 2, OrderProductTableMap::COL_QUANTITY => 3, OrderProductTableMap::COL_QUANTITY_PRICE => 4, OrderProductTableMap::COL_ORDER_ID => 5, OrderProductTableMap::COL_PRODUCT_ID => 6, OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID => 7, OrderProductTableMap::COL_VARIANT => 8, OrderProductTableMap::COL_VAT => 9, OrderProductTableMap::COL_PRICE_NETTO => 10, OrderProductTableMap::COL_DISCOUNT_PRICE => 11, OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO => 12, OrderProductTableMap::COL_EAN => 13, OrderProductTableMap::COL_PHOTO_ID => 14, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_NAME' => 1, 'COL_PRICE' => 2, 'COL_QUANTITY' => 3, 'COL_QUANTITY_PRICE' => 4, 'COL_ORDER_ID' => 5, 'COL_PRODUCT_ID' => 6, 'COL_PRODUCT_ATTRIBUTE_ID' => 7, 'COL_VARIANT' => 8, 'COL_VAT' => 9, 'COL_PRICE_NETTO' => 10, 'COL_DISCOUNT_PRICE' => 11, 'COL_DISCOUNT_PRICE_NETTO' => 12, 'COL_EAN' => 13, 'COL_PHOTO_ID' => 14, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'price' => 2, 'quantity' => 3, 'quantity_price' => 4, 'order_id' => 5, 'product_id' => 6, 'product_attribute_id' => 7, 'variant' => 8, 'vat' => 9, 'price_netto' => 10, 'discount_price' => 11, 'discount_price_netto' => 12, 'ean' => 13, 'photo_id' => 14, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
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
        $this->setName('order_product');
        $this->setPhpName('OrderProduct');
        $this->setClassName('\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderProduct');
        $this->setPackage('Gekosale.Plugin.Order.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 255, null);
        $this->addColumn('PRICE', 'Price', 'DECIMAL', true, 16, null);
        $this->addColumn('QUANTITY', 'Quantity', 'DECIMAL', true, 16, null);
        $this->addColumn('QUANTITY_PRICE', 'QuantityPrice', 'DECIMAL', true, 16, null);
        $this->addForeignKey('ORDER_ID', 'OrderId', 'INTEGER', 'order', 'ID', true, 10, null);
        $this->addForeignKey('PRODUCT_ID', 'ProductId', 'INTEGER', 'product', 'ID', false, 10, null);
        $this->addColumn('PRODUCT_ATTRIBUTE_ID', 'ProductAttributeId', 'INTEGER', false, 10, null);
        $this->addColumn('VARIANT', 'Variant', 'VARCHAR', false, 255, null);
        $this->addColumn('VAT', 'Vat', 'DECIMAL', true, 16, null);
        $this->addColumn('PRICE_NETTO', 'PriceNetto', 'DECIMAL', true, 16, null);
        $this->addColumn('DISCOUNT_PRICE', 'DiscountPrice', 'DECIMAL', false, 16, null);
        $this->addColumn('DISCOUNT_PRICE_NETTO', 'DiscountPriceNetto', 'DECIMAL', false, 16, null);
        $this->addColumn('EAN', 'Ean', 'VARCHAR', false, 128, null);
        $this->addColumn('PHOTO_ID', 'PhotoId', 'INTEGER', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Order', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Order', RelationMap::MANY_TO_ONE, array('order_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Product', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product', RelationMap::MANY_TO_ONE, array('product_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('OrderProductAttribute', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderProductAttribute', RelationMap::ONE_TO_MANY, array('id' => 'order_product_id', ), 'CASCADE', null, 'OrderProductAttributes');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to order_product     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                OrderProductAttributeTableMap::clearInstancePool();
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
        return $withPrefix ? OrderProductTableMap::CLASS_DEFAULT : OrderProductTableMap::OM_CLASS;
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
     * @return array (OrderProduct object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = OrderProductTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = OrderProductTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + OrderProductTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrderProductTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            OrderProductTableMap::addInstanceToPool($obj, $key);
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
            $key = OrderProductTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = OrderProductTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrderProductTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(OrderProductTableMap::COL_ID);
            $criteria->addSelectColumn(OrderProductTableMap::COL_NAME);
            $criteria->addSelectColumn(OrderProductTableMap::COL_PRICE);
            $criteria->addSelectColumn(OrderProductTableMap::COL_QUANTITY);
            $criteria->addSelectColumn(OrderProductTableMap::COL_QUANTITY_PRICE);
            $criteria->addSelectColumn(OrderProductTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(OrderProductTableMap::COL_PRODUCT_ID);
            $criteria->addSelectColumn(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID);
            $criteria->addSelectColumn(OrderProductTableMap::COL_VARIANT);
            $criteria->addSelectColumn(OrderProductTableMap::COL_VAT);
            $criteria->addSelectColumn(OrderProductTableMap::COL_PRICE_NETTO);
            $criteria->addSelectColumn(OrderProductTableMap::COL_DISCOUNT_PRICE);
            $criteria->addSelectColumn(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO);
            $criteria->addSelectColumn(OrderProductTableMap::COL_EAN);
            $criteria->addSelectColumn(OrderProductTableMap::COL_PHOTO_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.PRICE');
            $criteria->addSelectColumn($alias . '.QUANTITY');
            $criteria->addSelectColumn($alias . '.QUANTITY_PRICE');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.PRODUCT_ATTRIBUTE_ID');
            $criteria->addSelectColumn($alias . '.VARIANT');
            $criteria->addSelectColumn($alias . '.VAT');
            $criteria->addSelectColumn($alias . '.PRICE_NETTO');
            $criteria->addSelectColumn($alias . '.DISCOUNT_PRICE');
            $criteria->addSelectColumn($alias . '.DISCOUNT_PRICE_NETTO');
            $criteria->addSelectColumn($alias . '.EAN');
            $criteria->addSelectColumn($alias . '.PHOTO_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(OrderProductTableMap::DATABASE_NAME)->getTable(OrderProductTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(OrderProductTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(OrderProductTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new OrderProductTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a OrderProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or OrderProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Order\Model\ORM\OrderProduct) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrderProductTableMap::DATABASE_NAME);
            $criteria->add(OrderProductTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = OrderProductQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { OrderProductTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { OrderProductTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the order_product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return OrderProductQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a OrderProduct or Criteria object.
     *
     * @param mixed               $criteria Criteria or OrderProduct object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from OrderProduct object
        }

        if ($criteria->containsKey(OrderProductTableMap::COL_ID) && $criteria->keyContainsValue(OrderProductTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.OrderProductTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = OrderProductQuery::create()->mergeWith($criteria);

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

} // OrderProductTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
OrderProductTableMap::buildTableMap();
