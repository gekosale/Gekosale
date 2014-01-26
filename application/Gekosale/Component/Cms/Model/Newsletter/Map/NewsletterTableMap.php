<?php

namespace Gekosale\Component\Cms\Model\Newsletter\Map;

use Gekosale\Component\Cms\Model\Newsletter\Newsletter;
use Gekosale\Component\Cms\Model\Newsletter\NewsletterQuery;
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
 * This class defines the structure of the 'newsletter' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class NewsletterTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Component.Cms.Model.Newsletter.Map.NewsletterTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'newsletter';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Component\\Cms\\Model\\Newsletter\\Newsletter';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Component.Cms.Model.Newsletter.Newsletter';

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
    const ID = 'newsletter.ID';

    /**
     * the column name for the NAME field
     */
    const NAME = 'newsletter.NAME';

    /**
     * the column name for the ADD_DATE field
     */
    const ADD_DATE = 'newsletter.ADD_DATE';

    /**
     * the column name for the SUBJECT field
     */
    const SUBJECT = 'newsletter.SUBJECT';

    /**
     * the column name for the EMAIL field
     */
    const EMAIL = 'newsletter.EMAIL';

    /**
     * the column name for the HTML_CONTENT field
     */
    const HTML_CONTENT = 'newsletter.HTML_CONTENT';

    /**
     * the column name for the TEXT_CONTENT field
     */
    const TEXT_CONTENT = 'newsletter.TEXT_CONTENT';

    /**
     * the column name for the NEWSLETTER_RECIPIENTS field
     */
    const NEWSLETTER_RECIPIENTS = 'newsletter.NEWSLETTER_RECIPIENTS';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'AddDate', 'Subject', 'Email', 'HtmlContent', 'TextContent', 'NewsletterRecipients', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'addDate', 'subject', 'email', 'htmlContent', 'textContent', 'newsletterRecipients', ),
        self::TYPE_COLNAME       => array(NewsletterTableMap::ID, NewsletterTableMap::NAME, NewsletterTableMap::ADD_DATE, NewsletterTableMap::SUBJECT, NewsletterTableMap::EMAIL, NewsletterTableMap::HTML_CONTENT, NewsletterTableMap::TEXT_CONTENT, NewsletterTableMap::NEWSLETTER_RECIPIENTS, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'NAME', 'ADD_DATE', 'SUBJECT', 'EMAIL', 'HTML_CONTENT', 'TEXT_CONTENT', 'NEWSLETTER_RECIPIENTS', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'add_date', 'subject', 'email', 'html_content', 'text_content', 'newsletter_recipients', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'AddDate' => 2, 'Subject' => 3, 'Email' => 4, 'HtmlContent' => 5, 'TextContent' => 6, 'NewsletterRecipients' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'addDate' => 2, 'subject' => 3, 'email' => 4, 'htmlContent' => 5, 'textContent' => 6, 'newsletterRecipients' => 7, ),
        self::TYPE_COLNAME       => array(NewsletterTableMap::ID => 0, NewsletterTableMap::NAME => 1, NewsletterTableMap::ADD_DATE => 2, NewsletterTableMap::SUBJECT => 3, NewsletterTableMap::EMAIL => 4, NewsletterTableMap::HTML_CONTENT => 5, NewsletterTableMap::TEXT_CONTENT => 6, NewsletterTableMap::NEWSLETTER_RECIPIENTS => 7, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'NAME' => 1, 'ADD_DATE' => 2, 'SUBJECT' => 3, 'EMAIL' => 4, 'HTML_CONTENT' => 5, 'TEXT_CONTENT' => 6, 'NEWSLETTER_RECIPIENTS' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'add_date' => 2, 'subject' => 3, 'email' => 4, 'html_content' => 5, 'text_content' => 6, 'newsletter_recipients' => 7, ),
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
        $this->setName('newsletter');
        $this->setPhpName('Newsletter');
        $this->setClassName('\\Gekosale\\Component\\Cms\\Model\\Newsletter\\Newsletter');
        $this->setPackage('Gekosale.Component.Cms.Model.Newsletter');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, null);
        $this->addColumn('ADD_DATE', 'AddDate', 'TIMESTAMP', true, null, 'CURRENT_TIMESTAMP');
        $this->addColumn('SUBJECT', 'Subject', 'VARCHAR', true, 255, null);
        $this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 255, null);
        $this->addColumn('HTML_CONTENT', 'HtmlContent', 'VARCHAR', true, 5000, null);
        $this->addColumn('TEXT_CONTENT', 'TextContent', 'VARCHAR', true, 5000, null);
        $this->addColumn('NEWSLETTER_RECIPIENTS', 'NewsletterRecipients', 'VARCHAR', false, 255, null);
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
        return $withPrefix ? NewsletterTableMap::CLASS_DEFAULT : NewsletterTableMap::OM_CLASS;
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
     * @return array (Newsletter object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = NewsletterTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = NewsletterTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + NewsletterTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = NewsletterTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            NewsletterTableMap::addInstanceToPool($obj, $key);
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
            $key = NewsletterTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = NewsletterTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                NewsletterTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(NewsletterTableMap::ID);
            $criteria->addSelectColumn(NewsletterTableMap::NAME);
            $criteria->addSelectColumn(NewsletterTableMap::ADD_DATE);
            $criteria->addSelectColumn(NewsletterTableMap::SUBJECT);
            $criteria->addSelectColumn(NewsletterTableMap::EMAIL);
            $criteria->addSelectColumn(NewsletterTableMap::HTML_CONTENT);
            $criteria->addSelectColumn(NewsletterTableMap::TEXT_CONTENT);
            $criteria->addSelectColumn(NewsletterTableMap::NEWSLETTER_RECIPIENTS);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.ADD_DATE');
            $criteria->addSelectColumn($alias . '.SUBJECT');
            $criteria->addSelectColumn($alias . '.EMAIL');
            $criteria->addSelectColumn($alias . '.HTML_CONTENT');
            $criteria->addSelectColumn($alias . '.TEXT_CONTENT');
            $criteria->addSelectColumn($alias . '.NEWSLETTER_RECIPIENTS');
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
        return Propel::getServiceContainer()->getDatabaseMap(NewsletterTableMap::DATABASE_NAME)->getTable(NewsletterTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(NewsletterTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(NewsletterTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new NewsletterTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Newsletter or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Newsletter object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(NewsletterTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Component\Cms\Model\Newsletter\Newsletter) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(NewsletterTableMap::DATABASE_NAME);
            $criteria->add(NewsletterTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = NewsletterQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { NewsletterTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { NewsletterTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the newsletter table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return NewsletterQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Newsletter or Criteria object.
     *
     * @param mixed               $criteria Criteria or Newsletter object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NewsletterTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Newsletter object
        }

        if ($criteria->containsKey(NewsletterTableMap::ID) && $criteria->keyContainsValue(NewsletterTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.NewsletterTableMap::ID.')');
        }


        // Set the correct dbName
        $query = NewsletterQuery::create()->mergeWith($criteria);

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

} // NewsletterTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
NewsletterTableMap::buildTableMap();
