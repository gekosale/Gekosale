<?php

namespace Gekosale\Plugin\Producer\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Deliverer\Model\ORM\Deliverer;
use Gekosale\Plugin\Producer\Model\ORM\ProducerDeliverer as ChildProducerDeliverer;
use Gekosale\Plugin\Producer\Model\ORM\ProducerDelivererQuery as ChildProducerDelivererQuery;
use Gekosale\Plugin\Producer\Model\ORM\Map\ProducerDelivererTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'producer_deliverer' table.
 *
 * 
 *
 * @method     ChildProducerDelivererQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProducerDelivererQuery orderByProducerId($order = Criteria::ASC) Order by the producer_id column
 * @method     ChildProducerDelivererQuery orderByDelivererId($order = Criteria::ASC) Order by the deliverer_id column
 *
 * @method     ChildProducerDelivererQuery groupById() Group by the id column
 * @method     ChildProducerDelivererQuery groupByProducerId() Group by the producer_id column
 * @method     ChildProducerDelivererQuery groupByDelivererId() Group by the deliverer_id column
 *
 * @method     ChildProducerDelivererQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProducerDelivererQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProducerDelivererQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProducerDelivererQuery leftJoinDeliverer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Deliverer relation
 * @method     ChildProducerDelivererQuery rightJoinDeliverer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Deliverer relation
 * @method     ChildProducerDelivererQuery innerJoinDeliverer($relationAlias = null) Adds a INNER JOIN clause to the query using the Deliverer relation
 *
 * @method     ChildProducerDelivererQuery leftJoinProducer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Producer relation
 * @method     ChildProducerDelivererQuery rightJoinProducer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Producer relation
 * @method     ChildProducerDelivererQuery innerJoinProducer($relationAlias = null) Adds a INNER JOIN clause to the query using the Producer relation
 *
 * @method     ChildProducerDeliverer findOne(ConnectionInterface $con = null) Return the first ChildProducerDeliverer matching the query
 * @method     ChildProducerDeliverer findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProducerDeliverer matching the query, or a new ChildProducerDeliverer object populated from the query conditions when no match is found
 *
 * @method     ChildProducerDeliverer findOneById(int $id) Return the first ChildProducerDeliverer filtered by the id column
 * @method     ChildProducerDeliverer findOneByProducerId(int $producer_id) Return the first ChildProducerDeliverer filtered by the producer_id column
 * @method     ChildProducerDeliverer findOneByDelivererId(int $deliverer_id) Return the first ChildProducerDeliverer filtered by the deliverer_id column
 *
 * @method     array findById(int $id) Return ChildProducerDeliverer objects filtered by the id column
 * @method     array findByProducerId(int $producer_id) Return ChildProducerDeliverer objects filtered by the producer_id column
 * @method     array findByDelivererId(int $deliverer_id) Return ChildProducerDeliverer objects filtered by the deliverer_id column
 *
 */
