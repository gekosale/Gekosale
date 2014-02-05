<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Map;

use Gekosale\Plugin\CartRule\Model\ORM\CartRule;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery;
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
 * This class defines the structure of the 'cart_rule' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CartRuleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.CartRule.Model.ORM.Map.CartRuleTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'cart_rule';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRule';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.CartRule.Model.ORM.CartRule';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'cart_rule.ID';

    /**
     * the column name for the HIERARCHY field
     */
    const COL_HIERARCHY = 'cart_rule.HIERARCHY';

    /**
     * the column name for the SUFFIX_TYPE_ID field
     */
    const COL_SUFFIX_TYPE_ID = 'cart_rule.SUFFIX_TYPE_ID';

    /**
     * the column name for the DISCOUNT field
     */
    const COL_DISCOUNT = 'cart_rule.DISCOUNT';

    /**
     * the column name for the FREE_SHIPPING field
     */
    const COL_FREE_SHIPPING = 'cart_rule.FREE_SHIPPING';

    /**
     * the column name for the DATE_FROM field
     */
    const COL_DATE_FROM = 'cart_rule.DATE_FROM';

    /**
     * the column name for the DATE_TO field
     */
    const COL_DATE_TO = 'cart_rule.DATE_TO';

    /**
     * the column name for the DISCOUNT_FOR_ALL field
     */
    const COL_DISCOUNT_FOR_ALL = 'cart_rule.DISCOUNT_FOR_ALL';

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
        self::TYPE_PHPNAME       => array('Id', 'Hierarchy', 'SuffixTypeId', 'Discount', 'FreeShipping', 'DateFrom', 'DateTo', 'DiscountForAll', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'hierarchy', 'suffixTypeId', 'discount', 'freeShipping', 'dateFrom', 'dateTo', 'discountForAll', ),
        self::TYPE_COLNAME       => array(CartRuleTableMap::COL_ID, CartRuleTableMap::COL_HIERARCHY, CartRuleTableMap::COL_SUFFIX_TYPE_ID, CartRuleTableMap::COL_DISCOUNT, CartRuleTableMap::COL_FREE_SHIPPING, CartRuleTableMap::COL_DATE_FROM, CartRuleTableMap::COL_DATE_TO, CartRuleTableMap::COL_DISCOUNT_FOR_ALL, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_HIERARCHY', 'COL_SUFFIX_TYPE_ID', 'COL_DISCOUNT', 'COL_FREE_SHIPPING', 'COL_DATE_FROM', 'COL_DATE_TO', 'COL_DISCOUNT_FOR_ALL', ),
        self::TYPE_FIELDNAME     => array('id', 'hierarchy', 'suffix_type_id', 'discount', 'free_shipping', 'date_from', 'date_to', 'discount_for_all', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Hierarchy' => 1, 'SuffixTypeId' => 2, 'Discount' => 3, 'FreeShipping' => 4, 'DateFrom' => 5, 'DateTo' => 6, 'DiscountForAll' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'hierarchy' => 1, 'suffixTypeId' => 2, 'discount' => 3, 'freeShipping' => 4, 'dateFrom' => 5, 'dateTo' => 6, 'discountForAll' => 7, ),
        self::TYPE_COLNAME       => array(CartRuleTableMap::COL_ID => 0, CartRuleTableMap::COL_HIERARCHY => 1, CartRuleTableMap::COL_SUFFIX_TYPE_ID => 2, CartRuleTableMap::COL_DISCOUNT => 3, CartRuleTableMap::COL_FREE_SHIPPING => 4, CartRuleTableMap::COL_DATE_FROM => 5, CartRuleTableMap::COL_DATE_TO => 6, CartRuleTableMap::COL_DISCOUNT_FOR_ALL => 7, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_HIERARCHY' => 1, 'COL_SUFFIX_TYPE_ID' => 2, 'COL_DISCOUNT' => 3, 'COL_FREE_SHIPPING' => 4, 'COL_DATE_FROM' => 5, 'COL_DATE_TO' => 6, 'COL_DISCOUNT_FOR_ALL' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'hierarchy' => 1, 'suffix_type_id' => 2, 'discount' => 3, 'free_shipping' => 4, 'date_from' => 5, 'date_to' => 6, 'discount_for_all' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
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
        $this->setName('cart_rule');
        $this->setPhpName('CartRule');
        $this->setClassName('\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRule');
        $this->setPackage('Gekosale.Plugin.CartRule.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('HIERARCHY', 'Hierarchy', 'TINYINT', false, 3, 0);
        $this->addForeignKey('SUFFIX_TYPE_ID', 'SuffixTypeId', 'INTEGER', 'suffix_type', 'ID', false, 10, null);
        $this->addColumn('DISCOUNT', 'Discount', 'DECIMAL', true, 16, 0);
        $this->addColumn('FREE_SHIPPING', 'FreeShipping', 'INTEGER', true, null, 0);
        $this->addColumn('DATE_FROM', 'DateFrom', 'DATE', false, null, null);
        $this->addColumn('DATE_TO', 'DateTo', 'DATE', false, null, null);
        $this->addColumn('DISCOUNT_FOR_ALL', 'DiscountForAll', 'TINYINT', true, 3, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('SuffixType', '\\Gekosale\\Plugin\\SuffixType\\Model\\ORM\\SuffixType', RelationMap::MANY_TO_ONE, array('suffix_type_id' => 'id', ), null, null);
        $this->addRelation('CartRuleClientGroup', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleClientGroup', RelationMap::ONE_TO_MANY, array('id' => 'cart_rule_id', ), 'CASCADE', null, 'CartRuleClientGroups');
        $this->addRelation('CartRuleRule', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleRule', RelationMap::ONE_TO_MANY, array('id' => 'cart_rule_id', ), 'CASCADE', null, 'CartRuleRules');
        $this->addRelation('CartRuleShop', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleShop', RelationMap::ONE_TO_MANY, array('id' => 'cart_rule_id', ), 'CASCADE', null, 'CartRuleShops');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to cart_rule     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                CartRuleClientGroupTableMap::clearInstancePool();
                CartRuleRuleTableMap::clearInstancePool();
                CartRuleShopTableMap::clearInstancePool();
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
        return $withPrefix ? CartRuleTableMap::CLASS_DEFAULT : CartRuleTableMap::OM_CLASS;
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
     * @return array (CartRule object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CartRuleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CartRuleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CartRuleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CartRuleTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CartRuleTableMap::addInstanceToPool($obj, $key);
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
            $key = CartRuleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CartRuleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CartRuleTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CartRuleTableMap::COL_ID);
            $criteria->addSelectColumn(CartRuleTableMap::COL_HIERARCHY);
            $criteria->addSelectColumn(CartRuleTableMap::COL_SUFFIX_TYPE_ID);
            $criteria->addSelectColumn(CartRuleTableMap::COL_DISCOUNT);
            $criteria->addSelectColumn(CartRuleTableMap::COL_FREE_SHIPPING);
            $criteria->addSelectColumn(CartRuleTableMap::COL_DATE_FROM);
            $criteria->addSelectColumn(CartRuleTableMap::COL_DATE_TO);
            $criteria->addSelectColumn(CartRuleTableMap::COL_DISCOUNT_FOR_ALL);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.HIERARCHY');
            $criteria->addSelectColumn($alias . '.SUFFIX_TYPE_ID');
            $criteria->addSelectColumn($alias . '.DISCOUNT');
            $criteria->addSelectColumn($alias . '.FREE_SHIPPING');
            $criteria->addSelectColumn($alias . '.DATE_FROM');
            $criteria->addSelectColumn($alias . '.DATE_TO');
            $criteria->addSelectColumn($alias . '.DISCOUNT_FOR_ALL');
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
        return Propel::getServiceContainer()->getDatabaseMap(CartRuleTableMap::DATABASE_NAME)->getTable(CartRuleTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CartRuleTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CartRuleTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CartRuleTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a CartRule or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CartRule object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRule) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CartRuleTableMap::DATABASE_NAME);
            $criteria->add(CartRuleTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CartRuleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CartRuleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CartRuleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the cart_rule table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CartRuleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CartRule or Criteria object.
     *
     * @param mixed               $criteria Criteria or CartRule object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CartRule object
        }

        if ($criteria->containsKey(CartRuleTableMap::COL_ID) && $criteria->keyContainsValue(CartRuleTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CartRuleTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CartRuleQuery::create()->mergeWith($criteria);

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

} // CartRuleTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CartRuleTableMap::buildTableMap();
