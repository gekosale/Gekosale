<?php

namespace Gekosale\Plugin\OrderStatus\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatus as ChildOrderStatus;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusQuery as ChildOrderStatusQuery;
use Gekosale\Plugin\OrderStatus\Model\ORM\Map\OrderStatusTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'order_status' table.
 *
 * 
 *
 * @method     ChildOrderStatusQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderStatusQuery orderByIsDefault($order = Criteria::ASC) Order by the is_default column
 * @method     ChildOrderStatusQuery orderByIsEditable($order = Criteria::ASC) Order by the is_editable column
 *
 * @method     ChildOrderStatusQuery groupById() Group by the id column
 * @method     ChildOrderStatusQuery groupByIsDefault() Group by the is_default column
 * @method     ChildOrderStatusQuery groupByIsEditable() Group by the is_editable column
 *
 * @method     ChildOrderStatusQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderStatusQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderStatusQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderStatusQuery leftJoinOrderStatusOrderStatusGroups($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderStatusOrderStatusGroups relation
 * @method     ChildOrderStatusQuery rightJoinOrderStatusOrderStatusGroups($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderStatusOrderStatusGroups relation
 * @method     ChildOrderStatusQuery innerJoinOrderStatusOrderStatusGroups($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderStatusOrderStatusGroups relation
 *
 * @method     ChildOrderStatus findOne(ConnectionInterface $con = null) Return the first ChildOrderStatus matching the query
 * @method     ChildOrderStatus findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrderStatus matching the query, or a new ChildOrderStatus object populated from the query conditions when no match is found
 *
 * @method     ChildOrderStatus findOneById(int $id) Return the first ChildOrderStatus filtered by the id column
 * @method     ChildOrderStatus findOneByIsDefault(int $is_default) Return the first ChildOrderStatus filtered by the is_default column
 * @method     ChildOrderStatus findOneByIsEditable(int $is_editable) Return the first ChildOrderStatus filtered by the is_editable column
 *
 * @method     array findById(int $id) Return ChildOrderStatus objects filtered by the id column
 * @method     array findByIsDefault(int $is_default) Return ChildOrderStatus objects filtered by the is_default column
 * @method     array findByIsEditable(int $is_editable) Return ChildOrderStatus objects filtered by the is_editable column
 *
 */
abstract class OrderStatusQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\OrderStatus\Model\ORM\Base\OrderStatusQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\OrderStatus\\Model\\ORM\\OrderStatus', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderStatusQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOrderStatusQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusQuery();
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
     * @return ChildOrderStatus|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderStatusTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderStatusTableMap::DATABASE_NAME);
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
     * @return   ChildOrderStatus A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, IS_DEFAULT, IS_EDITABLE FROM order_status WHERE ID = :p0';
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
            $obj = new ChildOrderStatus();
            $obj->hydrate($row);
            OrderStatusTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildOrderStatus|array|mixed the result, formatted by the current formatter
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
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrderStatusTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrderStatusTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderStatusTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderStatusTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderStatusTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the is_default column
     *
     * Example usage:
     * <code>
     * $query->filterByIsDefault(1234); // WHERE is_default = 1234
     * $query->filterByIsDefault(array(12, 34)); // WHERE is_default IN (12, 34)
     * $query->filterByIsDefault(array('min' => 12)); // WHERE is_default > 12
     * </code>
     *
     * @param     mixed $isDefault The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function filterByIsDefault($isDefault = null, $comparison = null)
    {
        if (is_array($isDefault)) {
            $useMinMax = false;
            if (isset($isDefault['min'])) {
                $this->addUsingAlias(OrderStatusTableMap::COL_IS_DEFAULT, $isDefault['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isDefault['max'])) {
                $this->addUsingAlias(OrderStatusTableMap::COL_IS_DEFAULT, $isDefault['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderStatusTableMap::COL_IS_DEFAULT, $isDefault, $comparison);
    }

    /**
     * Filter the query on the is_editable column
     *
     * Example usage:
     * <code>
     * $query->filterByIsEditable(1234); // WHERE is_editable = 1234
     * $query->filterByIsEditable(array(12, 34)); // WHERE is_editable IN (12, 34)
     * $query->filterByIsEditable(array('min' => 12)); // WHERE is_editable > 12
     * </code>
     *
     * @param     mixed $isEditable The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function filterByIsEditable($isEditable = null, $comparison = null)
    {
        if (is_array($isEditable)) {
            $useMinMax = false;
            if (isset($isEditable['min'])) {
                $this->addUsingAlias(OrderStatusTableMap::COL_IS_EDITABLE, $isEditable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isEditable['max'])) {
                $this->addUsingAlias(OrderStatusTableMap::COL_IS_EDITABLE, $isEditable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderStatusTableMap::COL_IS_EDITABLE, $isEditable, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroups object
     *
     * @param \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroups|ObjectCollection $orderStatusOrderStatusGroups  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function filterByOrderStatusOrderStatusGroups($orderStatusOrderStatusGroups, $comparison = null)
    {
        if ($orderStatusOrderStatusGroups instanceof \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroups) {
            return $this
                ->addUsingAlias(OrderStatusTableMap::COL_ID, $orderStatusOrderStatusGroups->getOrderStatusId(), $comparison);
        } elseif ($orderStatusOrderStatusGroups instanceof ObjectCollection) {
            return $this
                ->useOrderStatusOrderStatusGroupsQuery()
                ->filterByPrimaryKeys($orderStatusOrderStatusGroups->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderStatusOrderStatusGroups() only accepts arguments of type \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroups or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderStatusOrderStatusGroups relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function joinOrderStatusOrderStatusGroups($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderStatusOrderStatusGroups');

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
            $this->addJoinObject($join, 'OrderStatusOrderStatusGroups');
        }

        return $this;
    }

    /**
     * Use the OrderStatusOrderStatusGroups relation OrderStatusOrderStatusGroups object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroupsQuery A secondary query class using the current class as primary query
     */
    public function useOrderStatusOrderStatusGroupsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderStatusOrderStatusGroups($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderStatusOrderStatusGroups', '\Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusOrderStatusGroupsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOrderStatus $orderStatus Object to remove from the list of results
     *
     * @return ChildOrderStatusQuery The current query, for fluid interface
     */
    public function prune($orderStatus = null)
    {
        if ($orderStatus) {
            $this->addUsingAlias(OrderStatusTableMap::COL_ID, $orderStatus->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order_status table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderStatusTableMap::DATABASE_NAME);
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
            OrderStatusTableMap::clearInstancePool();
            OrderStatusTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrderStatus or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildOrderStatus object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderStatusTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderStatusTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        OrderStatusTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            OrderStatusTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderStatusQuery
