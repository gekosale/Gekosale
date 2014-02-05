<?php

namespace Gekosale\Plugin\TechnicalData\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroup as ChildTechnicalDataSetGroup;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupQuery as ChildTechnicalDataSetGroupQuery;
use Gekosale\Plugin\TechnicalData\Model\ORM\Map\TechnicalDataSetGroupTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'technical_data_set_group' table.
 *
 * 
 *
 * @method     ChildTechnicalDataSetGroupQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTechnicalDataSetGroupQuery orderByTechnicalDataSetId($order = Criteria::ASC) Order by the technical_data_set_id column
 * @method     ChildTechnicalDataSetGroupQuery orderByTechnicalDataGroupId($order = Criteria::ASC) Order by the technical_data_group_id column
 * @method     ChildTechnicalDataSetGroupQuery orderByOrder($order = Criteria::ASC) Order by the order column
 *
 * @method     ChildTechnicalDataSetGroupQuery groupById() Group by the id column
 * @method     ChildTechnicalDataSetGroupQuery groupByTechnicalDataSetId() Group by the technical_data_set_id column
 * @method     ChildTechnicalDataSetGroupQuery groupByTechnicalDataGroupId() Group by the technical_data_group_id column
 * @method     ChildTechnicalDataSetGroupQuery groupByOrder() Group by the order column
 *
 * @method     ChildTechnicalDataSetGroupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTechnicalDataSetGroupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTechnicalDataSetGroupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTechnicalDataSetGroupQuery leftJoinTechnicalDataSet($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataSet relation
 * @method     ChildTechnicalDataSetGroupQuery rightJoinTechnicalDataSet($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataSet relation
 * @method     ChildTechnicalDataSetGroupQuery innerJoinTechnicalDataSet($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataSet relation
 *
 * @method     ChildTechnicalDataSetGroupQuery leftJoinTechnicalDataGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataGroup relation
 * @method     ChildTechnicalDataSetGroupQuery rightJoinTechnicalDataGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataGroup relation
 * @method     ChildTechnicalDataSetGroupQuery innerJoinTechnicalDataGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataGroup relation
 *
 * @method     ChildTechnicalDataSetGroupQuery leftJoinTechnicalDataSetGroupAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
 * @method     ChildTechnicalDataSetGroupQuery rightJoinTechnicalDataSetGroupAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
 * @method     ChildTechnicalDataSetGroupQuery innerJoinTechnicalDataSetGroupAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataSetGroupAttribute relation
 *
 * @method     ChildTechnicalDataSetGroup findOne(ConnectionInterface $con = null) Return the first ChildTechnicalDataSetGroup matching the query
 * @method     ChildTechnicalDataSetGroup findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTechnicalDataSetGroup matching the query, or a new ChildTechnicalDataSetGroup object populated from the query conditions when no match is found
 *
 * @method     ChildTechnicalDataSetGroup findOneById(int $id) Return the first ChildTechnicalDataSetGroup filtered by the id column
 * @method     ChildTechnicalDataSetGroup findOneByTechnicalDataSetId(int $technical_data_set_id) Return the first ChildTechnicalDataSetGroup filtered by the technical_data_set_id column
 * @method     ChildTechnicalDataSetGroup findOneByTechnicalDataGroupId(int $technical_data_group_id) Return the first ChildTechnicalDataSetGroup filtered by the technical_data_group_id column
 * @method     ChildTechnicalDataSetGroup findOneByOrder(int $order) Return the first ChildTechnicalDataSetGroup filtered by the order column
 *
 * @method     array findById(int $id) Return ChildTechnicalDataSetGroup objects filtered by the id column
 * @method     array findByTechnicalDataSetId(int $technical_data_set_id) Return ChildTechnicalDataSetGroup objects filtered by the technical_data_set_id column
 * @method     array findByTechnicalDataGroupId(int $technical_data_group_id) Return ChildTechnicalDataSetGroup objects filtered by the technical_data_group_id column
 * @method     array findByOrder(int $order) Return ChildTechnicalDataSetGroup objects filtered by the order column
 *
 */
abstract class TechnicalDataSetGroupQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\TechnicalData\Model\ORM\Base\TechnicalDataSetGroupQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\TechnicalData\\Model\\ORM\\TechnicalDataSetGroup', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTechnicalDataSetGroupQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTechnicalDataSetGroupQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupQuery();
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
     * @return ChildTechnicalDataSetGroup|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TechnicalDataSetGroupTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TechnicalDataSetGroupTableMap::DATABASE_NAME);
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
     * @return   ChildTechnicalDataSetGroup A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, TECHNICAL_DATA_SET_ID, TECHNICAL_DATA_GROUP_ID, ORDER FROM technical_data_set_group WHERE ID = :p0';
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
            $obj = new ChildTechnicalDataSetGroup();
            $obj->hydrate($row);
            TechnicalDataSetGroupTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildTechnicalDataSetGroup|array|mixed the result, formatted by the current formatter
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
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the technical_data_set_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTechnicalDataSetId(1234); // WHERE technical_data_set_id = 1234
     * $query->filterByTechnicalDataSetId(array(12, 34)); // WHERE technical_data_set_id IN (12, 34)
     * $query->filterByTechnicalDataSetId(array('min' => 12)); // WHERE technical_data_set_id > 12
     * </code>
     *
     * @see       filterByTechnicalDataSet()
     *
     * @param     mixed $technicalDataSetId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSetId($technicalDataSetId = null, $comparison = null)
    {
        if (is_array($technicalDataSetId)) {
            $useMinMax = false;
            if (isset($technicalDataSetId['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSetId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($technicalDataSetId['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSetId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSetId, $comparison);
    }

    /**
     * Filter the query on the technical_data_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTechnicalDataGroupId(1234); // WHERE technical_data_group_id = 1234
     * $query->filterByTechnicalDataGroupId(array(12, 34)); // WHERE technical_data_group_id IN (12, 34)
     * $query->filterByTechnicalDataGroupId(array('min' => 12)); // WHERE technical_data_group_id > 12
     * </code>
     *
     * @see       filterByTechnicalDataGroup()
     *
     * @param     mixed $technicalDataGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataGroupId($technicalDataGroupId = null, $comparison = null)
    {
        if (is_array($technicalDataGroupId)) {
            $useMinMax = false;
            if (isset($technicalDataGroupId['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID, $technicalDataGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($technicalDataGroupId['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID, $technicalDataGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID, $technicalDataGroupId, $comparison);
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
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByOrder($order = null, $comparison = null)
    {
        if (is_array($order)) {
            $useMinMax = false;
            if (isset($order['min'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ORDER, $order['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($order['max'])) {
                $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ORDER, $order['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ORDER, $order, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet|ObjectCollection $technicalDataSet The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSet($technicalDataSet, $comparison = null)
    {
        if ($technicalDataSet instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet) {
            return $this
                ->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSet->getId(), $comparison);
        } elseif ($technicalDataSet instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSet->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTechnicalDataSet() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TechnicalDataSet relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function joinTechnicalDataSet($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TechnicalDataSet');

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
            $this->addJoinObject($join, 'TechnicalDataSet');
        }

        return $this;
    }

    /**
     * Use the TechnicalDataSet relation TechnicalDataSet object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetQuery A secondary query class using the current class as primary query
     */
    public function useTechnicalDataSetQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTechnicalDataSet($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TechnicalDataSet', '\Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataGroup object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataGroup|ObjectCollection $technicalDataGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataGroup($technicalDataGroup, $comparison = null)
    {
        if ($technicalDataGroup instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataGroup) {
            return $this
                ->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID, $technicalDataGroup->getId(), $comparison);
        } elseif ($technicalDataGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TechnicalDataSetGroupTableMap::COL_TECHNICAL_DATA_GROUP_ID, $technicalDataGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTechnicalDataGroup() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TechnicalDataGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function joinTechnicalDataGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TechnicalDataGroup');

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
            $this->addJoinObject($join, 'TechnicalDataGroup');
        }

        return $this;
    }

    /**
     * Use the TechnicalDataGroup relation TechnicalDataGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataGroupQuery A secondary query class using the current class as primary query
     */
    public function useTechnicalDataGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTechnicalDataGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TechnicalDataGroup', '\Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute|ObjectCollection $technicalDataSetGroupAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSetGroupAttribute($technicalDataSetGroupAttribute, $comparison = null)
    {
        if ($technicalDataSetGroupAttribute instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetGroupAttribute) {
            return $this
                ->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $technicalDataSetGroupAttribute->getTechnicalDataSetGroupId(), $comparison);
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
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
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
     * @param   ChildTechnicalDataSetGroup $technicalDataSetGroup Object to remove from the list of results
     *
     * @return ChildTechnicalDataSetGroupQuery The current query, for fluid interface
     */
    public function prune($technicalDataSetGroup = null)
    {
        if ($technicalDataSetGroup) {
            $this->addUsingAlias(TechnicalDataSetGroupTableMap::COL_ID, $technicalDataSetGroup->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the technical_data_set_group table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataSetGroupTableMap::DATABASE_NAME);
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
            TechnicalDataSetGroupTableMap::clearInstancePool();
            TechnicalDataSetGroupTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildTechnicalDataSetGroup or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildTechnicalDataSetGroup object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(TechnicalDataSetGroupTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TechnicalDataSetGroupTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        TechnicalDataSetGroupTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            TechnicalDataSetGroupTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // TechnicalDataSetGroupQuery
