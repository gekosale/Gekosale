<?php

namespace Gekosale\Plugin\Settings\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Settings\Model\ORM\SettingsMailer as ChildSettingsMailer;
use Gekosale\Plugin\Settings\Model\ORM\SettingsMailerQuery as ChildSettingsMailerQuery;
use Gekosale\Plugin\Settings\Model\ORM\Map\SettingsMailerTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'settings_mailer' table.
 *
 * 
 *
 * @method     ChildSettingsMailerQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSettingsMailerQuery orderByMailer($order = Criteria::ASC) Order by the mailer column
 * @method     ChildSettingsMailerQuery orderByFromName($order = Criteria::ASC) Order by the from_name column
 * @method     ChildSettingsMailerQuery orderByFromEmail($order = Criteria::ASC) Order by the from_email column
 * @method     ChildSettingsMailerQuery orderByServer($order = Criteria::ASC) Order by the server column
 * @method     ChildSettingsMailerQuery orderByPort($order = Criteria::ASC) Order by the port column
 * @method     ChildSettingsMailerQuery orderBySmtpSecure($order = Criteria::ASC) Order by the smtp_secure column
 * @method     ChildSettingsMailerQuery orderBySmtpAuth($order = Criteria::ASC) Order by the smtp_auth column
 * @method     ChildSettingsMailerQuery orderBySmtpUsername($order = Criteria::ASC) Order by the smtp_username column
 * @method     ChildSettingsMailerQuery orderBySmtpPassword($order = Criteria::ASC) Order by the smtp_password column
 * @method     ChildSettingsMailerQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 *
 * @method     ChildSettingsMailerQuery groupById() Group by the id column
 * @method     ChildSettingsMailerQuery groupByMailer() Group by the mailer column
 * @method     ChildSettingsMailerQuery groupByFromName() Group by the from_name column
 * @method     ChildSettingsMailerQuery groupByFromEmail() Group by the from_email column
 * @method     ChildSettingsMailerQuery groupByServer() Group by the server column
 * @method     ChildSettingsMailerQuery groupByPort() Group by the port column
 * @method     ChildSettingsMailerQuery groupBySmtpSecure() Group by the smtp_secure column
 * @method     ChildSettingsMailerQuery groupBySmtpAuth() Group by the smtp_auth column
 * @method     ChildSettingsMailerQuery groupBySmtpUsername() Group by the smtp_username column
 * @method     ChildSettingsMailerQuery groupBySmtpPassword() Group by the smtp_password column
 * @method     ChildSettingsMailerQuery groupByShopId() Group by the shop_id column
 *
 * @method     ChildSettingsMailerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSettingsMailerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSettingsMailerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSettingsMailer findOne(ConnectionInterface $con = null) Return the first ChildSettingsMailer matching the query
 * @method     ChildSettingsMailer findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSettingsMailer matching the query, or a new ChildSettingsMailer object populated from the query conditions when no match is found
 *
 * @method     ChildSettingsMailer findOneById(int $id) Return the first ChildSettingsMailer filtered by the id column
 * @method     ChildSettingsMailer findOneByMailer(string $mailer) Return the first ChildSettingsMailer filtered by the mailer column
 * @method     ChildSettingsMailer findOneByFromName(string $from_name) Return the first ChildSettingsMailer filtered by the from_name column
 * @method     ChildSettingsMailer findOneByFromEmail(string $from_email) Return the first ChildSettingsMailer filtered by the from_email column
 * @method     ChildSettingsMailer findOneByServer(string $server) Return the first ChildSettingsMailer filtered by the server column
 * @method     ChildSettingsMailer findOneByPort(int $port) Return the first ChildSettingsMailer filtered by the port column
 * @method     ChildSettingsMailer findOneBySmtpSecure(string $smtp_secure) Return the first ChildSettingsMailer filtered by the smtp_secure column
 * @method     ChildSettingsMailer findOneBySmtpAuth(int $smtp_auth) Return the first ChildSettingsMailer filtered by the smtp_auth column
 * @method     ChildSettingsMailer findOneBySmtpUsername(string $smtp_username) Return the first ChildSettingsMailer filtered by the smtp_username column
 * @method     ChildSettingsMailer findOneBySmtpPassword(string $smtp_password) Return the first ChildSettingsMailer filtered by the smtp_password column
 * @method     ChildSettingsMailer findOneByShopId(int $shop_id) Return the first ChildSettingsMailer filtered by the shop_id column
 *
 * @method     array findById(int $id) Return ChildSettingsMailer objects filtered by the id column
 * @method     array findByMailer(string $mailer) Return ChildSettingsMailer objects filtered by the mailer column
 * @method     array findByFromName(string $from_name) Return ChildSettingsMailer objects filtered by the from_name column
 * @method     array findByFromEmail(string $from_email) Return ChildSettingsMailer objects filtered by the from_email column
 * @method     array findByServer(string $server) Return ChildSettingsMailer objects filtered by the server column
 * @method     array findByPort(int $port) Return ChildSettingsMailer objects filtered by the port column
 * @method     array findBySmtpSecure(string $smtp_secure) Return ChildSettingsMailer objects filtered by the smtp_secure column
 * @method     array findBySmtpAuth(int $smtp_auth) Return ChildSettingsMailer objects filtered by the smtp_auth column
 * @method     array findBySmtpUsername(string $smtp_username) Return ChildSettingsMailer objects filtered by the smtp_username column
 * @method     array findBySmtpPassword(string $smtp_password) Return ChildSettingsMailer objects filtered by the smtp_password column
 * @method     array findByShopId(int $shop_id) Return ChildSettingsMailer objects filtered by the shop_id column
 *
 */
