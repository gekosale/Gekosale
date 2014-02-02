<?php

namespace Gekosale\Plugin\Vat\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Language;
use Gekosale\Plugin\Vat\Model\ORM\VatTranslation as ChildVatTranslation;
use Gekosale\Plugin\Vat\Model\ORM\VatTranslationQuery as ChildVatTranslationQuery;
use Gekosale\Plugin\Vat\Model\ORM\Map\VatTranslationTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'vat_translation' table.
 *
 * 
 *
 * @method     ChildVatTranslationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildVatTranslationQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildVatTranslationQuery orderByLanguageId($order = Criteria::ASC) Order by the language_id column
 * @method     ChildVatTranslationQuery orderByVatId($order = Criteria::ASC) Order by the vat_id column
 *
 * @method     ChildVatTranslationQuery groupById() Group by the id column
 * @method     ChildVatTranslationQuery groupByName() Group by the name column
 * @method     ChildVatTranslationQuery groupByLanguageId() Group by the language_id column
 * @method     ChildVatTranslationQuery groupByVatId() Group by the vat_id column
 *
 * @method     ChildVatTranslationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildVatTranslationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildVatTranslationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildVatTranslationQuery leftJoinLanguage($relationAlias = null) Adds a LEFT JOIN clause to the query using the Language relation
 * @method     ChildVatTranslationQuery rightJoinLanguage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Language relation
 * @method     ChildVatTranslationQuery innerJoinLanguage($relationAlias = null) Adds a INNER JOIN clause to the query using the Language relation
 *
 * @method     ChildVatTranslationQuery leftJoinVat($relationAlias = null) Adds a LEFT JOIN clause to the query using the Vat relation
 * @method     ChildVatTranslationQuery rightJoinVat($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Vat relation
 * @method     ChildVatTranslationQuery innerJoinVat($relationAlias = null) Adds a INNER JOIN clause to the query using the Vat relation
 *
 * @method     ChildVatTranslation findOne(ConnectionInterface $con = null) Return the first ChildVatTranslation matching the query
 * @method     ChildVatTranslation findOneOrCreate(ConnectionInterface $con = null) Return the first ChildVatTranslation matching the query, or a new ChildVatTranslation object populated from the query conditions when no match is found
 *
 * @method     ChildVatTranslation findOneById(int $id) Return the first ChildVatTranslation filtered by the id column
 * @method     ChildVatTranslation findOneByName(string $name) Return the first ChildVatTranslation filtered by the name column
 * @method     ChildVatTranslation findOneByLanguageId(int $language_id) Return the first ChildVatTranslation filtered by the language_id column
 * @method     ChildVatTranslation findOneByVatId(int $vat_id) Return the first ChildVatTranslation filtered by the vat_id column
 *
 * @method     array findById(int $id) Return ChildVatTranslation objects filtered by the id column
 * @method     array findByName(string $name) Return ChildVatTranslation objects filtered by the name column
 * @method     array findByLanguageId(int $language_id) Return ChildVatTranslation objects filtered by the language_id column
 * @method     array findByVatId(int $vat_id) Return ChildVatTranslation objects filtered by the vat_id column
 *
 */
abstract class VatTranslationQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Vat\Model\ORM\Base\VatTranslationQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Vat\\Model\\ORM\\VatTranslation', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildVatTranslationQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildVatTranslationQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Vat\Model\ORM\VatTranslationQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Vat\Model\ORM\VatTranslationQuery();
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
     * @return ChildVatTranslation|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = VatTranslationTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(VatTranslationTableMap::DATABASE_NAME);
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
     * @return   ChildVatTranslation A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, LANGUAGE_ID, VAT_ID FROM vat_translation WHERE ID = :p0';
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
            $obj = new ChildVatTranslation();
            $obj->hydrate($row);
            VatTranslationTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildVatTranslation|array|mixed the result, formatted by the current formatter
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
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(VatTranslationTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(VatTranslationTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(VatTranslationTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(VatTranslationTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VatTranslationTableMap::ID, $id, $comparison);
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
     * @return ChildVatTranslationQuery The current query, for fluid interface
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

        return $this->addUsingAlias(VatTranslationTableMap::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the language_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLanguageId(1234); // WHERE language_id = 1234
     * $query->filterByLanguageId(array(12, 34)); // WHERE language_id IN (12, 34)
     * $query->filterByLanguageId(array('min' => 12)); // WHERE language_id > 12
     * </code>
     *
     * @see       filterByLanguage()
     *
     * @param     mixed $languageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterByLanguageId($languageId = null, $comparison = null)
    {
        if (is_array($languageId)) {
            $useMinMax = false;
            if (isset($languageId['min'])) {
                $this->addUsingAlias(VatTranslationTableMap::LANGUAGE_ID, $languageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($languageId['max'])) {
                $this->addUsingAlias(VatTranslationTableMap::LANGUAGE_ID, $languageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VatTranslationTableMap::LANGUAGE_ID, $languageId, $comparison);
    }

    /**
     * Filter the query on the vat_id column
     *
     * Example usage:
     * <code>
     * $query->filterByVatId(1234); // WHERE vat_id = 1234
     * $query->filterByVatId(array(12, 34)); // WHERE vat_id IN (12, 34)
     * $query->filterByVatId(array('min' => 12)); // WHERE vat_id > 12
     * </code>
     *
     * @see       filterByVat()
     *
     * @param     mixed $vatId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterByVatId($vatId = null, $comparison = null)
    {
        if (is_array($vatId)) {
            $useMinMax = false;
            if (isset($vatId['min'])) {
                $this->addUsingAlias(VatTranslationTableMap::VAT_ID, $vatId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vatId['max'])) {
                $this->addUsingAlias(VatTranslationTableMap::VAT_ID, $vatId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VatTranslationTableMap::VAT_ID, $vatId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Language object
     *
     * @param \Gekosale\Plugin\Language|ObjectCollection $language The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterByLanguage($language, $comparison = null)
    {
        if ($language instanceof \Gekosale\Plugin\Language) {
            return $this
                ->addUsingAlias(VatTranslationTableMap::LANGUAGE_ID, $language->getId(), $comparison);
        } elseif ($language instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(VatTranslationTableMap::LANGUAGE_ID, $language->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByLanguage() only accepts arguments of type \Gekosale\Plugin\Language or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Language relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function joinLanguage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Language');

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
            $this->addJoinObject($join, 'Language');
        }

        return $this;
    }

    /**
     * Use the Language relation Language object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\LanguageQuery A secondary query class using the current class as primary query
     */
    public function useLanguageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLanguage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Language', '\Gekosale\Plugin\LanguageQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Vat\Model\ORM\Vat object
     *
     * @param \Gekosale\Plugin\Vat\Model\ORM\Vat|ObjectCollection $vat The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function filterByVat($vat, $comparison = null)
    {
        if ($vat instanceof \Gekosale\Plugin\Vat\Model\ORM\Vat) {
            return $this
                ->addUsingAlias(VatTranslationTableMap::VAT_ID, $vat->getId(), $comparison);
        } elseif ($vat instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(VatTranslationTableMap::VAT_ID, $vat->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByVat() only accepts arguments of type \Gekosale\Plugin\Vat\Model\ORM\Vat or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Vat relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function joinVat($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Vat');

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
            $this->addJoinObject($join, 'Vat');
        }

        return $this;
    }

    /**
     * Use the Vat relation Vat object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Vat\Model\ORM\VatQuery A secondary query class using the current class as primary query
     */
    public function useVatQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinVat($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Vat', '\Gekosale\Plugin\Vat\Model\ORM\VatQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildVatTranslation $vatTranslation Object to remove from the list of results
     *
     * @return ChildVatTranslationQuery The current query, for fluid interface
     */
    public function prune($vatTranslation = null)
    {
        if ($vatTranslation) {
            $this->addUsingAlias(VatTranslationTableMap::ID, $vatTranslation->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the vat_translation table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VatTranslationTableMap::DATABASE_NAME);
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
            VatTranslationTableMap::clearInstancePool();
            VatTranslationTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildVatTranslation or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildVatTranslation object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(VatTranslationTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(VatTranslationTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        VatTranslationTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            VatTranslationTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // VatTranslationQuery
