<?php

namespace Gekosale\Plugin\TechnicalData\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttribute as ChildProductTechnicalDataGroupAttribute;
use Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttributeQuery as ChildProductTechnicalDataGroupAttributeQuery;
use Gekosale\Plugin\TechnicalData\Model\ORM\Map\ProductTechnicalDataGroupAttributeTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'product_technical_data_group_attribute' table.
 *
 * 
 *
 * @method     ChildProductTechnicalDataGroupAttributeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProductTechnicalDataGroupAttributeQuery orderByProductTechnicalDataGroupId($order = Criteria::ASC) Order by the product_technical_data_group_id column
 * @method     ChildProductTechnicalDataGroupAttributeQuery orderByTechnicalDataAttributeId($order = Criteria::ASC) Order by the technical_data_attribute_id column
 * @method     ChildProductTechnicalDataGroupAttributeQuery orderByOrder($order = Criteria::ASC) Order by the order column
 * @method     ChildProductTechnicalDataGroupAttributeQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     ChildProductTechnicalDataGroupAttributeQuery groupById() Group by the id column
 * @method     ChildProductTechnicalDataGroupAttributeQuery groupByProductTechnicalDataGroupId() Group by the product_technical_data_group_id column
 * @method     ChildProductTechnicalDataGroupAttributeQuery groupByTechnicalDataAttributeId() Group by the technical_data_attribute_id column
 * @method     ChildProductTechnicalDataGroupAttributeQuery groupByOrder() Group by the order column
 * @method     ChildProductTechnicalDataGroupAttributeQuery groupByValue() Group by the value column
 *
 * @method     ChildProductTechnicalDataGroupAttributeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProductTechnicalDataGroupAttributeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProductTechnicalDataGroupAttributeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProductTechnicalDataGroupAttributeQuery leftJoinProductTechnicalDataGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductTechnicalDataGroup relation
 * @method     ChildProductTechnicalDataGroupAttributeQuery rightJoinProductTechnicalDataGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductTechnicalDataGroup relation
 * @method     ChildProductTechnicalDataGroupAttributeQuery innerJoinProductTechnicalDataGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductTechnicalDataGroup relation
 *
 * @method     ChildProductTechnicalDataGroupAttributeQuery leftJoinTechnicalDataAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataAttribute relation
 * @method     ChildProductTechnicalDataGroupAttributeQuery rightJoinTechnicalDataAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataAttribute relation
 * @method     ChildProductTechnicalDataGroupAttributeQuery innerJoinTechnicalDataAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataAttribute relation
 *
 * @method     ChildProductTechnicalDataGroupAttribute findOne(ConnectionInterface $con = null) Return the first ChildProductTechnicalDataGroupAttribute matching the query
 * @method     ChildProductTechnicalDataGroupAttribute findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProductTechnicalDataGroupAttribute matching the query, or a new ChildProductTechnicalDataGroupAttribute object populated from the query conditions when no match is found
 *
 * @method     ChildProductTechnicalDataGroupAttribute findOneById(int $id) Return the first ChildProductTechnicalDataGroupAttribute filtered by the id column
 * @method     ChildProductTechnicalDataGroupAttribute findOneByProductTechnicalDataGroupId(int $product_technical_data_group_id) Return the first ChildProductTechnicalDataGroupAttribute filtered by the product_technical_data_group_id column
 * @method     ChildProductTechnicalDataGroupAttribute findOneByTechnicalDataAttributeId(int $technical_data_attribute_id) Return the first ChildProductTechnicalDataGroupAttribute filtered by the technical_data_attribute_id column
 * @method     ChildProductTechnicalDataGroupAttribute findOneByOrder(int $order) Return the first ChildProductTechnicalDataGroupAttribute filtered by the order column
 * @method     ChildProductTechnicalDataGroupAttribute findOneByValue(string $value) Return the first ChildProductTechnicalDataGroupAttribute filtered by the value column
 *
 * @method     array findById(int $id) Return ChildProductTechnicalDataGroupAttribute objects filtered by the id column
 * @method     array findByProductTechnicalDataGroupId(int $product_technical_data_group_id) Return ChildProductTechnicalDataGroupAttribute objects filtered by the product_technical_data_group_id column
 * @method     array findByTechnicalDataAttributeId(int $technical_data_attribute_id) Return ChildProductTechnicalDataGroupAttribute objects filtered by the technical_data_attribute_id column
 * @method     array findByOrder(int $order) Return ChildProductTechnicalDataGroupAttribute objects filtered by the order column
 * @method     array findByValue(string $value) Return ChildProductTechnicalDataGroupAttribute objects filtered by the value column
 *
 */
abstract class ProductTechnicalDataGroupAttributeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\TechnicalData\Model\ORM\Base\ProductTechnicalDataGroupAttributeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\ProductTechnicalDataGroupAttribute', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProductTechnicalDataGroupAttributeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttributeQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttributeQuery();
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
     * @return ChildProductTechnicalDataGroupAttribute|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductTechnicalDataGroupAttributeTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductTechnicalDataGroupAttributeTableMap::DATABASE_NAME);
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
     * @return   ChildProductTechnicalDataGroupAttribute A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PRODUCT_TECHNICAL_DATA_GROUP_ID, TECHNICAL_DATA_ATTRIBUTE_ID, ORDER, VALUE FROM product_technical_data_group_attribute WHERE ID = :p0';
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
            $obj = new ChildProductTechnicalDataGroupAttribute();
            $obj->hydrate($row);
            ProductTechnicalDataGroupAttributeTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProductTechnicalDataGroupAttribute|array|mixed the result, formatted by the current formatter
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
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the product_technical_data_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductTechnicalDataGroupId(1234); // WHERE product_technical_data_group_id = 1234
     * $query->filterByProductTechnicalDataGroupId(array(12, 34)); // WHERE product_technical_data_group_id IN (12, 34)
     * $query->filterByProductTechnicalDataGroupId(array('min' => 12)); // WHERE product_technical_data_group_id > 12
     * </code>
     *
     * @see       filterByProductTechnicalDataGroup()
     *
     * @param     mixed $productTechnicalDataGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByProductTechnicalDataGroupId($productTechnicalDataGroupId = null, $comparison = null)
    {
        if (is_array($productTechnicalDataGroupId)) {
            $useMinMax = false;
            if (isset($productTechnicalDataGroupId['min'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_PRODUCT_TECHNICAL_DATA_GROUP_ID, $productTechnicalDataGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productTechnicalDataGroupId['max'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_PRODUCT_TECHNICAL_DATA_GROUP_ID, $productTechnicalDataGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_PRODUCT_TECHNICAL_DATA_GROUP_ID, $productTechnicalDataGroupId, $comparison);
    }

    /**
     * Filter the query on the technical_data_attribute_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTechnicalDataAttributeId(1234); // WHERE technical_data_attribute_id = 1234
     * $query->filterByTechnicalDataAttributeId(array(12, 34)); // WHERE technical_data_attribute_id IN (12, 34)
     * $query->filterByTechnicalDataAttributeId(array('min' => 12)); // WHERE technical_data_attribute_id > 12
     * </code>
     *
     * @see       filterByTechnicalDataAttribute()
     *
     * @param     mixed $technicalDataAttributeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataAttributeId($technicalDataAttributeId = null, $comparison = null)
    {
        if (is_array($technicalDataAttributeId)) {
            $useMinMax = false;
            if (isset($technicalDataAttributeId['min'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttributeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($technicalDataAttributeId['max'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttributeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttributeId, $comparison);
    }

    /**
     * Filter the query on the order column
     *
     * Example usage:
     * <code>
     * $query->filterByOrder(1234); // WHERE order = 1234
     * $query->filterByOrder(array(12, 34)); // WHERE order IN (12, 34)
     * $query->filterByOrder(array('min' => 12)); // WHERE order > 12
     * </code>
     *
     * @param     mixed $order The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByOrder($order = null, $comparison = null)
    {
        if (is_array($order)) {
            $useMinMax = false;
            if (isset($order['min'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ORDER, $order['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($order['max'])) {
                $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ORDER, $order['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ORDER, $order, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup|ObjectCollection $productTechnicalDataGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByProductTechnicalDataGroup($productTechnicalDataGroup, $comparison = null)
    {
        if ($productTechnicalDataGroup instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup) {
            return $this
                ->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_PRODUCT_TECHNICAL_DATA_GROUP_ID, $productTechnicalDataGroup->getId(), $comparison);
        } elseif ($productTechnicalDataGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_PRODUCT_TECHNICAL_DATA_GROUP_ID, $productTechnicalDataGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProductTechnicalDataGroup() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductTechnicalDataGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function joinProductTechnicalDataGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductTechnicalDataGroup');

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
            $this->addJoinObject($join, 'ProductTechnicalDataGroup');
        }

        return $this;
    }

    /**
     * Use the ProductTechnicalDataGroup relation ProductTechnicalDataGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupQuery A secondary query class using the current class as primary query
     */
    public function useProductTechnicalDataGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductTechnicalDataGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductTechnicalDataGroup', '\Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute|ObjectCollection $technicalDataAttribute The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataAttribute($technicalDataAttribute, $comparison = null)
    {
        if ($technicalDataAttribute instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute) {
            return $this
                ->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttribute->getId(), $comparison);
        } elseif ($technicalDataAttribute instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttribute->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTechnicalDataAttribute() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TechnicalDataAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function joinTechnicalDataAttribute($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TechnicalDataAttribute');

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
            $this->addJoinObject($join, 'TechnicalDataAttribute');
        }

        return $this;
    }

    /**
     * Use the TechnicalDataAttribute relation TechnicalDataAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttributeQuery A secondary query class using the current class as primary query
     */
    public function useTechnicalDataAttributeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTechnicalDataAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TechnicalDataAttribute', '\Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttributeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProductTechnicalDataGroupAttribute $productTechnicalDataGroupAttribute Object to remove from the list of results
     *
     * @return ChildProductTechnicalDataGroupAttributeQuery The current query, for fluid interface
     */
    public function prune($productTechnicalDataGroupAttribute = null)
    {
        if ($productTechnicalDataGroupAttribute) {
            $this->addUsingAlias(ProductTechnicalDataGroupAttributeTableMap::COL_ID, $productTechnicalDataGroupAttribute->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the product_technical_data_group_attribute table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTechnicalDataGroupAttributeTableMap::DATABASE_NAME);
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
            ProductTechnicalDataGroupAttributeTableMap::clearInstancePool();
            ProductTechnicalDataGroupAttributeTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProductTechnicalDataGroupAttribute or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProductTechnicalDataGroupAttribute object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTechnicalDataGroupAttributeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProductTechnicalDataGroupAttributeTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProductTechnicalDataGroupAttributeTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProductTechnicalDataGroupAttributeTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ProductTechnicalDataGroupAttributeQuery
