<?php

namespace Gekosale\Plugin\DispatchMethod\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice as ChildDispatchMethodPrice;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery as ChildDispatchMethodPriceQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\Map\DispatchMethodPriceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dispatch_method_price' table.
 *
 * 
 *
 * @method     ChildDispatchMethodPriceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDispatchMethodPriceQuery orderByDispatchMethodId($order = Criteria::ASC) Order by the dispatch_method_id column
 * @method     ChildDispatchMethodPriceQuery orderByFrom($order = Criteria::ASC) Order by the from column
 * @method     ChildDispatchMethodPriceQuery orderByTo($order = Criteria::ASC) Order by the to column
 * @method     ChildDispatchMethodPriceQuery orderByCost($order = Criteria::ASC) Order by the cost column
 * @method     ChildDispatchMethodPriceQuery orderByVat($order = Criteria::ASC) Order by the vat column
 *
 * @method     ChildDispatchMethodPriceQuery groupById() Group by the id column
 * @method     ChildDispatchMethodPriceQuery groupByDispatchMethodId() Group by the dispatch_method_id column
 * @method     ChildDispatchMethodPriceQuery groupByFrom() Group by the from column
 * @method     ChildDispatchMethodPriceQuery groupByTo() Group by the to column
 * @method     ChildDispatchMethodPriceQuery groupByCost() Group by the cost column
 * @method     ChildDispatchMethodPriceQuery groupByVat() Group by the vat column
 *
 * @method     ChildDispatchMethodPriceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDispatchMethodPriceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDispatchMethodPriceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDispatchMethodPriceQuery leftJoinDispatchMethod($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethod relation
 * @method     ChildDispatchMethodPriceQuery rightJoinDispatchMethod($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethod relation
 * @method     ChildDispatchMethodPriceQuery innerJoinDispatchMethod($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethod relation
 *
 * @method     ChildDispatchMethodPrice findOne(ConnectionInterface $con = null) Return the first ChildDispatchMethodPrice matching the query
 * @method     ChildDispatchMethodPrice findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDispatchMethodPrice matching the query, or a new ChildDispatchMethodPrice object populated from the query conditions when no match is found
 *
 * @method     ChildDispatchMethodPrice findOneById(int $id) Return the first ChildDispatchMethodPrice filtered by the id column
 * @method     ChildDispatchMethodPrice findOneByDispatchMethodId(int $dispatch_method_id) Return the first ChildDispatchMethodPrice filtered by the dispatch_method_id column
 * @method     ChildDispatchMethodPrice findOneByFrom(string $from) Return the first ChildDispatchMethodPrice filtered by the from column
 * @method     ChildDispatchMethodPrice findOneByTo(string $to) Return the first ChildDispatchMethodPrice filtered by the to column
 * @method     ChildDispatchMethodPrice findOneByCost(string $cost) Return the first ChildDispatchMethodPrice filtered by the cost column
 * @method     ChildDispatchMethodPrice findOneByVat(int $vat) Return the first ChildDispatchMethodPrice filtered by the vat column
 *
 * @method     array findById(int $id) Return ChildDispatchMethodPrice objects filtered by the id column
 * @method     array findByDispatchMethodId(int $dispatch_method_id) Return ChildDispatchMethodPrice objects filtered by the dispatch_method_id column
 * @method     array findByFrom(string $from) Return ChildDispatchMethodPrice objects filtered by the from column
 * @method     array findByTo(string $to) Return ChildDispatchMethodPrice objects filtered by the to column
 * @method     array findByCost(string $cost) Return ChildDispatchMethodPrice objects filtered by the cost column
 * @method     array findByVat(int $vat) Return ChildDispatchMethodPrice objects filtered by the vat column
 *
 */
abstract class DispatchMethodPriceQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\DispatchMethod\Model\ORM\Base\DispatchMethodPriceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodPrice', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDispatchMethodPriceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDispatchMethodPriceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery();
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
     * @return ChildDispatchMethodPrice|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DispatchMethodPriceTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DispatchMethodPriceTableMap::DATABASE_NAME);
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
     * @return   ChildDispatchMethodPrice A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DISPATCH_METHOD_ID, FROM, TO, COST, VAT FROM dispatch_method_price WHERE ID = :p0';
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
            $obj = new ChildDispatchMethodPrice();
            $obj->hydrate($row);
            DispatchMethodPriceTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDispatchMethodPrice|array|mixed the result, formatted by the current formatter
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
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the dispatch_method_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDispatchMethodId(1234); // WHERE dispatch_method_id = 1234
     * $query->filterByDispatchMethodId(array(12, 34)); // WHERE dispatch_method_id IN (12, 34)
     * $query->filterByDispatchMethodId(array('min' => 12)); // WHERE dispatch_method_id > 12
     * </code>
     *
     * @see       filterByDispatchMethod()
     *
     * @param     mixed $dispatchMethodId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodId($dispatchMethodId = null, $comparison = null)
    {
        if (is_array($dispatchMethodId)) {
            $useMinMax = false;
            if (isset($dispatchMethodId['min'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethodId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dispatchMethodId['max'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethodId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethodId, $comparison);
    }

    /**
     * Filter the query on the from column
     *
     * Example usage:
     * <code>
     * $query->filterByFrom(1234); // WHERE from = 1234
     * $query->filterByFrom(array(12, 34)); // WHERE from IN (12, 34)
     * $query->filterByFrom(array('min' => 12)); // WHERE from > 12
     * </code>
     *
     * @param     mixed $from The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByFrom($from = null, $comparison = null)
    {
        if (is_array($from)) {
            $useMinMax = false;
            if (isset($from['min'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_FROM, $from['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($from['max'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_FROM, $from['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_FROM, $from, $comparison);
    }

    /**
     * Filter the query on the to column
     *
     * Example usage:
     * <code>
     * $query->filterByTo(1234); // WHERE to = 1234
     * $query->filterByTo(array(12, 34)); // WHERE to IN (12, 34)
     * $query->filterByTo(array('min' => 12)); // WHERE to > 12
     * </code>
     *
     * @param     mixed $to The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByTo($to = null, $comparison = null)
    {
        if (is_array($to)) {
            $useMinMax = false;
            if (isset($to['min'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_TO, $to['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($to['max'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_TO, $to['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_TO, $to, $comparison);
    }

    /**
     * Filter the query on the cost column
     *
     * Example usage:
     * <code>
     * $query->filterByCost(1234); // WHERE cost = 1234
     * $query->filterByCost(array(12, 34)); // WHERE cost IN (12, 34)
     * $query->filterByCost(array('min' => 12)); // WHERE cost > 12
     * </code>
     *
     * @param     mixed $cost The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByCost($cost = null, $comparison = null)
    {
        if (is_array($cost)) {
            $useMinMax = false;
            if (isset($cost['min'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_COST, $cost['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cost['max'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_COST, $cost['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_COST, $cost, $comparison);
    }

    /**
     * Filter the query on the vat column
     *
     * Example usage:
     * <code>
     * $query->filterByVat(1234); // WHERE vat = 1234
     * $query->filterByVat(array(12, 34)); // WHERE vat IN (12, 34)
     * $query->filterByVat(array('min' => 12)); // WHERE vat > 12
     * </code>
     *
     * @param     mixed $vat The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByVat($vat = null, $comparison = null)
    {
        if (is_array($vat)) {
            $useMinMax = false;
            if (isset($vat['min'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_VAT, $vat['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vat['max'])) {
                $this->addUsingAlias(DispatchMethodPriceTableMap::COL_VAT, $vat['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodPriceTableMap::COL_VAT, $vat, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod|ObjectCollection $dispatchMethod The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function filterByDispatchMethod($dispatchMethod, $comparison = null)
    {
        if ($dispatchMethod instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod) {
            return $this
                ->addUsingAlias(DispatchMethodPriceTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethod->getId(), $comparison);
        } elseif ($dispatchMethod instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DispatchMethodPriceTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethod->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDispatchMethod() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethod relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function joinDispatchMethod($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethod');

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
            $this->addJoinObject($join, 'DispatchMethod');
        }

        return $this;
    }

    /**
     * Use the DispatchMethod relation DispatchMethod object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDispatchMethod($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethod', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDispatchMethodPrice $dispatchMethodPrice Object to remove from the list of results
     *
     * @return ChildDispatchMethodPriceQuery The current query, for fluid interface
     */
    public function prune($dispatchMethodPrice = null)
    {
        if ($dispatchMethodPrice) {
            $this->addUsingAlias(DispatchMethodPriceTableMap::COL_ID, $dispatchMethodPrice->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dispatch_method_price table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodPriceTableMap::DATABASE_NAME);
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
            DispatchMethodPriceTableMap::clearInstancePool();
            DispatchMethodPriceTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDispatchMethodPrice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDispatchMethodPrice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodPriceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DispatchMethodPriceTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        DispatchMethodPriceTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            DispatchMethodPriceTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // DispatchMethodPriceQuery
