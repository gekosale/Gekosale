<?php

namespace Gekosale\Component\Product\Model\Producer\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Product\Model\Producer\Producer as ChildProducer;
use Gekosale\Component\Product\Model\Producer\ProducerI18nQuery as ChildProducerI18nQuery;
use Gekosale\Component\Product\Model\Producer\ProducerQuery as ChildProducerQuery;
use Gekosale\Component\Product\Model\Producer\Map\ProducerTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'producer' table.
 *
 * 
 *
 * @method     ChildProducerQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProducerQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method     ChildProducerQuery orderByIsEnabled($order = Criteria::ASC) Order by the is_enabled column
 *
 * @method     ChildProducerQuery groupById() Group by the id column
 * @method     ChildProducerQuery groupByPhotoId() Group by the photo_id column
 * @method     ChildProducerQuery groupByIsEnabled() Group by the is_enabled column
 *
 * @method     ChildProducerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProducerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProducerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProducerQuery leftJoinProducerI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProducerI18n relation
 * @method     ChildProducerQuery rightJoinProducerI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProducerI18n relation
 * @method     ChildProducerQuery innerJoinProducerI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the ProducerI18n relation
 *
 * @method     ChildProducer findOne(ConnectionInterface $con = null) Return the first ChildProducer matching the query
 * @method     ChildProducer findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProducer matching the query, or a new ChildProducer object populated from the query conditions when no match is found
 *
 * @method     ChildProducer findOneById(int $id) Return the first ChildProducer filtered by the id column
 * @method     ChildProducer findOneByPhotoId(int $photo_id) Return the first ChildProducer filtered by the photo_id column
 * @method     ChildProducer findOneByIsEnabled(int $is_enabled) Return the first ChildProducer filtered by the is_enabled column
 *
 * @method     array findById(int $id) Return ChildProducer objects filtered by the id column
 * @method     array findByPhotoId(int $photo_id) Return ChildProducer objects filtered by the photo_id column
 * @method     array findByIsEnabled(int $is_enabled) Return ChildProducer objects filtered by the is_enabled column
 *
 */
abstract class ProducerQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Component\Product\Model\Producer\Base\ProducerQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Component\\Product\\Model\\Producer\\Producer', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProducerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProducerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Component\Product\Model\Producer\ProducerQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Component\Product\Model\Producer\ProducerQuery();
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
     * @return ChildProducer|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProducerTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProducerTableMap::DATABASE_NAME);
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
     * @return   ChildProducer A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PHOTO_ID, IS_ENABLED FROM producer WHERE ID = :p0';
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
            $obj = new ChildProducer();
            $obj->hydrate($row);
            ProducerTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProducer|array|mixed the result, formatted by the current formatter
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
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProducerTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProducerTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProducerTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProducerTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProducerTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId(1234); // WHERE photo_id = 1234
     * $query->filterByPhotoId(array(12, 34)); // WHERE photo_id IN (12, 34)
     * $query->filterByPhotoId(array('min' => 12)); // WHERE photo_id > 12
     * </code>
     *
     * @param     mixed $photoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(ProducerTableMap::PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(ProducerTableMap::PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProducerTableMap::PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query on the is_enabled column
     *
     * Example usage:
     * <code>
     * $query->filterByIsEnabled(1234); // WHERE is_enabled = 1234
     * $query->filterByIsEnabled(array(12, 34)); // WHERE is_enabled IN (12, 34)
     * $query->filterByIsEnabled(array('min' => 12)); // WHERE is_enabled > 12
     * </code>
     *
     * @param     mixed $isEnabled The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function filterByIsEnabled($isEnabled = null, $comparison = null)
    {
        if (is_array($isEnabled)) {
            $useMinMax = false;
            if (isset($isEnabled['min'])) {
                $this->addUsingAlias(ProducerTableMap::IS_ENABLED, $isEnabled['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isEnabled['max'])) {
                $this->addUsingAlias(ProducerTableMap::IS_ENABLED, $isEnabled['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProducerTableMap::IS_ENABLED, $isEnabled, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Component\Product\Model\Producer\ProducerI18n object
     *
     * @param \Gekosale\Component\Product\Model\Producer\ProducerI18n|ObjectCollection $producerI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function filterByProducerI18n($producerI18n, $comparison = null)
    {
        if ($producerI18n instanceof \Gekosale\Component\Product\Model\Producer\ProducerI18n) {
            return $this
                ->addUsingAlias(ProducerTableMap::ID, $producerI18n->getId(), $comparison);
        } elseif ($producerI18n instanceof ObjectCollection) {
            return $this
                ->useProducerI18nQuery()
                ->filterByPrimaryKeys($producerI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProducerI18n() only accepts arguments of type \Gekosale\Component\Product\Model\Producer\ProducerI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProducerI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function joinProducerI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProducerI18n');

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
            $this->addJoinObject($join, 'ProducerI18n');
        }

        return $this;
    }

    /**
     * Use the ProducerI18n relation ProducerI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Component\Product\Model\Producer\ProducerI18nQuery A secondary query class using the current class as primary query
     */
    public function useProducerI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinProducerI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProducerI18n', '\Gekosale\Component\Product\Model\Producer\ProducerI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProducer $producer Object to remove from the list of results
     *
     * @return ChildProducerQuery The current query, for fluid interface
     */
    public function prune($producer = null)
    {
        if ($producer) {
            $this->addUsingAlias(ProducerTableMap::ID, $producer->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the producer table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProducerTableMap::DATABASE_NAME);
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
            ProducerTableMap::clearInstancePool();
            ProducerTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProducer or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProducer object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProducerTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProducerTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProducerTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProducerTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior
    
    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildProducerQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'ProducerI18n';
    
        return $this
            ->joinProducerI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildProducerQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('ProducerI18n');
        $this->with['ProducerI18n']->setIsWithOneToMany(false);
    
        return $this;
    }
    
    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildProducerI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProducerI18n', '\Gekosale\Component\Product\Model\Producer\ProducerI18nQuery');
    }

} // ProducerQuery