abstract class ProducerDelivererQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Producer\Model\ORM\Base\ProducerDelivererQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Producer\\Model\\ORM\\ProducerDeliverer', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProducerDelivererQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProducerDelivererQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Producer\Model\ORM\ProducerDelivererQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Producer\Model\ORM\ProducerDelivererQuery();
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
     * @return ChildProducerDeliverer|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProducerDelivererTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProducerDelivererTableMap::DATABASE_NAME);
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
     * @return   ChildProducerDeliverer A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PRODUCER_ID, DELIVERER_ID FROM producer_deliverer WHERE ID = :p0';
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
            $obj = new ChildProducerDeliverer();
            $obj->hydrate($row);
            ProducerDelivererTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProducerDeliverer|array|mixed the result, formatted by the current formatter
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
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProducerDelivererTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProducerDelivererTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProducerDelivererTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProducerDelivererTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProducerDelivererTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the producer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProducerId(1234); // WHERE producer_id = 1234
     * $query->filterByProducerId(array(12, 34)); // WHERE producer_id IN (12, 34)
     * $query->filterByProducerId(array('min' => 12)); // WHERE producer_id > 12
     * </code>
     *
     * @see       filterByProducer()
     *
     * @param     mixed $producerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterByProducerId($producerId = null, $comparison = null)
    {
        if (is_array($producerId)) {
            $useMinMax = false;
            if (isset($producerId['min'])) {
                $this->addUsingAlias(ProducerDelivererTableMap::COL_PRODUCER_ID, $producerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($producerId['max'])) {
                $this->addUsingAlias(ProducerDelivererTableMap::COL_PRODUCER_ID, $producerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProducerDelivererTableMap::COL_PRODUCER_ID, $producerId, $comparison);
    }

    /**
     * Filter the query on the deliverer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDelivererId(1234); // WHERE deliverer_id = 1234
     * $query->filterByDelivererId(array(12, 34)); // WHERE deliverer_id IN (12, 34)
     * $query->filterByDelivererId(array('min' => 12)); // WHERE deliverer_id > 12
     * </code>
     *
     * @see       filterByDeliverer()
     *
     * @param     mixed $delivererId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterByDelivererId($delivererId = null, $comparison = null)
    {
        if (is_array($delivererId)) {
            $useMinMax = false;
            if (isset($delivererId['min'])) {
                $this->addUsingAlias(ProducerDelivererTableMap::COL_DELIVERER_ID, $delivererId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($delivererId['max'])) {
                $this->addUsingAlias(ProducerDelivererTableMap::COL_DELIVERER_ID, $delivererId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProducerDelivererTableMap::COL_DELIVERER_ID, $delivererId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer object
     *
     * @param \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer|ObjectCollection $deliverer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterByDeliverer($deliverer, $comparison = null)
    {
        if ($deliverer instanceof \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer) {
            return $this
                ->addUsingAlias(ProducerDelivererTableMap::COL_DELIVERER_ID, $deliverer->getId(), $comparison);
        } elseif ($deliverer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProducerDelivererTableMap::COL_DELIVERER_ID, $deliverer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDeliverer() only accepts arguments of type \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Deliverer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function joinDeliverer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Deliverer');

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
            $this->addJoinObject($join, 'Deliverer');
        }

        return $this;
    }

    /**
     * Use the Deliverer relation Deliverer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Deliverer\Model\ORM\DelivererQuery A secondary query class using the current class as primary query
     */
    public function useDelivererQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDeliverer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Deliverer', '\Gekosale\Plugin\Deliverer\Model\ORM\DelivererQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Producer\Model\ORM\Producer object
     *
     * @param \Gekosale\Plugin\Producer\Model\ORM\Producer|ObjectCollection $producer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function filterByProducer($producer, $comparison = null)
    {
        if ($producer instanceof \Gekosale\Plugin\Producer\Model\ORM\Producer) {
            return $this
                ->addUsingAlias(ProducerDelivererTableMap::COL_PRODUCER_ID, $producer->getId(), $comparison);
        } elseif ($producer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProducerDelivererTableMap::COL_PRODUCER_ID, $producer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProducer() only accepts arguments of type \Gekosale\Plugin\Producer\Model\ORM\Producer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Producer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function joinProducer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Producer');

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
            $this->addJoinObject($join, 'Producer');
        }

        return $this;
    }

    /**
     * Use the Producer relation Producer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Producer\Model\ORM\ProducerQuery A secondary query class using the current class as primary query
     */
    public function useProducerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProducer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Producer', '\Gekosale\Plugin\Producer\Model\ORM\ProducerQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProducerDeliverer $producerDeliverer Object to remove from the list of results
     *
     * @return ChildProducerDelivererQuery The current query, for fluid interface
     */
    public function prune($producerDeliverer = null)
    {
        if ($producerDeliverer) {
            $this->addUsingAlias(ProducerDelivererTableMap::COL_ID, $producerDeliverer->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the producer_deliverer table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProducerDelivererTableMap::DATABASE_NAME);
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
            ProducerDelivererTableMap::clearInstancePool();
            ProducerDelivererTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProducerDeliverer or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProducerDeliverer object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProducerDelivererTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProducerDelivererTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProducerDelivererTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProducerDelivererTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ProducerDelivererQuery
