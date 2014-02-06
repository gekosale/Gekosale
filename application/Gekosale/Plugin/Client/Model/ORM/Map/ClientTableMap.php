<?php

namespace Gekosale\Plugin\Client\Model\ORM\Map;

use Gekosale\Plugin\Client\Model\ORM\Client;
use Gekosale\Plugin\Client\Model\ORM\ClientQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\Map\MissingCartTableMap;
use Gekosale\Plugin\Wishlist\Model\ORM\Map\WishlistTableMap;
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
 * This class defines the structure of the 'client' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ClientTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Client.Model.ORM.Map.ClientTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'client';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Client';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Client.Model.ORM.Client';

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
    const COL_ID = 'client.ID';

    /**
     * the column name for the LOGIN field
     */
    const COL_LOGIN = 'client.LOGIN';

    /**
     * the column name for the PASSWORD field
     */
    const COL_PASSWORD = 'client.PASSWORD';

    /**
     * the column name for the DISABLED field
     */
    const COL_DISABLED = 'client.DISABLED';

    /**
     * the column name for the SHOP_ID field
     */
    const COL_SHOP_ID = 'client.SHOP_ID';

    /**
     * the column name for the ACTIVE_LINK field
     */
    const COL_ACTIVE_LINK = 'client.ACTIVE_LINK';

    /**
     * the column name for the CLIENT_TYPE field
     */
    const COL_CLIENT_TYPE = 'client.CLIENT_TYPE';

    /**
     * the column name for the AUTO_ASSIGN field
     */
    const COL_AUTO_ASSIGN = 'client.AUTO_ASSIGN';

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
        self::TYPE_PHPNAME       => array('Id', 'Login', 'Password', 'IsDisabled', 'ShopId', 'ActiveLink', 'ClientType', 'AutoAssign', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'login', 'password', 'isDisabled', 'shopId', 'activeLink', 'clientType', 'autoAssign', ),
        self::TYPE_COLNAME       => array(ClientTableMap::COL_ID, ClientTableMap::COL_LOGIN, ClientTableMap::COL_PASSWORD, ClientTableMap::COL_DISABLED, ClientTableMap::COL_SHOP_ID, ClientTableMap::COL_ACTIVE_LINK, ClientTableMap::COL_CLIENT_TYPE, ClientTableMap::COL_AUTO_ASSIGN, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_LOGIN', 'COL_PASSWORD', 'COL_DISABLED', 'COL_SHOP_ID', 'COL_ACTIVE_LINK', 'COL_CLIENT_TYPE', 'COL_AUTO_ASSIGN', ),
        self::TYPE_FIELDNAME     => array('id', 'login', 'password', 'disabled', 'shop_id', 'active_link', 'client_type', 'auto_assign', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Login' => 1, 'Password' => 2, 'IsDisabled' => 3, 'ShopId' => 4, 'ActiveLink' => 5, 'ClientType' => 6, 'AutoAssign' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'login' => 1, 'password' => 2, 'isDisabled' => 3, 'shopId' => 4, 'activeLink' => 5, 'clientType' => 6, 'autoAssign' => 7, ),
        self::TYPE_COLNAME       => array(ClientTableMap::COL_ID => 0, ClientTableMap::COL_LOGIN => 1, ClientTableMap::COL_PASSWORD => 2, ClientTableMap::COL_DISABLED => 3, ClientTableMap::COL_SHOP_ID => 4, ClientTableMap::COL_ACTIVE_LINK => 5, ClientTableMap::COL_CLIENT_TYPE => 6, ClientTableMap::COL_AUTO_ASSIGN => 7, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_LOGIN' => 1, 'COL_PASSWORD' => 2, 'COL_DISABLED' => 3, 'COL_SHOP_ID' => 4, 'COL_ACTIVE_LINK' => 5, 'COL_CLIENT_TYPE' => 6, 'COL_AUTO_ASSIGN' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'login' => 1, 'password' => 2, 'disabled' => 3, 'shop_id' => 4, 'active_link' => 5, 'client_type' => 6, 'auto_assign' => 7, ),
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
        $this->setName('client');
        $this->setPhpName('Client');
        $this->setClassName('\\Gekosale\\Plugin\\Client\\Model\\ORM\\Client');
        $this->setPackage('Gekosale.Plugin.Client.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('LOGIN', 'Login', 'VARCHAR', true, 128, null);
        $this->addColumn('PASSWORD', 'Password', 'VARCHAR', true, 128, null);
        $this->addColumn('DISABLED', 'IsDisabled', 'INTEGER', true, 10, 1);
        $this->addForeignKey('SHOP_ID', 'ShopId', 'INTEGER', 'shop', 'ID', false, 10, null);
        $this->addColumn('ACTIVE_LINK', 'ActiveLink', 'VARCHAR', false, 255, null);
        $this->addColumn('CLIENT_TYPE', 'ClientType', 'INTEGER', false, null, 1);
        $this->addColumn('AUTO_ASSIGN', 'AutoAssign', 'INTEGER', true, null, 1);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Shop', '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop', RelationMap::MANY_TO_ONE, array('shop_id' => 'id', ), null, null);
        $this->addRelation('ClientAddress', '\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientAddress', RelationMap::ONE_TO_MANY, array('id' => 'client_id', ), 'CASCADE', null, 'ClientAddresses');
        $this->addRelation('ClientData', '\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientData', RelationMap::ONE_TO_MANY, array('id' => 'client_id', ), 'CASCADE', null, 'ClientDatas');
        $this->addRelation('MissingCart', '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCart', RelationMap::ONE_TO_MANY, array('id' => 'client_id', ), 'CASCADE', null, 'MissingCarts');
        $this->addRelation('Wishlist', '\\Gekosale\\Plugin\\Wishlist\\Model\\ORM\\Wishlist', RelationMap::ONE_TO_MANY, array('id' => 'client_id', ), 'CASCADE', null, 'Wishlists');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to client     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                ClientAddressTableMap::clearInstancePool();
                ClientDataTableMap::clearInstancePool();
                MissingCartTableMap::clearInstancePool();
                WishlistTableMap::clearInstancePool();
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
        return $withPrefix ? ClientTableMap::CLASS_DEFAULT : ClientTableMap::OM_CLASS;
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
     * @return array (Client object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ClientTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ClientTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ClientTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ClientTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ClientTableMap::addInstanceToPool($obj, $key);
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
            $key = ClientTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ClientTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ClientTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ClientTableMap::COL_ID);
            $criteria->addSelectColumn(ClientTableMap::COL_LOGIN);
            $criteria->addSelectColumn(ClientTableMap::COL_PASSWORD);
            $criteria->addSelectColumn(ClientTableMap::COL_DISABLED);
            $criteria->addSelectColumn(ClientTableMap::COL_SHOP_ID);
            $criteria->addSelectColumn(ClientTableMap::COL_ACTIVE_LINK);
            $criteria->addSelectColumn(ClientTableMap::COL_CLIENT_TYPE);
            $criteria->addSelectColumn(ClientTableMap::COL_AUTO_ASSIGN);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.LOGIN');
            $criteria->addSelectColumn($alias . '.PASSWORD');
            $criteria->addSelectColumn($alias . '.DISABLED');
            $criteria->addSelectColumn($alias . '.SHOP_ID');
            $criteria->addSelectColumn($alias . '.ACTIVE_LINK');
            $criteria->addSelectColumn($alias . '.CLIENT_TYPE');
            $criteria->addSelectColumn($alias . '.AUTO_ASSIGN');
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
        return Propel::getServiceContainer()->getDatabaseMap(ClientTableMap::DATABASE_NAME)->getTable(ClientTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ClientTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ClientTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ClientTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Client or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Client object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Client\Model\ORM\Client) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ClientTableMap::DATABASE_NAME);
            $criteria->add(ClientTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ClientQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ClientTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ClientTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the client table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ClientQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Client or Criteria object.
     *
     * @param mixed               $criteria Criteria or Client object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Client object
        }

        if ($criteria->containsKey(ClientTableMap::COL_ID) && $criteria->keyContainsValue(ClientTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ClientTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ClientQuery::create()->mergeWith($criteria);

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

} // ClientTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ClientTableMap::buildTableMap();
