<?php

namespace Gekosale\Plugin\Blog\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Blog\Model\ORM\Blog as ChildBlog;
use Gekosale\Plugin\Blog\Model\ORM\BlogI18nQuery as ChildBlogI18nQuery;
use Gekosale\Plugin\Blog\Model\ORM\BlogQuery as ChildBlogQuery;
use Gekosale\Plugin\Blog\Model\ORM\Map\BlogTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'blog' table.
 *
 * 
 *
 * @method     ChildBlogQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildBlogQuery orderByIsPublished($order = Criteria::ASC) Order by the is_published column
 * @method     ChildBlogQuery orderByIsFeatured($order = Criteria::ASC) Order by the is_featured column
 * @method     ChildBlogQuery orderByStartDate($order = Criteria::ASC) Order by the start_date column
 * @method     ChildBlogQuery orderByEndDate($order = Criteria::ASC) Order by the end_date column
 * @method     ChildBlogQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildBlogQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildBlogQuery groupById() Group by the id column
 * @method     ChildBlogQuery groupByIsPublished() Group by the is_published column
 * @method     ChildBlogQuery groupByIsFeatured() Group by the is_featured column
 * @method     ChildBlogQuery groupByStartDate() Group by the start_date column
 * @method     ChildBlogQuery groupByEndDate() Group by the end_date column
 * @method     ChildBlogQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildBlogQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildBlogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildBlogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildBlogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildBlogQuery leftJoinBlogPhoto($relationAlias = null) Adds a LEFT JOIN clause to the query using the BlogPhoto relation
 * @method     ChildBlogQuery rightJoinBlogPhoto($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BlogPhoto relation
 * @method     ChildBlogQuery innerJoinBlogPhoto($relationAlias = null) Adds a INNER JOIN clause to the query using the BlogPhoto relation
 *
 * @method     ChildBlogQuery leftJoinBlogShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the BlogShop relation
 * @method     ChildBlogQuery rightJoinBlogShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BlogShop relation
 * @method     ChildBlogQuery innerJoinBlogShop($relationAlias = null) Adds a INNER JOIN clause to the query using the BlogShop relation
 *
 * @method     ChildBlogQuery leftJoinBlogI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the BlogI18n relation
 * @method     ChildBlogQuery rightJoinBlogI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BlogI18n relation
 * @method     ChildBlogQuery innerJoinBlogI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the BlogI18n relation
 *
 * @method     ChildBlog findOne(ConnectionInterface $con = null) Return the first ChildBlog matching the query
 * @method     ChildBlog findOneOrCreate(ConnectionInterface $con = null) Return the first ChildBlog matching the query, or a new ChildBlog object populated from the query conditions when no match is found
 *
 * @method     ChildBlog findOneById(int $id) Return the first ChildBlog filtered by the id column
 * @method     ChildBlog findOneByIsPublished(int $is_published) Return the first ChildBlog filtered by the is_published column
 * @method     ChildBlog findOneByIsFeatured(int $is_featured) Return the first ChildBlog filtered by the is_featured column
 * @method     ChildBlog findOneByStartDate(string $start_date) Return the first ChildBlog filtered by the start_date column
 * @method     ChildBlog findOneByEndDate(string $end_date) Return the first ChildBlog filtered by the end_date column
 * @method     ChildBlog findOneByCreatedAt(string $created_at) Return the first ChildBlog filtered by the created_at column
 * @method     ChildBlog findOneByUpdatedAt(string $updated_at) Return the first ChildBlog filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildBlog objects filtered by the id column
 * @method     array findByIsPublished(int $is_published) Return ChildBlog objects filtered by the is_published column
 * @method     array findByIsFeatured(int $is_featured) Return ChildBlog objects filtered by the is_featured column
 * @method     array findByStartDate(string $start_date) Return ChildBlog objects filtered by the start_date column
 * @method     array findByEndDate(string $end_date) Return ChildBlog objects filtered by the end_date column
 * @method     array findByCreatedAt(string $created_at) Return ChildBlog objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildBlog objects filtered by the updated_at column
 *
 */
abstract class BlogQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Blog\Model\ORM\Base\BlogQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Blog\\Model\\ORM\\Blog', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildBlogQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildBlogQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Blog\Model\ORM\BlogQuery();
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
     * @return ChildBlog|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BlogTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(BlogTableMap::DATABASE_NAME);
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
     * @return   ChildBlog A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, IS_PUBLISHED, IS_FEATURED, START_DATE, END_DATE, CREATED_AT, UPDATED_AT FROM blog WHERE ID = :p0';
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
            $obj = new ChildBlog();
            $obj->hydrate($row);
            BlogTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildBlog|array|mixed the result, formatted by the current formatter
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
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BlogTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BlogTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the is_published column
     *
     * Example usage:
     * <code>
     * $query->filterByIsPublished(1234); // WHERE is_published = 1234
     * $query->filterByIsPublished(array(12, 34)); // WHERE is_published IN (12, 34)
     * $query->filterByIsPublished(array('min' => 12)); // WHERE is_published > 12
     * </code>
     *
     * @param     mixed $isPublished The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByIsPublished($isPublished = null, $comparison = null)
    {
        if (is_array($isPublished)) {
            $useMinMax = false;
            if (isset($isPublished['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_IS_PUBLISHED, $isPublished['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isPublished['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_IS_PUBLISHED, $isPublished['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_IS_PUBLISHED, $isPublished, $comparison);
    }

    /**
     * Filter the query on the is_featured column
     *
     * Example usage:
     * <code>
     * $query->filterByIsFeatured(1234); // WHERE is_featured = 1234
     * $query->filterByIsFeatured(array(12, 34)); // WHERE is_featured IN (12, 34)
     * $query->filterByIsFeatured(array('min' => 12)); // WHERE is_featured > 12
     * </code>
     *
     * @param     mixed $isFeatured The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByIsFeatured($isFeatured = null, $comparison = null)
    {
        if (is_array($isFeatured)) {
            $useMinMax = false;
            if (isset($isFeatured['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_IS_FEATURED, $isFeatured['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isFeatured['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_IS_FEATURED, $isFeatured['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_IS_FEATURED, $isFeatured, $comparison);
    }

    /**
     * Filter the query on the start_date column
     *
     * Example usage:
     * <code>
     * $query->filterByStartDate('2011-03-14'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate('now'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate(array('max' => 'yesterday')); // WHERE start_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $startDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByStartDate($startDate = null, $comparison = null)
    {
        if (is_array($startDate)) {
            $useMinMax = false;
            if (isset($startDate['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_START_DATE, $startDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startDate['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_START_DATE, $startDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_START_DATE, $startDate, $comparison);
    }

    /**
     * Filter the query on the end_date column
     *
     * Example usage:
     * <code>
     * $query->filterByEndDate('2011-03-14'); // WHERE end_date = '2011-03-14'
     * $query->filterByEndDate('now'); // WHERE end_date = '2011-03-14'
     * $query->filterByEndDate(array('max' => 'yesterday')); // WHERE end_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $endDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByEndDate($endDate = null, $comparison = null)
    {
        if (is_array($endDate)) {
            $useMinMax = false;
            if (isset($endDate['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_END_DATE, $endDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($endDate['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_END_DATE, $endDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_END_DATE, $endDate, $comparison);
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
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(BlogTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(BlogTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto object
     *
     * @param \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto|ObjectCollection $blogPhoto  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByBlogPhoto($blogPhoto, $comparison = null)
    {
        if ($blogPhoto instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto) {
            return $this
                ->addUsingAlias(BlogTableMap::COL_ID, $blogPhoto->getBlogId(), $comparison);
        } elseif ($blogPhoto instanceof ObjectCollection) {
            return $this
                ->useBlogPhotoQuery()
                ->filterByPrimaryKeys($blogPhoto->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBlogPhoto() only accepts arguments of type \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BlogPhoto relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function joinBlogPhoto($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BlogPhoto');

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
            $this->addJoinObject($join, 'BlogPhoto');
        }

        return $this;
    }

    /**
     * Use the BlogPhoto relation BlogPhoto object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery A secondary query class using the current class as primary query
     */
    public function useBlogPhotoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBlogPhoto($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BlogPhoto', '\Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Blog\Model\ORM\BlogShop object
     *
     * @param \Gekosale\Plugin\Blog\Model\ORM\BlogShop|ObjectCollection $blogShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByBlogShop($blogShop, $comparison = null)
    {
        if ($blogShop instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogShop) {
            return $this
                ->addUsingAlias(BlogTableMap::COL_ID, $blogShop->getBlogId(), $comparison);
        } elseif ($blogShop instanceof ObjectCollection) {
            return $this
                ->useBlogShopQuery()
                ->filterByPrimaryKeys($blogShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBlogShop() only accepts arguments of type \Gekosale\Plugin\Blog\Model\ORM\BlogShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BlogShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function joinBlogShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BlogShop');

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
            $this->addJoinObject($join, 'BlogShop');
        }

        return $this;
    }

    /**
     * Use the BlogShop relation BlogShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Blog\Model\ORM\BlogShopQuery A secondary query class using the current class as primary query
     */
    public function useBlogShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBlogShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BlogShop', '\Gekosale\Plugin\Blog\Model\ORM\BlogShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Blog\Model\ORM\BlogI18n object
     *
     * @param \Gekosale\Plugin\Blog\Model\ORM\BlogI18n|ObjectCollection $blogI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function filterByBlogI18n($blogI18n, $comparison = null)
    {
        if ($blogI18n instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogI18n) {
            return $this
                ->addUsingAlias(BlogTableMap::COL_ID, $blogI18n->getId(), $comparison);
        } elseif ($blogI18n instanceof ObjectCollection) {
            return $this
                ->useBlogI18nQuery()
                ->filterByPrimaryKeys($blogI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBlogI18n() only accepts arguments of type \Gekosale\Plugin\Blog\Model\ORM\BlogI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BlogI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function joinBlogI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BlogI18n');

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
            $this->addJoinObject($join, 'BlogI18n');
        }

        return $this;
    }

    /**
     * Use the BlogI18n relation BlogI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Blog\Model\ORM\BlogI18nQuery A secondary query class using the current class as primary query
     */
    public function useBlogI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinBlogI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BlogI18n', '\Gekosale\Plugin\Blog\Model\ORM\BlogI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildBlog $blog Object to remove from the list of results
     *
     * @return ChildBlogQuery The current query, for fluid interface
     */
    public function prune($blog = null)
    {
        if ($blog) {
            $this->addUsingAlias(BlogTableMap::COL_ID, $blog->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the blog table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BlogTableMap::DATABASE_NAME);
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
            BlogTableMap::clearInstancePool();
            BlogTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildBlog or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildBlog object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(BlogTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(BlogTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        BlogTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            BlogTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildBlogQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(BlogTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildBlogQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(BlogTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildBlogQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(BlogTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildBlogQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(BlogTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildBlogQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(BlogTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildBlogQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(BlogTableMap::COL_CREATED_AT);
    }

    // i18n behavior
    
    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildBlogQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'BlogI18n';
    
        return $this
            ->joinBlogI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildBlogQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('BlogI18n');
        $this->with['BlogI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildBlogI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BlogI18n', '\Gekosale\Plugin\Blog\Model\ORM\BlogI18nQuery');
    }

} // BlogQuery
