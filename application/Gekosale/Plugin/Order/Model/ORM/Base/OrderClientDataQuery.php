<?php

namespace Gekosale\Plugin\Order\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Order\Model\ORM\OrderClientData as ChildOrderClientData;
use Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery as ChildOrderClientDataQuery;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderClientDataTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'order_client_data' table.
 *
 * 
 *
 * @method     ChildOrderClientDataQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderClientDataQuery orderByFirstname($order = Criteria::ASC) Order by the firstname column
 * @method     ChildOrderClientDataQuery orderBySurname($order = Criteria::ASC) Order by the surname column
 * @method     ChildOrderClientDataQuery orderByCompanyName($order = Criteria::ASC) Order by the company_name column
 * @method     ChildOrderClientDataQuery orderByTaxId($order = Criteria::ASC) Order by the tax_id column
 * @method     ChildOrderClientDataQuery orderByStreet($order = Criteria::ASC) Order by the street column
 * @method     ChildOrderClientDataQuery orderByStreetNo($order = Criteria::ASC) Order by the street_no column
 * @method     ChildOrderClientDataQuery orderByPlaceNo($order = Criteria::ASC) Order by the place_no column
 * @method     ChildOrderClientDataQuery orderByPostCode($order = Criteria::ASC) Order by the post_code column
 * @method     ChildOrderClientDataQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method     ChildOrderClientDataQuery orderByPhone($order = Criteria::ASC) Order by the phone column
 * @method     ChildOrderClientDataQuery orderByPhone2($order = Criteria::ASC) Order by the phone2 column
 * @method     ChildOrderClientDataQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     ChildOrderClientDataQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildOrderClientDataQuery orderByClientId($order = Criteria::ASC) Order by the client_id column
 * @method     ChildOrderClientDataQuery orderByCountryId($order = Criteria::ASC) Order by the country_id column
 * @method     ChildOrderClientDataQuery orderByClientType($order = Criteria::ASC) Order by the client_type column
 *
 * @method     ChildOrderClientDataQuery groupById() Group by the id column
 * @method     ChildOrderClientDataQuery groupByFirstname() Group by the firstname column
 * @method     ChildOrderClientDataQuery groupBySurname() Group by the surname column
 * @method     ChildOrderClientDataQuery groupByCompanyName() Group by the company_name column
 * @method     ChildOrderClientDataQuery groupByTaxId() Group by the tax_id column
 * @method     ChildOrderClientDataQuery groupByStreet() Group by the street column
 * @method     ChildOrderClientDataQuery groupByStreetNo() Group by the street_no column
 * @method     ChildOrderClientDataQuery groupByPlaceNo() Group by the place_no column
 * @method     ChildOrderClientDataQuery groupByPostCode() Group by the post_code column
 * @method     ChildOrderClientDataQuery groupByCity() Group by the city column
 * @method     ChildOrderClientDataQuery groupByPhone() Group by the phone column
 * @method     ChildOrderClientDataQuery groupByPhone2() Group by the phone2 column
 * @method     ChildOrderClientDataQuery groupByEmail() Group by the email column
 * @method     ChildOrderClientDataQuery groupByOrderId() Group by the order_id column
 * @method     ChildOrderClientDataQuery groupByClientId() Group by the client_id column
 * @method     ChildOrderClientDataQuery groupByCountryId() Group by the country_id column
 * @method     ChildOrderClientDataQuery groupByClientType() Group by the client_type column
 *
 * @method     ChildOrderClientDataQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderClientDataQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderClientDataQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderClientDataQuery leftJoinOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the Order relation
 * @method     ChildOrderClientDataQuery rightJoinOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Order relation
 * @method     ChildOrderClientDataQuery innerJoinOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the Order relation
 *
 * @method     ChildOrderClientData findOne(ConnectionInterface $con = null) Return the first ChildOrderClientData matching the query
 * @method     ChildOrderClientData findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrderClientData matching the query, or a new ChildOrderClientData object populated from the query conditions when no match is found
 *
 * @method     ChildOrderClientData findOneById(int $id) Return the first ChildOrderClientData filtered by the id column
 * @method     ChildOrderClientData findOneByFirstname(resource $firstname) Return the first ChildOrderClientData filtered by the firstname column
 * @method     ChildOrderClientData findOneBySurname(resource $surname) Return the first ChildOrderClientData filtered by the surname column
 * @method     ChildOrderClientData findOneByCompanyName(resource $company_name) Return the first ChildOrderClientData filtered by the company_name column
 * @method     ChildOrderClientData findOneByTaxId(resource $tax_id) Return the first ChildOrderClientData filtered by the tax_id column
 * @method     ChildOrderClientData findOneByStreet(resource $street) Return the first ChildOrderClientData filtered by the street column
 * @method     ChildOrderClientData findOneByStreetNo(resource $street_no) Return the first ChildOrderClientData filtered by the street_no column
 * @method     ChildOrderClientData findOneByPlaceNo(resource $place_no) Return the first ChildOrderClientData filtered by the place_no column
 * @method     ChildOrderClientData findOneByPostCode(resource $post_code) Return the first ChildOrderClientData filtered by the post_code column
 * @method     ChildOrderClientData findOneByCity(resource $city) Return the first ChildOrderClientData filtered by the city column
 * @method     ChildOrderClientData findOneByPhone(resource $phone) Return the first ChildOrderClientData filtered by the phone column
 * @method     ChildOrderClientData findOneByPhone2(resource $phone2) Return the first ChildOrderClientData filtered by the phone2 column
 * @method     ChildOrderClientData findOneByEmail(resource $email) Return the first ChildOrderClientData filtered by the email column
 * @method     ChildOrderClientData findOneByOrderId(int $order_id) Return the first ChildOrderClientData filtered by the order_id column
 * @method     ChildOrderClientData findOneByClientId(int $client_id) Return the first ChildOrderClientData filtered by the client_id column
 * @method     ChildOrderClientData findOneByCountryId(int $country_id) Return the first ChildOrderClientData filtered by the country_id column
 * @method     ChildOrderClientData findOneByClientType(int $client_type) Return the first ChildOrderClientData filtered by the client_type column
 *
 * @method     array findById(int $id) Return ChildOrderClientData objects filtered by the id column
 * @method     array findByFirstname(resource $firstname) Return ChildOrderClientData objects filtered by the firstname column
 * @method     array findBySurname(resource $surname) Return ChildOrderClientData objects filtered by the surname column
 * @method     array findByCompanyName(resource $company_name) Return ChildOrderClientData objects filtered by the company_name column
 * @method     array findByTaxId(resource $tax_id) Return ChildOrderClientData objects filtered by the tax_id column
 * @method     array findByStreet(resource $street) Return ChildOrderClientData objects filtered by the street column
 * @method     array findByStreetNo(resource $street_no) Return ChildOrderClientData objects filtered by the street_no column
 * @method     array findByPlaceNo(resource $place_no) Return ChildOrderClientData objects filtered by the place_no column
 * @method     array findByPostCode(resource $post_code) Return ChildOrderClientData objects filtered by the post_code column
 * @method     array findByCity(resource $city) Return ChildOrderClientData objects filtered by the city column
 * @method     array findByPhone(resource $phone) Return ChildOrderClientData objects filtered by the phone column
 * @method     array findByPhone2(resource $phone2) Return ChildOrderClientData objects filtered by the phone2 column
 * @method     array findByEmail(resource $email) Return ChildOrderClientData objects filtered by the email column
 * @method     array findByOrderId(int $order_id) Return ChildOrderClientData objects filtered by the order_id column
 * @method     array findByClientId(int $client_id) Return ChildOrderClientData objects filtered by the client_id column
 * @method     array findByCountryId(int $country_id) Return ChildOrderClientData objects filtered by the country_id column
 * @method     array findByClientType(int $client_type) Return ChildOrderClientData objects filtered by the client_type column
 *
 */