abstract class SettingsMailerQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Settings\Model\ORM\Base\SettingsMailerQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Settings\\Model\\ORM\\SettingsMailer', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSettingsMailerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSettingsMailerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Settings\Model\ORM\SettingsMailerQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Settings\Model\ORM\SettingsMailerQuery();
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
     * @return ChildSettingsMailer|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SettingsMailerTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SettingsMailerTableMap::DATABASE_NAME);
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
     * @return   ChildSettingsMailer A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, MAILER, FROM_NAME, FROM_EMAIL, SERVER, PORT, SMTP_SECURE, SMTP_AUTH, SMTP_USERNAME, SMTP_PASSWORD, SHOP_ID FROM settings_mailer WHERE ID = :p0';
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
            $obj = new ChildSettingsMailer();
            $obj->hydrate($row);
            SettingsMailerTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSettingsMailer|array|mixed the result, formatted by the current formatter
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
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SettingsMailerTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SettingsMailerTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the mailer column
     *
     * Example usage:
     * <code>
     * $query->filterByMailer('fooValue');   // WHERE mailer = 'fooValue'
     * $query->filterByMailer('%fooValue%'); // WHERE mailer LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mailer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByMailer($mailer = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mailer)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mailer)) {
                $mailer = str_replace('*', '%', $mailer);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_MAILER, $mailer, $comparison);
    }

    /**
     * Filter the query on the from_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFromName('fooValue');   // WHERE from_name = 'fooValue'
     * $query->filterByFromName('%fooValue%'); // WHERE from_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fromName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByFromName($fromName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fromName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $fromName)) {
                $fromName = str_replace('*', '%', $fromName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_FROM_NAME, $fromName, $comparison);
    }

    /**
     * Filter the query on the from_email column
     *
     * Example usage:
     * <code>
     * $query->filterByFromEmail('fooValue');   // WHERE from_email = 'fooValue'
     * $query->filterByFromEmail('%fooValue%'); // WHERE from_email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fromEmail The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByFromEmail($fromEmail = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fromEmail)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $fromEmail)) {
                $fromEmail = str_replace('*', '%', $fromEmail);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_FROM_EMAIL, $fromEmail, $comparison);
    }

    /**
     * Filter the query on the server column
     *
     * Example usage:
     * <code>
     * $query->filterByServer('fooValue');   // WHERE server = 'fooValue'
     * $query->filterByServer('%fooValue%'); // WHERE server LIKE '%fooValue%'
     * </code>
     *
     * @param     string $server The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByServer($server = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($server)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $server)) {
                $server = str_replace('*', '%', $server);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_SERVER, $server, $comparison);
    }

    /**
     * Filter the query on the port column
     *
     * Example usage:
     * <code>
     * $query->filterByPort(1234); // WHERE port = 1234
     * $query->filterByPort(array(12, 34)); // WHERE port IN (12, 34)
     * $query->filterByPort(array('min' => 12)); // WHERE port > 12
     * </code>
     *
     * @param     mixed $port The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByPort($port = null, $comparison = null)
    {
        if (is_array($port)) {
            $useMinMax = false;
            if (isset($port['min'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_PORT, $port['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($port['max'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_PORT, $port['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_PORT, $port, $comparison);
    }

    /**
     * Filter the query on the smtp_secure column
     *
     * Example usage:
     * <code>
     * $query->filterBySmtpSecure('fooValue');   // WHERE smtp_secure = 'fooValue'
     * $query->filterBySmtpSecure('%fooValue%'); // WHERE smtp_secure LIKE '%fooValue%'
     * </code>
     *
     * @param     string $smtpSecure The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterBySmtpSecure($smtpSecure = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($smtpSecure)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $smtpSecure)) {
                $smtpSecure = str_replace('*', '%', $smtpSecure);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_SMTP_SECURE, $smtpSecure, $comparison);
    }

    /**
     * Filter the query on the smtp_auth column
     *
     * Example usage:
     * <code>
     * $query->filterBySmtpAuth(1234); // WHERE smtp_auth = 1234
     * $query->filterBySmtpAuth(array(12, 34)); // WHERE smtp_auth IN (12, 34)
     * $query->filterBySmtpAuth(array('min' => 12)); // WHERE smtp_auth > 12
     * </code>
     *
     * @param     mixed $smtpAuth The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterBySmtpAuth($smtpAuth = null, $comparison = null)
    {
        if (is_array($smtpAuth)) {
            $useMinMax = false;
            if (isset($smtpAuth['min'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_SMTP_AUTH, $smtpAuth['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($smtpAuth['max'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_SMTP_AUTH, $smtpAuth['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_SMTP_AUTH, $smtpAuth, $comparison);
    }

    /**
     * Filter the query on the smtp_username column
     *
     * Example usage:
     * <code>
     * $query->filterBySmtpUsername('fooValue');   // WHERE smtp_username = 'fooValue'
     * $query->filterBySmtpUsername('%fooValue%'); // WHERE smtp_username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $smtpUsername The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterBySmtpUsername($smtpUsername = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($smtpUsername)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $smtpUsername)) {
                $smtpUsername = str_replace('*', '%', $smtpUsername);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_SMTP_USERNAME, $smtpUsername, $comparison);
    }

    /**
     * Filter the query on the smtp_password column
     *
     * Example usage:
     * <code>
     * $query->filterBySmtpPassword('fooValue');   // WHERE smtp_password = 'fooValue'
     * $query->filterBySmtpPassword('%fooValue%'); // WHERE smtp_password LIKE '%fooValue%'
     * </code>
     *
     * @param     string $smtpPassword The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterBySmtpPassword($smtpPassword = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($smtpPassword)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $smtpPassword)) {
                $smtpPassword = str_replace('*', '%', $smtpPassword);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_SMTP_PASSWORD, $smtpPassword, $comparison);
    }

    /**
     * Filter the query on the shop_id column
     *
     * Example usage:
     * <code>
     * $query->filterByShopId(1234); // WHERE shop_id = 1234
     * $query->filterByShopId(array(12, 34)); // WHERE shop_id IN (12, 34)
     * $query->filterByShopId(array('min' => 12)); // WHERE shop_id > 12
     * </code>
     *
     * @param     mixed $shopId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(SettingsMailerTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsMailerTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSettingsMailer $settingsMailer Object to remove from the list of results
     *
     * @return ChildSettingsMailerQuery The current query, for fluid interface
     */
    public function prune($settingsMailer = null)
    {
        if ($settingsMailer) {
            $this->addUsingAlias(SettingsMailerTableMap::COL_ID, $settingsMailer->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the settings_mailer table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsMailerTableMap::DATABASE_NAME);
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
            SettingsMailerTableMap::clearInstancePool();
            SettingsMailerTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSettingsMailer or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSettingsMailer object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsMailerTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SettingsMailerTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        SettingsMailerTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            SettingsMailerTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SettingsMailerQuery
