<?php

namespace Gekosale\Plugin\Category\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct;
use Gekosale\Plugin\Category\Model\ORM\Category as ChildCategory;
use Gekosale\Plugin\Category\Model\ORM\CategoryI18nQuery as ChildCategoryI18nQuery;
use Gekosale\Plugin\Category\Model\ORM\CategoryQuery as ChildCategoryQuery;
use Gekosale\Plugin\Category\Model\ORM\Map\CategoryTableMap;
use Gekosale\Plugin\File\Model\ORM\File;
use Gekosale\Plugin\Product\Model\ORM\ProductCategory;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'category' table.
 *
 * 
 *
 * @method     ChildCategoryQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCategoryQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method     ChildCategoryQuery orderByHierarchy($order = Criteria::ASC) Order by the hierarchy column
 * @method     ChildCategoryQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 * @method     ChildCategoryQuery orderByIsEnabled($order = Criteria::ASC) Order by the enabled column
 * @method     ChildCategoryQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCategoryQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCategoryQuery groupById() Group by the id column
 * @method     ChildCategoryQuery groupByPhotoId() Group by the photo_id column
 * @method     ChildCategoryQuery groupByHierarchy() Group by the hierarchy column
 * @method     ChildCategoryQuery groupByCategoryId() Group by the category_id column
 * @method     ChildCategoryQuery groupByIsEnabled() Group by the enabled column
 * @method     ChildCategoryQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCategoryQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCategoryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCategoryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCategoryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCategoryQuery leftJoinCategoryRelatedByCategoryId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryRelatedByCategoryId relation
 * @method     ChildCategoryQuery rightJoinCategoryRelatedByCategoryId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryRelatedByCategoryId relation
 * @method     ChildCategoryQuery innerJoinCategoryRelatedByCategoryId($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryRelatedByCategoryId relation
 *
 * @method     ChildCategoryQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method     ChildCategoryQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method     ChildCategoryQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method     ChildCategoryQuery leftJoinCategoryRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryRelatedById relation
 * @method     ChildCategoryQuery rightJoinCategoryRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryRelatedById relation
 * @method     ChildCategoryQuery innerJoinCategoryRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryRelatedById relation
 *
 * @method     ChildCategoryQuery leftJoinCategoryAttributeProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryAttributeProduct relation
 * @method     ChildCategoryQuery rightJoinCategoryAttributeProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryAttributeProduct relation
 * @method     ChildCategoryQuery innerJoinCategoryAttributeProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryAttributeProduct relation
 *
 * @method     ChildCategoryQuery leftJoinCategoryPath($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryPath relation
 * @method     ChildCategoryQuery rightJoinCategoryPath($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryPath relation
 * @method     ChildCategoryQuery innerJoinCategoryPath($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryPath relation
 *
 * @method     ChildCategoryQuery leftJoinProductCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductCategory relation
 * @method     ChildCategoryQuery rightJoinProductCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductCategory relation
 * @method     ChildCategoryQuery innerJoinProductCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductCategory relation
 *
 * @method     ChildCategoryQuery leftJoinCategoryShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryShop relation
 * @method     ChildCategoryQuery rightJoinCategoryShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryShop relation
 * @method     ChildCategoryQuery innerJoinCategoryShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryShop relation
 *
 * @method     ChildCategoryQuery leftJoinCategoryI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryI18n relation
 * @method     ChildCategoryQuery rightJoinCategoryI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryI18n relation
 * @method     ChildCategoryQuery innerJoinCategoryI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryI18n relation
 *
 * @method     ChildCategory findOne(ConnectionInterface $con = null) Return the first ChildCategory matching the query
 * @method     ChildCategory findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCategory matching the query, or a new ChildCategory object populated from the query conditions when no match is found
 *
 * @method     ChildCategory findOneById(int $id) Return the first ChildCategory filtered by the id column
 * @method     ChildCategory findOneByPhotoId(int $photo_id) Return the first ChildCategory filtered by the photo_id column
 * @method     ChildCategory findOneByHierarchy(int $hierarchy) Return the first ChildCategory filtered by the hierarchy column
 * @method     ChildCategory findOneByCategoryId(int $category_id) Return the first ChildCategory filtered by the category_id column
 * @method     ChildCategory findOneByIsEnabled(int $enabled) Return the first ChildCategory filtered by the enabled column
 * @method     ChildCategory findOneByCreatedAt(string $created_at) Return the first ChildCategory filtered by the created_at column
 * @method     ChildCategory findOneByUpdatedAt(string $updated_at) Return the first ChildCategory filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildCategory objects filtered by the id column
 * @method     array findByPhotoId(int $photo_id) Return ChildCategory objects filtered by the photo_id column
 * @method     array findByHierarchy(int $hierarchy) Return ChildCategory objects filtered by the hierarchy column
 * @method     array findByCategoryId(int $category_id) Return ChildCategory objects filtered by the category_id column
 * @method     array findByIsEnabled(int $enabled) Return ChildCategory objects filtered by the enabled column
 * @method     array findByCreatedAt(string $created_at) Return ChildCategory objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildCategory objects filtered by the updated_at column
 *
 */
abstract class CategoryQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Category\Model\ORM\Base\CategoryQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCategoryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCategoryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Category\Model\ORM\CategoryQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Category\Model\ORM\CategoryQuery();
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
     * @return ChildCategory|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CategoryTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CategoryTableMap::DATABASE_NAME);
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
     * @return   ChildCategory A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PHOTO_ID, HIERARCHY, CATEGORY_ID, ENABLED, CREATED_AT, UPDATED_AT FROM category WHERE ID = :p0';
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
            $obj = new ChildCategory();
            $obj->hydrate($row);
            CategoryTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCategory|array|mixed the result, formatted by the current formatter
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
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CategoryTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CategoryTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId(1234); // WHERE photo_id = 1234
     * $query->filterByPhotoId(array(12, 34)); // WHERE photo_id IN (12, 34)
     * $query->filterByPhotoId(array('min' => 12)); // WHERE photo_id > 12
     * </code>
     *
     * @see       filterByFile()
     *
     * @param     mixed $photoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_PHOTO_ID, $photoId, $comparison);
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
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByHierarchy($hierarchy = null, $comparison = null)
    {
        if (is_array($hierarchy)) {
            $useMinMax = false;
            if (isset($hierarchy['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_HIERARCHY, $hierarchy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hierarchy['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_HIERARCHY, $hierarchy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_HIERARCHY, $hierarchy, $comparison);
    }

    /**
     * Filter the query on the category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoryId(1234); // WHERE category_id = 1234
     * $query->filterByCategoryId(array(12, 34)); // WHERE category_id IN (12, 34)
     * $query->filterByCategoryId(array('min' => 12)); // WHERE category_id > 12
     * </code>
     *
     * @see       filterByCategoryRelatedByCategoryId()
     *
     * @param     mixed $categoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_CATEGORY_ID, $categoryId, $comparison);
    }

    /**
     * Filter the query on the enabled column
     *
     * Example usage:
     * <code>
     * $query->filterByIsEnabled(1234); // WHERE enabled = 1234
     * $query->filterByIsEnabled(array(12, 34)); // WHERE enabled IN (12, 34)
     * $query->filterByIsEnabled(array('min' => 12)); // WHERE enabled > 12
     * </code>
     *
     * @param     mixed $isEnabled The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByIsEnabled($isEnabled = null, $comparison = null)
    {
        if (is_array($isEnabled)) {
            $useMinMax = false;
            if (isset($isEnabled['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_ENABLED, $isEnabled['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isEnabled['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_ENABLED, $isEnabled['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_ENABLED, $isEnabled, $comparison);
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
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CategoryTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CategoryTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\Category object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\Category|ObjectCollection $category The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryRelatedByCategoryId($category, $comparison = null)
    {
        if ($category instanceof \Gekosale\Plugin\Category\Model\ORM\Category) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_CATEGORY_ID, $category->getId(), $comparison);
        } elseif ($category instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CategoryTableMap::COL_CATEGORY_ID, $category->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategoryRelatedByCategoryId() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\Category or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryRelatedByCategoryId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinCategoryRelatedByCategoryId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryRelatedByCategoryId');

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
            $this->addJoinObject($join, 'CategoryRelatedByCategoryId');
        }

        return $this;
    }

    /**
     * Use the CategoryRelatedByCategoryId relation Category object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryRelatedByCategoryIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCategoryRelatedByCategoryId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryRelatedByCategoryId', '\Gekosale\Plugin\Category\Model\ORM\CategoryQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\File\Model\ORM\File object
     *
     * @param \Gekosale\Plugin\File\Model\ORM\File|ObjectCollection $file The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof \Gekosale\Plugin\File\Model\ORM\File) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_PHOTO_ID, $file->getId(), $comparison);
        } elseif ($file instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CategoryTableMap::COL_PHOTO_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFile() only accepts arguments of type \Gekosale\Plugin\File\Model\ORM\File or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the File relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinFile($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('File');

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
            $this->addJoinObject($join, 'File');
        }

        return $this;
    }

    /**
     * Use the File relation File object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\File\Model\ORM\FileQuery A secondary query class using the current class as primary query
     */
    public function useFileQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'File', '\Gekosale\Plugin\File\Model\ORM\FileQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\Category object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\Category|ObjectCollection $category  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryRelatedById($category, $comparison = null)
    {
        if ($category instanceof \Gekosale\Plugin\Category\Model\ORM\Category) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_ID, $category->getCategoryId(), $comparison);
        } elseif ($category instanceof ObjectCollection) {
            return $this
                ->useCategoryRelatedByIdQuery()
                ->filterByPrimaryKeys($category->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryRelatedById() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\Category or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinCategoryRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryRelatedById');

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
            $this->addJoinObject($join, 'CategoryRelatedById');
        }

        return $this;
    }

    /**
     * Use the CategoryRelatedById relation Category object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCategoryRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryRelatedById', '\Gekosale\Plugin\Category\Model\ORM\CategoryQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct|ObjectCollection $categoryAttributeProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryAttributeProduct($categoryAttributeProduct, $comparison = null)
    {
        if ($categoryAttributeProduct instanceof \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_ID, $categoryAttributeProduct->getCategoryId(), $comparison);
        } elseif ($categoryAttributeProduct instanceof ObjectCollection) {
            return $this
                ->useCategoryAttributeProductQuery()
                ->filterByPrimaryKeys($categoryAttributeProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryAttributeProduct() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryAttributeProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinCategoryAttributeProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryAttributeProduct');

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
            $this->addJoinObject($join, 'CategoryAttributeProduct');
        }

        return $this;
    }

    /**
     * Use the CategoryAttributeProduct relation CategoryAttributeProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery A secondary query class using the current class as primary query
     */
    public function useCategoryAttributeProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategoryAttributeProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryAttributeProduct', '\Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\CategoryPath object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\CategoryPath|ObjectCollection $categoryPath  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryPath($categoryPath, $comparison = null)
    {
        if ($categoryPath instanceof \Gekosale\Plugin\Category\Model\ORM\CategoryPath) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_ID, $categoryPath->getCategoryId(), $comparison);
        } elseif ($categoryPath instanceof ObjectCollection) {
            return $this
                ->useCategoryPathQuery()
                ->filterByPrimaryKeys($categoryPath->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryPath() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\CategoryPath or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryPath relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinCategoryPath($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryPath');

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
            $this->addJoinObject($join, 'CategoryPath');
        }

        return $this;
    }

    /**
     * Use the CategoryPath relation CategoryPath object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryPathQuery A secondary query class using the current class as primary query
     */
    public function useCategoryPathQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategoryPath($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryPath', '\Gekosale\Plugin\Category\Model\ORM\CategoryPathQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductCategory object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductCategory|ObjectCollection $productCategory  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByProductCategory($productCategory, $comparison = null)
    {
        if ($productCategory instanceof \Gekosale\Plugin\Product\Model\ORM\ProductCategory) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_ID, $productCategory->getCategoryId(), $comparison);
        } elseif ($productCategory instanceof ObjectCollection) {
            return $this
                ->useProductCategoryQuery()
                ->filterByPrimaryKeys($productCategory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductCategory() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductCategory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductCategory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinProductCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductCategory');

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
            $this->addJoinObject($join, 'ProductCategory');
        }

        return $this;
    }

    /**
     * Use the ProductCategory relation ProductCategory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery A secondary query class using the current class as primary query
     */
    public function useProductCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductCategory', '\Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\CategoryShop object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\CategoryShop|ObjectCollection $categoryShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryShop($categoryShop, $comparison = null)
    {
        if ($categoryShop instanceof \Gekosale\Plugin\Category\Model\ORM\CategoryShop) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_ID, $categoryShop->getCategoryId(), $comparison);
        } elseif ($categoryShop instanceof ObjectCollection) {
            return $this
                ->useCategoryShopQuery()
                ->filterByPrimaryKeys($categoryShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryShop() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\CategoryShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinCategoryShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryShop');

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
            $this->addJoinObject($join, 'CategoryShop');
        }

        return $this;
    }

    /**
     * Use the CategoryShop relation CategoryShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery A secondary query class using the current class as primary query
     */
    public function useCategoryShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategoryShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryShop', '\Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\CategoryI18n object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\CategoryI18n|ObjectCollection $categoryI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryI18n($categoryI18n, $comparison = null)
    {
        if ($categoryI18n instanceof \Gekosale\Plugin\Category\Model\ORM\CategoryI18n) {
            return $this
                ->addUsingAlias(CategoryTableMap::COL_ID, $categoryI18n->getId(), $comparison);
        } elseif ($categoryI18n instanceof ObjectCollection) {
            return $this
                ->useCategoryI18nQuery()
                ->filterByPrimaryKeys($categoryI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryI18n() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\CategoryI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function joinCategoryI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryI18n');

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
            $this->addJoinObject($join, 'CategoryI18n');
        }

        return $this;
    }

    /**
     * Use the CategoryI18n relation CategoryI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryI18nQuery A secondary query class using the current class as primary query
     */
    public function useCategoryI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCategoryI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryI18n', '\Gekosale\Plugin\Category\Model\ORM\CategoryI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCategory $category Object to remove from the list of results
     *
     * @return ChildCategoryQuery The current query, for fluid interface
     */
    public function prune($category = null)
    {
        if ($category) {
            $this->addUsingAlias(CategoryTableMap::COL_ID, $category->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the category table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
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
            CategoryTableMap::clearInstancePool();
            CategoryTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCategory or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCategory object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CategoryTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CategoryTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CategoryTableMap::clearRelatedInstancePool();
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
     * @return    ChildCategoryQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CategoryI18n';
    
        return $this
            ->joinCategoryI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCategoryQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CategoryI18n');
        $this->with['CategoryI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildCategoryI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryI18n', '\Gekosale\Plugin\Category\Model\ORM\CategoryI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildCategoryQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CategoryTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildCategoryQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CategoryTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildCategoryQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CategoryTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildCategoryQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CategoryTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildCategoryQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CategoryTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildCategoryQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CategoryTableMap::COL_CREATED_AT);
    }

} // CategoryQuery
