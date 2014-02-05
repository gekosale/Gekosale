<?php

namespace Gekosale\Plugin\Newsletter\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Newsletter\Model\ORM\Newsletter as ChildNewsletter;
use Gekosale\Plugin\Newsletter\Model\ORM\NewsletterQuery as ChildNewsletterQuery;
use Gekosale\Plugin\Newsletter\Model\ORM\Map\NewsletterTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'newsletter' table.
 *
 * 
 *
 * @method     ChildNewsletterQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildNewsletterQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildNewsletterQuery orderBySubject($order = Criteria::ASC) Order by the subject column
 * @method     ChildNewsletterQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     ChildNewsletterQuery orderByHtmlForm($order = Criteria::ASC) Order by the html_form column
 * @method     ChildNewsletterQuery orderByTextForm($order = Criteria::ASC) Order by the text_form column
 * @method     ChildNewsletterQuery orderByRecipients($order = Criteria::ASC) Order by the recipients column
 *
 * @method     ChildNewsletterQuery groupById() Group by the id column
 * @method     ChildNewsletterQuery groupByName() Group by the name column
 * @method     ChildNewsletterQuery groupBySubject() Group by the subject column
 * @method     ChildNewsletterQuery groupByEmail() Group by the email column
 * @method     ChildNewsletterQuery groupByHtmlForm() Group by the html_form column
 * @method     ChildNewsletterQuery groupByTextForm() Group by the text_form column
 * @method     ChildNewsletterQuery groupByRecipients() Group by the recipients column
 *
 * @method     ChildNewsletterQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNewsletterQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNewsletterQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNewsletter findOne(ConnectionInterface $con = null) Return the first ChildNewsletter matching the query
 * @method     ChildNewsletter findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNewsletter matching the query, or a new ChildNewsletter object populated from the query conditions when no match is found
 *
 * @method     ChildNewsletter findOneById(int $id) Return the first ChildNewsletter filtered by the id column
 * @method     ChildNewsletter findOneByName(string $name) Return the first ChildNewsletter filtered by the name column
 * @method     ChildNewsletter findOneBySubject(string $subject) Return the first ChildNewsletter filtered by the subject column
 * @method     ChildNewsletter findOneByEmail(string $email) Return the first ChildNewsletter filtered by the email column
 * @method     ChildNewsletter findOneByHtmlForm(string $html_form) Return the first ChildNewsletter filtered by the html_form column
 * @method     ChildNewsletter findOneByTextForm(string $text_form) Return the first ChildNewsletter filtered by the text_form column
 * @method     ChildNewsletter findOneByRecipients(string $recipients) Return the first ChildNewsletter filtered by the recipients column
 *
 * @method     array findById(int $id) Return ChildNewsletter objects filtered by the id column
 * @method     array findByName(string $name) Return ChildNewsletter objects filtered by the name column
 * @method     array findBySubject(string $subject) Return ChildNewsletter objects filtered by the subject column
 * @method     array findByEmail(string $email) Return ChildNewsletter objects filtered by the email column
 * @method     array findByHtmlForm(string $html_form) Return ChildNewsletter objects filtered by the html_form column
 * @method     array findByTextForm(string $text_form) Return ChildNewsletter objects filtered by the text_form column
 * @method     array findByRecipients(string $recipients) Return ChildNewsletter objects filtered by the recipients column
 *
 */
abstract class NewsletterQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Newsletter\Model\ORM\Base\NewsletterQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Newsletter\\Model\\ORM\\Newsletter', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNewsletterQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNewsletterQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Newsletter\Model\ORM\NewsletterQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Newsletter\Model\ORM\NewsletterQuery();
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
     * @return ChildNewsletter|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = NewsletterTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NewsletterTableMap::DATABASE_NAME);
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
     * @return   ChildNewsletter A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, SUBJECT, EMAIL, HTML_FORM, TEXT_FORM, RECIPIENTS FROM newsletter WHERE ID = :p0';
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
            $obj = new ChildNewsletter();
            $obj->hydrate($row);
            NewsletterTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildNewsletter|array|mixed the result, formatted by the current formatter
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
     * @return ChildNewsletterQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(NewsletterTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildNewsletterQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(NewsletterTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildNewsletterQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NewsletterTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NewsletterTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsletterTableMap::COL_ID, $id, $comparison);
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
     * @return ChildNewsletterQuery The current query, for fluid interface
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

        return $this->addUsingAlias(NewsletterTableMap::COL_NAME, $name, $comparison);
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
     * @return ChildNewsletterQuery The current query, for fluid interface
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

        return $this->addUsingAlias(NewsletterTableMap::COL_SUBJECT, $subject, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NewsletterTableMap::COL_EMAIL, $email, $comparison);
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
     * @return ChildNewsletterQuery The current query, for fluid interface
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

        return $this->addUsingAlias(NewsletterTableMap::COL_HTML_FORM, $htmlForm, $comparison);
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
     * @return ChildNewsletterQuery The current query, for fluid interface
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

        return $this->addUsingAlias(NewsletterTableMap::COL_TEXT_FORM, $textForm, $comparison);
    }

    /**
     * Filter the query on the recipients column
     *
     * Example usage:
     * <code>
     * $query->filterByRecipients('fooValue');   // WHERE recipients = 'fooValue'
     * $query->filterByRecipients('%fooValue%'); // WHERE recipients LIKE '%fooValue%'
     * </code>
     *
     * @param     string $recipients The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNewsletterQuery The current query, for fluid interface
     */
    public function filterByRecipients($recipients = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($recipients)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $recipients)) {
                $recipients = str_replace('*', '%', $recipients);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NewsletterTableMap::COL_RECIPIENTS, $recipients, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNewsletter $newsletter Object to remove from the list of results
     *
     * @return ChildNewsletterQuery The current query, for fluid interface
     */
    public function prune($newsletter = null)
    {
        if ($newsletter) {
            $this->addUsingAlias(NewsletterTableMap::COL_ID, $newsletter->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the newsletter table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NewsletterTableMap::DATABASE_NAME);
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
            NewsletterTableMap::clearInstancePool();
            NewsletterTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildNewsletter or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildNewsletter object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(NewsletterTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NewsletterTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        NewsletterTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            NewsletterTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // NewsletterQuery
