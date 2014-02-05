<?php

namespace Gekosale\Plugin\Client\Model\ORM\Map;

use Gekosale\Plugin\Client\Model\ORM\ClientData;
use Gekosale\Plugin\Client\Model\ORM\ClientDataQuery;
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
 * This class defines the structure of the 'client_data' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ClientDataTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Client.Model.ORM.Map.ClientDataTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'client_data';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientData';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Client.Model.ORM.ClientData';

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
    const COL_ID = 'client_data.ID';

    /**
     * the column name for the FIRSTNAME field
     */
    const COL_FIRSTNAME = 'client_data.FIRSTNAME';

    /**
     * the column name for the SURNAME field
     */
    const COL_SURNAME = 'client_data.SURNAME';

    /**
     * the column name for the EMAIL field
     */
    const COL_EMAIL = 'client_data.EMAIL';

    /**
     * the column name for the DESCRIPTION field
     */
    const COL_DESCRIPTION = 'client_data.DESCRIPTION';

    /**
     * the column name for the PHONE field
     */
    const COL_PHONE = 'client_data.PHONE';

    /**
     * the column name for the PHONE2 field
     */
    const COL_PHONE2 = 'client_data.PHONE2';

    /**
     * the column name for the CLIENT_GROUP_ID field
     */
    const COL_CLIENT_GROUP_ID = 'client_data.CLIENT_GROUP_ID';

    /**
     * the column name for the CLIENT_ID field
     */
    const COL_CLIENT_ID = 'client_data.CLIENT_ID';

    /**
     * the column name for the LAST_LOGGED field
     */
    const COL_LAST_LOGGED = 'client_data.LAST_LOGGED';

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
        self::TYPE_PHPNAME       => array('Id', 'Firstname', 'Surname', 'Email', 'Description', 'Phone', 'Phone2', 'ClientGroupId', 'ClientId', 'LastLogged', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'firstname', 'surname', 'email', 'description', 'phone', 'phone2', 'clientGroupId', 'clientId', 'lastLogged', ),
        self::TYPE_COLNAME       => array(ClientDataTableMap::COL_ID, ClientDataTableMap::COL_FIRSTNAME, ClientDataTableMap::COL_SURNAME, ClientDataTableMap::COL_EMAIL, ClientDataTableMap::COL_DESCRIPTION, ClientDataTableMap::COL_PHONE, ClientDataTableMap::COL_PHONE2, ClientDataTableMap::COL_CLIENT_GROUP_ID, ClientDataTableMap::COL_CLIENT_ID, ClientDataTableMap::COL_LAST_LOGGED, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_FIRSTNAME', 'COL_SURNAME', 'COL_EMAIL', 'COL_DESCRIPTION', 'COL_PHONE', 'COL_PHONE2', 'COL_CLIENT_GROUP_ID', 'COL_CLIENT_ID', 'COL_LAST_LOGGED', ),
        self::TYPE_FIELDNAME     => array('id', 'firstname', 'surname', 'email', 'description', 'phone', 'phone2', 'client_group_id', 'client_id', 'last_logged', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Firstname' => 1, 'Surname' => 2, 'Email' => 3, 'Description' => 4, 'Phone' => 5, 'Phone2' => 6, 'ClientGroupId' => 7, 'ClientId' => 8, 'LastLogged' => 9, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'firstname' => 1, 'surname' => 2, 'email' => 3, 'description' => 4, 'phone' => 5, 'phone2' => 6, 'clientGroupId' => 7, 'clientId' => 8, 'lastLogged' => 9, ),
        self::TYPE_COLNAME       => array(ClientDataTableMap::COL_ID => 0, ClientDataTableMap::COL_FIRSTNAME => 1, ClientDataTableMap::COL_SURNAME => 2, ClientDataTableMap::COL_EMAIL => 3, ClientDataTableMap::COL_DESCRIPTION => 4, ClientDataTableMap::COL_PHONE => 5, ClientDataTableMap::COL_PHONE2 => 6, ClientDataTableMap::COL_CLIENT_GROUP_ID => 7, ClientDataTableMap::COL_CLIENT_ID => 8, ClientDataTableMap::COL_LAST_LOGGED => 9, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_FIRSTNAME' => 1, 'COL_SURNAME' => 2, 'COL_EMAIL' => 3, 'COL_DESCRIPTION' => 4, 'COL_PHONE' => 5, 'COL_PHONE2' => 6, 'COL_CLIENT_GROUP_ID' => 7, 'COL_CLIENT_ID' => 8, 'COL_LAST_LOGGED' => 9, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'firstname' => 1, 'surname' => 2, 'email' => 3, 'description' => 4, 'phone' => 5, 'phone2' => 6, 'client_group_id' => 7, 'client_id' => 8, 'last_logged' => 9, ),
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
        $this->setName('client_data');
        $this->setPhpName('ClientData');
        $this->setClassName('\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientData');
        $this->setPackage('Gekosale.Plugin.Client.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('FIRSTNAME', 'Firstname', 'BLOB', true, null, null);
        $this->addColumn('SURNAME', 'Surname', 'BLOB', true, null, null);
        $this->addColumn('EMAIL', 'Email', 'BLOB', true, null, null);
        $this->addColumn('DESCRIPTION', 'Description', 'BLOB', false, null, null);
        $this->addColumn('PHONE', 'Phone', 'BLOB', true, null, null);
        $this->addColumn('PHONE2', 'Phone2', 'BLOB', true, null, null);
        $this->addForeignKey('CLIENT_GROUP_ID', 'ClientGroupId', 'INTEGER', 'client_group', 'ID', false, 10, null);
        $this->addForeignKey('CLIENT_ID', 'ClientId', 'INTEGER', 'client', 'ID', true, 10, null);
        $this->addColumn('LAST_LOGGED', 'LastLogged', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ClientGroup', '\\Gekosale\\Plugin\\ClientGroup\\Model\\ORM\\ClientGroup', RelationMap::MANY_TO_ONE, array('client_group_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('Client', '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Client', RelationMap::MANY_TO_ONE, array('client_id' => 'id', ), 'CASCADE', null);
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
        return $withPrefix ? ClientDataTableMap::CLASS_DEFAULT : ClientDataTableMap::OM_CLASS;
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
     * @return array (ClientData object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ClientDataTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ClientDataTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ClientDataTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ClientDataTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ClientDataTableMap::addInstanceToPool($obj, $key);
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
            $key = ClientDataTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ClientDataTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ClientDataTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ClientDataTableMap::COL_ID);
            $criteria->addSelectColumn(ClientDataTableMap::COL_FIRSTNAME);
            $criteria->addSelectColumn(ClientDataTableMap::COL_SURNAME);
            $criteria->addSelectColumn(ClientDataTableMap::COL_EMAIL);
            $criteria->addSelectColumn(ClientDataTableMap::COL_DESCRIPTION);
            $criteria->addSelectColumn(ClientDataTableMap::COL_PHONE);
            $criteria->addSelectColumn(ClientDataTableMap::COL_PHONE2);
            $criteria->addSelectColumn(ClientDataTableMap::COL_CLIENT_GROUP_ID);
            $criteria->addSelectColumn(ClientDataTableMap::COL_CLIENT_ID);
            $criteria->addSelectColumn(ClientDataTableMap::COL_LAST_LOGGED);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.FIRSTNAME');
            $criteria->addSelectColumn($alias . '.SURNAME');
            $criteria->addSelectColumn($alias . '.EMAIL');
            $criteria->addSelectColumn($alias . '.DESCRIPTION');
            $criteria->addSelectColumn($alias . '.PHONE');
            $criteria->addSelectColumn($alias . '.PHONE2');
            $criteria->addSelectColumn($alias . '.CLIENT_GROUP_ID');
            $criteria->addSelectColumn($alias . '.CLIENT_ID');
            $criteria->addSelectColumn($alias . '.LAST_LOGGED');
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
        return Propel::getServiceContainer()->getDatabaseMap(ClientDataTableMap::DATABASE_NAME)->getTable(ClientDataTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ClientDataTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ClientDataTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ClientDataTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a ClientData or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ClientData object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientDataTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Client\Model\ORM\ClientData) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ClientDataTableMap::DATABASE_NAME);
            $criteria->add(ClientDataTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ClientDataQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ClientDataTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ClientDataTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the client_data table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ClientDataQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a ClientData or Criteria object.
     *
     * @param mixed               $criteria Criteria or ClientData object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientDataTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from ClientData object
        }

        if ($criteria->containsKey(ClientDataTableMap::COL_ID) && $criteria->keyContainsValue(ClientDataTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ClientDataTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ClientDataQuery::create()->mergeWith($criteria);

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

} // ClientDataTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ClientDataTableMap::buildTableMap();
