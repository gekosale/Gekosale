<?php

namespace Gekosale\Plugin\TechnicalData\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute as ChildTechnicalDataSetGroupAttribute;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttributeQuery as ChildTechnicalDataSetGroupAttributeQuery;
use Gekosale\Plugin\TechnicalData\Model\ORM\Map\TechnicalDataSetGroupAttributeTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'technical_data_set_group_attribute' table.
 *
 * 
 *
 * @method     ChildTechnicalDataSetGroupAttributeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTechnicalDataSetGroupAttributeQuery orderByTechnicalDataSetGroupId($order = Criteria::ASC) Order by the technical_data_set_group_id column
 * @method     ChildTechnicalDataSetGroupAttributeQuery orderByTechnicalDataAttributeId($order = Criteria::ASC) Order by the technical_data_attribute_id column
 * @method     ChildTechnicalDataSetGroupAttributeQuery orderByOrder($order = Criteria::ASC) Order by the order column
 *
 * @method     ChildTechnicalDataSetGroupAttributeQuery groupById() Group by the id column
 * @method     ChildTechnicalDataSetGroupAttributeQuery groupByTechnicalDataSetGroupId() Group by the technical_data_set_group_id column
 * @method     ChildTechnicalDataSetGroupAttributeQuery groupByTechnicalDataAttributeId() Group by the technical_data_attribute_id column
 * @method     ChildTechnicalDataSetGroupAttributeQuery groupByOrder() Group by the order column
 *
 * @method     ChildTechnicalDataSetGroupAttributeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTechnicalDataSetGroupAttributeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTechnicalDataSetGroupAttributeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTechnicalDataSetGroupAttributeQuery leftJoinTechnicalDataSetGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataSetGroup relation
 * @method     ChildTechnicalDataSetGroupAttributeQuery rightJoinTechnicalDataSetGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataSetGroup relation
 * @method     ChildTechnicalDataSetGroupAttributeQuery innerJoinTechnicalDataSetGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataSetGroup relation
 *
 * @method     ChildTechnicalDataSetGroupAttributeQuery leftJoinTechnicalDataAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataAttribute relation
 * @method     ChildTechnicalDataSetGroupAttributeQuery rightJoinTechnicalDataAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataAttribute relation
 * @method     ChildTechnicalDataSetGroupAttributeQuery innerJoinTechnicalDataAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataAttribute relation
 *
 * @method     ChildTechnicalDataSetGroupAttribute findOne(ConnectionInterface $con = null) Return the first ChildTechnicalDataSetGroupAttribute matching the query
 * @method     ChildTechnicalDataSetGroupAttribute findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTechnicalDataSetGroupAttribute matching the query, or a new ChildTechnicalDataSetGroupAttribute object populated from the query conditions when no match is found
 *
 * @method     ChildTechnicalDataSetGroupAttribute findOneById(int $id) Return the first ChildTechnicalDataSetGroupAttribute filtered by the id column
 * @method     ChildTechnicalDataSetGroupAttribute findOneByTechnicalDataSetGroupId(int $technical_data_set_group_id) Return the first ChildTechnicalDataSetGroupAttribute filtered by the technical_data_set_group_id column
 * @method     ChildTechnicalDataSetGroupAttribute findOneByTechnicalDataAttributeId(int $technical_data_attribute_id) Return the first ChildTechnicalDataSetGroupAttribute filtered by the technical_data_attribute_id column
 * @method     ChildTechnicalDataSetGroupAttribute findOneByOrder(int $order) Return the first ChildTechnicalDataSetGroupAttribute filtered by the order column
 *
 * @method     array findById(int $id) Return ChildTechnicalDataSetGroupAttribute objects filtered by the id column
 * @method     array findByTechnicalDataSetGroupId(int $technical_data_set_group_id) Return ChildTechnicalDataSetGroupAttribute objects filtered by the technical_data_set_group_id column
 * @method     array findByTechnicalDataAttributeId(int $technical_data_attribute_id) Return ChildTechnicalDataSetGroupAttribute objects filtered by the technical_data_attribute_id column
 * @method     array findByOrder(int $order) Return ChildTechnicalDataSetGroupAttribute objects filtered by the order column
 *
 */
abstract class TechnicalDataSetGroupAttributeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\TechnicalData\Model\ORM\Base\TechnicalDataSetGroupAttributeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSetGroupAttribute', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTechnicalDataSetGroupAttributeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttributeQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttributeQuery();
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
     * @return ChildTechnicalDataSetGroupAttribute|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TechnicalDataSetGroupAttributeTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TechnicalDataSetGroupAttributeTableMap::DATABASE_NAME);
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
     * @return   ChildTechnicalDataSetGroupAttribute A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, TECHNICAL_DATA_SET_GROUP_ID, TECHNICAL_DATA_ATTRIBUTE_ID, ORDER FROM technical_data_set_group_attribute WHERE ID = :p0';
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
            $obj = new ChildTechnicalDataSetGroupAttribute();
            $obj->hydrate($row);
            TechnicalDataSetGroupAttributeTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildTechnicalDataSetGroupAttribute|array|mixed the result, formatted by the current formatter
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
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the technical_data_set_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTechnicalDataSetGroupId(1234); // WHERE technical_data_set_group_id = 1234
     * $query->filterByTechnicalDataSetGroupId(array(12, 34)); // WHERE technical_data_set_group_id IN (12, 34)
     * $query->filterByTechnicalDataSetGroupId(array('min' => 12)); // WHERE technical_data_set_group_id > 12
     * </code>
     *
     * @see       filterByTechnicalDataSetGroup()
     *
     * @param     mixed $technicalDataSetGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSetGroupId($technicalDataSetGroupId = null, $comparison = null)
    {
        if (is_array($technicalDataSetGroupId)) {
            $useMinMax = false;
            if (isset($technicalDataSetGroupId['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_SET_GROUP_ID, $technicalDataSetGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($technicalDataSetGroupId['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_SET_GROUP_ID, $technicalDataSetGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_SET_GROUP_ID, $technicalDataSetGroupId, $comparison);
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
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataAttributeId($technicalDataAttributeId = null, $comparison = null)
    {
        if (is_array($technicalDataAttributeId)) {
            $useMinMax = false;
            if (isset($technicalDataAttributeId['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttributeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($technicalDataAttributeId['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttributeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttributeId, $comparison);
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
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByOrder($order = null, $comparison = null)
    {
        if (is_array($order)) {
            $useMinMax = false;
            if (isset($order['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ORDER, $order['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($order['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ORDER, $order['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ORDER, $order, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup|ObjectCollection $technicalDataSetGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSetGroup($technicalDataSetGroup, $comparison = null)
    {
        if ($technicalDataSetGroup instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup) {
            return $this
                ->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_SET_GROUP_ID, $technicalDataSetGroup->getId(), $comparison);
        } elseif ($technicalDataSetGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_SET_GROUP_ID, $technicalDataSetGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTechnicalDataSetGroup() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TechnicalDataSetGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function joinTechnicalDataSetGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TechnicalDataSetGroup');

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
            $this->addJoinObject($join, 'TechnicalDataSetGroup');
        }

        return $this;
    }

    /**
     * Use the TechnicalDataSetGroup relation TechnicalDataSetGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupQuery A secondary query class using the current class as primary query
     */
    public function useTechnicalDataSetGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTechnicalDataSetGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TechnicalDataSetGroup', '\Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute|ObjectCollection $technicalDataAttribute The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataAttribute($technicalDataAttribute, $comparison = null)
    {
        if ($technicalDataAttribute instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute) {
            return $this
                ->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttribute->getId(), $comparison);
        } elseif ($technicalDataAttribute instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_TECHNICAL_DATA_ATTRIBUTE_ID, $technicalDataAttribute->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
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
     * @param   ChildTechnicalDataSetGroupAttribute $technicalDataSetGroupAttribute Object to remove from the list of results
     *
     * @return ChildTechnicalDataSetGroupAttributeQuery The current query, for fluid interface
     */
    public function prune($technicalDataSetGroupAttribute = null)
    {
        if ($technicalDataSetGroupAttribute) {
            $this->addUsingAlias(TechnicalDataSetGroupAttributeTableMap::COL_ID, $technicalDataSetGroupAttribute->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the technical_data_set_group_attribute table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataSetGroupAttributeTableMap::DATABASE_NAME);
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
            TechnicalDataSetGroupAttributeTableMap::clearInstancePool();
            TechnicalDataSetGroupAttributeTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildTechnicalDataSetGroupAttribute or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildTechnicalDataSetGroupAttribute object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataSetGroupAttributeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TechnicalDataSetGroupAttributeTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        TechnicalDataSetGroupAttributeTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            TechnicalDataSetGroupAttributeTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // TechnicalDataSetGroupAttributeQuery