abstract class OrderClientDataQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Order\Model\ORM\Base\OrderClientDataQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderClientData', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderClientDataQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOrderClientDataQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery();
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
     * @return ChildOrderClientData|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderClientDataTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderClientDataTableMap::DATABASE_NAME);
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
     * @return   ChildOrderClientData A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, FIRSTNAME, SURNAME, COMPANY_NAME, TAX_ID, STREET, STREET_NO, PLACE_NO, POST_CODE, CITY, PHONE, PHONE2, EMAIL, ORDER_ID, CLIENT_ID, COUNTRY_ID, CLIENT_TYPE FROM order_client_data WHERE ID = :p0';
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
            $obj = new ChildOrderClientData();
            $obj->hydrate($row);
            OrderClientDataTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildOrderClientData|array|mixed the result, formatted by the current formatter
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
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderClientDataTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the firstname column
     *
     * @param     mixed $firstname The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByFirstname($firstname = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_FIRSTNAME, $firstname, $comparison);
    }

    /**
     * Filter the query on the surname column
     *
     * @param     mixed $surname The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterBySurname($surname = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_SURNAME, $surname, $comparison);
    }

    /**
     * Filter the query on the company_name column
     *
     * @param     mixed $companyName The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByCompanyName($companyName = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_COMPANY_NAME, $companyName, $comparison);
    }

    /**
     * Filter the query on the tax_id column
     *
     * @param     mixed $taxId The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByTaxId($taxId = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_TAX_ID, $taxId, $comparison);
    }

    /**
     * Filter the query on the street column
     *
     * @param     mixed $street The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByStreet($street = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_STREET, $street, $comparison);
    }

    /**
     * Filter the query on the street_no column
     *
     * @param     mixed $streetNo The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByStreetNo($streetNo = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_STREET_NO, $streetNo, $comparison);
    }

    /**
     * Filter the query on the place_no column
     *
     * @param     mixed $placeNo The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByPlaceNo($placeNo = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_PLACE_NO, $placeNo, $comparison);
    }

    /**
     * Filter the query on the post_code column
     *
     * @param     mixed $postCode The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByPostCode($postCode = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_POST_CODE, $postCode, $comparison);
    }

    /**
     * Filter the query on the city column
     *
     * @param     mixed $city The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByCity($city = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_CITY, $city, $comparison);
    }

    /**
     * Filter the query on the phone column
     *
     * @param     mixed $phone The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByPhone($phone = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_PHONE, $phone, $comparison);
    }

    /**
     * Filter the query on the phone2 column
     *
     * @param     mixed $phone2 The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByPhone2($phone2 = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_PHONE2, $phone2, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * @param     mixed $email The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {

        return $this->addUsingAlias(OrderClientDataTableMap::COL_EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the order_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderId(1234); // WHERE order_id = 1234
     * $query->filterByOrderId(array(12, 34)); // WHERE order_id IN (12, 34)
     * $query->filterByOrderId(array('min' => 12)); // WHERE order_id > 12
     * </code>
     *
     * @see       filterByOrder()
     *
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderClientDataTableMap::COL_ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the client_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClientId(1234); // WHERE client_id = 1234
     * $query->filterByClientId(array(12, 34)); // WHERE client_id IN (12, 34)
     * $query->filterByClientId(array('min' => 12)); // WHERE client_id > 12
     * </code>
     *
     * @param     mixed $clientId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByClientId($clientId = null, $comparison = null)
    {
        if (is_array($clientId)) {
            $useMinMax = false;
            if (isset($clientId['min'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_CLIENT_ID, $clientId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientId['max'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_CLIENT_ID, $clientId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderClientDataTableMap::COL_CLIENT_ID, $clientId, $comparison);
    }

    /**
     * Filter the query on the country_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCountryId(1234); // WHERE country_id = 1234
     * $query->filterByCountryId(array(12, 34)); // WHERE country_id IN (12, 34)
     * $query->filterByCountryId(array('min' => 12)); // WHERE country_id > 12
     * </code>
     *
     * @param     mixed $countryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByCountryId($countryId = null, $comparison = null)
    {
        if (is_array($countryId)) {
            $useMinMax = false;
            if (isset($countryId['min'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_COUNTRY_ID, $countryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($countryId['max'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_COUNTRY_ID, $countryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderClientDataTableMap::COL_COUNTRY_ID, $countryId, $comparison);
    }

    /**
     * Filter the query on the client_type column
     *
     * Example usage:
     * <code>
     * $query->filterByClientType(1234); // WHERE client_type = 1234
     * $query->filterByClientType(array(12, 34)); // WHERE client_type IN (12, 34)
     * $query->filterByClientType(array('min' => 12)); // WHERE client_type > 12
     * </code>
     *
     * @param     mixed $clientType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByClientType($clientType = null, $comparison = null)
    {
        if (is_array($clientType)) {
            $useMinMax = false;
            if (isset($clientType['min'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_CLIENT_TYPE, $clientType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientType['max'])) {
                $this->addUsingAlias(OrderClientDataTableMap::COL_CLIENT_TYPE, $clientType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderClientDataTableMap::COL_CLIENT_TYPE, $clientType, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\Order object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\Order|ObjectCollection $order The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function filterByOrder($order, $comparison = null)
    {
        if ($order instanceof \Gekosale\Plugin\Order\Model\ORM\Order) {
            return $this
                ->addUsingAlias(OrderClientDataTableMap::COL_ORDER_ID, $order->getId(), $comparison);
        } elseif ($order instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderClientDataTableMap::COL_ORDER_ID, $order->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrder() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\Order or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Order relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function joinOrder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Order');

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
            $this->addJoinObject($join, 'Order');
        }

        return $this;
    }

    /**
     * Use the Order relation Order object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderQuery A secondary query class using the current class as primary query
     */
    public function useOrderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Order', '\Gekosale\Plugin\Order\Model\ORM\OrderQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOrderClientData $orderClientData Object to remove from the list of results
     *
     * @return ChildOrderClientDataQuery The current query, for fluid interface
     */
    public function prune($orderClientData = null)
    {
        if ($orderClientData) {
            $this->addUsingAlias(OrderClientDataTableMap::COL_ID, $orderClientData->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order_client_data table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderClientDataTableMap::DATABASE_NAME);
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
            OrderClientDataTableMap::clearInstancePool();
            OrderClientDataTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrderClientData or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildOrderClientData object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderClientDataTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderClientDataTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        OrderClientDataTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            OrderClientDataTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderClientDataQuery
