<?php

namespace Gekosale\Plugin\Settings\Model\ORM\Map;

use Gekosale\Plugin\Settings\Model\ORM\SettingsMailer;
use Gekosale\Plugin\Settings\Model\ORM\SettingsMailerQuery;
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
 * This class defines the structure of the 'settings_mailer' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class SettingsMailerTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Settings.Model.ORM.Map.SettingsMailerTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'settings_mailer';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Settings\\Model\\ORM\\SettingsMailer';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Settings.Model.ORM.SettingsMailer';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 11;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 11;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'settings_mailer.ID';

    /**
     * the column name for the MAILER field
     */
    const COL_MAILER = 'settings_mailer.MAILER';

    /**
     * the column name for the FROM_NAME field
     */
    const COL_FROM_NAME = 'settings_mailer.FROM_NAME';

    /**
     * the column name for the FROM_EMAIL field
     */
    const COL_FROM_EMAIL = 'settings_mailer.FROM_EMAIL';

    /**
     * the column name for the SERVER field
     */
    const COL_SERVER = 'settings_mailer.SERVER';

    /**
     * the column name for the PORT field
     */
    const COL_PORT = 'settings_mailer.PORT';

    /**
     * the column name for the SMTP_SECURE field
     */
    const COL_SMTP_SECURE = 'settings_mailer.SMTP_SECURE';

    /**
     * the column name for the SMTP_AUTH field
     */
    const COL_SMTP_AUTH = 'settings_mailer.SMTP_AUTH';

    /**
     * the column name for the SMTP_USERNAME field
     */
    const COL_SMTP_USERNAME = 'settings_mailer.SMTP_USERNAME';

    /**
     * the column name for the SMTP_PASSWORD field
     */
    const COL_SMTP_PASSWORD = 'settings_mailer.SMTP_PASSWORD';

    /**
     * the column name for the SHOP_ID field
     */
    const COL_SHOP_ID = 'settings_mailer.SHOP_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'Mailer', 'FromName', 'FromEmail', 'Server', 'Port', 'SmtpSecure', 'SmtpAuth', 'SmtpUsername', 'SmtpPassword', 'ShopId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'mailer', 'fromName', 'fromEmail', 'server', 'port', 'smtpSecure', 'smtpAuth', 'smtpUsername', 'smtpPassword', 'shopId', ),
        self::TYPE_COLNAME       => array(SettingsMailerTableMap::COL_ID, SettingsMailerTableMap::COL_MAILER, SettingsMailerTableMap::COL_FROM_NAME, SettingsMailerTableMap::COL_FROM_EMAIL, SettingsMailerTableMap::COL_SERVER, SettingsMailerTableMap::COL_PORT, SettingsMailerTableMap::COL_SMTP_SECURE, SettingsMailerTableMap::COL_SMTP_AUTH, SettingsMailerTableMap::COL_SMTP_USERNAME, SettingsMailerTableMap::COL_SMTP_PASSWORD, SettingsMailerTableMap::COL_SHOP_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_MAILER', 'COL_FROM_NAME', 'COL_FROM_EMAIL', 'COL_SERVER', 'COL_PORT', 'COL_SMTP_SECURE', 'COL_SMTP_AUTH', 'COL_SMTP_USERNAME', 'COL_SMTP_PASSWORD', 'COL_SHOP_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'mailer', 'from_name', 'from_email', 'server', 'port', 'smtp_secure', 'smtp_auth', 'smtp_username', 'smtp_password', 'shop_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Mailer' => 1, 'FromName' => 2, 'FromEmail' => 3, 'Server' => 4, 'Port' => 5, 'SmtpSecure' => 6, 'SmtpAuth' => 7, 'SmtpUsername' => 8, 'SmtpPassword' => 9, 'ShopId' => 10, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'mailer' => 1, 'fromName' => 2, 'fromEmail' => 3, 'server' => 4, 'port' => 5, 'smtpSecure' => 6, 'smtpAuth' => 7, 'smtpUsername' => 8, 'smtpPassword' => 9, 'shopId' => 10, ),
        self::TYPE_COLNAME       => array(SettingsMailerTableMap::COL_ID => 0, SettingsMailerTableMap::COL_MAILER => 1, SettingsMailerTableMap::COL_FROM_NAME => 2, SettingsMailerTableMap::COL_FROM_EMAIL => 3, SettingsMailerTableMap::COL_SERVER => 4, SettingsMailerTableMap::COL_PORT => 5, SettingsMailerTableMap::COL_SMTP_SECURE => 6, SettingsMailerTableMap::COL_SMTP_AUTH => 7, SettingsMailerTableMap::COL_SMTP_USERNAME => 8, SettingsMailerTableMap::COL_SMTP_PASSWORD => 9, SettingsMailerTableMap::COL_SHOP_ID => 10, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_MAILER' => 1, 'COL_FROM_NAME' => 2, 'COL_FROM_EMAIL' => 3, 'COL_SERVER' => 4, 'COL_PORT' => 5, 'COL_SMTP_SECURE' => 6, 'COL_SMTP_AUTH' => 7, 'COL_SMTP_USERNAME' => 8, 'COL_SMTP_PASSWORD' => 9, 'COL_SHOP_ID' => 10, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'mailer' => 1, 'from_name' => 2, 'from_email' => 3, 'server' => 4, 'port' => 5, 'smtp_secure' => 6, 'smtp_auth' => 7, 'smtp_username' => 8, 'smtp_password' => 9, 'shop_id' => 10, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
        $this->setName('settings_mailer');
        $this->setPhpName('SettingsMailer');
        $this->setClassName('\\Gekosale\\Plugin\\Settings\\Model\\ORM\\SettingsMailer');
        $this->setPackage('Gekosale.Plugin.Settings.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('MAILER', 'Mailer', 'VARCHAR', false, 45, null);
        $this->addColumn('FROM_NAME', 'FromName', 'VARCHAR', false, 255, null);
        $this->addColumn('FROM_EMAIL', 'FromEmail', 'VARCHAR', false, 255, null);
        $this->addColumn('SERVER', 'Server', 'VARCHAR', false, 255, null);
        $this->addColumn('PORT', 'Port', 'INTEGER', false, null, null);
        $this->addColumn('SMTP_SECURE', 'SmtpSecure', 'VARCHAR', false, 3, null);
        $this->addColumn('SMTP_AUTH', 'SmtpAuth', 'INTEGER', false, null, null);
        $this->addColumn('SMTP_USERNAME', 'SmtpUsername', 'VARCHAR', false, 255, null);
        $this->addColumn('SMTP_PASSWORD', 'SmtpPassword', 'VARCHAR', false, 255, null);
        $this->addColumn('SHOP_ID', 'ShopId', 'INTEGER', true, null, null);
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
        return $withPrefix ? SettingsMailerTableMap::CLASS_DEFAULT : SettingsMailerTableMap::OM_CLASS;
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
     * @return array (SettingsMailer object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = SettingsMailerTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = SettingsMailerTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + SettingsMailerTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SettingsMailerTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            SettingsMailerTableMap::addInstanceToPool($obj, $key);
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
            $key = SettingsMailerTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = SettingsMailerTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SettingsMailerTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_ID);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_MAILER);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_FROM_NAME);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_FROM_EMAIL);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_SERVER);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_PORT);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_SMTP_SECURE);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_SMTP_AUTH);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_SMTP_USERNAME);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_SMTP_PASSWORD);
            $criteria->addSelectColumn(SettingsMailerTableMap::COL_SHOP_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.MAILER');
            $criteria->addSelectColumn($alias . '.FROM_NAME');
            $criteria->addSelectColumn($alias . '.FROM_EMAIL');
            $criteria->addSelectColumn($alias . '.SERVER');
            $criteria->addSelectColumn($alias . '.PORT');
            $criteria->addSelectColumn($alias . '.SMTP_SECURE');
            $criteria->addSelectColumn($alias . '.SMTP_AUTH');
            $criteria->addSelectColumn($alias . '.SMTP_USERNAME');
            $criteria->addSelectColumn($alias . '.SMTP_PASSWORD');
            $criteria->addSelectColumn($alias . '.SHOP_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(SettingsMailerTableMap::DATABASE_NAME)->getTable(SettingsMailerTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(SettingsMailerTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(SettingsMailerTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new SettingsMailerTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a SettingsMailer or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or SettingsMailer object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsMailerTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Settings\Model\ORM\SettingsMailer) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SettingsMailerTableMap::DATABASE_NAME);
            $criteria->add(SettingsMailerTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = SettingsMailerQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { SettingsMailerTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { SettingsMailerTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the settings_mailer table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return SettingsMailerQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a SettingsMailer or Criteria object.
     *
     * @param mixed               $criteria Criteria or SettingsMailer object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsMailerTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from SettingsMailer object
        }

        if ($criteria->containsKey(SettingsMailerTableMap::COL_ID) && $criteria->keyContainsValue(SettingsMailerTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SettingsMailerTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = SettingsMailerQuery::create()->mergeWith($criteria);

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

} // SettingsMailerTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
SettingsMailerTableMap::buildTableMap();
