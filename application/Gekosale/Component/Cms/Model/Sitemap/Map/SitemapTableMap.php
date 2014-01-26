<?php

namespace Gekosale\Component\Cms\Model\Sitemap\Map;

use Gekosale\Component\Cms\Model\Sitemap\Sitemap;
use Gekosale\Component\Cms\Model\Sitemap\SitemapQuery;
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
 * This class defines the structure of the 'sitemap' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class SitemapTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Component.Cms.Model.Sitemap.Map.SitemapTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'sitemap';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Component\\Cms\\Model\\Sitemap\\Sitemap';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Component.Cms.Model.Sitemap.Sitemap';

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
    const ID = 'sitemap.ID';

    /**
     * the column name for the NAME field
     */
    const NAME = 'sitemap.NAME';

    /**
     * the column name for the PUBLISH_CATEGORIES field
     */
    const PUBLISH_CATEGORIES = 'sitemap.PUBLISH_CATEGORIES';

    /**
     * the column name for the PRIORITY_CATEGORIES field
     */
    const PRIORITY_CATEGORIES = 'sitemap.PRIORITY_CATEGORIES';

    /**
     * the column name for the PUBLISH_PRODUCTS field
     */
    const PUBLISH_PRODUCTS = 'sitemap.PUBLISH_PRODUCTS';

    /**
     * the column name for the PRIORITY_PRODUCTS field
     */
    const PRIORITY_PRODUCTS = 'sitemap.PRIORITY_PRODUCTS';

    /**
     * the column name for the PUBLISH_PRODUCERS field
     */
    const PUBLISH_PRODUCERS = 'sitemap.PUBLISH_PRODUCERS';

    /**
     * the column name for the PRIORITY_PRODUCERS field
     */
    const PRIORITY_PRODUCERS = 'sitemap.PRIORITY_PRODUCERS';

    /**
     * the column name for the PUBLISH_NEWS field
     */
    const PUBLISH_NEWS = 'sitemap.PUBLISH_NEWS';

    /**
     * the column name for the PRIORITY_NEWS field
     */
    const PRIORITY_NEWS = 'sitemap.PRIORITY_NEWS';

    /**
     * the column name for the PUBLISH_PAGES field
     */
    const PUBLISH_PAGES = 'sitemap.PUBLISH_PAGES';

    /**
     * the column name for the PRIORITY_PAGES field
     */
    const PRIORITY_PAGES = 'sitemap.PRIORITY_PAGES';

    /**
     * the column name for the ADD_DATE field
     */
    const ADD_DATE = 'sitemap.ADD_DATE';

    /**
     * the column name for the LAST_UPDATE field
     */
    const LAST_UPDATE = 'sitemap.LAST_UPDATE';

    /**
     * the column name for the PING_SERVER field
     */
    const PING_SERVER = 'sitemap.PING_SERVER';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'PublishCategories', 'PriorityCategories', 'PublishProducts', 'PriorityProducts', 'PublishProducers', 'PriorityProducers', 'PublishNews', 'PriorityNews', 'PublishPages', 'PriorityPages', 'AddDate', 'LastUpdate', 'PingServer', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'publishCategories', 'priorityCategories', 'publishProducts', 'priorityProducts', 'publishProducers', 'priorityProducers', 'publishNews', 'priorityNews', 'publishPages', 'priorityPages', 'addDate', 'lastUpdate', 'pingServer', ),
        self::TYPE_COLNAME       => array(SitemapTableMap::ID, SitemapTableMap::NAME, SitemapTableMap::PUBLISH_CATEGORIES, SitemapTableMap::PRIORITY_CATEGORIES, SitemapTableMap::PUBLISH_PRODUCTS, SitemapTableMap::PRIORITY_PRODUCTS, SitemapTableMap::PUBLISH_PRODUCERS, SitemapTableMap::PRIORITY_PRODUCERS, SitemapTableMap::PUBLISH_NEWS, SitemapTableMap::PRIORITY_NEWS, SitemapTableMap::PUBLISH_PAGES, SitemapTableMap::PRIORITY_PAGES, SitemapTableMap::ADD_DATE, SitemapTableMap::LAST_UPDATE, SitemapTableMap::PING_SERVER, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'NAME', 'PUBLISH_CATEGORIES', 'PRIORITY_CATEGORIES', 'PUBLISH_PRODUCTS', 'PRIORITY_PRODUCTS', 'PUBLISH_PRODUCERS', 'PRIORITY_PRODUCERS', 'PUBLISH_NEWS', 'PRIORITY_NEWS', 'PUBLISH_PAGES', 'PRIORITY_PAGES', 'ADD_DATE', 'LAST_UPDATE', 'PING_SERVER', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'publish_categories', 'priority_categories', 'publish_products', 'priority_products', 'publish_producers', 'priority_producers', 'publish_news', 'priority_news', 'publish_pages', 'priority_pages', 'add_date', 'last_update', 'ping_server', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'PublishCategories' => 2, 'PriorityCategories' => 3, 'PublishProducts' => 4, 'PriorityProducts' => 5, 'PublishProducers' => 6, 'PriorityProducers' => 7, 'PublishNews' => 8, 'PriorityNews' => 9, 'PublishPages' => 10, 'PriorityPages' => 11, 'AddDate' => 12, 'LastUpdate' => 13, 'PingServer' => 14, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'publishCategories' => 2, 'priorityCategories' => 3, 'publishProducts' => 4, 'priorityProducts' => 5, 'publishProducers' => 6, 'priorityProducers' => 7, 'publishNews' => 8, 'priorityNews' => 9, 'publishPages' => 10, 'priorityPages' => 11, 'addDate' => 12, 'lastUpdate' => 13, 'pingServer' => 14, ),
        self::TYPE_COLNAME       => array(SitemapTableMap::ID => 0, SitemapTableMap::NAME => 1, SitemapTableMap::PUBLISH_CATEGORIES => 2, SitemapTableMap::PRIORITY_CATEGORIES => 3, SitemapTableMap::PUBLISH_PRODUCTS => 4, SitemapTableMap::PRIORITY_PRODUCTS => 5, SitemapTableMap::PUBLISH_PRODUCERS => 6, SitemapTableMap::PRIORITY_PRODUCERS => 7, SitemapTableMap::PUBLISH_NEWS => 8, SitemapTableMap::PRIORITY_NEWS => 9, SitemapTableMap::PUBLISH_PAGES => 10, SitemapTableMap::PRIORITY_PAGES => 11, SitemapTableMap::ADD_DATE => 12, SitemapTableMap::LAST_UPDATE => 13, SitemapTableMap::PING_SERVER => 14, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'NAME' => 1, 'PUBLISH_CATEGORIES' => 2, 'PRIORITY_CATEGORIES' => 3, 'PUBLISH_PRODUCTS' => 4, 'PRIORITY_PRODUCTS' => 5, 'PUBLISH_PRODUCERS' => 6, 'PRIORITY_PRODUCERS' => 7, 'PUBLISH_NEWS' => 8, 'PRIORITY_NEWS' => 9, 'PUBLISH_PAGES' => 10, 'PRIORITY_PAGES' => 11, 'ADD_DATE' => 12, 'LAST_UPDATE' => 13, 'PING_SERVER' => 14, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'publish_categories' => 2, 'priority_categories' => 3, 'publish_products' => 4, 'priority_products' => 5, 'publish_producers' => 6, 'priority_producers' => 7, 'publish_news' => 8, 'priority_news' => 9, 'publish_pages' => 10, 'priority_pages' => 11, 'add_date' => 12, 'last_update' => 13, 'ping_server' => 14, ),
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
        $this->setName('sitemap');
        $this->setPhpName('Sitemap');
        $this->setClassName('\\Gekosale\\Component\\Cms\\Model\\Sitemap\\Sitemap');
        $this->setPackage('Gekosale.Component.Cms.Model.Sitemap');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 255, null);
        $this->addColumn('PUBLISH_CATEGORIES', 'PublishCategories', 'INTEGER', true, 10, 1);
        $this->addColumn('PRIORITY_CATEGORIES', 'PriorityCategories', 'CHAR', false, 3, null);
        $this->addColumn('PUBLISH_PRODUCTS', 'PublishProducts', 'INTEGER', true, 10, 1);
        $this->addColumn('PRIORITY_PRODUCTS', 'PriorityProducts', 'CHAR', false, 3, null);
        $this->addColumn('PUBLISH_PRODUCERS', 'PublishProducers', 'INTEGER', true, 10, 1);
        $this->addColumn('PRIORITY_PRODUCERS', 'PriorityProducers', 'CHAR', false, 3, null);
        $this->addColumn('PUBLISH_NEWS', 'PublishNews', 'INTEGER', true, 10, 1);
        $this->addColumn('PRIORITY_NEWS', 'PriorityNews', 'CHAR', false, 3, null);
        $this->addColumn('PUBLISH_PAGES', 'PublishPages', 'INTEGER', true, 10, 1);
        $this->addColumn('PRIORITY_PAGES', 'PriorityPages', 'CHAR', false, 3, null);
        $this->addColumn('ADD_DATE', 'AddDate', 'TIMESTAMP', true, null, 'CURRENT_TIMESTAMP');
        $this->addColumn('LAST_UPDATE', 'LastUpdate', 'TIMESTAMP', false, null, null);
        $this->addColumn('PING_SERVER', 'PingServer', 'VARCHAR', true, 255, null);
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
        return $withPrefix ? SitemapTableMap::CLASS_DEFAULT : SitemapTableMap::OM_CLASS;
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
     * @return array (Sitemap object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = SitemapTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = SitemapTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + SitemapTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SitemapTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            SitemapTableMap::addInstanceToPool($obj, $key);
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
            $key = SitemapTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = SitemapTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SitemapTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(SitemapTableMap::ID);
            $criteria->addSelectColumn(SitemapTableMap::NAME);
            $criteria->addSelectColumn(SitemapTableMap::PUBLISH_CATEGORIES);
            $criteria->addSelectColumn(SitemapTableMap::PRIORITY_CATEGORIES);
            $criteria->addSelectColumn(SitemapTableMap::PUBLISH_PRODUCTS);
            $criteria->addSelectColumn(SitemapTableMap::PRIORITY_PRODUCTS);
            $criteria->addSelectColumn(SitemapTableMap::PUBLISH_PRODUCERS);
            $criteria->addSelectColumn(SitemapTableMap::PRIORITY_PRODUCERS);
            $criteria->addSelectColumn(SitemapTableMap::PUBLISH_NEWS);
            $criteria->addSelectColumn(SitemapTableMap::PRIORITY_NEWS);
            $criteria->addSelectColumn(SitemapTableMap::PUBLISH_PAGES);
            $criteria->addSelectColumn(SitemapTableMap::PRIORITY_PAGES);
            $criteria->addSelectColumn(SitemapTableMap::ADD_DATE);
            $criteria->addSelectColumn(SitemapTableMap::LAST_UPDATE);
            $criteria->addSelectColumn(SitemapTableMap::PING_SERVER);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.PUBLISH_CATEGORIES');
            $criteria->addSelectColumn($alias . '.PRIORITY_CATEGORIES');
            $criteria->addSelectColumn($alias . '.PUBLISH_PRODUCTS');
            $criteria->addSelectColumn($alias . '.PRIORITY_PRODUCTS');
            $criteria->addSelectColumn($alias . '.PUBLISH_PRODUCERS');
            $criteria->addSelectColumn($alias . '.PRIORITY_PRODUCERS');
            $criteria->addSelectColumn($alias . '.PUBLISH_NEWS');
            $criteria->addSelectColumn($alias . '.PRIORITY_NEWS');
            $criteria->addSelectColumn($alias . '.PUBLISH_PAGES');
            $criteria->addSelectColumn($alias . '.PRIORITY_PAGES');
            $criteria->addSelectColumn($alias . '.ADD_DATE');
            $criteria->addSelectColumn($alias . '.LAST_UPDATE');
            $criteria->addSelectColumn($alias . '.PING_SERVER');
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
        return Propel::getServiceContainer()->getDatabaseMap(SitemapTableMap::DATABASE_NAME)->getTable(SitemapTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(SitemapTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(SitemapTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new SitemapTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Sitemap or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Sitemap object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SitemapTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Component\Cms\Model\Sitemap\Sitemap) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SitemapTableMap::DATABASE_NAME);
            $criteria->add(SitemapTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = SitemapQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { SitemapTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { SitemapTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the sitemap table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return SitemapQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Sitemap or Criteria object.
     *
     * @param mixed               $criteria Criteria or Sitemap object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SitemapTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Sitemap object
        }

        if ($criteria->containsKey(SitemapTableMap::ID) && $criteria->keyContainsValue(SitemapTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SitemapTableMap::ID.')');
        }


        // Set the correct dbName
        $query = SitemapQuery::create()->mergeWith($criteria);

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

} // SitemapTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
SitemapTableMap::buildTableMap();
