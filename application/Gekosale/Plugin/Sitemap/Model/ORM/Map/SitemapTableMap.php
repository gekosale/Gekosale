<?php

namespace Gekosale\Plugin\Sitemap\Model\ORM\Map;

use Gekosale\Plugin\Sitemap\Model\ORM\Sitemap;
use Gekosale\Plugin\Sitemap\Model\ORM\SitemapQuery;
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
    const CLASS_NAME = 'Gekosale.Plugin.Sitemap.Model.ORM.Map.SitemapTableMap';

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
    const OM_CLASS = '\\Gekosale\\Plugin\\Sitemap\\Model\\ORM\\Sitemap';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Sitemap.Model.ORM.Sitemap';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 20;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 20;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'sitemap.ID';

    /**
     * the column name for the NAME field
     */
    const COL_NAME = 'sitemap.NAME';

    /**
     * the column name for the PUBLISH_CATEGORIES field
     */
    const COL_PUBLISH_CATEGORIES = 'sitemap.PUBLISH_CATEGORIES';

    /**
     * the column name for the PRIORITY_CATEGORIES field
     */
    const COL_PRIORITY_CATEGORIES = 'sitemap.PRIORITY_CATEGORIES';

    /**
     * the column name for the PUBLISH_PRODUCTS field
     */
    const COL_PUBLISH_PRODUCTS = 'sitemap.PUBLISH_PRODUCTS';

    /**
     * the column name for the PRIORITY_PRODUCTS field
     */
    const COL_PRIORITY_PRODUCTS = 'sitemap.PRIORITY_PRODUCTS';

    /**
     * the column name for the PUBLISH_PRODUCERS field
     */
    const COL_PUBLISH_PRODUCERS = 'sitemap.PUBLISH_PRODUCERS';

    /**
     * the column name for the PRIORITY_PRODUCERS field
     */
    const COL_PRIORITY_PRODUCERS = 'sitemap.PRIORITY_PRODUCERS';

    /**
     * the column name for the PUBLISH_NEWS field
     */
    const COL_PUBLISH_NEWS = 'sitemap.PUBLISH_NEWS';

    /**
     * the column name for the PRIORITY_NEWS field
     */
    const COL_PRIORITY_NEWS = 'sitemap.PRIORITY_NEWS';

    /**
     * the column name for the PUBLISH_PAGES field
     */
    const COL_PUBLISH_PAGES = 'sitemap.PUBLISH_PAGES';

    /**
     * the column name for the PRIORITY_PAGES field
     */
    const COL_PRIORITY_PAGES = 'sitemap.PRIORITY_PAGES';

    /**
     * the column name for the ADD_DATE field
     */
    const COL_ADD_DATE = 'sitemap.ADD_DATE';

    /**
     * the column name for the LAST_UPDATE field
     */
    const COL_LAST_UPDATE = 'sitemap.LAST_UPDATE';

    /**
     * the column name for the PING_SERVER field
     */
    const COL_PING_SERVER = 'sitemap.PING_SERVER';

    /**
     * the column name for the CHANGEFREQ_CATEGORIES field
     */
    const COL_CHANGEFREQ_CATEGORIES = 'sitemap.CHANGEFREQ_CATEGORIES';

    /**
     * the column name for the CHANGEFREQ_PRODUCTS field
     */
    const COL_CHANGEFREQ_PRODUCTS = 'sitemap.CHANGEFREQ_PRODUCTS';

    /**
     * the column name for the CHANGEFREQ_PRODUCERS field
     */
    const COL_CHANGEFREQ_PRODUCERS = 'sitemap.CHANGEFREQ_PRODUCERS';

    /**
     * the column name for the CHANGEFREQ_NEWS field
     */
    const COL_CHANGEFREQ_NEWS = 'sitemap.CHANGEFREQ_NEWS';

    /**
     * the column name for the CHANGEFREQ_PAGES field
     */
    const COL_CHANGEFREQ_PAGES = 'sitemap.CHANGEFREQ_PAGES';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'PublishCategories', 'PriorityCategories', 'PublishProducts', 'PriorityProducts', 'PublishProducers', 'PriorityProducers', 'PublishNews', 'PriorityNews', 'PublishPages', 'PriorityPages', 'AddDate', 'LastUpdate', 'PingServer', 'ChangefreqCategories', 'ChangefreqProducts', 'ChangefreqProducers', 'ChangefreqNews', 'ChangefreqPages', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'publishCategories', 'priorityCategories', 'publishProducts', 'priorityProducts', 'publishProducers', 'priorityProducers', 'publishNews', 'priorityNews', 'publishPages', 'priorityPages', 'addDate', 'lastUpdate', 'pingServer', 'changefreqCategories', 'changefreqProducts', 'changefreqProducers', 'changefreqNews', 'changefreqPages', ),
        self::TYPE_COLNAME       => array(SitemapTableMap::COL_ID, SitemapTableMap::COL_NAME, SitemapTableMap::COL_PUBLISH_CATEGORIES, SitemapTableMap::COL_PRIORITY_CATEGORIES, SitemapTableMap::COL_PUBLISH_PRODUCTS, SitemapTableMap::COL_PRIORITY_PRODUCTS, SitemapTableMap::COL_PUBLISH_PRODUCERS, SitemapTableMap::COL_PRIORITY_PRODUCERS, SitemapTableMap::COL_PUBLISH_NEWS, SitemapTableMap::COL_PRIORITY_NEWS, SitemapTableMap::COL_PUBLISH_PAGES, SitemapTableMap::COL_PRIORITY_PAGES, SitemapTableMap::COL_ADD_DATE, SitemapTableMap::COL_LAST_UPDATE, SitemapTableMap::COL_PING_SERVER, SitemapTableMap::COL_CHANGEFREQ_CATEGORIES, SitemapTableMap::COL_CHANGEFREQ_PRODUCTS, SitemapTableMap::COL_CHANGEFREQ_PRODUCERS, SitemapTableMap::COL_CHANGEFREQ_NEWS, SitemapTableMap::COL_CHANGEFREQ_PAGES, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_NAME', 'COL_PUBLISH_CATEGORIES', 'COL_PRIORITY_CATEGORIES', 'COL_PUBLISH_PRODUCTS', 'COL_PRIORITY_PRODUCTS', 'COL_PUBLISH_PRODUCERS', 'COL_PRIORITY_PRODUCERS', 'COL_PUBLISH_NEWS', 'COL_PRIORITY_NEWS', 'COL_PUBLISH_PAGES', 'COL_PRIORITY_PAGES', 'COL_ADD_DATE', 'COL_LAST_UPDATE', 'COL_PING_SERVER', 'COL_CHANGEFREQ_CATEGORIES', 'COL_CHANGEFREQ_PRODUCTS', 'COL_CHANGEFREQ_PRODUCERS', 'COL_CHANGEFREQ_NEWS', 'COL_CHANGEFREQ_PAGES', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'publish_categories', 'priority_categories', 'publish_products', 'priority_products', 'publish_producers', 'priority_producers', 'publish_news', 'priority_news', 'publish_pages', 'priority_pages', 'add_date', 'last_update', 'ping_server', 'changefreq_categories', 'changefreq_products', 'changefreq_producers', 'changefreq_news', 'changefreq_pages', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'PublishCategories' => 2, 'PriorityCategories' => 3, 'PublishProducts' => 4, 'PriorityProducts' => 5, 'PublishProducers' => 6, 'PriorityProducers' => 7, 'PublishNews' => 8, 'PriorityNews' => 9, 'PublishPages' => 10, 'PriorityPages' => 11, 'AddDate' => 12, 'LastUpdate' => 13, 'PingServer' => 14, 'ChangefreqCategories' => 15, 'ChangefreqProducts' => 16, 'ChangefreqProducers' => 17, 'ChangefreqNews' => 18, 'ChangefreqPages' => 19, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'publishCategories' => 2, 'priorityCategories' => 3, 'publishProducts' => 4, 'priorityProducts' => 5, 'publishProducers' => 6, 'priorityProducers' => 7, 'publishNews' => 8, 'priorityNews' => 9, 'publishPages' => 10, 'priorityPages' => 11, 'addDate' => 12, 'lastUpdate' => 13, 'pingServer' => 14, 'changefreqCategories' => 15, 'changefreqProducts' => 16, 'changefreqProducers' => 17, 'changefreqNews' => 18, 'changefreqPages' => 19, ),
        self::TYPE_COLNAME       => array(SitemapTableMap::COL_ID => 0, SitemapTableMap::COL_NAME => 1, SitemapTableMap::COL_PUBLISH_CATEGORIES => 2, SitemapTableMap::COL_PRIORITY_CATEGORIES => 3, SitemapTableMap::COL_PUBLISH_PRODUCTS => 4, SitemapTableMap::COL_PRIORITY_PRODUCTS => 5, SitemapTableMap::COL_PUBLISH_PRODUCERS => 6, SitemapTableMap::COL_PRIORITY_PRODUCERS => 7, SitemapTableMap::COL_PUBLISH_NEWS => 8, SitemapTableMap::COL_PRIORITY_NEWS => 9, SitemapTableMap::COL_PUBLISH_PAGES => 10, SitemapTableMap::COL_PRIORITY_PAGES => 11, SitemapTableMap::COL_ADD_DATE => 12, SitemapTableMap::COL_LAST_UPDATE => 13, SitemapTableMap::COL_PING_SERVER => 14, SitemapTableMap::COL_CHANGEFREQ_CATEGORIES => 15, SitemapTableMap::COL_CHANGEFREQ_PRODUCTS => 16, SitemapTableMap::COL_CHANGEFREQ_PRODUCERS => 17, SitemapTableMap::COL_CHANGEFREQ_NEWS => 18, SitemapTableMap::COL_CHANGEFREQ_PAGES => 19, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_NAME' => 1, 'COL_PUBLISH_CATEGORIES' => 2, 'COL_PRIORITY_CATEGORIES' => 3, 'COL_PUBLISH_PRODUCTS' => 4, 'COL_PRIORITY_PRODUCTS' => 5, 'COL_PUBLISH_PRODUCERS' => 6, 'COL_PRIORITY_PRODUCERS' => 7, 'COL_PUBLISH_NEWS' => 8, 'COL_PRIORITY_NEWS' => 9, 'COL_PUBLISH_PAGES' => 10, 'COL_PRIORITY_PAGES' => 11, 'COL_ADD_DATE' => 12, 'COL_LAST_UPDATE' => 13, 'COL_PING_SERVER' => 14, 'COL_CHANGEFREQ_CATEGORIES' => 15, 'COL_CHANGEFREQ_PRODUCTS' => 16, 'COL_CHANGEFREQ_PRODUCERS' => 17, 'COL_CHANGEFREQ_NEWS' => 18, 'COL_CHANGEFREQ_PAGES' => 19, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'publish_categories' => 2, 'priority_categories' => 3, 'publish_products' => 4, 'priority_products' => 5, 'publish_producers' => 6, 'priority_producers' => 7, 'publish_news' => 8, 'priority_news' => 9, 'publish_pages' => 10, 'priority_pages' => 11, 'add_date' => 12, 'last_update' => 13, 'ping_server' => 14, 'changefreq_categories' => 15, 'changefreq_products' => 16, 'changefreq_producers' => 17, 'changefreq_news' => 18, 'changefreq_pages' => 19, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
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
        $this->setClassName('\\Gekosale\\Plugin\\Sitemap\\Model\\ORM\\Sitemap');
        $this->setPackage('Gekosale.Plugin.Sitemap.Model.ORM');
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
        $this->addColumn('CHANGEFREQ_CATEGORIES', 'ChangefreqCategories', 'VARCHAR', true, 45, 'always');
        $this->addColumn('CHANGEFREQ_PRODUCTS', 'ChangefreqProducts', 'VARCHAR', true, 45, 'always');
        $this->addColumn('CHANGEFREQ_PRODUCERS', 'ChangefreqProducers', 'VARCHAR', true, 45, 'always');
        $this->addColumn('CHANGEFREQ_NEWS', 'ChangefreqNews', 'VARCHAR', true, 45, 'always');
        $this->addColumn('CHANGEFREQ_PAGES', 'ChangefreqPages', 'VARCHAR', true, 45, 'always');
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
            $criteria->addSelectColumn(SitemapTableMap::COL_ID);
            $criteria->addSelectColumn(SitemapTableMap::COL_NAME);
            $criteria->addSelectColumn(SitemapTableMap::COL_PUBLISH_CATEGORIES);
            $criteria->addSelectColumn(SitemapTableMap::COL_PRIORITY_CATEGORIES);
            $criteria->addSelectColumn(SitemapTableMap::COL_PUBLISH_PRODUCTS);
            $criteria->addSelectColumn(SitemapTableMap::COL_PRIORITY_PRODUCTS);
            $criteria->addSelectColumn(SitemapTableMap::COL_PUBLISH_PRODUCERS);
            $criteria->addSelectColumn(SitemapTableMap::COL_PRIORITY_PRODUCERS);
            $criteria->addSelectColumn(SitemapTableMap::COL_PUBLISH_NEWS);
            $criteria->addSelectColumn(SitemapTableMap::COL_PRIORITY_NEWS);
            $criteria->addSelectColumn(SitemapTableMap::COL_PUBLISH_PAGES);
            $criteria->addSelectColumn(SitemapTableMap::COL_PRIORITY_PAGES);
            $criteria->addSelectColumn(SitemapTableMap::COL_ADD_DATE);
            $criteria->addSelectColumn(SitemapTableMap::COL_LAST_UPDATE);
            $criteria->addSelectColumn(SitemapTableMap::COL_PING_SERVER);
            $criteria->addSelectColumn(SitemapTableMap::COL_CHANGEFREQ_CATEGORIES);
            $criteria->addSelectColumn(SitemapTableMap::COL_CHANGEFREQ_PRODUCTS);
            $criteria->addSelectColumn(SitemapTableMap::COL_CHANGEFREQ_PRODUCERS);
            $criteria->addSelectColumn(SitemapTableMap::COL_CHANGEFREQ_NEWS);
            $criteria->addSelectColumn(SitemapTableMap::COL_CHANGEFREQ_PAGES);
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
            $criteria->addSelectColumn($alias . '.CHANGEFREQ_CATEGORIES');
            $criteria->addSelectColumn($alias . '.CHANGEFREQ_PRODUCTS');
            $criteria->addSelectColumn($alias . '.CHANGEFREQ_PRODUCERS');
            $criteria->addSelectColumn($alias . '.CHANGEFREQ_NEWS');
            $criteria->addSelectColumn($alias . '.CHANGEFREQ_PAGES');
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
        } elseif ($values instanceof \Gekosale\Plugin\Sitemap\Model\ORM\Sitemap) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SitemapTableMap::DATABASE_NAME);
            $criteria->add(SitemapTableMap::COL_ID, (array) $values, Criteria::IN);
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

        if ($criteria->containsKey(SitemapTableMap::COL_ID) && $criteria->keyContainsValue(SitemapTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SitemapTableMap::COL_ID.')');
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
