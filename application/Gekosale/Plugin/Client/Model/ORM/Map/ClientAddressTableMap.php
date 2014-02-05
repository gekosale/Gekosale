<?php

namespace Gekosale\Plugin\Client\Model\ORM\Map;

use Gekosale\Plugin\Client\Model\ORM\ClientAddress;
use Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery;
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
 * This class defines the structure of the 'client_address' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ClientAddressTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Client.Model.ORM.Map.ClientAddressTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'client_address';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientAddress';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Client.Model.ORM.ClientAddress';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 15;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 15;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'client_address.ID';

    /**
     * the column name for the STREET field
     */
    const COL_STREET = 'client_address.STREET';

    /**
     * the column name for the STREET_NO field
     */
    const COL_STREET_NO = 'client_address.STREET_NO';

    /**
     * the column name for the PLACE_NO field
     */
    const COL_PLACE_NO = 'client_address.PLACE_NO';

    /**
     * the column name for the POST_CODE field
     */
    const COL_POST_CODE = 'client_address.POST_CODE';

    /**
     * the column name for the COMPANY_NAME field
     */
    const COL_COMPANY_NAME = 'client_address.COMPANY_NAME';

    /**
     * the column name for the FIRSTNAME field
     */
    const COL_FIRSTNAME = 'client_address.FIRSTNAME';

    /**
     * the column name for the SURNAME field
     */
    const COL_SURNAME = 'client_address.SURNAME';

    /**
     * the column name for the CLIENT_ID field
     */
    const COL_CLIENT_ID = 'client_address.CLIENT_ID';

    /**
     * the column name for the REGON field
     */
    const COL_REGON = 'client_address.REGON';

    /**
     * the column name for the TAX_ID field
     */
    const COL_TAX_ID = 'client_address.TAX_ID';

    /**
     * the column name for the CITY field
     */
    const COL_CITY = 'client_address.CITY';

    /**
     * the column name for the IS_MAIN field
     */
    const COL_IS_MAIN = 'client_address.IS_MAIN';

    /**
     * the column name for the COUNTRY_ID field
     */
    const COL_COUNTRY_ID = 'client_address.COUNTRY_ID';

    /**
     * the column name for the CLIENT_TYPE field
     */
    const COL_CLIENT_TYPE = 'client_address.CLIENT_TYPE';

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
        self::TYPE_PHPNAME       => array('Id', 'Street', 'StreetNo', 'PlaceNo', 'PostCode', 'CompanyName', 'Firstname', 'Surname', 'ClientId', 'Regon', 'TaxId', 'City', 'IsMain', 'CountryId', 'ClientType', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'street', 'streetNo', 'placeNo', 'postCode', 'companyName', 'firstname', 'surname', 'clientId', 'regon', 'taxId', 'city', 'isMain', 'countryId', 'clientType', ),
        self::TYPE_COLNAME       => array(ClientAddressTableMap::COL_ID, ClientAddressTableMap::COL_STREET, ClientAddressTableMap::COL_STREET_NO, ClientAddressTableMap::COL_PLACE_NO, ClientAddressTableMap::COL_POST_CODE, ClientAddressTableMap::COL_COMPANY_NAME, ClientAddressTableMap::COL_FIRSTNAME, ClientAddressTableMap::COL_SURNAME, ClientAddressTableMap::COL_CLIENT_ID, ClientAddressTableMap::COL_REGON, ClientAddressTableMap::COL_TAX_ID, ClientAddressTableMap::COL_CITY, ClientAddressTableMap::COL_IS_MAIN, ClientAddressTableMap::COL_COUNTRY_ID, ClientAddressTableMap::COL_CLIENT_TYPE, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_STREET', 'COL_STREET_NO', 'COL_PLACE_NO', 'COL_POST_CODE', 'COL_COMPANY_NAME', 'COL_FIRSTNAME', 'COL_SURNAME', 'COL_CLIENT_ID', 'COL_REGON', 'COL_TAX_ID', 'COL_CITY', 'COL_IS_MAIN', 'COL_COUNTRY_ID', 'COL_CLIENT_TYPE', ),
        self::TYPE_FIELDNAME     => array('id', 'street', 'street_no', 'place_no', 'post_code', 'company_name', 'firstname', 'surname', 'client_id', 'regon', 'tax_id', 'city', 'is_main', 'country_id', 'client_type', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Street' => 1, 'StreetNo' => 2, 'PlaceNo' => 3, 'PostCode' => 4, 'CompanyName' => 5, 'Firstname' => 6, 'Surname' => 7, 'ClientId' => 8, 'Regon' => 9, 'TaxId' => 10, 'City' => 11, 'IsMain' => 12, 'CountryId' => 13, 'ClientType' => 14, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'street' => 1, 'streetNo' => 2, 'placeNo' => 3, 'postCode' => 4, 'companyName' => 5, 'firstname' => 6, 'surname' => 7, 'clientId' => 8, 'regon' => 9, 'taxId' => 10, 'city' => 11, 'isMain' => 12, 'countryId' => 13, 'clientType' => 14, ),
        self::TYPE_COLNAME       => array(ClientAddressTableMap::COL_ID => 0, ClientAddressTableMap::COL_STREET => 1, ClientAddressTableMap::COL_STREET_NO => 2, ClientAddressTableMap::COL_PLACE_NO => 3, ClientAddressTableMap::COL_POST_CODE => 4, ClientAddressTableMap::COL_COMPANY_NAME => 5, ClientAddressTableMap::COL_FIRSTNAME => 6, ClientAddressTableMap::COL_SURNAME => 7, ClientAddressTableMap::COL_CLIENT_ID => 8, ClientAddressTableMap::COL_REGON => 9, ClientAddressTableMap::COL_TAX_ID => 10, ClientAddressTableMap::COL_CITY => 11, ClientAddressTableMap::COL_IS_MAIN => 12, ClientAddressTableMap::COL_COUNTRY_ID => 13, ClientAddressTableMap::COL_CLIENT_TYPE => 14, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_STREET' => 1, 'COL_STREET_NO' => 2, 'COL_PLACE_NO' => 3, 'COL_POST_CODE' => 4, 'COL_COMPANY_NAME' => 5, 'COL_FIRSTNAME' => 6, 'COL_SURNAME' => 7, 'COL_CLIENT_ID' => 8, 'COL_REGON' => 9, 'COL_TAX_ID' => 10, 'COL_CITY' => 11, 'COL_IS_MAIN' => 12, 'COL_COUNTRY_ID' => 13, 'COL_CLIENT_TYPE' => 14, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'street' => 1, 'street_no' => 2, 'place_no' => 3, 'post_code' => 4, 'company_name' => 5, 'firstname' => 6, 'surname' => 7, 'client_id' => 8, 'regon' => 9, 'tax_id' => 10, 'city' => 11, 'is_main' => 12, 'country_id' => 13, 'client_type' => 14, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
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
        $this->setName('client_address');
        $this->setPhpName('ClientAddress');
        $this->setClassName('\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientAddress');
        $this->setPackage('Gekosale.Plugin.Client.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('STREET', 'Street', 'BLOB', true, null, null);
        $this->addColumn('STREET_NO', 'StreetNo', 'BLOB', true, null, null);
        $this->addColumn('PLACE_NO', 'PlaceNo', 'BLOB', false, null, null);
        $this->addColumn('POST_CODE', 'PostCode', 'BLOB', true, null, null);
        $this->addColumn('COMPANY_NAME', 'CompanyName', 'BLOB', false, null, null);
        $this->addColumn('FIRSTNAME', 'Firstname', 'BLOB', false, null, null);
        $this->addColumn('SURNAME', 'Surname', 'BLOB', false, null, null);
        $this->addForeignKey('CLIENT_ID', 'ClientId', 'INTEGER', 'client', 'ID', true, 10, null);
        $this->addColumn('REGON', 'Regon', 'BLOB', false, null, null);
        $this->addColumn('TAX_ID', 'TaxId', 'BLOB', false, null, null);
        $this->addColumn('CITY', 'City', 'BLOB', true, null, null);
        $this->addColumn('IS_MAIN', 'IsMain', 'INTEGER', true, 10, 1);
        $this->addForeignKey('COUNTRY_ID', 'CountryId', 'INTEGER', 'country', 'ID', false, 10, null);
        $this->addColumn('CLIENT_TYPE', 'ClientType', 'INTEGER', true, 10, 1);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Client', '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Client', RelationMap::MANY_TO_ONE, array('client_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Country', '\\Gekosale\\Plugin\\Country\\Model\\ORM\\Country', RelationMap::MANY_TO_ONE, array('country_id' => 'id', ), null, null);
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
        return $withPrefix ? ClientAddressTableMap::CLASS_DEFAULT : ClientAddressTableMap::OM_CLASS;
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
     * @return array (ClientAddress object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ClientAddressTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ClientAddressTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ClientAddressTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ClientAddressTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ClientAddressTableMap::addInstanceToPool($obj, $key);
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
            $key = ClientAddressTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ClientAddressTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ClientAddressTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ClientAddressTableMap::COL_ID);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_STREET);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_STREET_NO);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_PLACE_NO);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_POST_CODE);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_COMPANY_NAME);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_FIRSTNAME);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_SURNAME);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_CLIENT_ID);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_REGON);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_TAX_ID);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_CITY);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_IS_MAIN);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_COUNTRY_ID);
            $criteria->addSelectColumn(ClientAddressTableMap::COL_CLIENT_TYPE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.STREET');
            $criteria->addSelectColumn($alias . '.STREET_NO');
            $criteria->addSelectColumn($alias . '.PLACE_NO');
            $criteria->addSelectColumn($alias . '.POST_CODE');
            $criteria->addSelectColumn($alias . '.COMPANY_NAME');
            $criteria->addSelectColumn($alias . '.FIRSTNAME');
            $criteria->addSelectColumn($alias . '.SURNAME');
            $criteria->addSelectColumn($alias . '.CLIENT_ID');
            $criteria->addSelectColumn($alias . '.REGON');
            $criteria->addSelectColumn($alias . '.TAX_ID');
            $criteria->addSelectColumn($alias . '.CITY');
            $criteria->addSelectColumn($alias . '.IS_MAIN');
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
        return Propel::getServiceContainer()->getDatabaseMap(ClientAddressTableMap::DATABASE_NAME)->getTable(ClientAddressTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ClientAddressTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ClientAddressTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ClientAddressTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a ClientAddress or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ClientAddress object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientAddressTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Client\Model\ORM\ClientAddress) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ClientAddressTableMap::DATABASE_NAME);
            $criteria->add(ClientAddressTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ClientAddressQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ClientAddressTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ClientAddressTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the client_address table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ClientAddressQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a ClientAddress or Criteria object.
     *
     * @param mixed               $criteria Criteria or ClientAddress object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientAddressTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from ClientAddress object
        }

        if ($criteria->containsKey(ClientAddressTableMap::COL_ID) && $criteria->keyContainsValue(ClientAddressTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ClientAddressTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ClientAddressQuery::create()->mergeWith($criteria);

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

} // ClientAddressTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ClientAddressTableMap::buildTableMap();
