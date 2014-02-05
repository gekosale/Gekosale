<?php

namespace Gekosale\Plugin\TechnicalData\Model\ORM\Map;

use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupQuery;
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
 * This class defines the structure of the 'technical_data_set_group' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class TechnicalDataSetGroupTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.TechnicalData.Model.ORM.Map.TechnicalDataSetGroupTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'technical_data_set_group';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSetGroup';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.TechnicalData.Model.ORM.TechnicalDataSetGroup';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 4;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 4;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'technical_data_set_group.ID';

    /**
     * the column name for the TECHNICAL_DATA_SET_ID field
     */
    const COL_TECHNICAL_DATA_SET_ID = 'technical_data_set_group.TECHNICAL_DATA_SET_ID';

    /**
     * the column name for the TECHNICAL_DATA_GROUP_ID field
     */
    const COL_TECHNICAL_DATA_GROUP_ID = 'technical_data_set_group.TECHNICAL_DATA_GROUP_ID';

    /**
     * the column name for the ORDER field
     */
    const COL_ORDER = 'technical_data_set_group.ORDER';

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
        self::TYPE_PHPNAME       => array('Id', 'TechnicalDataSetId', 'TechnicalDataGroupId', 'Order', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'technicalDataSetId', 'technicalDataGroupId', 'order', ),
        self::TYPE_COLNAME       => array(TechnicalDataSetGroupTableMap::COL_ID, TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID, TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID, TechnicalDataSetGroupTableMap::COL_ORDER, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_TECHNICAL_DATA_SET_ID', 'COL_TECHNICAL_DATA_GROUP_ID', 'COL_ORDER', ),
        self::TYPE_FIELDNAME     => array('id', 'technical_data_set_id', 'technical_data_group_id', 'order', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'TechnicalDataSetId' => 1, 'TechnicalDataGroupId' => 2, 'Order' => 3, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'technicalDataSetId' => 1, 'technicalDataGroupId' => 2, 'order' => 3, ),
        self::TYPE_COLNAME       => array(TechnicalDataSetGroupTableMap::COL_ID => 0, TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID => 1, TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID => 2, TechnicalDataSetGroupTableMap::COL_ORDER => 3, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_TECHNICAL_DATA_SET_ID' => 1, 'COL_TECHNICAL_DATA_GROUP_ID' => 2, 'COL_ORDER' => 3, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'technical_data_set_id' => 1, 'technical_data_group_id' => 2, 'order' => 3, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
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
        $this->setName('technical_data_set_group');
        $this->setPhpName('TechnicalDataSetGroup');
        $this->setClassName('\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSetGroup');
        $this->setPackage('Gekosale.Plugin.TechnicalData.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('TECHNICAL_DATA_SET_ID', 'TechnicalDataSetId', 'INTEGER', 'technical_data_set', 'ID', true, 10, null);
        $this->addForeignKey('TECHNICAL_DATA_GROUP_ID', 'TechnicalDataGroupId', 'INTEGER', 'technical_data_group', 'ID', true, 10, null);
        $this->addColumn('ORDER', 'Order', 'SMALLINT', false, 5, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('TechnicalDataSet', '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSet', RelationMap::MANY_TO_ONE, array('technical_data_set_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('TechnicalDataGroup', '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataGroup', RelationMap::MANY_TO_ONE, array('technical_data_group_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('TechnicalDataSetGroupAttribute', '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSetGroupAttribute', RelationMap::ONE_TO_MANY, array('id' => 'technical_data_set_group_id', ), 'CASCADE', null, 'TechnicalDataSetGroupAttributes');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to technical_data_set_group     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                TechnicalDataSetGroupAttributeTableMap::clearInstancePool();
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
        return $withPrefix ? TechnicalDataSetGroupTableMap::CLASS_DEFAULT : TechnicalDataSetGroupTableMap::OM_CLASS;
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
     * @return array (TechnicalDataSetGroup object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = TechnicalDataSetGroupTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = TechnicalDataSetGroupTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + TechnicalDataSetGroupTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = TechnicalDataSetGroupTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            TechnicalDataSetGroupTableMap::addInstanceToPool($obj, $key);
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
            $key = TechnicalDataSetGroupTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = TechnicalDataSetGroupTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                TechnicalDataSetGroupTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(TechnicalDataSetGroupTableMap::COL_ID);
            $criteria->addSelectColumn(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID);
            $criteria->addSelectColumn(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID);
            $criteria->addSelectColumn(TechnicalDataSetGroupTableMap::COL_ORDER);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.TECHNICAL_DATA_SET_ID');
            $criteria->addSelectColumn($alias . '.TECHNICAL_DATA_GROUP_ID');
            $criteria->addSelectColumn($alias . '.ORDER');
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
        return Propel::getServiceContainer()->getDatabaseMap(TechnicalDataSetGroupTableMap::DATABASE_NAME)->getTable(TechnicalDataSetGroupTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(TechnicalDataSetGroupTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(TechnicalDataSetGroupTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new TechnicalDataSetGroupTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a TechnicalDataSetGroup or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or TechnicalDataSetGroup object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataSetGroupTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(TechnicalDataSetGroupTableMap::DATABASE_NAME);
            $criteria->add(TechnicalDataSetGroupTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = TechnicalDataSetGroupQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { TechnicalDataSetGroupTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { TechnicalDataSetGroupTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the technical_data_set_group table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return TechnicalDataSetGroupQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a TechnicalDataSetGroup or Criteria object.
     *
     * @param mixed               $criteria Criteria or TechnicalDataSetGroup object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataSetGroupTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from TechnicalDataSetGroup object
        }

        if ($criteria->containsKey(TechnicalDataSetGroupTableMap::COL_ID) && $criteria->keyContainsValue(TechnicalDataSetGroupTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TechnicalDataSetGroupTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = TechnicalDataSetGroupQuery::create()->mergeWith($criteria);

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

} // TechnicalDataSetGroupTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
TechnicalDataSetGroupTableMap::buildTableMap();
