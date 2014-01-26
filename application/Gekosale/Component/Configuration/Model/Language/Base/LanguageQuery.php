<?php

namespace Gekosale\Component\Configuration\Model\Language\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Configuration\Model\Language\Language as ChildLanguage;
use Gekosale\Component\Configuration\Model\Language\LanguageQuery as ChildLanguageQuery;
use Gekosale\Component\Configuration\Model\Language\Map\LanguageTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'language' table.
 *
 * 
 *
 * @method     ChildLanguageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLanguageQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildLanguageQuery orderByTranslation($order = Criteria::ASC) Order by the translation column
 * @method     ChildLanguageQuery orderByAddDate($order = Criteria::ASC) Order by the add_date column
 * @method     ChildLanguageQuery orderByCurrency($order = Criteria::ASC) Order by the currency column
 *
 * @method     ChildLanguageQuery groupById() Group by the id column
 * @method     ChildLanguageQuery groupByName() Group by the name column
 * @method     ChildLanguageQuery groupByTranslation() Group by the translation column
 * @method     ChildLanguageQuery groupByAddDate() Group by the add_date column
 * @method     ChildLanguageQuery groupByCurrency() Group by the currency column
 *
 * @method     ChildLanguageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLanguageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLanguageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLanguage findOne(ConnectionInterface $con = null) Return the first ChildLanguage matching the query
 * @method     ChildLanguage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLanguage matching the query, or a new ChildLanguage object populated from the query conditions when no match is found
 *
 * @method     ChildLanguage findOneById(int $id) Return the first ChildLanguage filtered by the id column
 * @method     ChildLanguage findOneByName(string $name) Return the first ChildLanguage filtered by the name column
 * @method     ChildLanguage findOneByTranslation(string $translation) Return the first ChildLanguage filtered by the translation column
 * @method     ChildLanguage findOneByAddDate(string $add_date) Return the first ChildLanguage filtered by the add_date column
 * @method     ChildLanguage findOneByCurrency(int $currency) Return the first ChildLanguage filtered by the currency column
 *
 * @method     array findById(int $id) Return ChildLanguage objects filtered by the id column
 * @method     array findByName(string $name) Return ChildLanguage objects filtered by the name column
 * @method     array findByTranslation(string $translation) Return ChildLanguage objects filtered by the translation column
 * @method     array findByAddDate(string $add_date) Return ChildLanguage objects filtered by the add_date column
 * @method     array findByCurrency(int $currency) Return ChildLanguage objects filtered by the currency column
 *
 */
abstract class LanguageQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Component\Configuration\Model\Language\Base\LanguageQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Component\\Configuration\\Model\\Language\\Language', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLanguageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLanguageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Component\Configuration\Model\Language\LanguageQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Component\Configuration\Model\Language\LanguageQuery();
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
     * @return ChildLanguage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LanguageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LanguageTableMap::DATABASE_NAME);
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
     * @return   ChildLanguage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, TRANSLATION, ADD_DATE, CURRENCY FROM language WHERE ID = :p0';
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
            $obj = new ChildLanguage();
            $obj->hydrate($row);
            LanguageTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildLanguage|array|mixed the result, formatted by the current formatter
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
     * @return ChildLanguageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LanguageTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildLanguageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LanguageTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildLanguageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LanguageTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LanguageTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LanguageTableMap::ID, $id, $comparison);
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
     * @return ChildLanguageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LanguageTableMap::NAME, $name, $comparison);
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
     * @return ChildLanguageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LanguageTableMap::TRANSLATION, $translation, $comparison);
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
     * @return ChildLanguageQuery The current query, for fluid interface
     */
    public function filterByAddDate($addDate = null, $comparison = null)
    {
        if (is_array($addDate)) {
            $useMinMax = false;
            if (isset($addDate['min'])) {
                $this->addUsingAlias(LanguageTableMap::ADD_DATE, $addDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($addDate['max'])) {
                $this->addUsingAlias(LanguageTableMap::ADD_DATE, $addDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LanguageTableMap::ADD_DATE, $addDate, $comparison);
    }

    /**
     * Filter the query on the currency column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrency(1234); // WHERE currency = 1234
     * $query->filterByCurrency(array(12, 34)); // WHERE currency IN (12, 34)
     * $query->filterByCurrency(array('min' => 12)); // WHERE currency > 12
     * </code>
     *
     * @param     mixed $currency The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLanguageQuery The current query, for fluid interface
     */
    public function filterByCurrency($currency = null, $comparison = null)
    {
        if (is_array($currency)) {
            $useMinMax = false;
            if (isset($currency['min'])) {
                $this->addUsingAlias(LanguageTableMap::CURRENCY, $currency['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currency['max'])) {
                $this->addUsingAlias(LanguageTableMap::CURRENCY, $currency['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LanguageTableMap::CURRENCY, $currency, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildLanguage $language Object to remove from the list of results
     *
     * @return ChildLanguageQuery The current query, for fluid interface
     */
    public function prune($language = null)
    {
        if ($language) {
            $this->addUsingAlias(LanguageTableMap::ID, $language->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the language table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LanguageTableMap::DATABASE_NAME);
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
            LanguageTableMap::clearInstancePool();
            LanguageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildLanguage or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildLanguage object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(LanguageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LanguageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        LanguageTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            LanguageTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // LanguageQuery
