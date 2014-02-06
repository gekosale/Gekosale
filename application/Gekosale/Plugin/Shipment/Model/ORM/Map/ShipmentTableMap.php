<?php

namespace Gekosale\Plugin\Shipment\Model\ORM\Map;

use Gekosale\Plugin\Shipment\Model\ORM\Shipment;
use Gekosale\Plugin\Shipment\Model\ORM\ShipmentQuery;
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
 * This class defines the structure of the 'shipment' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ShipmentTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Shipment.Model.ORM.Map.ShipmentTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'shipment';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Shipment\\Model\\ORM\\Shipment';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Shipment.Model.ORM.Shipment';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 10;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 10;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'shipment.ID';

    /**
     * the column name for the ORDER_ID field
     */
    const COL_ORDER_ID = 'shipment.ORDER_ID';

    /**
     * the column name for the GUID field
     */
    const COL_GUID = 'shipment.GUID';

    /**
     * the column name for the PACKAGE_NUMBER field
     */
    const COL_PACKAGE_NUMBER = 'shipment.PACKAGE_NUMBER';

    /**
     * the column name for the LABEL field
     */
    const COL_LABEL = 'shipment.LABEL';

    /**
     * the column name for the ORDER_DATA field
     */
    const COL_ORDER_DATA = 'shipment.ORDER_DATA';

    /**
     * the column name for the FORM_DATA field
     */
    const COL_FORM_DATA = 'shipment.FORM_DATA';

    /**
     * the column name for the MODEL field
     */
    const COL_MODEL = 'shipment.MODEL';

    /**
     * the column name for the SENT field
     */
    const COL_SENT = 'shipment.SENT';

    /**
     * the column name for the ENVELOPE_ID field
     */
    const COL_ENVELOPE_ID = 'shipment.ENVELOPE_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'OrderId', 'Guid', 'PackageNumber', 'Label', 'OrderData', 'FormData', 'Model', 'IsSent', 'EnvelopeId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'orderId', 'guid', 'packageNumber', 'label', 'orderData', 'formData', 'model', 'isSent', 'envelopeId', ),
        self::TYPE_COLNAME       => array(ShipmentTableMap::COL_ID, ShipmentTableMap::COL_ORDER_ID, ShipmentTableMap::COL_GUID, ShipmentTableMap::COL_PACKAGE_NUMBER, ShipmentTableMap::COL_LABEL, ShipmentTableMap::COL_ORDER_DATA, ShipmentTableMap::COL_FORM_DATA, ShipmentTableMap::COL_MODEL, ShipmentTableMap::COL_SENT, ShipmentTableMap::COL_ENVELOPE_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_ORDER_ID', 'COL_GUID', 'COL_PACKAGE_NUMBER', 'COL_LABEL', 'COL_ORDER_DATA', 'COL_FORM_DATA', 'COL_MODEL', 'COL_SENT', 'COL_ENVELOPE_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'order_id', 'guid', 'package_number', 'label', 'order_data', 'form_data', 'model', 'sent', 'envelope_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'OrderId' => 1, 'Guid' => 2, 'PackageNumber' => 3, 'Label' => 4, 'OrderData' => 5, 'FormData' => 6, 'Model' => 7, 'IsSent' => 8, 'EnvelopeId' => 9, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'orderId' => 1, 'guid' => 2, 'packageNumber' => 3, 'label' => 4, 'orderData' => 5, 'formData' => 6, 'model' => 7, 'isSent' => 8, 'envelopeId' => 9, ),
        self::TYPE_COLNAME       => array(ShipmentTableMap::COL_ID => 0, ShipmentTableMap::COL_ORDER_ID => 1, ShipmentTableMap::COL_GUID => 2, ShipmentTableMap::COL_PACKAGE_NUMBER => 3, ShipmentTableMap::COL_LABEL => 4, ShipmentTableMap::COL_ORDER_DATA => 5, ShipmentTableMap::COL_FORM_DATA => 6, ShipmentTableMap::COL_MODEL => 7, ShipmentTableMap::COL_SENT => 8, ShipmentTableMap::COL_ENVELOPE_ID => 9, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_ORDER_ID' => 1, 'COL_GUID' => 2, 'COL_PACKAGE_NUMBER' => 3, 'COL_LABEL' => 4, 'COL_ORDER_DATA' => 5, 'COL_FORM_DATA' => 6, 'COL_MODEL' => 7, 'COL_SENT' => 8, 'COL_ENVELOPE_ID' => 9, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'order_id' => 1, 'guid' => 2, 'package_number' => 3, 'label' => 4, 'order_data' => 5, 'form_data' => 6, 'model' => 7, 'sent' => 8, 'envelope_id' => 9, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
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
        $this->setName('shipment');
        $this->setPhpName('Shipment');
        $this->setClassName('\\Gekosale\\Plugin\\Shipment\\Model\\ORM\\Shipment');
        $this->setPackage('Gekosale.Plugin.Shipment.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('ORDER_ID', 'OrderId', 'INTEGER', true, null, null);
        $this->addColumn('GUID', 'Guid', 'VARCHAR', true, 128, null);
        $this->addColumn('PACKAGE_NUMBER', 'PackageNumber', 'VARCHAR', true, 128, null);
        $this->addColumn('LABEL', 'Label', 'BLOB', false, null, null);
        $this->addColumn('ORDER_DATA', 'OrderData', 'LONGVARCHAR', false, null, null);
        $this->addColumn('FORM_DATA', 'FormData', 'LONGVARCHAR', false, null, null);
        $this->addColumn('MODEL', 'Model', 'VARCHAR', false, 45, null);
        $this->addColumn('SENT', 'IsSent', 'INTEGER', true, null, 0);
        $this->addColumn('ENVELOPE_ID', 'EnvelopeId', 'VARCHAR', false, 255, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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
        return $withPrefix ? ShipmentTableMap::CLASS_DEFAULT : ShipmentTableMap::OM_CLASS;
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
     * @return array (Shipment object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ShipmentTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ShipmentTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ShipmentTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ShipmentTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ShipmentTableMap::addInstanceToPool($obj, $key);
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
            $key = ShipmentTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ShipmentTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ShipmentTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ShipmentTableMap::COL_ID);
            $criteria->addSelectColumn(ShipmentTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(ShipmentTableMap::COL_GUID);
            $criteria->addSelectColumn(ShipmentTableMap::COL_PACKAGE_NUMBER);
            $criteria->addSelectColumn(ShipmentTableMap::COL_LABEL);
            $criteria->addSelectColumn(ShipmentTableMap::COL_ORDER_DATA);
            $criteria->addSelectColumn(ShipmentTableMap::COL_FORM_DATA);
            $criteria->addSelectColumn(ShipmentTableMap::COL_MODEL);
            $criteria->addSelectColumn(ShipmentTableMap::COL_SENT);
            $criteria->addSelectColumn(ShipmentTableMap::COL_ENVELOPE_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.GUID');
            $criteria->addSelectColumn($alias . '.PACKAGE_NUMBER');
            $criteria->addSelectColumn($alias . '.LABEL');
            $criteria->addSelectColumn($alias . '.ORDER_DATA');
            $criteria->addSelectColumn($alias . '.FORM_DATA');
            $criteria->addSelectColumn($alias . '.MODEL');
            $criteria->addSelectColumn($alias . '.SENT');
            $criteria->addSelectColumn($alias . '.ENVELOPE_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(ShipmentTableMap::DATABASE_NAME)->getTable(ShipmentTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ShipmentTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ShipmentTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ShipmentTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Shipment or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Shipment object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ShipmentTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Shipment\Model\ORM\Shipment) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ShipmentTableMap::DATABASE_NAME);
            $criteria->add(ShipmentTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ShipmentQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ShipmentTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ShipmentTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the shipment table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ShipmentQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Shipment or Criteria object.
     *
     * @param mixed               $criteria Criteria or Shipment object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShipmentTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Shipment object
        }

        if ($criteria->containsKey(ShipmentTableMap::COL_ID) && $criteria->keyContainsValue(ShipmentTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ShipmentTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ShipmentQuery::create()->mergeWith($criteria);

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

} // ShipmentTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ShipmentTableMap::buildTableMap();
