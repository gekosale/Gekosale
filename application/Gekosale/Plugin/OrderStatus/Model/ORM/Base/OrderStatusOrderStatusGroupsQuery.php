<?php

namespace Gekosale\Plugin\OrderStatus\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroups as ChildOrderStatusOrderStatusGroups;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroupsQuery as ChildOrderStatusOrderStatusGroupsQuery;
use Gekosale\Plugin\OrderStatus\Model\ORM\Map\OrderStatusOrderStatusGroupsTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'order_status_order_status_groups' table.
 *
 * 
 *
 * @method     ChildOrderStatusOrderStatusGroupsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderStatusOrderStatusGroupsQuery orderByOrderStatusId($order = Criteria::ASC) Order by the order_status_id column
 * @method     ChildOrderStatusOrderStatusGroupsQuery orderByOrderStatusGroupsId($order = Criteria::ASC) Order by the order_status_groups_id column
 *
 * @method     ChildOrderStatusOrderStatusGroupsQuery groupById() Group by the id column
 * @method     ChildOrderStatusOrderStatusGroupsQuery groupByOrderStatusId() Group by the order_status_id column
 * @method     ChildOrderStatusOrderStatusGroupsQuery groupByOrderStatusGroupsId() Group by the order_status_groups_id column
 *
 * @method     ChildOrderStatusOrderStatusGroupsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderStatusOrderStatusGroupsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderStatusOrderStatusGroupsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderStatusOrderStatusGroupsQuery leftJoinOrderStatusGroups($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderStatusGroups relation
 * @method     ChildOrderStatusOrderStatusGroupsQuery rightJoinOrderStatusGroups($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderStatusGroups relation
 * @method     ChildOrderStatusOrderStatusGroupsQuery innerJoinOrderStatusGroups($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderStatusGroups relation
 *
 * @method     ChildOrderStatusOrderStatusGroupsQuery leftJoinOrderStatus($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderStatus relation
 * @method     ChildOrderStatusOrderStatusGroupsQuery rightJoinOrderStatus($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderStatus relation
 * @method     ChildOrderStatusOrderStatusGroupsQuery innerJoinOrderStatus($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderStatus relation
 *
 * @method     ChildOrderStatusOrderStatusGroups findOne(ConnectionInterface $con = null) Return the first ChildOrderStatusOrderStatusGroups matching the query
 * @method     ChildOrderStatusOrderStatusGroups findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrderStatusOrderStatusGroups matching the query, or a new ChildOrderStatusOrderStatusGroups object populated from the query conditions when no match is found
 *
 * @method     ChildOrderStatusOrderStatusGroups findOneById(int $id) Return the first ChildOrderStatusOrderStatusGroups filtered by the id column
 * @method     ChildOrderStatusOrderStatusGroups findOneByOrderStatusId(int $order_status_id) Return the first ChildOrderStatusOrderStatusGroups filtered by the order_status_id column
 * @method     ChildOrderStatusOrderStatusGroups findOneByOrderStatusGroupsId(int $order_status_groups_id) Return the first ChildOrderStatusOrderStatusGroups filtered by the order_status_groups_id column
 *
 * @method     array findById(int $id) Return ChildOrderStatusOrderStatusGroups objects filtered by the id column
 * @method     array findByOrderStatusId(int $order_status_id) Return ChildOrderStatusOrderStatusGroups objects filtered by the order_status_id column
 * @method     array findByOrderStatusGroupsId(int $order_status_groups_id) Return ChildOrderStatusOrderStatusGroups objects filtered by the order_status_groups_id column
 *
 */
abstract class OrderStatusOrderStatusGroupsQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\OrderStatus\Model\ORM\Base\OrderStatusOrderStatusGroupsQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\OrderStatus\\Model\\ORM\\OrderStatusOrderStatusGroups', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderStatusOrderStatusGroupsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroupsQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroupsQuery();
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
     * @return ChildOrderStatusOrderStatusGroups|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderStatusOrderStatusGroupsTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderStatusOrderStatusGroupsTableMap::DATABASE_NAME);
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
     * @return   ChildOrderStatusOrderStatusGroups A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ORDER_STATUS_ID, ORDER_STATUS_GROUPS_ID FROM order_status_order_status_groups WHERE ID = :p0';
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
            $obj = new ChildOrderStatusOrderStatusGroups();
            $obj->hydrate($row);
            OrderStatusOrderStatusGroupsTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildOrderStatusOrderStatusGroups|array|mixed the result, formatted by the current formatter
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
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the order_status_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderStatusId(1234); // WHERE order_status_id = 1234
     * $query->filterByOrderStatusId(array(12, 34)); // WHERE order_status_id IN (12, 34)
     * $query->filterByOrderStatusId(array('min' => 12)); // WHERE order_status_id > 12
     * </code>
     *
     * @see       filterByOrderStatus()
     *
     * @param     mixed $orderStatusId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterByOrderStatusId($orderStatusId = null, $comparison = null)
    {
        if (is_array($orderStatusId)) {
            $useMinMax = false;
            if (isset($orderStatusId['min'])) {
                $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_ID, $orderStatusId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderStatusId['max'])) {
                $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_ID, $orderStatusId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_ID, $orderStatusId, $comparison);
    }

    /**
     * Filter the query on the order_status_groups_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderStatusGroupsId(1234); // WHERE order_status_groups_id = 1234
     * $query->filterByOrderStatusGroupsId(array(12, 34)); // WHERE order_status_groups_id IN (12, 34)
     * $query->filterByOrderStatusGroupsId(array('min' => 12)); // WHERE order_status_groups_id > 12
     * </code>
     *
     * @see       filterByOrderStatusGroups()
     *
     * @param     mixed $orderStatusGroupsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterByOrderStatusGroupsId($orderStatusGroupsId = null, $comparison = null)
    {
        if (is_array($orderStatusGroupsId)) {
            $useMinMax = false;
            if (isset($orderStatusGroupsId['min'])) {
                $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroupsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderStatusGroupsId['max'])) {
                $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroupsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroupsId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups object
     *
     * @param \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups|ObjectCollection $orderStatusGroups The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterByOrderStatusGroups($orderStatusGroups, $comparison = null)
    {
        if ($orderStatusGroups instanceof \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups) {
            return $this
                ->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroups->getId(), $comparison);
        } elseif ($orderStatusGroups instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroups->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrderStatusGroups() only accepts arguments of type \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderStatusGroups relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function joinOrderStatusGroups($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderStatusGroups');

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
            $this->addJoinObject($join, 'OrderStatusGroups');
        }

        return $this;
    }

    /**
     * Use the OrderStatusGroups relation OrderStatusGroups object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroupsQuery A secondary query class using the current class as primary query
     */
    public function useOrderStatusGroupsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderStatusGroups($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderStatusGroups', '\Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroupsQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatus object
     *
     * @param \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatus|ObjectCollection $orderStatus The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function filterByOrderStatus($orderStatus, $comparison = null)
    {
        if ($orderStatus instanceof \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatus) {
            return $this
                ->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_ID, $orderStatus->getId(), $comparison);
        } elseif ($orderStatus instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ORDER_STATUS_ID, $orderStatus->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrderStatus() only accepts arguments of type \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatus or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderStatus relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function joinOrderStatus($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderStatus');

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
            $this->addJoinObject($join, 'OrderStatus');
        }

        return $this;
    }

    /**
     * Use the OrderStatus relation OrderStatus object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusQuery A secondary query class using the current class as primary query
     */
    public function useOrderStatusQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderStatus($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderStatus', '\Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOrderStatusOrderStatusGroups $orderStatusOrderStatusGroups Object to remove from the list of results
     *
     * @return ChildOrderStatusOrderStatusGroupsQuery The current query, for fluid interface
     */
    public function prune($orderStatusOrderStatusGroups = null)
    {
        if ($orderStatusOrderStatusGroups) {
            $this->addUsingAlias(OrderStatusOrderStatusGroupsTableMap::COL_ID, $orderStatusOrderStatusGroups->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order_status_order_status_groups table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderStatusOrderStatusGroupsTableMap::DATABASE_NAME);
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
            OrderStatusOrderStatusGroupsTableMap::clearInstancePool();
            OrderStatusOrderStatusGroupsTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrderStatusOrderStatusGroups or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildOrderStatusOrderStatusGroups object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderStatusOrderStatusGroupsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderStatusOrderStatusGroupsTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        OrderStatusOrderStatusGroupsTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            OrderStatusOrderStatusGroupsTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderStatusOrderStatusGroupsQuery
