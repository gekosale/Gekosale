<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValue as ChildAttributeProductValue;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18nQuery as ChildAttributeProductValueI18nQuery;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueQuery as ChildAttributeProductValueQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Map\AttributeProductValueTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'attribute_product_value' table.
 *
 * 
 *
 * @method     ChildAttributeProductValueQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildAttributeProductValueQuery orderByAttributeProductId($order = Criteria::ASC) Order by the attribute_product_id column
 * @method     ChildAttributeProductValueQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildAttributeProductValueQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildAttributeProductValueQuery groupById() Group by the id column
 * @method     ChildAttributeProductValueQuery groupByAttributeProductId() Group by the attribute_product_id column
 * @method     ChildAttributeProductValueQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildAttributeProductValueQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildAttributeProductValueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAttributeProductValueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAttributeProductValueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAttributeProductValueQuery leftJoinAttributeProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeProduct relation
 * @method     ChildAttributeProductValueQuery rightJoinAttributeProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeProduct relation
 * @method     ChildAttributeProductValueQuery innerJoinAttributeProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeProduct relation
 *
 * @method     ChildAttributeProductValueQuery leftJoinProductAttributeValueSet($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAttributeValueSet relation
 * @method     ChildAttributeProductValueQuery rightJoinProductAttributeValueSet($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAttributeValueSet relation
 * @method     ChildAttributeProductValueQuery innerJoinProductAttributeValueSet($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAttributeValueSet relation
 *
 * @method     ChildAttributeProductValueQuery leftJoinAttributeProductValueI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeProductValueI18n relation
 * @method     ChildAttributeProductValueQuery rightJoinAttributeProductValueI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeProductValueI18n relation
 * @method     ChildAttributeProductValueQuery innerJoinAttributeProductValueI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeProductValueI18n relation
 *
 * @method     ChildAttributeProductValue findOne(ConnectionInterface $con = null) Return the first ChildAttributeProductValue matching the query
 * @method     ChildAttributeProductValue findOneOrCreate(ConnectionInterface $con = null) Return the first ChildAttributeProductValue matching the query, or a new ChildAttributeProductValue object populated from the query conditions when no match is found
 *
 * @method     ChildAttributeProductValue findOneById(int $id) Return the first ChildAttributeProductValue filtered by the id column
 * @method     ChildAttributeProductValue findOneByAttributeProductId(int $attribute_product_id) Return the first ChildAttributeProductValue filtered by the attribute_product_id column
 * @method     ChildAttributeProductValue findOneByCreatedAt(string $created_at) Return the first ChildAttributeProductValue filtered by the created_at column
 * @method     ChildAttributeProductValue findOneByUpdatedAt(string $updated_at) Return the first ChildAttributeProductValue filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildAttributeProductValue objects filtered by the id column
 * @method     array findByAttributeProductId(int $attribute_product_id) Return ChildAttributeProductValue objects filtered by the attribute_product_id column
 * @method     array findByCreatedAt(string $created_at) Return ChildAttributeProductValue objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildAttributeProductValue objects filtered by the updated_at column
 *
 */
abstract class AttributeProductValueQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Attribute\Model\ORM\Base\AttributeProductValueQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\AttributeProductValue', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAttributeProductValueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildAttributeProductValueQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueQuery();
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
     * @return ChildAttributeProductValue|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AttributeProductValueTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AttributeProductValueTableMap::DATABASE_NAME);
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
     * @return   ChildAttributeProductValue A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ATTRIBUTE_PRODUCT_ID, CREATED_AT, UPDATED_AT FROM attribute_product_value WHERE ID = :p0';
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
            $obj = new ChildAttributeProductValue();
            $obj->hydrate($row);
            AttributeProductValueTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildAttributeProductValue|array|mixed the result, formatted by the current formatter
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
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the attribute_product_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAttributeProductId(1234); // WHERE attribute_product_id = 1234
     * $query->filterByAttributeProductId(array(12, 34)); // WHERE attribute_product_id IN (12, 34)
     * $query->filterByAttributeProductId(array('min' => 12)); // WHERE attribute_product_id > 12
     * </code>
     *
     * @see       filterByAttributeProduct()
     *
     * @param     mixed $attributeProductId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByAttributeProductId($attributeProductId = null, $comparison = null)
    {
        if (is_array($attributeProductId)) {
            $useMinMax = false;
            if (isset($attributeProductId['min'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProductId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attributeProductId['max'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProductId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProductId, $comparison);
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
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(AttributeProductValueTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct|ObjectCollection $attributeProduct The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByAttributeProduct($attributeProduct, $comparison = null)
    {
        if ($attributeProduct instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct) {
            return $this
                ->addUsingAlias(AttributeProductValueTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProduct->getId(), $comparison);
        } elseif ($attributeProduct instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AttributeProductValueTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProduct->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAttributeProduct() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function joinAttributeProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeProduct');

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
            $this->addJoinObject($join, 'AttributeProduct');
        }

        return $this;
    }

    /**
     * Use the AttributeProduct relation AttributeProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductQuery A secondary query class using the current class as primary query
     */
    public function useAttributeProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttributeProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeProduct', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet|ObjectCollection $productAttributeValueSet  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByProductAttributeValueSet($productAttributeValueSet, $comparison = null)
    {
        if ($productAttributeValueSet instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet) {
            return $this
                ->addUsingAlias(AttributeProductValueTableMap::COL_ID, $productAttributeValueSet->getAttributeProductValueId(), $comparison);
        } elseif ($productAttributeValueSet instanceof ObjectCollection) {
            return $this
                ->useProductAttributeValueSetQuery()
                ->filterByPrimaryKeys($productAttributeValueSet->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductAttributeValueSet() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductAttributeValueSet relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function joinProductAttributeValueSet($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductAttributeValueSet');

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
            $this->addJoinObject($join, 'ProductAttributeValueSet');
        }

        return $this;
    }

    /**
     * Use the ProductAttributeValueSet relation ProductAttributeValueSet object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery A secondary query class using the current class as primary query
     */
    public function useProductAttributeValueSetQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductAttributeValueSet($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAttributeValueSet', '\Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18n object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18n|ObjectCollection $attributeProductValueI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function filterByAttributeProductValueI18n($attributeProductValueI18n, $comparison = null)
    {
        if ($attributeProductValueI18n instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18n) {
            return $this
                ->addUsingAlias(AttributeProductValueTableMap::COL_ID, $attributeProductValueI18n->getId(), $comparison);
        } elseif ($attributeProductValueI18n instanceof ObjectCollection) {
            return $this
                ->useAttributeProductValueI18nQuery()
                ->filterByPrimaryKeys($attributeProductValueI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAttributeProductValueI18n() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeProductValueI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function joinAttributeProductValueI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeProductValueI18n');

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
            $this->addJoinObject($join, 'AttributeProductValueI18n');
        }

        return $this;
    }

    /**
     * Use the AttributeProductValueI18n relation AttributeProductValueI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18nQuery A secondary query class using the current class as primary query
     */
    public function useAttributeProductValueI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinAttributeProductValueI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeProductValueI18n', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildAttributeProductValue $attributeProductValue Object to remove from the list of results
     *
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function prune($attributeProductValue = null)
    {
        if ($attributeProductValue) {
            $this->addUsingAlias(AttributeProductValueTableMap::COL_ID, $attributeProductValue->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the attribute_product_value table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttributeProductValueTableMap::DATABASE_NAME);
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
            AttributeProductValueTableMap::clearInstancePool();
            AttributeProductValueTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildAttributeProductValue or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildAttributeProductValue object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(AttributeProductValueTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AttributeProductValueTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        AttributeProductValueTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            AttributeProductValueTableMap::clearRelatedInstancePool();
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
     * @return    ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'AttributeProductValueI18n';
    
        return $this
            ->joinAttributeProductValueI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('AttributeProductValueI18n');
        $this->with['AttributeProductValueI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildAttributeProductValueI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeProductValueI18n', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(AttributeProductValueTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(AttributeProductValueTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(AttributeProductValueTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(AttributeProductValueTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(AttributeProductValueTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildAttributeProductValueQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(AttributeProductValueTableMap::COL_CREATED_AT);
    }

} // AttributeProductValueQuery
