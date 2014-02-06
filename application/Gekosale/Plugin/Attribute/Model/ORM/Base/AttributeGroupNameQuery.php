<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName as ChildAttributeGroupName;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18nQuery as ChildAttributeGroupNameI18nQuery;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery as ChildAttributeGroupNameQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Map\AttributeGroupNameTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'attribute_group_name' table.
 *
 * 
 *
 * @method     ChildAttributeGroupNameQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildAttributeGroupNameQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildAttributeGroupNameQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildAttributeGroupNameQuery groupById() Group by the id column
 * @method     ChildAttributeGroupNameQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildAttributeGroupNameQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildAttributeGroupNameQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAttributeGroupNameQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAttributeGroupNameQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAttributeGroupNameQuery leftJoinAttributeGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeGroup relation
 * @method     ChildAttributeGroupNameQuery rightJoinAttributeGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeGroup relation
 * @method     ChildAttributeGroupNameQuery innerJoinAttributeGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeGroup relation
 *
 * @method     ChildAttributeGroupNameQuery leftJoinCategoryAttributeProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryAttributeProduct relation
 * @method     ChildAttributeGroupNameQuery rightJoinCategoryAttributeProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryAttributeProduct relation
 * @method     ChildAttributeGroupNameQuery innerJoinCategoryAttributeProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryAttributeProduct relation
 *
 * @method     ChildAttributeGroupNameQuery leftJoinProductAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildAttributeGroupNameQuery rightJoinProductAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildAttributeGroupNameQuery innerJoinProductAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAttribute relation
 *
 * @method     ChildAttributeGroupNameQuery leftJoinAttributeGroupNameI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeGroupNameI18n relation
 * @method     ChildAttributeGroupNameQuery rightJoinAttributeGroupNameI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeGroupNameI18n relation
 * @method     ChildAttributeGroupNameQuery innerJoinAttributeGroupNameI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeGroupNameI18n relation
 *
 * @method     ChildAttributeGroupName findOne(ConnectionInterface $con = null) Return the first ChildAttributeGroupName matching the query
 * @method     ChildAttributeGroupName findOneOrCreate(ConnectionInterface $con = null) Return the first ChildAttributeGroupName matching the query, or a new ChildAttributeGroupName object populated from the query conditions when no match is found
 *
 * @method     ChildAttributeGroupName findOneById(int $id) Return the first ChildAttributeGroupName filtered by the id column
 * @method     ChildAttributeGroupName findOneByCreatedAt(string $created_at) Return the first ChildAttributeGroupName filtered by the created_at column
 * @method     ChildAttributeGroupName findOneByUpdatedAt(string $updated_at) Return the first ChildAttributeGroupName filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildAttributeGroupName objects filtered by the id column
 * @method     array findByCreatedAt(string $created_at) Return ChildAttributeGroupName objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildAttributeGroupName objects filtered by the updated_at column
 *
 */
abstract class AttributeGroupNameQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Attribute\Model\ORM\Base\AttributeGroupNameQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\AttributeGroupName', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAttributeGroupNameQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildAttributeGroupNameQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery();
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
     * @return ChildAttributeGroupName|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AttributeGroupNameTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AttributeGroupNameTableMap::DATABASE_NAME);
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
     * @return   ChildAttributeGroupName A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CREATED_AT, UPDATED_AT FROM attribute_group_name WHERE ID = :p0';
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
            $obj = new ChildAttributeGroupName();
            $obj->hydrate($row);
            AttributeGroupNameTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildAttributeGroupName|array|mixed the result, formatted by the current formatter
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
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $id, $comparison);
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
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(AttributeGroupNameTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(AttributeGroupNameTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(AttributeGroupNameTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(AttributeGroupNameTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroup object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroup|ObjectCollection $attributeGroup  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByAttributeGroup($attributeGroup, $comparison = null)
    {
        if ($attributeGroup instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroup) {
            return $this
                ->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $attributeGroup->getAttributeGroupNameId(), $comparison);
        } elseif ($attributeGroup instanceof ObjectCollection) {
            return $this
                ->useAttributeGroupQuery()
                ->filterByPrimaryKeys($attributeGroup->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAttributeGroup() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function joinAttributeGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeGroup');

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
            $this->addJoinObject($join, 'AttributeGroup');
        }

        return $this;
    }

    /**
     * Use the AttributeGroup relation AttributeGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupQuery A secondary query class using the current class as primary query
     */
    public function useAttributeGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttributeGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeGroup', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct|ObjectCollection $categoryAttributeProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByCategoryAttributeProduct($categoryAttributeProduct, $comparison = null)
    {
        if ($categoryAttributeProduct instanceof \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct) {
            return $this
                ->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $categoryAttributeProduct->getAttributeGroupNameId(), $comparison);
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
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute|ObjectCollection $productAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByProductAttribute($productAttribute, $comparison = null)
    {
        if ($productAttribute instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute) {
            return $this
                ->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $productAttribute->getAttributeGroupNameId(), $comparison);
        } elseif ($productAttribute instanceof ObjectCollection) {
            return $this
                ->useProductAttributeQuery()
                ->filterByPrimaryKeys($productAttribute->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductAttribute() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function joinProductAttribute($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductAttribute');

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
            $this->addJoinObject($join, 'ProductAttribute');
        }

        return $this;
    }

    /**
     * Use the ProductAttribute relation ProductAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery A secondary query class using the current class as primary query
     */
    public function useProductAttributeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAttribute', '\Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n|ObjectCollection $attributeGroupNameI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function filterByAttributeGroupNameI18n($attributeGroupNameI18n, $comparison = null)
    {
        if ($attributeGroupNameI18n instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n) {
            return $this
                ->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $attributeGroupNameI18n->getId(), $comparison);
        } elseif ($attributeGroupNameI18n instanceof ObjectCollection) {
            return $this
                ->useAttributeGroupNameI18nQuery()
                ->filterByPrimaryKeys($attributeGroupNameI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAttributeGroupNameI18n() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeGroupNameI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function joinAttributeGroupNameI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeGroupNameI18n');

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
            $this->addJoinObject($join, 'AttributeGroupNameI18n');
        }

        return $this;
    }

    /**
     * Use the AttributeGroupNameI18n relation AttributeGroupNameI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18nQuery A secondary query class using the current class as primary query
     */
    public function useAttributeGroupNameI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinAttributeGroupNameI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeGroupNameI18n', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildAttributeGroupName $attributeGroupName Object to remove from the list of results
     *
     * @return ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function prune($attributeGroupName = null)
    {
        if ($attributeGroupName) {
            $this->addUsingAlias(AttributeGroupNameTableMap::COL_ID, $attributeGroupName->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the attribute_group_name table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttributeGroupNameTableMap::DATABASE_NAME);
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
            AttributeGroupNameTableMap::clearInstancePool();
            AttributeGroupNameTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildAttributeGroupName or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildAttributeGroupName object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(AttributeGroupNameTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AttributeGroupNameTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        AttributeGroupNameTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            AttributeGroupNameTableMap::clearRelatedInstancePool();
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
     * @return    ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'AttributeGroupNameI18n';
    
        return $this
            ->joinAttributeGroupNameI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('AttributeGroupNameI18n');
        $this->with['AttributeGroupNameI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildAttributeGroupNameI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeGroupNameI18n', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(AttributeGroupNameTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(AttributeGroupNameTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(AttributeGroupNameTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(AttributeGroupNameTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildAttributeGroupNameQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(AttributeGroupNameTableMap::COL_CREATED_AT);
    }

} // AttributeGroupNameQuery
