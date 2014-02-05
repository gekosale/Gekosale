<?php

namespace Gekosale\Plugin\TechnicalData\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttribute as ChildTechnicalDataAttribute;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttributeQuery as ChildTechnicalDataAttributeQuery;
use Gekosale\Plugin\TechnicalData\Model\ORM\Map\TechnicalDataAttributeTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'technical_data_attribute' table.
 *
 * 
 *
 * @method     ChildTechnicalDataAttributeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTechnicalDataAttributeQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method     ChildTechnicalDataAttributeQuery groupById() Group by the id column
 * @method     ChildTechnicalDataAttributeQuery groupByType() Group by the type column
 *
 * @method     ChildTechnicalDataAttributeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTechnicalDataAttributeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTechnicalDataAttributeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTechnicalDataAttributeQuery leftJoinProductTechnicalDataGroupAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductTechnicalDataGroupAttribute relation
 * @method     ChildTechnicalDataAttributeQuery rightJoinProductTechnicalDataGroupAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductTechnicalDataGroupAttribute relation
 * @method     ChildTechnicalDataAttributeQuery innerJoinProductTechnicalDataGroupAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductTechnicalDataGroupAttribute relation
 *
 * @method     ChildTechnicalDataAttributeQuery leftJoinTechnicalDataSetGroupAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
 * @method     ChildTechnicalDataAttributeQuery rightJoinTechnicalDataSetGroupAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
 * @method     ChildTechnicalDataAttributeQuery innerJoinTechnicalDataSetGroupAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
 *
 * @method     ChildTechnicalDataAttribute findOne(ConnectionInterface $con = null) Return the first ChildTechnicalDataAttribute matching the query
 * @method     ChildTechnicalDataAttribute findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTechnicalDataAttribute matching the query, or a new ChildTechnicalDataAttribute object populated from the query conditions when no match is found
 *
 * @method     ChildTechnicalDataAttribute findOneById(int $id) Return the first ChildTechnicalDataAttribute filtered by the id column
 * @method     ChildTechnicalDataAttribute findOneByType(int $type) Return the first ChildTechnicalDataAttribute filtered by the type column
 *
 * @method     array findById(int $id) Return ChildTechnicalDataAttribute objects filtered by the id column
 * @method     array findByType(int $type) Return ChildTechnicalDataAttribute objects filtered by the type column
 *
 */
abstract class TechnicalDataAttributeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\TechnicalData\Model\ORM\Base\TechnicalDataAttributeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataAttribute', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTechnicalDataAttributeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTechnicalDataAttributeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttributeQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataAttributeQuery();
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
     * @return ChildTechnicalDataAttribute|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TechnicalDataAttributeTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TechnicalDataAttributeTableMap::DATABASE_NAME);
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
     * @return   ChildTechnicalDataAttribute A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, TYPE FROM technical_data_attribute WHERE ID = :p0';
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
            $obj = new ChildTechnicalDataAttribute();
            $obj->hydrate($row);
            TechnicalDataAttributeTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildTechnicalDataAttribute|array|mixed the result, formatted by the current formatter
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
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType(1234); // WHERE type = 1234
     * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
     * $query->filterByType(array('min' => 12)); // WHERE type > 12
     * </code>
     *
     * @param     mixed $type The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_TYPE, $type, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttribute object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttribute|ObjectCollection $productTechnicalDataGroupAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function filterByProductTechnicalDataGroupAttribute($productTechnicalDataGroupAttribute, $comparison = null)
    {
        if ($productTechnicalDataGroupAttribute instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttribute) {
            return $this
                ->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $productTechnicalDataGroupAttribute->getTechnicalDataAttributeId(), $comparison);
        } elseif ($productTechnicalDataGroupAttribute instanceof ObjectCollection) {
            return $this
                ->useProductTechnicalDataGroupAttributeQuery()
                ->filterByPrimaryKeys($productTechnicalDataGroupAttribute->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductTechnicalDataGroupAttribute() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductTechnicalDataGroupAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function joinProductTechnicalDataGroupAttribute($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductTechnicalDataGroupAttribute');

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
            $this->addJoinObject($join, 'ProductTechnicalDataGroupAttribute');
        }

        return $this;
    }

    /**
     * Use the ProductTechnicalDataGroupAttribute relation ProductTechnicalDataGroupAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttributeQuery A secondary query class using the current class as primary query
     */
    public function useProductTechnicalDataGroupAttributeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductTechnicalDataGroupAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductTechnicalDataGroupAttribute', '\Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupAttributeQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute|ObjectCollection $technicalDataSetGroupAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSetGroupAttribute($technicalDataSetGroupAttribute, $comparison = null)
    {
        if ($technicalDataSetGroupAttribute instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute) {
            return $this
                ->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $technicalDataSetGroupAttribute->getTechnicalDataAttributeId(), $comparison);
        } elseif ($technicalDataSetGroupAttribute instanceof ObjectCollection) {
            return $this
                ->useTechnicalDataSetGroupAttributeQuery()
                ->filterByPrimaryKeys($technicalDataSetGroupAttribute->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTechnicalDataSetGroupAttribute() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function joinTechnicalDataSetGroupAttribute($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TechnicalDataSetGroupAttribute');

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
            $this->addJoinObject($join, 'TechnicalDataSetGroupAttribute');
        }

        return $this;
    }

    /**
     * Use the TechnicalDataSetGroupAttribute relation TechnicalDataSetGroupAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttributeQuery A secondary query class using the current class as primary query
     */
    public function useTechnicalDataSetGroupAttributeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTechnicalDataSetGroupAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TechnicalDataSetGroupAttribute', '\Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttributeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildTechnicalDataAttribute $technicalDataAttribute Object to remove from the list of results
     *
     * @return ChildTechnicalDataAttributeQuery The current query, for fluid interface
     */
    public function prune($technicalDataAttribute = null)
    {
        if ($technicalDataAttribute) {
            $this->addUsingAlias(TechnicalDataAttributeTableMap::COL_ID, $technicalDataAttribute->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the technical_data_attribute table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataAttributeTableMap::DATABASE_NAME);
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
            TechnicalDataAttributeTableMap::clearInstancePool();
            TechnicalDataAttributeTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildTechnicalDataAttribute or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildTechnicalDataAttribute object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataAttributeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TechnicalDataAttributeTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        TechnicalDataAttributeTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            TechnicalDataAttributeTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // TechnicalDataAttributeQuery
