<?php

namespace Gekosale\Component\Configuration\Model\File\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Configuration\Model\File\File as ChildFile;
use Gekosale\Component\Configuration\Model\File\FileQuery as ChildFileQuery;
use Gekosale\Component\Configuration\Model\File\Map\FileTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'file' table.
 *
 * 
 *
 * @method     ChildFileQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildFileQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildFileQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildFileQuery orderByExtension($order = Criteria::ASC) Order by the extension column
 * @method     ChildFileQuery orderByAddDate($order = Criteria::ASC) Order by the add_date column
 *
 * @method     ChildFileQuery groupById() Group by the id column
 * @method     ChildFileQuery groupByName() Group by the name column
 * @method     ChildFileQuery groupByType() Group by the type column
 * @method     ChildFileQuery groupByExtension() Group by the extension column
 * @method     ChildFileQuery groupByAddDate() Group by the add_date column
 *
 * @method     ChildFileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildFileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildFileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildFile findOne(ConnectionInterface $con = null) Return the first ChildFile matching the query
 * @method     ChildFile findOneOrCreate(ConnectionInterface $con = null) Return the first ChildFile matching the query, or a new ChildFile object populated from the query conditions when no match is found
 *
 * @method     ChildFile findOneById(int $id) Return the first ChildFile filtered by the id column
 * @method     ChildFile findOneByName(string $name) Return the first ChildFile filtered by the name column
 * @method     ChildFile findOneByType(string $type) Return the first ChildFile filtered by the type column
 * @method     ChildFile findOneByExtension(string $extension) Return the first ChildFile filtered by the extension column
 * @method     ChildFile findOneByAddDate(string $add_date) Return the first ChildFile filtered by the add_date column
 *
 * @method     array findById(int $id) Return ChildFile objects filtered by the id column
 * @method     array findByName(string $name) Return ChildFile objects filtered by the name column
 * @method     array findByType(string $type) Return ChildFile objects filtered by the type column
 * @method     array findByExtension(string $extension) Return ChildFile objects filtered by the extension column
 * @method     array findByAddDate(string $add_date) Return ChildFile objects filtered by the add_date column
 *
 */
abstract class FileQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Component\Configuration\Model\File\Base\FileQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Component\\Configuration\\Model\\File\\File', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildFileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildFileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Component\Configuration\Model\File\FileQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Component\Configuration\Model\File\FileQuery();
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
     * @return ChildFile|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FileTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(FileTableMap::DATABASE_NAME);
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
     * @return   ChildFile A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, TYPE, EXTENSION, ADD_DATE FROM file WHERE ID = :p0';
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
            $obj = new ChildFile();
            $obj->hydrate($row);
            FileTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildFile|array|mixed the result, formatted by the current formatter
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
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FileTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FileTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(FileTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(FileTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FileTableMap::ID, $id, $comparison);
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
     * @return ChildFileQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FileTableMap::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FileTableMap::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the extension column
     *
     * Example usage:
     * <code>
     * $query->filterByExtension('fooValue');   // WHERE extension = 'fooValue'
     * $query->filterByExtension('%fooValue%'); // WHERE extension LIKE '%fooValue%'
     * </code>
     *
     * @param     string $extension The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByExtension($extension = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($extension)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $extension)) {
                $extension = str_replace('*', '%', $extension);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FileTableMap::EXTENSION, $extension, $comparison);
    }

    /**
     * Filter the query on the add_date column
     *
     * Example usage:
     * <code>
     * $query->filterByAddDate('2011-03-14'); // WHERE add_date = '2011-03-14'
     * $query->filterByAddDate('now'); // WHERE add_date = '2011-03-14'
     * $query->filterByAddDate(array('max' => 'yesterday')); // WHERE add_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $addDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByAddDate($addDate = null, $comparison = null)
    {
        if (is_array($addDate)) {
            $useMinMax = false;
            if (isset($addDate['min'])) {
                $this->addUsingAlias(FileTableMap::ADD_DATE, $addDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($addDate['max'])) {
                $this->addUsingAlias(FileTableMap::ADD_DATE, $addDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FileTableMap::ADD_DATE, $addDate, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildFile $file Object to remove from the list of results
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function prune($file = null)
    {
        if ($file) {
            $this->addUsingAlias(FileTableMap::ID, $file->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the file table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
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
            FileTableMap::clearInstancePool();
            FileTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildFile or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildFile object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(FileTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        FileTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            FileTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // FileQuery
