<?php

namespace Gekosale\Plugin\Page\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Page\Model\ORM\Page as ChildPage;
use Gekosale\Plugin\Page\Model\ORM\PageI18nQuery as ChildPageI18nQuery;
use Gekosale\Plugin\Page\Model\ORM\PageQuery as ChildPageQuery;
use Gekosale\Plugin\Page\Model\ORM\Map\PageTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'page' table.
 *
 * 
 *
 * @method     ChildPageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPageQuery orderByPageId($order = Criteria::ASC) Order by the page_id column
 * @method     ChildPageQuery orderByHierarchy($order = Criteria::ASC) Order by the hierarchy column
 * @method     ChildPageQuery orderByFooter($order = Criteria::ASC) Order by the footer column
 * @method     ChildPageQuery orderByHeader($order = Criteria::ASC) Order by the header column
 * @method     ChildPageQuery orderByAlias($order = Criteria::ASC) Order by the alias column
 * @method     ChildPageQuery orderByRedirect($order = Criteria::ASC) Order by the redirect column
 * @method     ChildPageQuery orderByRedirectRoute($order = Criteria::ASC) Order by the redirect_route column
 * @method     ChildPageQuery orderByRedirectUrl($order = Criteria::ASC) Order by the redirect_url column
 * @method     ChildPageQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildPageQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildPageQuery groupById() Group by the id column
 * @method     ChildPageQuery groupByPageId() Group by the page_id column
 * @method     ChildPageQuery groupByHierarchy() Group by the hierarchy column
 * @method     ChildPageQuery groupByFooter() Group by the footer column
 * @method     ChildPageQuery groupByHeader() Group by the header column
 * @method     ChildPageQuery groupByAlias() Group by the alias column
 * @method     ChildPageQuery groupByRedirect() Group by the redirect column
 * @method     ChildPageQuery groupByRedirectRoute() Group by the redirect_route column
 * @method     ChildPageQuery groupByRedirectUrl() Group by the redirect_url column
 * @method     ChildPageQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildPageQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildPageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPageQuery leftJoinPageRelatedByPageId($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageRelatedByPageId relation
 * @method     ChildPageQuery rightJoinPageRelatedByPageId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageRelatedByPageId relation
 * @method     ChildPageQuery innerJoinPageRelatedByPageId($relationAlias = null) Adds a INNER JOIN clause to the query using the PageRelatedByPageId relation
 *
 * @method     ChildPageQuery leftJoinPageRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageRelatedById relation
 * @method     ChildPageQuery rightJoinPageRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageRelatedById relation
 * @method     ChildPageQuery innerJoinPageRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the PageRelatedById relation
 *
 * @method     ChildPageQuery leftJoinPageShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageShop relation
 * @method     ChildPageQuery rightJoinPageShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageShop relation
 * @method     ChildPageQuery innerJoinPageShop($relationAlias = null) Adds a INNER JOIN clause to the query using the PageShop relation
 *
 * @method     ChildPageQuery leftJoinPageI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageI18n relation
 * @method     ChildPageQuery rightJoinPageI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageI18n relation
 * @method     ChildPageQuery innerJoinPageI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the PageI18n relation
 *
 * @method     ChildPage findOne(ConnectionInterface $con = null) Return the first ChildPage matching the query
 * @method     ChildPage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPage matching the query, or a new ChildPage object populated from the query conditions when no match is found
 *
 * @method     ChildPage findOneById(int $id) Return the first ChildPage filtered by the id column
 * @method     ChildPage findOneByPageId(int $page_id) Return the first ChildPage filtered by the page_id column
 * @method     ChildPage findOneByHierarchy(int $hierarchy) Return the first ChildPage filtered by the hierarchy column
 * @method     ChildPage findOneByFooter(int $footer) Return the first ChildPage filtered by the footer column
 * @method     ChildPage findOneByHeader(int $header) Return the first ChildPage filtered by the header column
 * @method     ChildPage findOneByAlias(string $alias) Return the first ChildPage filtered by the alias column
 * @method     ChildPage findOneByRedirect(int $redirect) Return the first ChildPage filtered by the redirect column
 * @method     ChildPage findOneByRedirectRoute(string $redirect_route) Return the first ChildPage filtered by the redirect_route column
 * @method     ChildPage findOneByRedirectUrl(string $redirect_url) Return the first ChildPage filtered by the redirect_url column
 * @method     ChildPage findOneByCreatedAt(string $created_at) Return the first ChildPage filtered by the created_at column
 * @method     ChildPage findOneByUpdatedAt(string $updated_at) Return the first ChildPage filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildPage objects filtered by the id column
 * @method     array findByPageId(int $page_id) Return ChildPage objects filtered by the page_id column
 * @method     array findByHierarchy(int $hierarchy) Return ChildPage objects filtered by the hierarchy column
 * @method     array findByFooter(int $footer) Return ChildPage objects filtered by the footer column
 * @method     array findByHeader(int $header) Return ChildPage objects filtered by the header column
 * @method     array findByAlias(string $alias) Return ChildPage objects filtered by the alias column
 * @method     array findByRedirect(int $redirect) Return ChildPage objects filtered by the redirect column
 * @method     array findByRedirectRoute(string $redirect_route) Return ChildPage objects filtered by the redirect_route column
 * @method     array findByRedirectUrl(string $redirect_url) Return ChildPage objects filtered by the redirect_url column
 * @method     array findByCreatedAt(string $created_at) Return ChildPage objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildPage objects filtered by the updated_at column
 *
 */
abstract class PageQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Page\Model\ORM\Base\PageQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Page\\Model\\ORM\\Page', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Page\Model\ORM\PageQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Page\Model\ORM\PageQuery();
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
     * @return ChildPage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PageTableMap::DATABASE_NAME);
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
     * @return   ChildPage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PAGE_ID, HIERARCHY, FOOTER, HEADER, ALIAS, REDIRECT, REDIRECT_ROUTE, REDIRECT_URL, CREATED_AT, UPDATED_AT FROM page WHERE ID = :p0';
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
            $obj = new ChildPage();
            $obj->hydrate($row);
            PageTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildPage|array|mixed the result, formatted by the current formatter
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
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PageTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PageTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PageTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PageTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the page_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPageId(1234); // WHERE page_id = 1234
     * $query->filterByPageId(array(12, 34)); // WHERE page_id IN (12, 34)
     * $query->filterByPageId(array('min' => 12)); // WHERE page_id > 12
     * </code>
     *
     * @see       filterByPageRelatedByPageId()
     *
     * @param     mixed $pageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPageId($pageId = null, $comparison = null)
    {
        if (is_array($pageId)) {
            $useMinMax = false;
            if (isset($pageId['min'])) {
                $this->addUsingAlias(PageTableMap::COL_PAGE_ID, $pageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pageId['max'])) {
                $this->addUsingAlias(PageTableMap::COL_PAGE_ID, $pageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_PAGE_ID, $pageId, $comparison);
    }

    /**
     * Filter the query on the hierarchy column
     *
     * Example usage:
     * <code>
     * $query->filterByHierarchy(1234); // WHERE hierarchy = 1234
     * $query->filterByHierarchy(array(12, 34)); // WHERE hierarchy IN (12, 34)
     * $query->filterByHierarchy(array('min' => 12)); // WHERE hierarchy > 12
     * </code>
     *
     * @param     mixed $hierarchy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByHierarchy($hierarchy = null, $comparison = null)
    {
        if (is_array($hierarchy)) {
            $useMinMax = false;
            if (isset($hierarchy['min'])) {
                $this->addUsingAlias(PageTableMap::COL_HIERARCHY, $hierarchy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hierarchy['max'])) {
                $this->addUsingAlias(PageTableMap::COL_HIERARCHY, $hierarchy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_HIERARCHY, $hierarchy, $comparison);
    }

    /**
     * Filter the query on the footer column
     *
     * Example usage:
     * <code>
     * $query->filterByFooter(1234); // WHERE footer = 1234
     * $query->filterByFooter(array(12, 34)); // WHERE footer IN (12, 34)
     * $query->filterByFooter(array('min' => 12)); // WHERE footer > 12
     * </code>
     *
     * @param     mixed $footer The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByFooter($footer = null, $comparison = null)
    {
        if (is_array($footer)) {
            $useMinMax = false;
            if (isset($footer['min'])) {
                $this->addUsingAlias(PageTableMap::COL_FOOTER, $footer['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($footer['max'])) {
                $this->addUsingAlias(PageTableMap::COL_FOOTER, $footer['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_FOOTER, $footer, $comparison);
    }

    /**
     * Filter the query on the header column
     *
     * Example usage:
     * <code>
     * $query->filterByHeader(1234); // WHERE header = 1234
     * $query->filterByHeader(array(12, 34)); // WHERE header IN (12, 34)
     * $query->filterByHeader(array('min' => 12)); // WHERE header > 12
     * </code>
     *
     * @param     mixed $header The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByHeader($header = null, $comparison = null)
    {
        if (is_array($header)) {
            $useMinMax = false;
            if (isset($header['min'])) {
                $this->addUsingAlias(PageTableMap::COL_HEADER, $header['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($header['max'])) {
                $this->addUsingAlias(PageTableMap::COL_HEADER, $header['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_HEADER, $header, $comparison);
    }

    /**
     * Filter the query on the alias column
     *
     * Example usage:
     * <code>
     * $query->filterByAlias('fooValue');   // WHERE alias = 'fooValue'
     * $query->filterByAlias('%fooValue%'); // WHERE alias LIKE '%fooValue%'
     * </code>
     *
     * @param     string $alias The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByAlias($alias = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($alias)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $alias)) {
                $alias = str_replace('*', '%', $alias);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_ALIAS, $alias, $comparison);
    }

    /**
     * Filter the query on the redirect column
     *
     * Example usage:
     * <code>
     * $query->filterByRedirect(1234); // WHERE redirect = 1234
     * $query->filterByRedirect(array(12, 34)); // WHERE redirect IN (12, 34)
     * $query->filterByRedirect(array('min' => 12)); // WHERE redirect > 12
     * </code>
     *
     * @param     mixed $redirect The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByRedirect($redirect = null, $comparison = null)
    {
        if (is_array($redirect)) {
            $useMinMax = false;
            if (isset($redirect['min'])) {
                $this->addUsingAlias(PageTableMap::COL_REDIRECT, $redirect['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($redirect['max'])) {
                $this->addUsingAlias(PageTableMap::COL_REDIRECT, $redirect['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_REDIRECT, $redirect, $comparison);
    }

    /**
     * Filter the query on the redirect_route column
     *
     * Example usage:
     * <code>
     * $query->filterByRedirectRoute('fooValue');   // WHERE redirect_route = 'fooValue'
     * $query->filterByRedirectRoute('%fooValue%'); // WHERE redirect_route LIKE '%fooValue%'
     * </code>
     *
     * @param     string $redirectRoute The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByRedirectRoute($redirectRoute = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($redirectRoute)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $redirectRoute)) {
                $redirectRoute = str_replace('*', '%', $redirectRoute);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_REDIRECT_ROUTE, $redirectRoute, $comparison);
    }

    /**
     * Filter the query on the redirect_url column
     *
     * Example usage:
     * <code>
     * $query->filterByRedirectUrl('fooValue');   // WHERE redirect_url = 'fooValue'
     * $query->filterByRedirectUrl('%fooValue%'); // WHERE redirect_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $redirectUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByRedirectUrl($redirectUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($redirectUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $redirectUrl)) {
                $redirectUrl = str_replace('*', '%', $redirectUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_REDIRECT_URL, $redirectUrl, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(PageTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(PageTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(PageTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(PageTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PageTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Page\Model\ORM\Page object
     *
     * @param \Gekosale\Plugin\Page\Model\ORM\Page|ObjectCollection $page The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPageRelatedByPageId($page, $comparison = null)
    {
        if ($page instanceof \Gekosale\Plugin\Page\Model\ORM\Page) {
            return $this
                ->addUsingAlias(PageTableMap::COL_PAGE_ID, $page->getId(), $comparison);
        } elseif ($page instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PageTableMap::COL_PAGE_ID, $page->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPageRelatedByPageId() only accepts arguments of type \Gekosale\Plugin\Page\Model\ORM\Page or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageRelatedByPageId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function joinPageRelatedByPageId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageRelatedByPageId');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PageRelatedByPageId');
        }

        return $this;
    }

    /**
     * Use the PageRelatedByPageId relation Page object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Page\Model\ORM\PageQuery A secondary query class using the current class as primary query
     */
    public function usePageRelatedByPageIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPageRelatedByPageId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageRelatedByPageId', '\Gekosale\Plugin\Page\Model\ORM\PageQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Page\Model\ORM\Page object
     *
     * @param \Gekosale\Plugin\Page\Model\ORM\Page|ObjectCollection $page  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPageRelatedById($page, $comparison = null)
    {
        if ($page instanceof \Gekosale\Plugin\Page\Model\ORM\Page) {
            return $this
                ->addUsingAlias(PageTableMap::COL_ID, $page->getPageId(), $comparison);
        } elseif ($page instanceof ObjectCollection) {
            return $this
                ->usePageRelatedByIdQuery()
                ->filterByPrimaryKeys($page->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPageRelatedById() only accepts arguments of type \Gekosale\Plugin\Page\Model\ORM\Page or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function joinPageRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageRelatedById');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PageRelatedById');
        }

        return $this;
    }

    /**
     * Use the PageRelatedById relation Page object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Page\Model\ORM\PageQuery A secondary query class using the current class as primary query
     */
    public function usePageRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPageRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageRelatedById', '\Gekosale\Plugin\Page\Model\ORM\PageQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Page\Model\ORM\PageShop object
     *
     * @param \Gekosale\Plugin\Page\Model\ORM\PageShop|ObjectCollection $pageShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPageShop($pageShop, $comparison = null)
    {
        if ($pageShop instanceof \Gekosale\Plugin\Page\Model\ORM\PageShop) {
            return $this
                ->addUsingAlias(PageTableMap::COL_ID, $pageShop->getPageId(), $comparison);
        } elseif ($pageShop instanceof ObjectCollection) {
            return $this
                ->usePageShopQuery()
                ->filterByPrimaryKeys($pageShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPageShop() only accepts arguments of type \Gekosale\Plugin\Page\Model\ORM\PageShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function joinPageShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageShop');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PageShop');
        }

        return $this;
    }

    /**
     * Use the PageShop relation PageShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Page\Model\ORM\PageShopQuery A secondary query class using the current class as primary query
     */
    public function usePageShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPageShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageShop', '\Gekosale\Plugin\Page\Model\ORM\PageShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Page\Model\ORM\PageI18n object
     *
     * @param \Gekosale\Plugin\Page\Model\ORM\PageI18n|ObjectCollection $pageI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function filterByPageI18n($pageI18n, $comparison = null)
    {
        if ($pageI18n instanceof \Gekosale\Plugin\Page\Model\ORM\PageI18n) {
            return $this
                ->addUsingAlias(PageTableMap::COL_ID, $pageI18n->getId(), $comparison);
        } elseif ($pageI18n instanceof ObjectCollection) {
            return $this
                ->usePageI18nQuery()
                ->filterByPrimaryKeys($pageI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPageI18n() only accepts arguments of type \Gekosale\Plugin\Page\Model\ORM\PageI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function joinPageI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageI18n');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PageI18n');
        }

        return $this;
    }

    /**
     * Use the PageI18n relation PageI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Page\Model\ORM\PageI18nQuery A secondary query class using the current class as primary query
     */
    public function usePageI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinPageI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageI18n', '\Gekosale\Plugin\Page\Model\ORM\PageI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPage $page Object to remove from the list of results
     *
     * @return ChildPageQuery The current query, for fluid interface
     */
    public function prune($page = null)
    {
        if ($page) {
            $this->addUsingAlias(PageTableMap::COL_ID, $page->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the page table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PageTableMap::DATABASE_NAME);
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
            PageTableMap::clearInstancePool();
            PageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildPage or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildPage object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(PageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        PageTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            PageTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior
    
    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildPageQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'PageI18n';
    
        return $this
            ->joinPageI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildPageQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('PageI18n');
        $this->with['PageI18n']->setIsWithOneToMany(false);
    
        return $this;
    }
    
    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildPageI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageI18n', '\Gekosale\Plugin\Page\Model\ORM\PageI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildPageQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(PageTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildPageQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(PageTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildPageQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(PageTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildPageQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(PageTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildPageQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(PageTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildPageQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(PageTableMap::COL_CREATED_AT);
    }

} // PageQuery
