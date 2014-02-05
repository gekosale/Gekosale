<?php

namespace Gekosale\Plugin\ProductGroupPrice\Model\ORM\Map;

use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery;
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
 * This class defines the structure of the 'product_group_price' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ProductGroupPriceTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.ProductGroupPrice.Model.ORM.Map.ProductGroupPriceTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'product_group_price';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\ProductGroupPrice\\Model\\ORM\\ProductGroupPrice';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.ProductGroupPrice.Model.ORM.ProductGroupPrice';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 9;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 9;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'product_group_price.ID';

    /**
     * the column name for the CLIENT_GROUP_ID field
     */
    const COL_CLIENT_GROUP_ID = 'product_group_price.CLIENT_GROUP_ID';

    /**
     * the column name for the PRODUCT_ID field
     */
    const COL_PRODUCT_ID = 'product_group_price.PRODUCT_ID';

    /**
     * the column name for the GROUP_PRICE field
     */
    const COL_GROUP_PRICE = 'product_group_price.GROUP_PRICE';

    /**
     * the column name for the SELL_PRICE field
     */
    const COL_SELL_PRICE = 'product_group_price.SELL_PRICE';

    /**
     * the column name for the PROMOTION field
     */
    const COL_PROMOTION = 'product_group_price.PROMOTION';

    /**
     * the column name for the DISCOUNT_PRICE field
     */
    const COL_DISCOUNT_PRICE = 'product_group_price.DISCOUNT_PRICE';

    /**
     * the column name for the PROMOTION_START field
     */
    const COL_PROMOTION_START = 'product_group_price.PROMOTION_START';

    /**
     * the column name for the PROMOTION_END field
     */
    const COL_PROMOTION_END = 'product_group_price.PROMOTION_END';

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
        self::TYPE_PHPNAME       => array('Id', 'ClientGroupId', 'ProductId', 'GroupPrice', 'SellPrice', 'Promotion', 'DiscountPrice', 'PromotionStart', 'PromotionEnd', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'clientGroupId', 'productId', 'groupPrice', 'sellPrice', 'promotion', 'discountPrice', 'promotionStart', 'promotionEnd', ),
        self::TYPE_COLNAME       => array(ProductGroupPriceTableMap::COL_ID, ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID, ProductGroupPriceTableMap::COL_PRODUCT_ID, ProductGroupPriceTableMap::COL_GROUP_PRICE, ProductGroupPriceTableMap::COL_SELL_PRICE, ProductGroupPriceTableMap::COL_PROMOTION, ProductGroupPriceTableMap::COL_DISCOUNT_PRICE, ProductGroupPriceTableMap::COL_PROMOTION_START, ProductGroupPriceTableMap::COL_PROMOTION_END, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_CLIENT_GROUP_ID', 'COL_PRODUCT_ID', 'COL_GROUP_PRICE', 'COL_SELL_PRICE', 'COL_PROMOTION', 'COL_DISCOUNT_PRICE', 'COL_PROMOTION_START', 'COL_PROMOTION_END', ),
        self::TYPE_FIELDNAME     => array('id', 'client_group_id', 'product_id', 'group_price', 'sell_price', 'promotion', 'discount_price', 'promotion_start', 'promotion_end', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'ClientGroupId' => 1, 'ProductId' => 2, 'GroupPrice' => 3, 'SellPrice' => 4, 'Promotion' => 5, 'DiscountPrice' => 6, 'PromotionStart' => 7, 'PromotionEnd' => 8, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'clientGroupId' => 1, 'productId' => 2, 'groupPrice' => 3, 'sellPrice' => 4, 'promotion' => 5, 'discountPrice' => 6, 'promotionStart' => 7, 'promotionEnd' => 8, ),
        self::TYPE_COLNAME       => array(ProductGroupPriceTableMap::COL_ID => 0, ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID => 1, ProductGroupPriceTableMap::COL_PRODUCT_ID => 2, ProductGroupPriceTableMap::COL_GROUP_PRICE => 3, ProductGroupPriceTableMap::COL_SELL_PRICE => 4, ProductGroupPriceTableMap::COL_PROMOTION => 5, ProductGroupPriceTableMap::COL_DISCOUNT_PRICE => 6, ProductGroupPriceTableMap::COL_PROMOTION_START => 7, ProductGroupPriceTableMap::COL_PROMOTION_END => 8, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_CLIENT_GROUP_ID' => 1, 'COL_PRODUCT_ID' => 2, 'COL_GROUP_PRICE' => 3, 'COL_SELL_PRICE' => 4, 'COL_PROMOTION' => 5, 'COL_DISCOUNT_PRICE' => 6, 'COL_PROMOTION_START' => 7, 'COL_PROMOTION_END' => 8, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'client_group_id' => 1, 'product_id' => 2, 'group_price' => 3, 'sell_price' => 4, 'promotion' => 5, 'discount_price' => 6, 'promotion_start' => 7, 'promotion_end' => 8, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
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
        $this->setName('product_group_price');
        $this->setPhpName('ProductGroupPrice');
        $this->setClassName('\\Gekosale\\Plugin\\ProductGroupPrice\\Model\\ORM\\ProductGroupPrice');
        $this->setPackage('Gekosale.Plugin.ProductGroupPrice.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('CLIENT_GROUP_ID', 'ClientGroupId', 'INTEGER', 'client_group', 'ID', true, 10, null);
        $this->addForeignKey('PRODUCT_ID', 'ProductId', 'INTEGER', 'product', 'ID', true, 10, null);
        $this->addColumn('GROUP_PRICE', 'GroupPrice', 'INTEGER', false, 10, 0);
        $this->addColumn('SELL_PRICE', 'SellPrice', 'DECIMAL', true, 15, null);
        $this->addColumn('PROMOTION', 'Promotion', 'INTEGER', false, 10, 0);
        $this->addColumn('DISCOUNT_PRICE', 'DiscountPrice', 'DECIMAL', false, 15, null);
        $this->addColumn('PROMOTION_START', 'PromotionStart', 'DATE', false, null, null);
        $this->addColumn('PROMOTION_END', 'PromotionEnd', 'DATE', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ClientGroup', '\\Gekosale\\Plugin\\ClientGroup\\Model\\ORM\\ClientGroup', RelationMap::MANY_TO_ONE, array('client_group_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Product', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product', RelationMap::MANY_TO_ONE, array('product_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

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
        return $withPrefix ? ProductGroupPriceTableMap::CLASS_DEFAULT : ProductGroupPriceTableMap::OM_CLASS;
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
     * @return array (ProductGroupPrice object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ProductGroupPriceTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ProductGroupPriceTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ProductGroupPriceTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ProductGroupPriceTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ProductGroupPriceTableMap::addInstanceToPool($obj, $key);
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
            $key = ProductGroupPriceTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ProductGroupPriceTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ProductGroupPriceTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_ID);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_PRODUCT_ID);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_GROUP_PRICE);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_SELL_PRICE);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_PROMOTION);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_DISCOUNT_PRICE);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_PROMOTION_START);
            $criteria->addSelectColumn(ProductGroupPriceTableMap::COL_PROMOTION_END);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CLIENT_GROUP_ID');
            $criteria->addSelectColumn($alias . '.PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.GROUP_PRICE');
            $criteria->addSelectColumn($alias . '.SELL_PRICE');
            $criteria->addSelectColumn($alias . '.PROMOTION');
            $criteria->addSelectColumn($alias . '.DISCOUNT_PRICE');
            $criteria->addSelectColumn($alias . '.PROMOTION_START');
            $criteria->addSelectColumn($alias . '.PROMOTION_END');
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
        return Propel::getServiceContainer()->getDatabaseMap(ProductGroupPriceTableMap::DATABASE_NAME)->getTable(ProductGroupPriceTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ProductGroupPriceTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ProductGroupPriceTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ProductGroupPriceTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a ProductGroupPrice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ProductGroupPrice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductGroupPriceTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ProductGroupPriceTableMap::DATABASE_NAME);
            $criteria->add(ProductGroupPriceTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ProductGroupPriceQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ProductGroupPriceTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ProductGroupPriceTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the product_group_price table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ProductGroupPriceQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a ProductGroupPrice or Criteria object.
     *
     * @param mixed               $criteria Criteria or ProductGroupPrice object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductGroupPriceTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from ProductGroupPrice object
        }

        if ($criteria->containsKey(ProductGroupPriceTableMap::COL_ID) && $criteria->keyContainsValue(ProductGroupPriceTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ProductGroupPriceTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ProductGroupPriceQuery::create()->mergeWith($criteria);

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

} // ProductGroupPriceTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ProductGroupPriceTableMap::buildTableMap();
