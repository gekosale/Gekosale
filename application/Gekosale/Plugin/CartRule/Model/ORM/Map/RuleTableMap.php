<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Map;

use Gekosale\Plugin\CartRule\Model\ORM\Rule;
use Gekosale\Plugin\CartRule\Model\ORM\RuleQuery;
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
 * This class defines the structure of the 'rule' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class RuleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.CartRule.Model.ORM.Map.RuleTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'rule';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\Rule';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.CartRule.Model.ORM.Rule';

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
    const COL_ID = 'rule.ID';

    /**
     * the column name for the NAME field
     */
    const COL_NAME = 'rule.NAME';

    /**
     * the column name for the TABLE_REFERER field
     */
    const COL_TABLE_REFERER = 'rule.TABLE_REFERER';

    /**
     * the column name for the PRIMARY_KEY_REFERER field
     */
    const COL_PRIMARY_KEY_REFERER = 'rule.PRIMARY_KEY_REFERER';

    /**
     * the column name for the COLUMN_REFERER field
     */
    const COL_COLUMN_REFERER = 'rule.COLUMN_REFERER';

    /**
     * the column name for the RULE_TYPE_ID field
     */
    const COL_RULE_TYPE_ID = 'rule.RULE_TYPE_ID';

    /**
     * the column name for the FIELD field
     */
    const COL_FIELD = 'rule.FIELD';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'TableReferer', 'PrimaryKeyReferer', 'ColumnReferer', 'RuleTypeId', 'Field', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'tableReferer', 'primaryKeyReferer', 'columnReferer', 'ruleTypeId', 'field', ),
        self::TYPE_COLNAME       => array(RuleTableMap::COL_ID, RuleTableMap::COL_NAME, RuleTableMap::COL_TABLE_REFERER, RuleTableMap::COL_PRIMARY_KEY_REFERER, RuleTableMap::COL_COLUMN_REFERER, RuleTableMap::COL_RULE_TYPE_ID, RuleTableMap::COL_FIELD, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_NAME', 'COL_TABLE_REFERER', 'COL_PRIMARY_KEY_REFERER', 'COL_COLUMN_REFERER', 'COL_RULE_TYPE_ID', 'COL_FIELD', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'table_referer', 'primary_key_referer', 'column_referer', 'rule_type_id', 'field', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'TableReferer' => 2, 'PrimaryKeyReferer' => 3, 'ColumnReferer' => 4, 'RuleTypeId' => 5, 'Field' => 6, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'tableReferer' => 2, 'primaryKeyReferer' => 3, 'columnReferer' => 4, 'ruleTypeId' => 5, 'field' => 6, ),
        self::TYPE_COLNAME       => array(RuleTableMap::COL_ID => 0, RuleTableMap::COL_NAME => 1, RuleTableMap::COL_TABLE_REFERER => 2, RuleTableMap::COL_PRIMARY_KEY_REFERER => 3, RuleTableMap::COL_COLUMN_REFERER => 4, RuleTableMap::COL_RULE_TYPE_ID => 5, RuleTableMap::COL_FIELD => 6, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_NAME' => 1, 'COL_TABLE_REFERER' => 2, 'COL_PRIMARY_KEY_REFERER' => 3, 'COL_COLUMN_REFERER' => 4, 'COL_RULE_TYPE_ID' => 5, 'COL_FIELD' => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'table_referer' => 2, 'primary_key_referer' => 3, 'column_referer' => 4, 'rule_type_id' => 5, 'field' => 6, ),
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
        $this->setName('rule');
        $this->setPhpName('Rule');
        $this->setClassName('\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\Rule');
        $this->setPackage('Gekosale.Plugin.CartRule.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 45, null);
        $this->addColumn('TABLE_REFERER', 'TableReferer', 'VARCHAR', false, 45, null);
        $this->addColumn('PRIMARY_KEY_REFERER', 'PrimaryKeyReferer', 'VARCHAR', false, 45, null);
        $this->addColumn('COLUMN_REFERER', 'ColumnReferer', 'VARCHAR', false, 45, null);
        $this->addColumn('RULE_TYPE_ID', 'RuleTypeId', 'INTEGER', true, 10, null);
        $this->addColumn('FIELD', 'Field', 'VARCHAR', false, 45, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CartRuleRule', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleRule', RelationMap::ONE_TO_MANY, array('id' => 'rule_id', ), 'CASCADE', null, 'CartRuleRules');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to rule     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                CartRuleRuleTableMap::clearInstancePool();
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
        return $withPrefix ? RuleTableMap::CLASS_DEFAULT : RuleTableMap::OM_CLASS;
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
     * @return array (Rule object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = RuleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = RuleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + RuleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = RuleTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            RuleTableMap::addInstanceToPool($obj, $key);
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
            $key = RuleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = RuleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                RuleTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(RuleTableMap::COL_ID);
            $criteria->addSelectColumn(RuleTableMap::COL_NAME);
            $criteria->addSelectColumn(RuleTableMap::COL_TABLE_REFERER);
            $criteria->addSelectColumn(RuleTableMap::COL_PRIMARY_KEY_REFERER);
            $criteria->addSelectColumn(RuleTableMap::COL_COLUMN_REFERER);
            $criteria->addSelectColumn(RuleTableMap::COL_RULE_TYPE_ID);
            $criteria->addSelectColumn(RuleTableMap::COL_FIELD);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.TABLE_REFERER');
            $criteria->addSelectColumn($alias . '.PRIMARY_KEY_REFERER');
            $criteria->addSelectColumn($alias . '.COLUMN_REFERER');
            $criteria->addSelectColumn($alias . '.RULE_TYPE_ID');
            $criteria->addSelectColumn($alias . '.FIELD');
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
        return Propel::getServiceContainer()->getDatabaseMap(RuleTableMap::DATABASE_NAME)->getTable(RuleTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(RuleTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(RuleTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new RuleTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Rule or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Rule object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(RuleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\CartRule\Model\ORM\Rule) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(RuleTableMap::DATABASE_NAME);
            $criteria->add(RuleTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = RuleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { RuleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { RuleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the rule table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return RuleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Rule or Criteria object.
     *
     * @param mixed               $criteria Criteria or Rule object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(RuleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Rule object
        }

        if ($criteria->containsKey(RuleTableMap::COL_ID) && $criteria->keyContainsValue(RuleTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.RuleTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = RuleQuery::create()->mergeWith($criteria);

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

} // RuleTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
RuleTableMap::buildTableMap();
