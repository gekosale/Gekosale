<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Map;

use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery;
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
 * This class defines the structure of the 'cart_rule_client_group' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CartRuleClientGroupTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.CartRule.Model.ORM.Map.CartRuleClientGroupTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'cart_rule_client_group';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleClientGroup';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.CartRule.Model.ORM.CartRuleClientGroup';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 6;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 6;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'cart_rule_client_group.ID';

    /**
     * the column name for the CART_RULE_ID field
     */
    const COL_CART_RULE_ID = 'cart_rule_client_group.CART_RULE_ID';

    /**
     * the column name for the CLIENT_GROUP_ID field
     */
    const COL_CLIENT_GROUP_ID = 'cart_rule_client_group.CLIENT_GROUP_ID';

    /**
     * the column name for the SUFFIX_TYPE_ID field
     */
    const COL_SUFFIX_TYPE_ID = 'cart_rule_client_group.SUFFIX_TYPE_ID';

    /**
     * the column name for the DISCOUNT field
     */
    const COL_DISCOUNT = 'cart_rule_client_group.DISCOUNT';

    /**
     * the column name for the FREE_SHIPPING field
     */
    const COL_FREE_SHIPPING = 'cart_rule_client_group.FREE_SHIPPING';

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
        self::TYPE_PHPNAME       => array('Id', 'CartRuleId', 'ClientGroupId', 'SuffixTypeId', 'Discount', 'FreeShipping', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'cartRuleId', 'clientGroupId', 'suffixTypeId', 'discount', 'freeShipping', ),
        self::TYPE_COLNAME       => array(CartRuleClientGroupTableMap::COL_ID, CartRuleClientGroupTableMap::COL_CART_RULE_ID, CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID, CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID, CartRuleClientGroupTableMap::COL_DISCOUNT, CartRuleClientGroupTableMap::COL_FREE_SHIPPING, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_CART_RULE_ID', 'COL_CLIENT_GROUP_ID', 'COL_SUFFIX_TYPE_ID', 'COL_DISCOUNT', 'COL_FREE_SHIPPING', ),
        self::TYPE_FIELDNAME     => array('id', 'cart_rule_id', 'client_group_id', 'suffix_type_id', 'discount', 'free_shipping', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CartRuleId' => 1, 'ClientGroupId' => 2, 'SuffixTypeId' => 3, 'Discount' => 4, 'FreeShipping' => 5, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'cartRuleId' => 1, 'clientGroupId' => 2, 'suffixTypeId' => 3, 'discount' => 4, 'freeShipping' => 5, ),
        self::TYPE_COLNAME       => array(CartRuleClientGroupTableMap::COL_ID => 0, CartRuleClientGroupTableMap::COL_CART_RULE_ID => 1, CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID => 2, CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID => 3, CartRuleClientGroupTableMap::COL_DISCOUNT => 4, CartRuleClientGroupTableMap::COL_FREE_SHIPPING => 5, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_CART_RULE_ID' => 1, 'COL_CLIENT_GROUP_ID' => 2, 'COL_SUFFIX_TYPE_ID' => 3, 'COL_DISCOUNT' => 4, 'COL_FREE_SHIPPING' => 5, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'cart_rule_id' => 1, 'client_group_id' => 2, 'suffix_type_id' => 3, 'discount' => 4, 'free_shipping' => 5, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, )
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
        $this->setName('cart_rule_client_group');
        $this->setPhpName('CartRuleClientGroup');
        $this->setClassName('\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleClientGroup');
        $this->setPackage('Gekosale.Plugin.CartRule.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('CART_RULE_ID', 'CartRuleId', 'INTEGER', 'cart_rule', 'ID', true, 10, null);
        $this->addForeignKey('CLIENT_GROUP_ID', 'ClientGroupId', 'INTEGER', 'client_group', 'ID', true, 10, null);
        $this->addForeignKey('SUFFIX_TYPE_ID', 'SuffixTypeId', 'INTEGER', 'suffix_type', 'ID', true, 10, null);
        $this->addColumn('DISCOUNT', 'Discount', 'DECIMAL', true, null, null);
        $this->addColumn('FREE_SHIPPING', 'FreeShipping', 'INTEGER', true, null, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ClientGroup', '\\Gekosale\\Plugin\\ClientGroup\\Model\\ORM\\ClientGroup', RelationMap::MANY_TO_ONE, array('client_group_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CartRule', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRule', RelationMap::MANY_TO_ONE, array('cart_rule_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('SuffixType', '\\Gekosale\\Plugin\\SuffixType\\Model\\ORM\\SuffixType', RelationMap::MANY_TO_ONE, array('suffix_type_id' => 'id', ), 'CASCADE', null);
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
        return $withPrefix ? CartRuleClientGroupTableMap::CLASS_DEFAULT : CartRuleClientGroupTableMap::OM_CLASS;
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
     * @return array (CartRuleClientGroup object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CartRuleClientGroupTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CartRuleClientGroupTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CartRuleClientGroupTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CartRuleClientGroupTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CartRuleClientGroupTableMap::addInstanceToPool($obj, $key);
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
            $key = CartRuleClientGroupTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CartRuleClientGroupTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CartRuleClientGroupTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CartRuleClientGroupTableMap::COL_ID);
            $criteria->addSelectColumn(CartRuleClientGroupTableMap::COL_CART_RULE_ID);
            $criteria->addSelectColumn(CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID);
            $criteria->addSelectColumn(CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID);
            $criteria->addSelectColumn(CartRuleClientGroupTableMap::COL_DISCOUNT);
            $criteria->addSelectColumn(CartRuleClientGroupTableMap::COL_FREE_SHIPPING);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CART_RULE_ID');
            $criteria->addSelectColumn($alias . '.CLIENT_GROUP_ID');
            $criteria->addSelectColumn($alias . '.SUFFIX_TYPE_ID');
            $criteria->addSelectColumn($alias . '.DISCOUNT');
            $criteria->addSelectColumn($alias . '.FREE_SHIPPING');
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
        return Propel::getServiceContainer()->getDatabaseMap(CartRuleClientGroupTableMap::DATABASE_NAME)->getTable(CartRuleClientGroupTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CartRuleClientGroupTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CartRuleClientGroupTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CartRuleClientGroupTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a CartRuleClientGroup or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CartRuleClientGroup object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleClientGroupTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CartRuleClientGroupTableMap::DATABASE_NAME);
            $criteria->add(CartRuleClientGroupTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CartRuleClientGroupQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CartRuleClientGroupTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CartRuleClientGroupTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the cart_rule_client_group table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CartRuleClientGroupQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CartRuleClientGroup or Criteria object.
     *
     * @param mixed               $criteria Criteria or CartRuleClientGroup object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleClientGroupTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CartRuleClientGroup object
        }

        if ($criteria->containsKey(CartRuleClientGroupTableMap::COL_ID) && $criteria->keyContainsValue(CartRuleClientGroupTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CartRuleClientGroupTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CartRuleClientGroupQuery::create()->mergeWith($criteria);

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

} // CartRuleClientGroupTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CartRuleClientGroupTableMap::buildTableMap();
