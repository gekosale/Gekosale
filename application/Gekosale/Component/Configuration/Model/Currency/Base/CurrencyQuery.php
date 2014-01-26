<?php

namespace Gekosale\Component\Configuration\Model\Currency\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Configuration\Model\Currency\Currency as ChildCurrency;
use Gekosale\Component\Configuration\Model\Currency\CurrencyQuery as ChildCurrencyQuery;
use Gekosale\Component\Configuration\Model\Currency\Map\CurrencyTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'currency' table.
 *
 * 
 *
 * @method     ChildCurrencyQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCurrencyQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildCurrencyQuery orderBySymbol($order = Criteria::ASC) Order by the symbol column
 * @method     ChildCurrencyQuery orderByDecimalSeparator($order = Criteria::ASC) Order by the decimal_separator column
 * @method     ChildCurrencyQuery orderByThousandSeparator($order = Criteria::ASC) Order by the thousand_separator column
 * @method     ChildCurrencyQuery orderByPositivePreffix($order = Criteria::ASC) Order by the positive_preffix column
 * @method     ChildCurrencyQuery orderByPositiveSuffix($order = Criteria::ASC) Order by the positive_suffix column
 * @method     ChildCurrencyQuery orderByNegativePreffix($order = Criteria::ASC) Order by the negative_preffix column
 * @method     ChildCurrencyQuery orderByNegativeSuffix($order = Criteria::ASC) Order by the negative_suffix column
 * @method     ChildCurrencyQuery orderByDecimalCount($order = Criteria::ASC) Order by the decimal_count column
 *
 * @method     ChildCurrencyQuery groupById() Group by the id column
 * @method     ChildCurrencyQuery groupByName() Group by the name column
 * @method     ChildCurrencyQuery groupBySymbol() Group by the symbol column
 * @method     ChildCurrencyQuery groupByDecimalSeparator() Group by the decimal_separator column
 * @method     ChildCurrencyQuery groupByThousandSeparator() Group by the thousand_separator column
 * @method     ChildCurrencyQuery groupByPositivePreffix() Group by the positive_preffix column
 * @method     ChildCurrencyQuery groupByPositiveSuffix() Group by the positive_suffix column
 * @method     ChildCurrencyQuery groupByNegativePreffix() Group by the negative_preffix column
 * @method     ChildCurrencyQuery groupByNegativeSuffix() Group by the negative_suffix column
 * @method     ChildCurrencyQuery groupByDecimalCount() Group by the decimal_count column
 *
 * @method     ChildCurrencyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCurrencyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCurrencyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCurrency findOne(ConnectionInterface $con = null) Return the first ChildCurrency matching the query
 * @method     ChildCurrency findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCurrency matching the query, or a new ChildCurrency object populated from the query conditions when no match is found
 *
 * @method     ChildCurrency findOneById(int $id) Return the first ChildCurrency filtered by the id column
 * @method     ChildCurrency findOneByName(string $name) Return the first ChildCurrency filtered by the name column
 * @method     ChildCurrency findOneBySymbol(string $symbol) Return the first ChildCurrency filtered by the symbol column
 * @method     ChildCurrency findOneByDecimalSeparator(string $decimal_separator) Return the first ChildCurrency filtered by the decimal_separator column
 * @method     ChildCurrency findOneByThousandSeparator(string $thousand_separator) Return the first ChildCurrency filtered by the thousand_separator column
 * @method     ChildCurrency findOneByPositivePreffix(string $positive_preffix) Return the first ChildCurrency filtered by the positive_preffix column
 * @method     ChildCurrency findOneByPositiveSuffix(string $positive_suffix) Return the first ChildCurrency filtered by the positive_suffix column
 * @method     ChildCurrency findOneByNegativePreffix(string $negative_preffix) Return the first ChildCurrency filtered by the negative_preffix column
 * @method     ChildCurrency findOneByNegativeSuffix(string $negative_suffix) Return the first ChildCurrency filtered by the negative_suffix column
 * @method     ChildCurrency findOneByDecimalCount(int $decimal_count) Return the first ChildCurrency filtered by the decimal_count column
 *
 * @method     array findById(int $id) Return ChildCurrency objects filtered by the id column
 * @method     array findByName(string $name) Return ChildCurrency objects filtered by the name column
 * @method     array findBySymbol(string $symbol) Return ChildCurrency objects filtered by the symbol column
 * @method     array findByDecimalSeparator(string $decimal_separator) Return ChildCurrency objects filtered by the decimal_separator column
 * @method     array findByThousandSeparator(string $thousand_separator) Return ChildCurrency objects filtered by the thousand_separator column
 * @method     array findByPositivePreffix(string $positive_preffix) Return ChildCurrency objects filtered by the positive_preffix column
 * @method     array findByPositiveSuffix(string $positive_suffix) Return ChildCurrency objects filtered by the positive_suffix column
 * @method     array findByNegativePreffix(string $negative_preffix) Return ChildCurrency objects filtered by the negative_preffix column
 * @method     array findByNegativeSuffix(string $negative_suffix) Return ChildCurrency objects filtered by the negative_suffix column
 * @method     array findByDecimalCount(int $decimal_count) Return ChildCurrency objects filtered by the decimal_count column
 *
 */
