<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet as ChildProductAttributeValueSet;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery as ChildProductAttributeValueSetQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Map\ProductAttributeValueSetTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'product_attribute_value_set' table.
 *
 * 
 *
 * @method     ChildProductAttributeValueSetQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProductAttributeValueSetQuery orderByAttributeProductValueId($order = Criteria::ASC) Order by the attribute_product_value_id column
 * @method     ChildProductAttributeValueSetQuery orderByProductAttributeId($order = Criteria::ASC) Order by the product_attribute_id column
 *
 * @method     ChildProductAttributeValueSetQuery groupById() Group by the id column
 * @method     ChildProductAttributeValueSetQuery groupByAttributeProductValueId() Group by the attribute_product_value_id column
 * @method     ChildProductAttributeValueSetQuery groupByProductAttributeId() Group by the product_attribute_id column
 *
 * @method     ChildProductAttributeValueSetQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProductAttributeValueSetQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProductAttributeValueSetQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProductAttributeValueSetQuery leftJoinAttributeProductValue($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeProductValue relation
 * @method     ChildProductAttributeValueSetQuery rightJoinAttributeProductValue($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeProductValue relation
 * @method     ChildProductAttributeValueSetQuery innerJoinAttributeProductValue($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeProductValue relation
 *
 * @method     ChildProductAttributeValueSetQuery leftJoinProductAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildProductAttributeValueSetQuery rightJoinProductAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildProductAttributeValueSetQuery innerJoinProductAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAttribute relation
 *
 * @method     ChildProductAttributeValueSet findOne(ConnectionInterface $con = null) Return the first ChildProductAttributeValueSet matching the query
 * @method     ChildProductAttributeValueSet findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProductAttributeValueSet matching the query, or a new ChildProductAttributeValueSet object populated from the query conditions when no match is found
 *
 * @method     ChildProductAttributeValueSet findOneById(int $id) Return the first ChildProductAttributeValueSet filtered by the id column
 * @method     ChildProductAttributeValueSet findOneByAttributeProductValueId(int $attribute_product_value_id) Return the first ChildProductAttributeValueSet filtered by the attribute_product_value_id column
 * @method     ChildProductAttributeValueSet findOneByProductAttributeId(int $product_attribute_id) Return the first ChildProductAttributeValueSet filtered by the product_attribute_id column
 *
 * @method     array findById(int $id) Return ChildProductAttributeValueSet objects filtered by the id column
 * @method     array findByAttributeProductValueId(int $attribute_product_value_id) Return ChildProductAttributeValueSet objects filtered by the attribute_product_value_id column
 * @method     array findByProductAttributeId(int $product_attribute_id) Return ChildProductAttributeValueSet objects filtered by the product_attribute_id column
 *
 */
abstract class ProductAttributeValueSetQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Attribute\Model\ORM\Base\ProductAttributeValueSetQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttributeValueSet', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProductAttributeValueSetQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProductAttributeValueSetQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery();
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
     * @return ChildProductAttributeValueSet|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductAttributeValueSetTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductAttributeValueSetTableMap::DATABASE_NAME);
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
     * @return   ChildProductAttributeValueSet A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ATTRIBUTE_PRODUCT_VALUE_ID, PRODUCT_ATTRIBUTE_ID FROM product_attribute_value_set WHERE ID = :p0';
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
            $obj = new ChildProductAttributeValueSet();
            $obj->hydrate($row);
            ProductAttributeValueSetTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProductAttributeValueSet|array|mixed the result, formatted by the current formatter
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
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the attribute_product_value_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAttributeProductValueId(1234); // WHERE attribute_product_value_id = 1234
     * $query->filterByAttributeProductValueId(array(12, 34)); // WHERE attribute_product_value_id IN (12, 34)
     * $query->filterByAttributeProductValueId(array('min' => 12)); // WHERE attribute_product_value_id > 12
     * </code>
     *
     * @see       filterByAttributeProductValue()
     *
     * @param     mixed $attributeProductValueId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterByAttributeProductValueId($attributeProductValueId = null, $comparison = null)
    {
        if (is_array($attributeProductValueId)) {
            $useMinMax = false;
            if (isset($attributeProductValueId['min'])) {
                $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ATTRIBUTE_PRODUCT_VALUE_ID, $attributeProductValueId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attributeProductValueId['max'])) {
                $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ATTRIBUTE_PRODUCT_VALUE_ID, $attributeProductValueId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ATTRIBUTE_PRODUCT_VALUE_ID, $attributeProductValueId, $comparison);
    }

    /**
     * Filter the query on the product_attribute_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductAttributeId(1234); // WHERE product_attribute_id = 1234
     * $query->filterByProductAttributeId(array(12, 34)); // WHERE product_attribute_id IN (12, 34)
     * $query->filterByProductAttributeId(array('min' => 12)); // WHERE product_attribute_id > 12
     * </code>
     *
     * @see       filterByProductAttribute()
     *
     * @param     mixed $productAttributeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterByProductAttributeId($productAttributeId = null, $comparison = null)
    {
        if (is_array($productAttributeId)) {
            $useMinMax = false;
            if (isset($productAttributeId['min'])) {
                $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productAttributeId['max'])) {
                $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValue object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValue|ObjectCollection $attributeProductValue The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterByAttributeProductValue($attributeProductValue, $comparison = null)
    {
        if ($attributeProductValue instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValue) {
            return $this
                ->addUsingAlias(ProductAttributeValueSetTableMap::COL_ATTRIBUTE_PRODUCT_VALUE_ID, $attributeProductValue->getId(), $comparison);
        } elseif ($attributeProductValue instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductAttributeValueSetTableMap::COL_ATTRIBUTE_PRODUCT_VALUE_ID, $attributeProductValue->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAttributeProductValue() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValue or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeProductValue relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function joinAttributeProductValue($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeProductValue');

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
            $this->addJoinObject($join, 'AttributeProductValue');
        }

        return $this;
    }

    /**
     * Use the AttributeProductValue relation AttributeProductValue object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueQuery A secondary query class using the current class as primary query
     */
    public function useAttributeProductValueQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttributeProductValue($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeProductValue', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValueQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute|ObjectCollection $productAttribute The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function filterByProductAttribute($productAttribute, $comparison = null)
    {
        if ($productAttribute instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute) {
            return $this
                ->addUsingAlias(ProductAttributeValueSetTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttribute->getId(), $comparison);
        } elseif ($productAttribute instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductAttributeValueSetTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttribute->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function joinProductAttribute($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useProductAttributeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAttribute', '\Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProductAttributeValueSet $productAttributeValueSet Object to remove from the list of results
     *
     * @return ChildProductAttributeValueSetQuery The current query, for fluid interface
     */
    public function prune($productAttributeValueSet = null)
    {
        if ($productAttributeValueSet) {
            $this->addUsingAlias(ProductAttributeValueSetTableMap::COL_ID, $productAttributeValueSet->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the product_attribute_value_set table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductAttributeValueSetTableMap::DATABASE_NAME);
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
            ProductAttributeValueSetTableMap::clearInstancePool();
            ProductAttributeValueSetTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProductAttributeValueSet or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProductAttributeValueSet object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductAttributeValueSetTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProductAttributeValueSetTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProductAttributeValueSetTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProductAttributeValueSetTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ProductAttributeValueSetQuery
