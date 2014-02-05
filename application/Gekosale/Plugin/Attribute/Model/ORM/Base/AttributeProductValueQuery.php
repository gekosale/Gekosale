<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeProductValue as ChildAttributeProductValue;
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
 * @method     ChildAttributeProductValueQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildAttributeProductValueQuery orderByAttributeProductId($order = Criteria::ASC) Order by the attribute_product_id column
 *
 * @method     ChildAttributeProductValueQuery groupById() Group by the id column
 * @method     ChildAttributeProductValueQuery groupByName() Group by the name column
 * @method     ChildAttributeProductValueQuery groupByAttributeProductId() Group by the attribute_product_id column
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
 * @method     ChildAttributeProductValue findOne(ConnectionInterface $con = null) Return the first ChildAttributeProductValue matching the query
 * @method     ChildAttributeProductValue findOneOrCreate(ConnectionInterface $con = null) Return the first ChildAttributeProductValue matching the query, or a new ChildAttributeProductValue object populated from the query conditions when no match is found
 *
 * @method     ChildAttributeProductValue findOneById(int $id) Return the first ChildAttributeProductValue filtered by the id column
 * @method     ChildAttributeProductValue findOneByName(string $name) Return the first ChildAttributeProductValue filtered by the name column
 * @method     ChildAttributeProductValue findOneByAttributeProductId(int $attribute_product_id) Return the first ChildAttributeProductValue filtered by the attribute_product_id column
 *
 * @method     array findById(int $id) Return ChildAttributeProductValue objects filtered by the id column
 * @method     array findByName(string $name) Return ChildAttributeProductValue objects filtered by the name column
 * @method     array findByAttributeProductId(int $attribute_product_id) Return ChildAttributeProductValue objects filtered by the attribute_product_id column
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
        $sql = 'SELECT ID, NAME, ATTRIBUTE_PRODUCT_ID FROM attribute_product_value WHERE ID = :p0';
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
     * @return ChildAttributeProductValueQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AttributeProductValueTableMap::COL_NAME, $name, $comparison);
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

} // AttributeProductValueQuery
