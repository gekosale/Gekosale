<?php

namespace Gekosale\Plugin\MissingCart\Model\ORM\Map;

use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery;
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
 * This class defines the structure of the 'missing_cart_product' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class MissingCartProductTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.MissingCart.Model.ORM.Map.MissingCartProductTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'missing_cart_product';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCartProduct';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.MissingCart.Model.ORM.MissingCartProduct';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 7;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 7;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'missing_cart_product.ID';

    /**
     * the column name for the MISSING_CART_ID field
     */
    const COL_MISSING_CART_ID = 'missing_cart_product.MISSING_CART_ID';

    /**
     * the column name for the PRODUCT_ID field
     */
    const COL_PRODUCT_ID = 'missing_cart_product.PRODUCT_ID';

    /**
     * the column name for the STOCK field
     */
    const COL_STOCK = 'missing_cart_product.STOCK';

    /**
     * the column name for the QUANTITY field
     */
    const COL_QUANTITY = 'missing_cart_product.QUANTITY';

    /**
     * the column name for the PRODUCT_ATTRIBUTE_ID field
     */
    const COL_PRODUCT_ATTRIBUTE_ID = 'missing_cart_product.PRODUCT_ATTRIBUTE_ID';

    /**
     * the column name for the SHOP_ID field
     */
    const COL_SHOP_ID = 'missing_cart_product.SHOP_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'MissingCartId', 'ProductId', 'Stock', 'Quantity', 'ProductAttributeId', 'ShopId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'missingCartId', 'productId', 'stock', 'quantity', 'productAttributeId', 'shopId', ),
        self::TYPE_COLNAME       => array(MissingCartProductTableMap::COL_ID, MissingCartProductTableMap::COL_MISSING_CART_ID, MissingCartProductTableMap::COL_PRODUCT_ID, MissingCartProductTableMap::COL_STOCK, MissingCartProductTableMap::COL_QUANTITY, MissingCartProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, MissingCartProductTableMap::COL_SHOP_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_MISSING_CART_ID', 'COL_PRODUCT_ID', 'COL_STOCK', 'COL_QUANTITY', 'COL_PRODUCT_ATTRIBUTE_ID', 'COL_SHOP_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'missing_cart_id', 'product_id', 'stock', 'quantity', 'product_attribute_id', 'shop_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'MissingCartId' => 1, 'ProductId' => 2, 'Stock' => 3, 'Quantity' => 4, 'ProductAttributeId' => 5, 'ShopId' => 6, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'missingCartId' => 1, 'productId' => 2, 'stock' => 3, 'quantity' => 4, 'productAttributeId' => 5, 'shopId' => 6, ),
        self::TYPE_COLNAME       => array(MissingCartProductTableMap::COL_ID => 0, MissingCartProductTableMap::COL_MISSING_CART_ID => 1, MissingCartProductTableMap::COL_PRODUCT_ID => 2, MissingCartProductTableMap::COL_STOCK => 3, MissingCartProductTableMap::COL_QUANTITY => 4, MissingCartProductTableMap::COL_PRODUCT_ATTRIBUTE_ID => 5, MissingCartProductTableMap::COL_SHOP_ID => 6, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_MISSING_CART_ID' => 1, 'COL_PRODUCT_ID' => 2, 'COL_STOCK' => 3, 'COL_QUANTITY' => 4, 'COL_PRODUCT_ATTRIBUTE_ID' => 5, 'COL_SHOP_ID' => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'missing_cart_id' => 1, 'product_id' => 2, 'stock' => 3, 'quantity' => 4, 'product_attribute_id' => 5, 'shop_id' => 6, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
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
        $this->setName('missing_cart_product');
        $this->setPhpName('MissingCartProduct');
        $this->setClassName('\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCartProduct');
        $this->setPackage('Gekosale.Plugin.MissingCart.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('MISSING_CART_ID', 'MissingCartId', 'INTEGER', 'missing_cart', 'ID', true, 10, null);
        $this->addForeignKey('PRODUCT_ID', 'ProductId', 'INTEGER', 'product', 'ID', true, 10, null);
        $this->addColumn('STOCK', 'Stock', 'INTEGER', true, 10, null);
        $this->addColumn('QUANTITY', 'Quantity', 'INTEGER', true, 10, null);
        $this->addColumn('PRODUCT_ATTRIBUTE_ID', 'ProductAttributeId', 'INTEGER', false, 10, null);
        $this->addForeignKey('SHOP_ID', 'ShopId', 'INTEGER', 'shop', 'ID', false, 10, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('MissingCart', '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCart', RelationMap::MANY_TO_ONE, array('missing_cart_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Shop', '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop', RelationMap::MANY_TO_ONE, array('shop_id' => 'id', ), 'CASCADE', null);
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
        return $withPrefix ? MissingCartProductTableMap::CLASS_DEFAULT : MissingCartProductTableMap::OM_CLASS;
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
     * @return array (MissingCartProduct object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = MissingCartProductTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = MissingCartProductTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + MissingCartProductTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = MissingCartProductTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            MissingCartProductTableMap::addInstanceToPool($obj, $key);
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
            $key = MissingCartProductTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = MissingCartProductTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                MissingCartProductTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_ID);
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_MISSING_CART_ID);
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_PRODUCT_ID);
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_STOCK);
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_QUANTITY);
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_PRODUCT_ATTRIBUTE_ID);
            $criteria->addSelectColumn(MissingCartProductTableMap::COL_SHOP_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.MISSING_CART_ID');
            $criteria->addSelectColumn($alias . '.PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.STOCK');
            $criteria->addSelectColumn($alias . '.QUANTITY');
            $criteria->addSelectColumn($alias . '.PRODUCT_ATTRIBUTE_ID');
            $criteria->addSelectColumn($alias . '.SHOP_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(MissingCartProductTableMap::DATABASE_NAME)->getTable(MissingCartProductTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(MissingCartProductTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(MissingCartProductTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new MissingCartProductTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a MissingCartProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or MissingCartProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(MissingCartProductTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(MissingCartProductTableMap::DATABASE_NAME);
            $criteria->add(MissingCartProductTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = MissingCartProductQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { MissingCartProductTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { MissingCartProductTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the missing_cart_product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return MissingCartProductQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a MissingCartProduct or Criteria object.
     *
     * @param mixed               $criteria Criteria or MissingCartProduct object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(MissingCartProductTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from MissingCartProduct object
        }

        if ($criteria->containsKey(MissingCartProductTableMap::COL_ID) && $criteria->keyContainsValue(MissingCartProductTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.MissingCartProductTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = MissingCartProductQuery::create()->mergeWith($criteria);

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

} // MissingCartProductTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
MissingCartProductTableMap::buildTableMap();
