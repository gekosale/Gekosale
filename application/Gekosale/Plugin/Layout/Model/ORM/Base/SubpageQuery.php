<?php

namespace Gekosale\Plugin\Layout\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Layout\Model\ORM\Subpage as ChildSubpage;
use Gekosale\Plugin\Layout\Model\ORM\SubpageQuery as ChildSubpageQuery;
use Gekosale\Plugin\Layout\Model\ORM\Map\SubpageTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'subpage' table.
 *
 * 
 *
 * @method     ChildSubpageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSubpageQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildSubpageQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     ChildSubpageQuery orderByTranslation($order = Criteria::ASC) Order by the translation column
 *
 * @method     ChildSubpageQuery groupById() Group by the id column
 * @method     ChildSubpageQuery groupByName() Group by the name column
 * @method     ChildSubpageQuery groupByDescription() Group by the description column
 * @method     ChildSubpageQuery groupByTranslation() Group by the translation column
 *
 * @method     ChildSubpageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSubpageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSubpageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSubpageQuery leftJoinLayoutSubpage($relationAlias = null) Adds a LEFT JOIN clause to the query using the LayoutSubpage relation
 * @method     ChildSubpageQuery rightJoinLayoutSubpage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LayoutSubpage relation
 * @method     ChildSubpageQuery innerJoinLayoutSubpage($relationAlias = null) Adds a INNER JOIN clause to the query using the LayoutSubpage relation
 *
 * @method     ChildSubpage findOne(ConnectionInterface $con = null) Return the first ChildSubpage matching the query
 * @method     ChildSubpage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSubpage matching the query, or a new ChildSubpage object populated from the query conditions when no match is found
 *
 * @method     ChildSubpage findOneById(int $id) Return the first ChildSubpage filtered by the id column
 * @method     ChildSubpage findOneByName(string $name) Return the first ChildSubpage filtered by the name column
 * @method     ChildSubpage findOneByDescription(string $description) Return the first ChildSubpage filtered by the description column
 * @method     ChildSubpage findOneByTranslation(string $translation) Return the first ChildSubpage filtered by the translation column
 *
 * @method     array findById(int $id) Return ChildSubpage objects filtered by the id column
 * @method     array findByName(string $name) Return ChildSubpage objects filtered by the name column
 * @method     array findByDescription(string $description) Return ChildSubpage objects filtered by the description column
 * @method     array findByTranslation(string $translation) Return ChildSubpage objects filtered by the translation column
 *
 */
abstract class SubpageQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Layout\Model\ORM\Base\SubpageQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Layout\\Model\\ORM\\Subpage', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSubpageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSubpageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Layout\Model\ORM\SubpageQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Layout\Model\ORM\SubpageQuery();
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
     * @return ChildSubpage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SubpageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SubpageTableMap::DATABASE_NAME);
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
     * @return   ChildSubpage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, DESCRIPTION, TRANSLATION FROM subpage WHERE ID = :p0';
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
            $obj = new ChildSubpage();
            $obj->hydrate($row);
            SubpageTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSubpage|array|mixed the result, formatted by the current formatter
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
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SubpageTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SubpageTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SubpageTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SubpageTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubpageTableMap::COL_ID, $id, $comparison);
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
     * @return ChildSubpageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SubpageTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SubpageTableMap::COL_DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the translation column
     *
     * Example usage:
     * <code>
     * $query->filterByTranslation('fooValue');   // WHERE translation = 'fooValue'
     * $query->filterByTranslation('%fooValue%'); // WHERE translation LIKE '%fooValue%'
     * </code>
     *
     * @param     string $translation The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function filterByTranslation($translation = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($translation)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $translation)) {
                $translation = str_replace('*', '%', $translation);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SubpageTableMap::COL_TRANSLATION, $translation, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage object
     *
     * @param \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage|ObjectCollection $layoutSubpage  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function filterByLayoutSubpage($layoutSubpage, $comparison = null)
    {
        if ($layoutSubpage instanceof \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage) {
            return $this
                ->addUsingAlias(SubpageTableMap::COL_ID, $layoutSubpage->getSubpageId(), $comparison);
        } elseif ($layoutSubpage instanceof ObjectCollection) {
            return $this
                ->useLayoutSubpageQuery()
                ->filterByPrimaryKeys($layoutSubpage->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByLayoutSubpage() only accepts arguments of type \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LayoutSubpage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function joinLayoutSubpage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LayoutSubpage');

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
            $this->addJoinObject($join, 'LayoutSubpage');
        }

        return $this;
    }

    /**
     * Use the LayoutSubpage relation LayoutSubpage object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery A secondary query class using the current class as primary query
     */
    public function useLayoutSubpageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLayoutSubpage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LayoutSubpage', '\Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSubpage $subpage Object to remove from the list of results
     *
     * @return ChildSubpageQuery The current query, for fluid interface
     */
    public function prune($subpage = null)
    {
        if ($subpage) {
            $this->addUsingAlias(SubpageTableMap::COL_ID, $subpage->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the subpage table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SubpageTableMap::DATABASE_NAME);
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
            SubpageTableMap::clearInstancePool();
            SubpageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSubpage or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSubpage object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SubpageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SubpageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        SubpageTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            SubpageTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SubpageQuery
