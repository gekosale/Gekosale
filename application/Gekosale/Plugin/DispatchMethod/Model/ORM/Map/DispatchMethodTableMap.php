<?php

namespace Gekosale\Plugin\DispatchMethod\Model\ORM\Map;

use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery;
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
 * This class defines the structure of the 'dispatch_method' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class DispatchMethodTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.DispatchMethod.Model.ORM.Map.DispatchMethodTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'dispatch_method';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethod';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.DispatchMethod.Model.ORM.DispatchMethod';

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
    const COL_ID = 'dispatch_method.ID';

    /**
     * the column name for the NAME field
     */
    const COL_NAME = 'dispatch_method.NAME';

    /**
     * the column name for the DESCRIPTION field
     */
    const COL_DESCRIPTION = 'dispatch_method.DESCRIPTION';

    /**
     * the column name for the TYPE field
     */
    const COL_TYPE = 'dispatch_method.TYPE';

    /**
     * the column name for the MAXIMUM_WEIGHT field
     */
    const COL_MAXIMUM_WEIGHT = 'dispatch_method.MAXIMUM_WEIGHT';

    /**
     * the column name for the FREE_DELIVERY field
     */
    const COL_FREE_DELIVERY = 'dispatch_method.FREE_DELIVERY';

    /**
     * the column name for the COUNTRY_IDS field
     */
    const COL_COUNTRY_IDS = 'dispatch_method.COUNTRY_IDS';

    /**
     * the column name for the CURRENCY_ID field
     */
    const COL_CURRENCY_ID = 'dispatch_method.CURRENCY_ID';

    /**
     * the column name for the HIERARCHY field
     */
    const COL_HIERARCHY = 'dispatch_method.HIERARCHY';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'Description', 'Type', 'MaximumWeight', 'FreeDelivery', 'CountryIds', 'CurrencyId', 'Hierarchy', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'description', 'type', 'maximumWeight', 'freeDelivery', 'countryIds', 'currencyId', 'hierarchy', ),
        self::TYPE_COLNAME       => array(DispatchMethodTableMap::COL_ID, DispatchMethodTableMap::COL_NAME, DispatchMethodTableMap::COL_DESCRIPTION, DispatchMethodTableMap::COL_TYPE, DispatchMethodTableMap::COL_MAXIMUM_WEIGHT, DispatchMethodTableMap::COL_FREE_DELIVERY, DispatchMethodTableMap::COL_COUNTRY_IDS, DispatchMethodTableMap::COL_CURRENCY_ID, DispatchMethodTableMap::COL_HIERARCHY, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_NAME', 'COL_DESCRIPTION', 'COL_TYPE', 'COL_MAXIMUM_WEIGHT', 'COL_FREE_DELIVERY', 'COL_COUNTRY_IDS', 'COL_CURRENCY_ID', 'COL_HIERARCHY', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'description', 'type', 'maximum_weight', 'free_delivery', 'country_ids', 'currency_id', 'hierarchy', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'Description' => 2, 'Type' => 3, 'MaximumWeight' => 4, 'FreeDelivery' => 5, 'CountryIds' => 6, 'CurrencyId' => 7, 'Hierarchy' => 8, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'description' => 2, 'type' => 3, 'maximumWeight' => 4, 'freeDelivery' => 5, 'countryIds' => 6, 'currencyId' => 7, 'hierarchy' => 8, ),
        self::TYPE_COLNAME       => array(DispatchMethodTableMap::COL_ID => 0, DispatchMethodTableMap::COL_NAME => 1, DispatchMethodTableMap::COL_DESCRIPTION => 2, DispatchMethodTableMap::COL_TYPE => 3, DispatchMethodTableMap::COL_MAXIMUM_WEIGHT => 4, DispatchMethodTableMap::COL_FREE_DELIVERY => 5, DispatchMethodTableMap::COL_COUNTRY_IDS => 6, DispatchMethodTableMap::COL_CURRENCY_ID => 7, DispatchMethodTableMap::COL_HIERARCHY => 8, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_NAME' => 1, 'COL_DESCRIPTION' => 2, 'COL_TYPE' => 3, 'COL_MAXIMUM_WEIGHT' => 4, 'COL_FREE_DELIVERY' => 5, 'COL_COUNTRY_IDS' => 6, 'COL_CURRENCY_ID' => 7, 'COL_HIERARCHY' => 8, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'description' => 2, 'type' => 3, 'maximum_weight' => 4, 'free_delivery' => 5, 'country_ids' => 6, 'currency_id' => 7, 'hierarchy' => 8, ),
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
        $this->setName('dispatch_method');
        $this->setPhpName('DispatchMethod');
        $this->setClassName('\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethod');
        $this->setPackage('Gekosale.Plugin.DispatchMethod.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 64, null);
        $this->addColumn('DESCRIPTION', 'Description', 'VARCHAR', false, 5000, null);
        $this->addColumn('TYPE', 'Type', 'INTEGER', true, 10, 1);
        $this->addColumn('MAXIMUM_WEIGHT', 'MaximumWeight', 'DECIMAL', false, 15, null);
        $this->addColumn('FREE_DELIVERY', 'FreeDelivery', 'DECIMAL', false, 15, null);
        $this->addColumn('COUNTRY_IDS', 'CountryIds', 'LONGVARCHAR', true, null, null);
        $this->addColumn('CURRENCY_ID', 'CurrencyId', 'INTEGER', true, null, 28);
        $this->addColumn('HIERARCHY', 'Hierarchy', 'INTEGER', false, null, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('DispatchMethodPrice', '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodPrice', RelationMap::ONE_TO_MANY, array('id' => 'dispatch_method_id', ), 'CASCADE', null, 'DispatchMethodPrices');
        $this->addRelation('DispatchMethodWeight', '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodWeight', RelationMap::ONE_TO_MANY, array('id' => 'dispatch_method_id', ), 'CASCADE', null, 'DispatchMethodWeights');
        $this->addRelation('DispatchMethodpaymentMethod', '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodpaymentMethod', RelationMap::ONE_TO_MANY, array('id' => 'dispatch_method_id', ), 'CASCADE', null, 'DispatchMethodpaymentMethods');
        $this->addRelation('DispatchMethodShop', '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodShop', RelationMap::ONE_TO_MANY, array('id' => 'dispatch_method_id', ), 'CASCADE', null, 'DispatchMethodShops');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to dispatch_method     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                DispatchMethodPriceTableMap::clearInstancePool();
                DispatchMethodWeightTableMap::clearInstancePool();
                DispatchMethodpaymentMethodTableMap::clearInstancePool();
                DispatchMethodShopTableMap::clearInstancePool();
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
        return $withPrefix ? DispatchMethodTableMap::CLASS_DEFAULT : DispatchMethodTableMap::OM_CLASS;
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
     * @return array (DispatchMethod object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = DispatchMethodTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = DispatchMethodTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + DispatchMethodTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = DispatchMethodTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            DispatchMethodTableMap::addInstanceToPool($obj, $key);
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
            $key = DispatchMethodTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = DispatchMethodTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                DispatchMethodTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_ID);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_NAME);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_DESCRIPTION);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_TYPE);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_FREE_DELIVERY);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_COUNTRY_IDS);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_CURRENCY_ID);
            $criteria->addSelectColumn(DispatchMethodTableMap::COL_HIERARCHY);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.DESCRIPTION');
            $criteria->addSelectColumn($alias . '.TYPE');
            $criteria->addSelectColumn($alias . '.MAXIMUM_WEIGHT');
            $criteria->addSelectColumn($alias . '.FREE_DELIVERY');
            $criteria->addSelectColumn($alias . '.COUNTRY_IDS');
            $criteria->addSelectColumn($alias . '.CURRENCY_ID');
            $criteria->addSelectColumn($alias . '.HIERARCHY');
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
        return Propel::getServiceContainer()->getDatabaseMap(DispatchMethodTableMap::DATABASE_NAME)->getTable(DispatchMethodTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(DispatchMethodTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(DispatchMethodTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new DispatchMethodTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a DispatchMethod or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or DispatchMethod object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(DispatchMethodTableMap::DATABASE_NAME);
            $criteria->add(DispatchMethodTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = DispatchMethodQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { DispatchMethodTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { DispatchMethodTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the dispatch_method table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return DispatchMethodQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a DispatchMethod or Criteria object.
     *
     * @param mixed               $criteria Criteria or DispatchMethod object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from DispatchMethod object
        }

        if ($criteria->containsKey(DispatchMethodTableMap::COL_ID) && $criteria->keyContainsValue(DispatchMethodTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.DispatchMethodTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = DispatchMethodQuery::create()->mergeWith($criteria);

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

} // DispatchMethodTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
DispatchMethodTableMap::buildTableMap();
