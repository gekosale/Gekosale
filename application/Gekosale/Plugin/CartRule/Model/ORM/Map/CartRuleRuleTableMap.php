<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Map;

use Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery;
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
 * This class defines the structure of the 'cart_rule_rule' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CartRuleRuleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.CartRule.Model.ORM.Map.CartRuleRuleTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'cart_rule_rule';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleRule';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.CartRule.Model.ORM.CartRuleRule';

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
    const COL_ID = 'cart_rule_rule.ID';

    /**
     * the column name for the RULE_ID field
     */
    const COL_RULE_ID = 'cart_rule_rule.RULE_ID';

    /**
     * the column name for the CART_RULE_ID field
     */
    const COL_CART_RULE_ID = 'cart_rule_rule.CART_RULE_ID';

    /**
     * the column name for the PKID field
     */
    const COL_PKID = 'cart_rule_rule.PKID';

    /**
     * the column name for the PRICE_FROM field
     */
    const COL_PRICE_FROM = 'cart_rule_rule.PRICE_FROM';

    /**
     * the column name for the PRICE_TO field
     */
    const COL_PRICE_TO = 'cart_rule_rule.PRICE_TO';

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
        self::TYPE_PHPNAME       => array('Id', 'RuleId', 'CartRuleId', 'Pkid', 'PriceFrom', 'PriceTo', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'ruleId', 'cartRuleId', 'pkid', 'priceFrom', 'priceTo', ),
        self::TYPE_COLNAME       => array(CartRuleRuleTableMap::COL_ID, CartRuleRuleTableMap::COL_RULE_ID, CartRuleRuleTableMap::COL_CART_RULE_ID, CartRuleRuleTableMap::COL_PKID, CartRuleRuleTableMap::COL_PRICE_FROM, CartRuleRuleTableMap::COL_PRICE_TO, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_RULE_ID', 'COL_CART_RULE_ID', 'COL_PKID', 'COL_PRICE_FROM', 'COL_PRICE_TO', ),
        self::TYPE_FIELDNAME     => array('id', 'rule_id', 'cart_rule_id', 'pkid', 'price_from', 'price_to', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'RuleId' => 1, 'CartRuleId' => 2, 'Pkid' => 3, 'PriceFrom' => 4, 'PriceTo' => 5, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'ruleId' => 1, 'cartRuleId' => 2, 'pkid' => 3, 'priceFrom' => 4, 'priceTo' => 5, ),
        self::TYPE_COLNAME       => array(CartRuleRuleTableMap::COL_ID => 0, CartRuleRuleTableMap::COL_RULE_ID => 1, CartRuleRuleTableMap::COL_CART_RULE_ID => 2, CartRuleRuleTableMap::COL_PKID => 3, CartRuleRuleTableMap::COL_PRICE_FROM => 4, CartRuleRuleTableMap::COL_PRICE_TO => 5, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_RULE_ID' => 1, 'COL_CART_RULE_ID' => 2, 'COL_PKID' => 3, 'COL_PRICE_FROM' => 4, 'COL_PRICE_TO' => 5, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'rule_id' => 1, 'cart_rule_id' => 2, 'pkid' => 3, 'price_from' => 4, 'price_to' => 5, ),
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
        $this->setName('cart_rule_rule');
        $this->setPhpName('CartRuleRule');
        $this->setClassName('\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleRule');
        $this->setPackage('Gekosale.Plugin.CartRule.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('RULE_ID', 'RuleId', 'INTEGER', 'rule', 'ID', true, 10, null);
        $this->addForeignKey('CART_RULE_ID', 'CartRuleId', 'INTEGER', 'cart_rule', 'ID', true, 10, null);
        $this->addColumn('PKID', 'Pkid', 'INTEGER', false, 10, null);
        $this->addColumn('PRICE_FROM', 'PriceFrom', 'DECIMAL', false, null, null);
        $this->addColumn('PRICE_TO', 'PriceTo', 'DECIMAL', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Rule', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\Rule', RelationMap::MANY_TO_ONE, array('rule_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CartRule', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRule', RelationMap::MANY_TO_ONE, array('cart_rule_id' => 'id', ), 'CASCADE', null);
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
        return $withPrefix ? CartRuleRuleTableMap::CLASS_DEFAULT : CartRuleRuleTableMap::OM_CLASS;
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
     * @return array (CartRuleRule object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CartRuleRuleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CartRuleRuleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CartRuleRuleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CartRuleRuleTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CartRuleRuleTableMap::addInstanceToPool($obj, $key);
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
            $key = CartRuleRuleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CartRuleRuleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CartRuleRuleTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CartRuleRuleTableMap::COL_ID);
            $criteria->addSelectColumn(CartRuleRuleTableMap::COL_RULE_ID);
            $criteria->addSelectColumn(CartRuleRuleTableMap::COL_CART_RULE_ID);
            $criteria->addSelectColumn(CartRuleRuleTableMap::COL_PKID);
            $criteria->addSelectColumn(CartRuleRuleTableMap::COL_PRICE_FROM);
            $criteria->addSelectColumn(CartRuleRuleTableMap::COL_PRICE_TO);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.RULE_ID');
            $criteria->addSelectColumn($alias . '.CART_RULE_ID');
            $criteria->addSelectColumn($alias . '.PKID');
            $criteria->addSelectColumn($alias . '.PRICE_FROM');
            $criteria->addSelectColumn($alias . '.PRICE_TO');
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
        return Propel::getServiceContainer()->getDatabaseMap(CartRuleRuleTableMap::DATABASE_NAME)->getTable(CartRuleRuleTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CartRuleRuleTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CartRuleRuleTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CartRuleRuleTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a CartRuleRule or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CartRuleRule object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleRuleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CartRuleRuleTableMap::DATABASE_NAME);
            $criteria->add(CartRuleRuleTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CartRuleRuleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CartRuleRuleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CartRuleRuleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the cart_rule_rule table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CartRuleRuleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CartRuleRule or Criteria object.
     *
     * @param mixed               $criteria Criteria or CartRuleRule object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleRuleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CartRuleRule object
        }

        if ($criteria->containsKey(CartRuleRuleTableMap::COL_ID) && $criteria->keyContainsValue(CartRuleRuleTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CartRuleRuleTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CartRuleRuleQuery::create()->mergeWith($criteria);

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

} // CartRuleRuleTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CartRuleRuleTableMap::buildTableMap();