abstract class CurrencyQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Component\Configuration\Model\Currency\Base\CurrencyQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Component\\Configuration\\Model\\Currency\\Currency', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCurrencyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCurrencyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Component\Configuration\Model\Currency\CurrencyQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Component\Configuration\Model\Currency\CurrencyQuery();
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
     * @return ChildCurrency|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CurrencyTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CurrencyTableMap::DATABASE_NAME);
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
     * @return   ChildCurrency A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, SYMBOL, DECIMAL_SEPARATOR, THOUSAND_SEPARATOR, POSITIVE_PREFFIX, POSITIVE_SUFFIX, NEGATIVE_PREFFIX, NEGATIVE_SUFFIX, DECIMAL_COUNT FROM currency WHERE ID = :p0';
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
            $obj = new ChildCurrency();
            $obj->hydrate($row);
            CurrencyTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCurrency|array|mixed the result, formatted by the current formatter
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
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CurrencyTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CurrencyTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CurrencyTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CurrencyTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::ID, $id, $comparison);
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
     * @return ChildCurrencyQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CurrencyTableMap::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the symbol column
     *
     * Example usage:
     * <code>
     * $query->filterBySymbol('fooValue');   // WHERE symbol = 'fooValue'
     * $query->filterBySymbol('%fooValue%'); // WHERE symbol LIKE '%fooValue%'
     * </code>
     *
     * @param     string $symbol The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterBySymbol($symbol = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($symbol)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $symbol)) {
                $symbol = str_replace('*', '%', $symbol);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::SYMBOL, $symbol, $comparison);
    }

    /**
     * Filter the query on the decimal_separator column
     *
     * Example usage:
     * <code>
     * $query->filterByDecimalSeparator('fooValue');   // WHERE decimal_separator = 'fooValue'
     * $query->filterByDecimalSeparator('%fooValue%'); // WHERE decimal_separator LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decimalSeparator The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByDecimalSeparator($decimalSeparator = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decimalSeparator)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decimalSeparator)) {
                $decimalSeparator = str_replace('*', '%', $decimalSeparator);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::DECIMAL_SEPARATOR, $decimalSeparator, $comparison);
    }

    /**
     * Filter the query on the thousand_separator column
     *
     * Example usage:
     * <code>
     * $query->filterByThousandSeparator('fooValue');   // WHERE thousand_separator = 'fooValue'
     * $query->filterByThousandSeparator('%fooValue%'); // WHERE thousand_separator LIKE '%fooValue%'
     * </code>
     *
     * @param     string $thousandSeparator The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByThousandSeparator($thousandSeparator = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($thousandSeparator)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $thousandSeparator)) {
                $thousandSeparator = str_replace('*', '%', $thousandSeparator);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::THOUSAND_SEPARATOR, $thousandSeparator, $comparison);
    }

    /**
     * Filter the query on the positive_preffix column
     *
     * Example usage:
     * <code>
     * $query->filterByPositivePreffix('fooValue');   // WHERE positive_preffix = 'fooValue'
     * $query->filterByPositivePreffix('%fooValue%'); // WHERE positive_preffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $positivePreffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPositivePreffix($positivePreffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($positivePreffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $positivePreffix)) {
                $positivePreffix = str_replace('*', '%', $positivePreffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::POSITIVE_PREFFIX, $positivePreffix, $comparison);
    }

    /**
     * Filter the query on the positive_suffix column
     *
     * Example usage:
     * <code>
     * $query->filterByPositiveSuffix('fooValue');   // WHERE positive_suffix = 'fooValue'
     * $query->filterByPositiveSuffix('%fooValue%'); // WHERE positive_suffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $positiveSuffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPositiveSuffix($positiveSuffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($positiveSuffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $positiveSuffix)) {
                $positiveSuffix = str_replace('*', '%', $positiveSuffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::POSITIVE_SUFFIX, $positiveSuffix, $comparison);
    }

    /**
     * Filter the query on the negative_preffix column
     *
     * Example usage:
     * <code>
     * $query->filterByNegativePreffix('fooValue');   // WHERE negative_preffix = 'fooValue'
     * $query->filterByNegativePreffix('%fooValue%'); // WHERE negative_preffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $negativePreffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByNegativePreffix($negativePreffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($negativePreffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $negativePreffix)) {
                $negativePreffix = str_replace('*', '%', $negativePreffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::NEGATIVE_PREFFIX, $negativePreffix, $comparison);
    }

    /**
     * Filter the query on the negative_suffix column
     *
     * Example usage:
     * <code>
     * $query->filterByNegativeSuffix('fooValue');   // WHERE negative_suffix = 'fooValue'
     * $query->filterByNegativeSuffix('%fooValue%'); // WHERE negative_suffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $negativeSuffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByNegativeSuffix($negativeSuffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($negativeSuffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $negativeSuffix)) {
                $negativeSuffix = str_replace('*', '%', $negativeSuffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::NEGATIVE_SUFFIX, $negativeSuffix, $comparison);
    }

    /**
     * Filter the query on the decimal_count column
     *
     * Example usage:
     * <code>
     * $query->filterByDecimalCount(1234); // WHERE decimal_count = 1234
     * $query->filterByDecimalCount(array(12, 34)); // WHERE decimal_count IN (12, 34)
     * $query->filterByDecimalCount(array('min' => 12)); // WHERE decimal_count > 12
     * </code>
     *
     * @param     mixed $decimalCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByDecimalCount($decimalCount = null, $comparison = null)
    {
        if (is_array($decimalCount)) {
            $useMinMax = false;
            if (isset($decimalCount['min'])) {
                $this->addUsingAlias(CurrencyTableMap::DECIMAL_COUNT, $decimalCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($decimalCount['max'])) {
                $this->addUsingAlias(CurrencyTableMap::DECIMAL_COUNT, $decimalCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::DECIMAL_COUNT, $decimalCount, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCurrency $currency Object to remove from the list of results
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function prune($currency = null)
    {
        if ($currency) {
            $this->addUsingAlias(CurrencyTableMap::ID, $currency->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the currency table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
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
            CurrencyTableMap::clearInstancePool();
            CurrencyTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCurrency or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCurrency object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CurrencyTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CurrencyTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CurrencyTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CurrencyQuery
