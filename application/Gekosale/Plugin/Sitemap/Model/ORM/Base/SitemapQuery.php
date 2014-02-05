<?php

namespace Gekosale\Plugin\Sitemap\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Sitemap\Model\ORM\Sitemap as ChildSitemap;
use Gekosale\Plugin\Sitemap\Model\ORM\SitemapQuery as ChildSitemapQuery;
use Gekosale\Plugin\Sitemap\Model\ORM\Map\SitemapTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'sitemap' table.
 *
 * 
 *
 * @method     ChildSitemapQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSitemapQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildSitemapQuery orderByPublishCategories($order = Criteria::ASC) Order by the publish_categories column
 * @method     ChildSitemapQuery orderByPriorityCategories($order = Criteria::ASC) Order by the priority_categories column
 * @method     ChildSitemapQuery orderByPublishProducts($order = Criteria::ASC) Order by the publish_products column
 * @method     ChildSitemapQuery orderByPriorityProducts($order = Criteria::ASC) Order by the priority_products column
 * @method     ChildSitemapQuery orderByPublishProducers($order = Criteria::ASC) Order by the publish_producers column
 * @method     ChildSitemapQuery orderByPriorityProducers($order = Criteria::ASC) Order by the priority_producers column
 * @method     ChildSitemapQuery orderByPublishNews($order = Criteria::ASC) Order by the publish_news column
 * @method     ChildSitemapQuery orderByPriorityNews($order = Criteria::ASC) Order by the priority_news column
 * @method     ChildSitemapQuery orderByPublishPages($order = Criteria::ASC) Order by the publish_pages column
 * @method     ChildSitemapQuery orderByPriorityPages($order = Criteria::ASC) Order by the priority_pages column
 * @method     ChildSitemapQuery orderByAddDate($order = Criteria::ASC) Order by the add_date column
 * @method     ChildSitemapQuery orderByLastUpdate($order = Criteria::ASC) Order by the last_update column
 * @method     ChildSitemapQuery orderByPingServer($order = Criteria::ASC) Order by the ping_server column
 * @method     ChildSitemapQuery orderByChangefreqCategories($order = Criteria::ASC) Order by the changefreq_categories column
 * @method     ChildSitemapQuery orderByChangefreqProducts($order = Criteria::ASC) Order by the changefreq_products column
 * @method     ChildSitemapQuery orderByChangefreqProducers($order = Criteria::ASC) Order by the changefreq_producers column
 * @method     ChildSitemapQuery orderByChangefreqNews($order = Criteria::ASC) Order by the changefreq_news column
 * @method     ChildSitemapQuery orderByChangefreqPages($order = Criteria::ASC) Order by the changefreq_pages column
 *
 * @method     ChildSitemapQuery groupById() Group by the id column
 * @method     ChildSitemapQuery groupByName() Group by the name column
 * @method     ChildSitemapQuery groupByPublishCategories() Group by the publish_categories column
 * @method     ChildSitemapQuery groupByPriorityCategories() Group by the priority_categories column
 * @method     ChildSitemapQuery groupByPublishProducts() Group by the publish_products column
 * @method     ChildSitemapQuery groupByPriorityProducts() Group by the priority_products column
 * @method     ChildSitemapQuery groupByPublishProducers() Group by the publish_producers column
 * @method     ChildSitemapQuery groupByPriorityProducers() Group by the priority_producers column
 * @method     ChildSitemapQuery groupByPublishNews() Group by the publish_news column
 * @method     ChildSitemapQuery groupByPriorityNews() Group by the priority_news column
 * @method     ChildSitemapQuery groupByPublishPages() Group by the publish_pages column
 * @method     ChildSitemapQuery groupByPriorityPages() Group by the priority_pages column
 * @method     ChildSitemapQuery groupByAddDate() Group by the add_date column
 * @method     ChildSitemapQuery groupByLastUpdate() Group by the last_update column
 * @method     ChildSitemapQuery groupByPingServer() Group by the ping_server column
 * @method     ChildSitemapQuery groupByChangefreqCategories() Group by the changefreq_categories column
 * @method     ChildSitemapQuery groupByChangefreqProducts() Group by the changefreq_products column
 * @method     ChildSitemapQuery groupByChangefreqProducers() Group by the changefreq_producers column
 * @method     ChildSitemapQuery groupByChangefreqNews() Group by the changefreq_news column
 * @method     ChildSitemapQuery groupByChangefreqPages() Group by the changefreq_pages column
 *
 * @method     ChildSitemapQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSitemapQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSitemapQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSitemap findOne(ConnectionInterface $con = null) Return the first ChildSitemap matching the query
 * @method     ChildSitemap findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSitemap matching the query, or a new ChildSitemap object populated from the query conditions when no match is found
 *
 * @method     ChildSitemap findOneById(int $id) Return the first ChildSitemap filtered by the id column
 * @method     ChildSitemap findOneByName(string $name) Return the first ChildSitemap filtered by the name column
 * @method     ChildSitemap findOneByPublishCategories(int $publish_categories) Return the first ChildSitemap filtered by the publish_categories column
 * @method     ChildSitemap findOneByPriorityCategories(string $priority_categories) Return the first ChildSitemap filtered by the priority_categories column
 * @method     ChildSitemap findOneByPublishProducts(int $publish_products) Return the first ChildSitemap filtered by the publish_products column
 * @method     ChildSitemap findOneByPriorityProducts(string $priority_products) Return the first ChildSitemap filtered by the priority_products column
 * @method     ChildSitemap findOneByPublishProducers(int $publish_producers) Return the first ChildSitemap filtered by the publish_producers column
 * @method     ChildSitemap findOneByPriorityProducers(string $priority_producers) Return the first ChildSitemap filtered by the priority_producers column
 * @method     ChildSitemap findOneByPublishNews(int $publish_news) Return the first ChildSitemap filtered by the publish_news column
 * @method     ChildSitemap findOneByPriorityNews(string $priority_news) Return the first ChildSitemap filtered by the priority_news column
 * @method     ChildSitemap findOneByPublishPages(int $publish_pages) Return the first ChildSitemap filtered by the publish_pages column
 * @method     ChildSitemap findOneByPriorityPages(string $priority_pages) Return the first ChildSitemap filtered by the priority_pages column
 * @method     ChildSitemap findOneByAddDate(string $add_date) Return the first ChildSitemap filtered by the add_date column
 * @method     ChildSitemap findOneByLastUpdate(string $last_update) Return the first ChildSitemap filtered by the last_update column
 * @method     ChildSitemap findOneByPingServer(string $ping_server) Return the first ChildSitemap filtered by the ping_server column
 * @method     ChildSitemap findOneByChangefreqCategories(string $changefreq_categories) Return the first ChildSitemap filtered by the changefreq_categories column
 * @method     ChildSitemap findOneByChangefreqProducts(string $changefreq_products) Return the first ChildSitemap filtered by the changefreq_products column
 * @method     ChildSitemap findOneByChangefreqProducers(string $changefreq_producers) Return the first ChildSitemap filtered by the changefreq_producers column
 * @method     ChildSitemap findOneByChangefreqNews(string $changefreq_news) Return the first ChildSitemap filtered by the changefreq_news column
 * @method     ChildSitemap findOneByChangefreqPages(string $changefreq_pages) Return the first ChildSitemap filtered by the changefreq_pages column
 *
 * @method     array findById(int $id) Return ChildSitemap objects filtered by the id column
 * @method     array findByName(string $name) Return ChildSitemap objects filtered by the name column
 * @method     array findByPublishCategories(int $publish_categories) Return ChildSitemap objects filtered by the publish_categories column
 * @method     array findByPriorityCategories(string $priority_categories) Return ChildSitemap objects filtered by the priority_categories column
 * @method     array findByPublishProducts(int $publish_products) Return ChildSitemap objects filtered by the publish_products column
 * @method     array findByPriorityProducts(string $priority_products) Return ChildSitemap objects filtered by the priority_products column
 * @method     array findByPublishProducers(int $publish_producers) Return ChildSitemap objects filtered by the publish_producers column
 * @method     array findByPriorityProducers(string $priority_producers) Return ChildSitemap objects filtered by the priority_producers column
 * @method     array findByPublishNews(int $publish_news) Return ChildSitemap objects filtered by the publish_news column
 * @method     array findByPriorityNews(string $priority_news) Return ChildSitemap objects filtered by the priority_news column
 * @method     array findByPublishPages(int $publish_pages) Return ChildSitemap objects filtered by the publish_pages column
 * @method     array findByPriorityPages(string $priority_pages) Return ChildSitemap objects filtered by the priority_pages column
 * @method     array findByAddDate(string $add_date) Return ChildSitemap objects filtered by the add_date column
 * @method     array findByLastUpdate(string $last_update) Return ChildSitemap objects filtered by the last_update column
 * @method     array findByPingServer(string $ping_server) Return ChildSitemap objects filtered by the ping_server column
 * @method     array findByChangefreqCategories(string $changefreq_categories) Return ChildSitemap objects filtered by the changefreq_categories column
 * @method     array findByChangefreqProducts(string $changefreq_products) Return ChildSitemap objects filtered by the changefreq_products column
 * @method     array findByChangefreqProducers(string $changefreq_producers) Return ChildSitemap objects filtered by the changefreq_producers column
 * @method     array findByChangefreqNews(string $changefreq_news) Return ChildSitemap objects filtered by the changefreq_news column
 * @method     array findByChangefreqPages(string $changefreq_pages) Return ChildSitemap objects filtered by the changefreq_pages column
 *
 */
