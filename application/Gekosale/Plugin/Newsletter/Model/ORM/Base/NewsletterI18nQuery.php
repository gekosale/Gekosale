<?php

namespace Gekosale\Plugin\Newsletter\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Newsletter\Model\ORM\NewsletterI18n as ChildNewsletterI18n;
use Gekosale\Plugin\Newsletter\Model\ORM\NewsletterI18nQuery as ChildNewsletterI18nQuery;
use Gekosale\Plugin\Newsletter\Model\ORM\Map\NewsletterI18nTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'newsletter_i18n' table.
 *
 * 
 *
 * @method     ChildNewsletterI18nQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildNewsletterI18nQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 * @method     ChildNewsletterI18nQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildNewsletterI18nQuery orderBySubject($order = Criteria::ASC) Order by the subject column
 * @method     ChildNewsletterI18nQuery orderByHtmlForm($order = Criteria::ASC) Order by the html_form column
 * @method     ChildNewsletterI18nQuery orderByTextForm($order = Criteria::ASC) Order by the text_form column
 *
 * @method     ChildNewsletterI18nQuery groupById() Group by the id column
 * @method     ChildNewsletterI18nQuery groupByLocale() Group by the locale column
 * @method     ChildNewsletterI18nQuery groupByName() Group by the name column
 * @method     ChildNewsletterI18nQuery groupBySubject() Group by the subject column
 * @method     ChildNewsletterI18nQuery groupByHtmlForm() Group by the html_form column
 * @method     ChildNewsletterI18nQuery groupByTextForm() Group by the text_form column
 *
 * @method     ChildNewsletterI18nQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNewsletterI18nQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNewsletterI18nQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNewsletterI18nQuery leftJoinNewsletter($relationAlias = null) Adds a LEFT JOIN clause to the query using the Newsletter relation
 * @method     ChildNewsletterI18nQuery rightJoinNewsletter($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Newsletter relation
 * @method     ChildNewsletterI18nQuery innerJoinNewsletter($relationAlias = null) Adds a INNER JOIN clause to the query using the Newsletter relation
 *
 * @method     ChildNewsletterI18n findOne(ConnectionInterface $con = null) Return the first ChildNewsletterI18n matching the query
 * @method     ChildNewsletterI18n findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNewsletterI18n matching the query, or a new ChildNewsletterI18n object populated from the query conditions when no match is found
 *
 * @method     ChildNewsletterI18n findOneById(int $id) Return the first ChildNewsletterI18n filtered by the id column
 * @method     ChildNewsletterI18n findOneByLocale(string $locale) Return the first ChildNewsletterI18n filtered by the locale column
 * @method     ChildNewsletterI18n findOneByName(string $name) Return the first ChildNewsletterI18n filtered by the name column
 * @method     ChildNewsletterI18n findOneBySubject(string $subject) Return the first ChildNewsletterI18n filtered by the subject column
 * @method     ChildNewsletterI18n findOneByHtmlForm(string $html_form) Return the first ChildNewsletterI18n filtered by the html_form column
 * @method     ChildNewsletterI18n findOneByTextForm(string $text_form) Return the first ChildNewsletterI18n filtered by the text_form column
 *
 * @method     array findById(int $id) Return ChildNewsletterI18n objects filtered by the id column
 * @method     array findByLocale(string $locale) Return ChildNewsletterI18n objects filtered by the locale column
 * @method     array findByName(string $name) Return ChildNewsletterI18n objects filtered by the name column
 * @method     array findBySubject(string $subject) Return ChildNewsletterI18n objects filtered by the subject column
 * @method     array findByHtmlForm(string $html_form) Return ChildNewsletterI18n objects filtered by the html_form column
 * @method     array findByTextForm(string $text_form) Return ChildNewsletterI18n objects filtered by the text_form column
 *
 */
abstract class NewsletterI18nQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Newsletter\Model\ORM\Base\NewsletterI18nQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Newsletter\\Model\\ORM\\NewsletterI18n', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNewsletterI18nQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNewsletterI18nQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Newsletter\Model\ORM\NewsletterI18nQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Newsletter\Model\ORM\NewsletterI18nQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$id, $locale] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildNewsletterI18n|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = NewsletterI18nTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NewsletterI18nTableMap::DATABASE_NAME);
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
     * @return   ChildNewsletterI18n A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, LOCALE, NAME, SUBJECT, HTML_FORM, TEXT_FORM FROM newsletter_i18n WHERE ID = :p0 AND LOCALE = :p1';
        try {
            $stmt = $con->prepare($sql);            
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);            
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildNewsletterI18n();
            $obj->hydrate($row);
            NewsletterI18nTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildNewsletterI18n|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(NewsletterI18nTableMap::COL_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(NewsletterI18nTableMap::COL_LOCALE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(NewsletterI18nTableMap::COL_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(NewsletterI18nTableMap::COL_LOCALE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByNewsletter()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NewsletterI18nTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NewsletterI18nTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsletterI18nTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the locale column
     *
     * Example usage:
     * <code>
     * $query->filterByLocale('fooValue');   // WHERE locale = 'fooValue'
     * $query->filterByLocale('%fooValue%'); // WHERE locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $locale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterByLocale($locale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($locale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $locale)) {
                $locale = str_replace('*', '%', $locale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NewsletterI18nTableMap::COL_LOCALE, $locale, $comparison);
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
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
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

        return $this->addUsingAlias(NewsletterI18nTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the subject column
     *
     * Example usage:
     * <code>
     * $query->filterBySubject('fooValue');   // WHERE subject = 'fooValue'
     * $query->filterBySubject('%fooValue%'); // WHERE subject LIKE '%fooValue%'
     * </code>
     *
     * @param     string $subject The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterBySubject($subject = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($subject)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $subject)) {
                $subject = str_replace('*', '%', $subject);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NewsletterI18nTableMap::COL_SUBJECT, $subject, $comparison);
    }

    /**
     * Filter the query on the html_form column
     *
     * Example usage:
     * <code>
     * $query->filterByHtmlForm('fooValue');   // WHERE html_form = 'fooValue'
     * $query->filterByHtmlForm('%fooValue%'); // WHERE html_form LIKE '%fooValue%'
     * </code>
     *
     * @param     string $htmlForm The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterByHtmlForm($htmlForm = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($htmlForm)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $htmlForm)) {
                $htmlForm = str_replace('*', '%', $htmlForm);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NewsletterI18nTableMap::COL_HTML_FORM, $htmlForm, $comparison);
    }

    /**
     * Filter the query on the text_form column
     *
     * Example usage:
     * <code>
     * $query->filterByTextForm('fooValue');   // WHERE text_form = 'fooValue'
     * $query->filterByTextForm('%fooValue%'); // WHERE text_form LIKE '%fooValue%'
     * </code>
     *
     * @param     string $textForm The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterByTextForm($textForm = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($textForm)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $textForm)) {
                $textForm = str_replace('*', '%', $textForm);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NewsletterI18nTableMap::COL_TEXT_FORM, $textForm, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Newsletter\Model\ORM\Newsletter object
     *
     * @param \Gekosale\Plugin\Newsletter\Model\ORM\Newsletter|ObjectCollection $newsletter The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function filterByNewsletter($newsletter, $comparison = null)
    {
        if ($newsletter instanceof \Gekosale\Plugin\Newsletter\Model\ORM\Newsletter) {
            return $this
                ->addUsingAlias(NewsletterI18nTableMap::COL_ID, $newsletter->getId(), $comparison);
        } elseif ($newsletter instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NewsletterI18nTableMap::COL_ID, $newsletter->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByNewsletter() only accepts arguments of type \Gekosale\Plugin\Newsletter\Model\ORM\Newsletter or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Newsletter relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function joinNewsletter($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Newsletter');

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
            $this->addJoinObject($join, 'Newsletter');
        }

        return $this;
    }

    /**
     * Use the Newsletter relation Newsletter object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Newsletter\Model\ORM\NewsletterQuery A secondary query class using the current class as primary query
     */
    public function useNewsletterQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinNewsletter($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Newsletter', '\Gekosale\Plugin\Newsletter\Model\ORM\NewsletterQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNewsletterI18n $newsletterI18n Object to remove from the list of results
     *
     * @return ChildNewsletterI18nQuery The current query, for fluid interface
     */
    public function prune($newsletterI18n = null)
    {
        if ($newsletterI18n) {
            $this->addCond('pruneCond0', $this->getAliasedColName(NewsletterI18nTableMap::COL_ID), $newsletterI18n->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(NewsletterI18nTableMap::COL_LOCALE), $newsletterI18n->getLocale(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the newsletter_i18n table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NewsletterI18nTableMap::DATABASE_NAME);
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
            NewsletterI18nTableMap::clearInstancePool();
            NewsletterI18nTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildNewsletterI18n or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildNewsletterI18n object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(NewsletterI18nTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NewsletterI18nTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        NewsletterI18nTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            NewsletterI18nTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // NewsletterI18nQuery
