<?php

namespace Gekosale\Plugin\Order\Model\ORM\Map;

use Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData;
use Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryDataQuery;
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
 * This class defines the structure of the 'order_client_delivery_data' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class OrderClientDeliveryDataTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Order.Model.ORM.Map.OrderClientDeliveryDataTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'order_client_delivery_data';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderClientDeliveryData';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Order.Model.ORM.OrderClientDeliveryData';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 17;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 17;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'order_client_delivery_data.ID';

    /**
     * the column name for the FIRSTNAME field
     */
    const COL_FIRSTNAME = 'order_client_delivery_data.FIRSTNAME';

    /**
     * the column name for the SURNAME field
     */
    const COL_SURNAME = 'order_client_delivery_data.SURNAME';

    /**
     * the column name for the COMPANY_NAME field
     */
    const COL_COMPANY_NAME = 'order_client_delivery_data.COMPANY_NAME';

    /**
     * the column name for the TAX_ID field
     */
    const COL_TAX_ID = 'order_client_delivery_data.TAX_ID';

    /**
     * the column name for the STREET field
     */
    const COL_STREET = 'order_client_delivery_data.STREET';

    /**
     * the column name for the STREET_NO field
     */
    const COL_STREET_NO = 'order_client_delivery_data.STREET_NO';

    /**
     * the column name for the PLACE_NO field
     */
    const COL_PLACE_NO = 'order_client_delivery_data.PLACE_NO';

    /**
     * the column name for the POST_CODE field
     */
    const COL_POST_CODE = 'order_client_delivery_data.POST_CODE';

    /**
     * the column name for the CITY field
     */
    const COL_CITY = 'order_client_delivery_data.CITY';

    /**
     * the column name for the PLACEID field
     */
    const COL_PLACEID = 'order_client_delivery_data.PLACEID';

    /**
     * the column name for the PHONE field
     */
    const COL_PHONE = 'order_client_delivery_data.PHONE';

    /**
     * the column name for the PHONE2 field
     */
    const COL_PHONE2 = 'order_client_delivery_data.PHONE2';

    /**
     * the column name for the EMAIL field
     */
    const COL_EMAIL = 'order_client_delivery_data.EMAIL';

    /**
     * the column name for the ORDER_ID field
     */
    const COL_ORDER_ID = 'order_client_delivery_data.ORDER_ID';

    /**
     * the column name for the COUNTRY_ID field
     */
    const COL_COUNTRY_ID = 'order_client_delivery_data.COUNTRY_ID';

    /**
     * the column name for the CLIENT_TYPE field
     */
    const COL_CLIENT_TYPE = 'order_client_delivery_data.CLIENT_TYPE';

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
        self::TYPE_PHPNAME       => array('Id', 'Firstname', 'Surname', 'CompanyName', 'TaxId', 'Street', 'StreetNo', 'PlaceNo', 'PostCode', 'City', 'Placeid', 'Phone', 'Phone2', 'Email', 'OrderId', 'CountryId', 'ClientType', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'firstname', 'surname', 'companyName', 'taxId', 'street', 'streetNo', 'placeNo', 'postCode', 'city', 'placeid', 'phone', 'phone2', 'email', 'orderId', 'countryId', 'clientType', ),
        self::TYPE_COLNAME       => array(OrderClientDeliveryDataTableMap::COL_ID, OrderClientDeliveryDataTableMap::COL_FIRSTNAME, OrderClientDeliveryDataTableMap::COL_SURNAME, OrderClientDeliveryDataTableMap::COL_COMPANY_NAME, OrderClientDeliveryDataTableMap::COL_TAX_ID, OrderClientDeliveryDataTableMap::COL_STREET, OrderClientDeliveryDataTableMap::COL_STREET_NO, OrderClientDeliveryDataTableMap::COL_PLACE_NO, OrderClientDeliveryDataTableMap::COL_POST_CODE, OrderClientDeliveryDataTableMap::COL_CITY, OrderClientDeliveryDataTableMap::COL_PLACEID, OrderClientDeliveryDataTableMap::COL_PHONE, OrderClientDeliveryDataTableMap::COL_PHONE2, OrderClientDeliveryDataTableMap::COL_EMAIL, OrderClientDeliveryDataTableMap::COL_ORDER_ID, OrderClientDeliveryDataTableMap::COL_COUNTRY_ID, OrderClientDeliveryDataTableMap::COL_CLIENT_TYPE, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_FIRSTNAME', 'COL_SURNAME', 'COL_COMPANY_NAME', 'COL_TAX_ID', 'COL_STREET', 'COL_STREET_NO', 'COL_PLACE_NO', 'COL_POST_CODE', 'COL_CITY', 'COL_PLACEID', 'COL_PHONE', 'COL_PHONE2', 'COL_EMAIL', 'COL_ORDER_ID', 'COL_COUNTRY_ID', 'COL_CLIENT_TYPE', ),
        self::TYPE_FIELDNAME     => array('id', 'firstname', 'surname', 'company_name', 'tax_id', 'street', 'street_no', 'place_no', 'post_code', 'city', 'placeid', 'phone', 'phone2', 'email', 'order_id', 'country_id', 'client_type', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Firstname' => 1, 'Surname' => 2, 'CompanyName' => 3, 'TaxId' => 4, 'Street' => 5, 'StreetNo' => 6, 'PlaceNo' => 7, 'PostCode' => 8, 'City' => 9, 'Placeid' => 10, 'Phone' => 11, 'Phone2' => 12, 'Email' => 13, 'OrderId' => 14, 'CountryId' => 15, 'ClientType' => 16, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'firstname' => 1, 'surname' => 2, 'companyName' => 3, 'taxId' => 4, 'street' => 5, 'streetNo' => 6, 'placeNo' => 7, 'postCode' => 8, 'city' => 9, 'placeid' => 10, 'phone' => 11, 'phone2' => 12, 'email' => 13, 'orderId' => 14, 'countryId' => 15, 'clientType' => 16, ),
        self::TYPE_COLNAME       => array(OrderClientDeliveryDataTableMap::COL_ID => 0, OrderClientDeliveryDataTableMap::COL_FIRSTNAME => 1, OrderClientDeliveryDataTableMap::COL_SURNAME => 2, OrderClientDeliveryDataTableMap::COL_COMPANY_NAME => 3, OrderClientDeliveryDataTableMap::COL_TAX_ID => 4, OrderClientDeliveryDataTableMap::COL_STREET => 5, OrderClientDeliveryDataTableMap::COL_STREET_NO => 6, OrderClientDeliveryDataTableMap::COL_PLACE_NO => 7, OrderClientDeliveryDataTableMap::COL_POST_CODE => 8, OrderClientDeliveryDataTableMap::COL_CITY => 9, OrderClientDeliveryDataTableMap::COL_PLACEID => 10, OrderClientDeliveryDataTableMap::COL_PHONE => 11, OrderClientDeliveryDataTableMap::COL_PHONE2 => 12, OrderClientDeliveryDataTableMap::COL_EMAIL => 13, OrderClientDeliveryDataTableMap::COL_ORDER_ID => 14, OrderClientDeliveryDataTableMap::COL_COUNTRY_ID => 15, OrderClientDeliveryDataTableMap::COL_CLIENT_TYPE => 16, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_FIRSTNAME' => 1, 'COL_SURNAME' => 2, 'COL_COMPANY_NAME' => 3, 'COL_TAX_ID' => 4, 'COL_STREET' => 5, 'COL_STREET_NO' => 6, 'COL_PLACE_NO' => 7, 'COL_POST_CODE' => 8, 'COL_CITY' => 9, 'COL_PLACEID' => 10, 'COL_PHONE' => 11, 'COL_PHONE2' => 12, 'COL_EMAIL' => 13, 'COL_ORDER_ID' => 14, 'COL_COUNTRY_ID' => 15, 'COL_CLIENT_TYPE' => 16, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'firstname' => 1, 'surname' => 2, 'company_name' => 3, 'tax_id' => 4, 'street' => 5, 'street_no' => 6, 'place_no' => 7, 'post_code' => 8, 'city' => 9, 'placeid' => 10, 'phone' => 11, 'phone2' => 12, 'email' => 13, 'order_id' => 14, 'country_id' => 15, 'client_type' => 16, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
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
        $this->setName('order_client_delivery_data');
        $this->setPhpName('OrderClientDeliveryData');
        $this->setClassName('\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderClientDeliveryData');
        $this->setPackage('Gekosale.Plugin.Order.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('FIRSTNAME', 'Firstname', 'BLOB', true, null, null);
        $this->addColumn('SURNAME', 'Surname', 'BLOB', true, null, null);
        $this->addColumn('COMPANY_NAME', 'CompanyName', 'BLOB', false, null, null);
        $this->addColumn('TAX_ID', 'TaxId', 'BLOB', false, null, null);
        $this->addColumn('STREET', 'Street', 'BLOB', true, null, null);
        $this->addColumn('STREET_NO', 'StreetNo', 'BLOB', true, null, null);
        $this->addColumn('PLACE_NO', 'PlaceNo', 'BLOB', false, null, null);
        $this->addColumn('POST_CODE', 'PostCode', 'BLOB', true, null, null);
        $this->addColumn('CITY', 'City', 'BLOB', true, null, null);
        $this->addColumn('PLACEID', 'Placeid', 'INTEGER', false, 10, null);
        $this->addColumn('PHONE', 'Phone', 'BLOB', false, null, null);
        $this->addColumn('PHONE2', 'Phone2', 'BLOB', true, null, null);
        $this->addColumn('EMAIL', 'Email', 'BLOB', false, null, null);
        $this->addForeignKey('ORDER_ID', 'OrderId', 'INTEGER', 'order', 'ID', true, 10, null);
        $this->addColumn('COUNTRY_ID', 'CountryId', 'INTEGER', false, null, null);
        $this->addColumn('CLIENT_TYPE', 'ClientType', 'INTEGER', true, 10, 1);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Order', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Order', RelationMap::MANY_TO_ONE, array('order_id' => 'id', ), 'CASCADE', null);
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
        return $withPrefix ? OrderClientDeliveryDataTableMap::CLASS_DEFAULT : OrderClientDeliveryDataTableMap::OM_CLASS;
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
     * @return array (OrderClientDeliveryData object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = OrderClientDeliveryDataTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = OrderClientDeliveryDataTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + OrderClientDeliveryDataTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrderClientDeliveryDataTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            OrderClientDeliveryDataTableMap::addInstanceToPool($obj, $key);
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
            $key = OrderClientDeliveryDataTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = OrderClientDeliveryDataTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrderClientDeliveryDataTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_ID);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_FIRSTNAME);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_SURNAME);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_COMPANY_NAME);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_TAX_ID);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_STREET);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_STREET_NO);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_PLACE_NO);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_POST_CODE);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_CITY);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_PLACEID);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_PHONE);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_PHONE2);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_EMAIL);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_COUNTRY_ID);
            $criteria->addSelectColumn(OrderClientDeliveryDataTableMap::COL_CLIENT_TYPE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.FIRSTNAME');
            $criteria->addSelectColumn($alias . '.SURNAME');
            $criteria->addSelectColumn($alias . '.COMPANY_NAME');
            $criteria->addSelectColumn($alias . '.TAX_ID');
            $criteria->addSelectColumn($alias . '.STREET');
            $criteria->addSelectColumn($alias . '.STREET_NO');
            $criteria->addSelectColumn($alias . '.PLACE_NO');
            $criteria->addSelectColumn($alias . '.POST_CODE');
            $criteria->addSelectColumn($alias . '.CITY');
            $criteria->addSelectColumn($alias . '.PLACEID');
            $criteria->addSelectColumn($alias . '.PHONE');
            $criteria->addSelectColumn($alias . '.PHONE2');
            $criteria->addSelectColumn($alias . '.EMAIL');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.COUNTRY_ID');
            $criteria->addSelectColumn($alias . '.CLIENT_TYPE');
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
        return Propel::getServiceContainer()->getDatabaseMap(OrderClientDeliveryDataTableMap::DATABASE_NAME)->getTable(OrderClientDeliveryDataTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(OrderClientDeliveryDataTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(OrderClientDeliveryDataTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new OrderClientDeliveryDataTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a OrderClientDeliveryData or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or OrderClientDeliveryData object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderClientDeliveryDataTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrderClientDeliveryDataTableMap::DATABASE_NAME);
            $criteria->add(OrderClientDeliveryDataTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = OrderClientDeliveryDataQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { OrderClientDeliveryDataTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { OrderClientDeliveryDataTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the order_client_delivery_data table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return OrderClientDeliveryDataQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a OrderClientDeliveryData or Criteria object.
     *
     * @param mixed               $criteria Criteria or OrderClientDeliveryData object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderClientDeliveryDataTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from OrderClientDeliveryData object
        }

        if ($criteria->containsKey(OrderClientDeliveryDataTableMap::COL_ID) && $criteria->keyContainsValue(OrderClientDeliveryDataTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.OrderClientDeliveryDataTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = OrderClientDeliveryDataQuery::create()->mergeWith($criteria);

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

} // OrderClientDeliveryDataTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
OrderClientDeliveryDataTableMap::buildTableMap();