abstract class SitemapQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Sitemap\Model\ORM\Base\SitemapQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Sitemap\\Model\\ORM\\Sitemap', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSitemapQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSitemapQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Sitemap\Model\ORM\SitemapQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Sitemap\Model\ORM\SitemapQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildSitemap|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SitemapTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SitemapTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildSitemap A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, PUBLISH_CATEGORIES, PRIORITY_CATEGORIES, PUBLISH_PRODUCTS, PRIORITY_PRODUCTS, PUBLISH_PRODUCERS, PRIORITY_PRODUCERS, PUBLISH_NEWS, PRIORITY_NEWS, PUBLISH_PAGES, PRIORITY_PAGES, ADD_DATE, LAST_UPDATE, PING_SERVER, CHANGEFREQ_CATEGORIES, CHANGEFREQ_PRODUCTS, CHANGEFREQ_PRODUCERS, CHANGEFREQ_NEWS, CHANGEFREQ_PAGES FROM sitemap WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);            
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildSitemap();
            $obj->hydrate($row);
            SitemapTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildSitemap|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SitemapTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SitemapTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the publish_categories column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishCategories(1234); // WHERE publish_categories = 1234
     * $query->filterByPublishCategories(array(12, 34)); // WHERE publish_categories IN (12, 34)
     * $query->filterByPublishCategories(array('min' => 12)); // WHERE publish_categories > 12
     * </code>
     *
     * @param     mixed $publishCategories The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPublishCategories($publishCategories = null, $comparison = null)
    {
        if (is_array($publishCategories)) {
            $useMinMax = false;
            if (isset($publishCategories['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_CATEGORIES, $publishCategories['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishCategories['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_CATEGORIES, $publishCategories['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_CATEGORIES, $publishCategories, $comparison);
    }

    /**
     * Filter the query on the priority_categories column
     *
     * Example usage:
     * <code>
     * $query->filterByPriorityCategories('fooValue');   // WHERE priority_categories = 'fooValue'
     * $query->filterByPriorityCategories('%fooValue%'); // WHERE priority_categories LIKE '%fooValue%'
     * </code>
     *
     * @param     string $priorityCategories The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPriorityCategories($priorityCategories = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($priorityCategories)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $priorityCategories)) {
                $priorityCategories = str_replace('*', '%', $priorityCategories);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PRIORITY_CATEGORIES, $priorityCategories, $comparison);
    }

    /**
     * Filter the query on the publish_products column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishProducts(1234); // WHERE publish_products = 1234
     * $query->filterByPublishProducts(array(12, 34)); // WHERE publish_products IN (12, 34)
     * $query->filterByPublishProducts(array('min' => 12)); // WHERE publish_products > 12
     * </code>
     *
     * @param     mixed $publishProducts The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPublishProducts($publishProducts = null, $comparison = null)
    {
        if (is_array($publishProducts)) {
            $useMinMax = false;
            if (isset($publishProducts['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PRODUCTS, $publishProducts['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishProducts['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PRODUCTS, $publishProducts['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PRODUCTS, $publishProducts, $comparison);
    }

    /**
     * Filter the query on the priority_products column
     *
     * Example usage:
     * <code>
     * $query->filterByPriorityProducts('fooValue');   // WHERE priority_products = 'fooValue'
     * $query->filterByPriorityProducts('%fooValue%'); // WHERE priority_products LIKE '%fooValue%'
     * </code>
     *
     * @param     string $priorityProducts The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPriorityProducts($priorityProducts = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($priorityProducts)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $priorityProducts)) {
                $priorityProducts = str_replace('*', '%', $priorityProducts);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PRIORITY_PRODUCTS, $priorityProducts, $comparison);
    }

    /**
     * Filter the query on the publish_producers column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishProducers(1234); // WHERE publish_producers = 1234
     * $query->filterByPublishProducers(array(12, 34)); // WHERE publish_producers IN (12, 34)
     * $query->filterByPublishProducers(array('min' => 12)); // WHERE publish_producers > 12
     * </code>
     *
     * @param     mixed $publishProducers The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPublishProducers($publishProducers = null, $comparison = null)
    {
        if (is_array($publishProducers)) {
            $useMinMax = false;
            if (isset($publishProducers['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PRODUCERS, $publishProducers['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishProducers['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PRODUCERS, $publishProducers['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PRODUCERS, $publishProducers, $comparison);
    }

    /**
     * Filter the query on the priority_producers column
     *
     * Example usage:
     * <code>
     * $query->filterByPriorityProducers('fooValue');   // WHERE priority_producers = 'fooValue'
     * $query->filterByPriorityProducers('%fooValue%'); // WHERE priority_producers LIKE '%fooValue%'
     * </code>
     *
     * @param     string $priorityProducers The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPriorityProducers($priorityProducers = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($priorityProducers)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $priorityProducers)) {
                $priorityProducers = str_replace('*', '%', $priorityProducers);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PRIORITY_PRODUCERS, $priorityProducers, $comparison);
    }

    /**
     * Filter the query on the publish_news column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishNews(1234); // WHERE publish_news = 1234
     * $query->filterByPublishNews(array(12, 34)); // WHERE publish_news IN (12, 34)
     * $query->filterByPublishNews(array('min' => 12)); // WHERE publish_news > 12
     * </code>
     *
     * @param     mixed $publishNews The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPublishNews($publishNews = null, $comparison = null)
    {
        if (is_array($publishNews)) {
            $useMinMax = false;
            if (isset($publishNews['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_NEWS, $publishNews['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishNews['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_NEWS, $publishNews['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_NEWS, $publishNews, $comparison);
    }

    /**
     * Filter the query on the priority_news column
     *
     * Example usage:
     * <code>
     * $query->filterByPriorityNews('fooValue');   // WHERE priority_news = 'fooValue'
     * $query->filterByPriorityNews('%fooValue%'); // WHERE priority_news LIKE '%fooValue%'
     * </code>
     *
     * @param     string $priorityNews The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPriorityNews($priorityNews = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($priorityNews)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $priorityNews)) {
                $priorityNews = str_replace('*', '%', $priorityNews);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PRIORITY_NEWS, $priorityNews, $comparison);
    }

    /**
     * Filter the query on the publish_pages column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishPages(1234); // WHERE publish_pages = 1234
     * $query->filterByPublishPages(array(12, 34)); // WHERE publish_pages IN (12, 34)
     * $query->filterByPublishPages(array('min' => 12)); // WHERE publish_pages > 12
     * </code>
     *
     * @param     mixed $publishPages The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPublishPages($publishPages = null, $comparison = null)
    {
        if (is_array($publishPages)) {
            $useMinMax = false;
            if (isset($publishPages['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PAGES, $publishPages['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishPages['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PAGES, $publishPages['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PUBLISH_PAGES, $publishPages, $comparison);
    }

    /**
     * Filter the query on the priority_pages column
     *
     * Example usage:
     * <code>
     * $query->filterByPriorityPages('fooValue');   // WHERE priority_pages = 'fooValue'
     * $query->filterByPriorityPages('%fooValue%'); // WHERE priority_pages LIKE '%fooValue%'
     * </code>
     *
     * @param     string $priorityPages The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPriorityPages($priorityPages = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($priorityPages)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $priorityPages)) {
                $priorityPages = str_replace('*', '%', $priorityPages);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PRIORITY_PAGES, $priorityPages, $comparison);
    }

    /**
     * Filter the query on the add_date column
     *
     * Example usage:
     * <code>
     * $query->filterByAddDate('2011-03-14'); // WHERE add_date = '2011-03-14'
     * $query->filterByAddDate('now'); // WHERE add_date = '2011-03-14'
     * $query->filterByAddDate(array('max' => 'yesterday')); // WHERE add_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $addDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByAddDate($addDate = null, $comparison = null)
    {
        if (is_array($addDate)) {
            $useMinMax = false;
            if (isset($addDate['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_ADD_DATE, $addDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($addDate['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_ADD_DATE, $addDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_ADD_DATE, $addDate, $comparison);
    }

    /**
     * Filter the query on the last_update column
     *
     * Example usage:
     * <code>
     * $query->filterByLastUpdate('2011-03-14'); // WHERE last_update = '2011-03-14'
     * $query->filterByLastUpdate('now'); // WHERE last_update = '2011-03-14'
     * $query->filterByLastUpdate(array('max' => 'yesterday')); // WHERE last_update > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastUpdate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByLastUpdate($lastUpdate = null, $comparison = null)
    {
        if (is_array($lastUpdate)) {
            $useMinMax = false;
            if (isset($lastUpdate['min'])) {
                $this->addUsingAlias(SitemapTableMap::COL_LAST_UPDATE, $lastUpdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastUpdate['max'])) {
                $this->addUsingAlias(SitemapTableMap::COL_LAST_UPDATE, $lastUpdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_LAST_UPDATE, $lastUpdate, $comparison);
    }

    /**
     * Filter the query on the ping_server column
     *
     * Example usage:
     * <code>
     * $query->filterByPingServer('fooValue');   // WHERE ping_server = 'fooValue'
     * $query->filterByPingServer('%fooValue%'); // WHERE ping_server LIKE '%fooValue%'
     * </code>
     *
     * @param     string $pingServer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByPingServer($pingServer = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($pingServer)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $pingServer)) {
                $pingServer = str_replace('*', '%', $pingServer);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_PING_SERVER, $pingServer, $comparison);
    }

    /**
     * Filter the query on the changefreq_categories column
     *
     * Example usage:
     * <code>
     * $query->filterByChangefreqCategories('fooValue');   // WHERE changefreq_categories = 'fooValue'
     * $query->filterByChangefreqCategories('%fooValue%'); // WHERE changefreq_categories LIKE '%fooValue%'
     * </code>
     *
     * @param     string $changefreqCategories The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByChangefreqCategories($changefreqCategories = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($changefreqCategories)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $changefreqCategories)) {
                $changefreqCategories = str_replace('*', '%', $changefreqCategories);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_CHANGEFREQ_CATEGORIES, $changefreqCategories, $comparison);
    }

    /**
     * Filter the query on the changefreq_products column
     *
     * Example usage:
     * <code>
     * $query->filterByChangefreqProducts('fooValue');   // WHERE changefreq_products = 'fooValue'
     * $query->filterByChangefreqProducts('%fooValue%'); // WHERE changefreq_products LIKE '%fooValue%'
     * </code>
     *
     * @param     string $changefreqProducts The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByChangefreqProducts($changefreqProducts = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($changefreqProducts)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $changefreqProducts)) {
                $changefreqProducts = str_replace('*', '%', $changefreqProducts);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_CHANGEFREQ_PRODUCTS, $changefreqProducts, $comparison);
    }

    /**
     * Filter the query on the changefreq_producers column
     *
     * Example usage:
     * <code>
     * $query->filterByChangefreqProducers('fooValue');   // WHERE changefreq_producers = 'fooValue'
     * $query->filterByChangefreqProducers('%fooValue%'); // WHERE changefreq_producers LIKE '%fooValue%'
     * </code>
     *
     * @param     string $changefreqProducers The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByChangefreqProducers($changefreqProducers = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($changefreqProducers)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $changefreqProducers)) {
                $changefreqProducers = str_replace('*', '%', $changefreqProducers);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_CHANGEFREQ_PRODUCERS, $changefreqProducers, $comparison);
    }

    /**
     * Filter the query on the changefreq_news column
     *
     * Example usage:
     * <code>
     * $query->filterByChangefreqNews('fooValue');   // WHERE changefreq_news = 'fooValue'
     * $query->filterByChangefreqNews('%fooValue%'); // WHERE changefreq_news LIKE '%fooValue%'
     * </code>
     *
     * @param     string $changefreqNews The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByChangefreqNews($changefreqNews = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($changefreqNews)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $changefreqNews)) {
                $changefreqNews = str_replace('*', '%', $changefreqNews);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_CHANGEFREQ_NEWS, $changefreqNews, $comparison);
    }

    /**
     * Filter the query on the changefreq_pages column
     *
     * Example usage:
     * <code>
     * $query->filterByChangefreqPages('fooValue');   // WHERE changefreq_pages = 'fooValue'
     * $query->filterByChangefreqPages('%fooValue%'); // WHERE changefreq_pages LIKE '%fooValue%'
     * </code>
     *
     * @param     string $changefreqPages The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function filterByChangefreqPages($changefreqPages = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($changefreqPages)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $changefreqPages)) {
                $changefreqPages = str_replace('*', '%', $changefreqPages);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SitemapTableMap::COL_CHANGEFREQ_PAGES, $changefreqPages, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSitemap $sitemap Object to remove from the list of results
     *
     * @return ChildSitemapQuery The current query, for fluid interface
     */
    public function prune($sitemap = null)
    {
        if ($sitemap) {
            $this->addUsingAlias(SitemapTableMap::COL_ID, $sitemap->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the sitemap table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SitemapTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SitemapTableMap::clearInstancePool();
            SitemapTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSitemap or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSitemap object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SitemapTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SitemapTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        SitemapTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            SitemapTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SitemapQuery
