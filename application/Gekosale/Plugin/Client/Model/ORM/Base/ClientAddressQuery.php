<?php

namespace Gekosale\Plugin\Client\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Client\Model\ORM\ClientAddress as ChildClientAddress;
use Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery as ChildClientAddressQuery;
use Gekosale\Plugin\Client\Model\ORM\Map\ClientAddressTableMap;
use Gekosale\Plugin\Country\Model\ORM\Country;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'client_address' table.
 *
 * 
 *
 * @method     ChildClientAddressQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildClientAddressQuery orderByStreet($order = Criteria::ASC) Order by the street column
 * @method     ChildClientAddressQuery orderByStreetNo($order = Criteria::ASC) Order by the street_no column
 * @method     ChildClientAddressQuery orderByPlaceNo($order = Criteria::ASC) Order by the place_no column
 * @method     ChildClientAddressQuery orderByPostCode($order = Criteria::ASC) Order by the post_code column
 * @method     ChildClientAddressQuery orderByCompanyName($order = Criteria::ASC) Order by the company_name column
 * @method     ChildClientAddressQuery orderByFirstname($order = Criteria::ASC) Order by the firstname column
 * @method     ChildClientAddressQuery orderBySurname($order = Criteria::ASC) Order by the surname column
 * @method     ChildClientAddressQuery orderByClientId($order = Criteria::ASC) Order by the client_id column
 * @method     ChildClientAddressQuery orderByRegon($order = Criteria::ASC) Order by the regon column
 * @method     ChildClientAddressQuery orderByTaxId($order = Criteria::ASC) Order by the tax_id column
 * @method     ChildClientAddressQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method     ChildClientAddressQuery orderByIsMain($order = Criteria::ASC) Order by the main column
 * @method     ChildClientAddressQuery orderByCountryId($order = Criteria::ASC) Order by the country_id column
 * @method     ChildClientAddressQuery orderByClientType($order = Criteria::ASC) Order by the client_type column
 *
 * @method     ChildClientAddressQuery groupById() Group by the id column
 * @method     ChildClientAddressQuery groupByStreet() Group by the street column
 * @method     ChildClientAddressQuery groupByStreetNo() Group by the street_no column
 * @method     ChildClientAddressQuery groupByPlaceNo() Group by the place_no column
 * @method     ChildClientAddressQuery groupByPostCode() Group by the post_code column
 * @method     ChildClientAddressQuery groupByCompanyName() Group by the company_name column
 * @method     ChildClientAddressQuery groupByFirstname() Group by the firstname column
 * @method     ChildClientAddressQuery groupBySurname() Group by the surname column
 * @method     ChildClientAddressQuery groupByClientId() Group by the client_id column
 * @method     ChildClientAddressQuery groupByRegon() Group by the regon column
 * @method     ChildClientAddressQuery groupByTaxId() Group by the tax_id column
 * @method     ChildClientAddressQuery groupByCity() Group by the city column
 * @method     ChildClientAddressQuery groupByIsMain() Group by the main column
 * @method     ChildClientAddressQuery groupByCountryId() Group by the country_id column
 * @method     ChildClientAddressQuery groupByClientType() Group by the client_type column
 *
 * @method     ChildClientAddressQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildClientAddressQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildClientAddressQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildClientAddressQuery leftJoinClient($relationAlias = null) Adds a LEFT JOIN clause to the query using the Client relation
 * @method     ChildClientAddressQuery rightJoinClient($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Client relation
 * @method     ChildClientAddressQuery innerJoinClient($relationAlias = null) Adds a INNER JOIN clause to the query using the Client relation
 *
 * @method     ChildClientAddressQuery leftJoinCountry($relationAlias = null) Adds a LEFT JOIN clause to the query using the Country relation
 * @method     ChildClientAddressQuery rightJoinCountry($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Country relation
 * @method     ChildClientAddressQuery innerJoinCountry($relationAlias = null) Adds a INNER JOIN clause to the query using the Country relation
 *
 * @method     ChildClientAddress findOne(ConnectionInterface $con = null) Return the first ChildClientAddress matching the query
 * @method     ChildClientAddress findOneOrCreate(ConnectionInterface $con = null) Return the first ChildClientAddress matching the query, or a new ChildClientAddress object populated from the query conditions when no match is found
 *
 * @method     ChildClientAddress findOneById(int $id) Return the first ChildClientAddress filtered by the id column
 * @method     ChildClientAddress findOneByStreet(resource $street) Return the first ChildClientAddress filtered by the street column
 * @method     ChildClientAddress findOneByStreetNo(resource $street_no) Return the first ChildClientAddress filtered by the street_no column
 * @method     ChildClientAddress findOneByPlaceNo(resource $place_no) Return the first ChildClientAddress filtered by the place_no column
 * @method     ChildClientAddress findOneByPostCode(resource $post_code) Return the first ChildClientAddress filtered by the post_code column
 * @method     ChildClientAddress findOneByCompanyName(resource $company_name) Return the first ChildClientAddress filtered by the company_name column
 * @method     ChildClientAddress findOneByFirstname(resource $firstname) Return the first ChildClientAddress filtered by the firstname column
 * @method     ChildClientAddress findOneBySurname(resource $surname) Return the first ChildClientAddress filtered by the surname column
 * @method     ChildClientAddress findOneByClientId(int $client_id) Return the first ChildClientAddress filtered by the client_id column
 * @method     ChildClientAddress findOneByRegon(resource $regon) Return the first ChildClientAddress filtered by the regon column
 * @method     ChildClientAddress findOneByTaxId(resource $tax_id) Return the first ChildClientAddress filtered by the tax_id column
 * @method     ChildClientAddress findOneByCity(resource $city) Return the first ChildClientAddress filtered by the city column
 * @method     ChildClientAddress findOneByIsMain(int $main) Return the first ChildClientAddress filtered by the main column
 * @method     ChildClientAddress findOneByCountryId(int $country_id) Return the first ChildClientAddress filtered by the country_id column
 * @method     ChildClientAddress findOneByClientType(int $client_type) Return the first ChildClientAddress filtered by the client_type column
 *
 * @method     array findById(int $id) Return ChildClientAddress objects filtered by the id column
 * @method     array findByStreet(resource $street) Return ChildClientAddress objects filtered by the street column
 * @method     array findByStreetNo(resource $street_no) Return ChildClientAddress objects filtered by the street_no column
 * @method     array findByPlaceNo(resource $place_no) Return ChildClientAddress objects filtered by the place_no column
 * @method     array findByPostCode(resource $post_code) Return ChildClientAddress objects filtered by the post_code column
 * @method     array findByCompanyName(resource $company_name) Return ChildClientAddress objects filtered by the company_name column
 * @method     array findByFirstname(resource $firstname) Return ChildClientAddress objects filtered by the firstname column
 * @method     array findBySurname(resource $surname) Return ChildClientAddress objects filtered by the surname column
 * @method     array findByClientId(int $client_id) Return ChildClientAddress objects filtered by the client_id column
 * @method     array findByRegon(resource $regon) Return ChildClientAddress objects filtered by the regon column
 * @method     array findByTaxId(resource $tax_id) Return ChildClientAddress objects filtered by the tax_id column
 * @method     array findByCity(resource $city) Return ChildClientAddress objects filtered by the city column
 * @method     array findByIsMain(int $main) Return ChildClientAddress objects filtered by the main column
 * @method     array findByCountryId(int $country_id) Return ChildClientAddress objects filtered by the country_id column
 * @method     array findByClientType(int $client_type) Return ChildClientAddress objects filtered by the client_type column
 *
 */
abstract class ClientAddressQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Client\Model\ORM\Base\ClientAddressQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientAddress', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildClientAddressQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildClientAddressQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery();
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
     * @return ChildClientAddress|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClientAddressTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ClientAddressTableMap::DATABASE_NAME);
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
     * @return   ChildClientAddress A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, STREET, STREET_NO, PLACE_NO, POST_CODE, COMPANY_NAME, FIRSTNAME, SURNAME, CLIENT_ID, REGON, TAX_ID, CITY, MAIN, COUNTRY_ID, CLIENT_TYPE FROM client_address WHERE ID = :p0';
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
            $obj = new ChildClientAddress();
            $obj->hydrate($row);
            ClientAddressTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildClientAddress|array|mixed the result, formatted by the current formatter
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
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientAddressTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the street column
     *
     * @param     mixed $street The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByStreet($street = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_STREET, $street, $comparison);
    }

    /**
     * Filter the query on the street_no column
     *
     * @param     mixed $streetNo The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByStreetNo($streetNo = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_STREET_NO, $streetNo, $comparison);
    }

    /**
     * Filter the query on the place_no column
     *
     * @param     mixed $placeNo The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByPlaceNo($placeNo = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_PLACE_NO, $placeNo, $comparison);
    }

    /**
     * Filter the query on the post_code column
     *
     * @param     mixed $postCode The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByPostCode($postCode = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_POST_CODE, $postCode, $comparison);
    }

    /**
     * Filter the query on the company_name column
     *
     * @param     mixed $companyName The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByCompanyName($companyName = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_COMPANY_NAME, $companyName, $comparison);
    }

    /**
     * Filter the query on the firstname column
     *
     * @param     mixed $firstname The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByFirstname($firstname = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_FIRSTNAME, $firstname, $comparison);
    }

    /**
     * Filter the query on the surname column
     *
     * @param     mixed $surname The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterBySurname($surname = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_SURNAME, $surname, $comparison);
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
     * @see       filterByClient()
     *
     * @param     mixed $clientId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByClientId($clientId = null, $comparison = null)
    {
        if (is_array($clientId)) {
            $useMinMax = false;
            if (isset($clientId['min'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_CLIENT_ID, $clientId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientId['max'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_CLIENT_ID, $clientId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientAddressTableMap::COL_CLIENT_ID, $clientId, $comparison);
    }

    /**
     * Filter the query on the regon column
     *
     * @param     mixed $regon The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByRegon($regon = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_REGON, $regon, $comparison);
    }

    /**
     * Filter the query on the tax_id column
     *
     * @param     mixed $taxId The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByTaxId($taxId = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_TAX_ID, $taxId, $comparison);
    }

    /**
     * Filter the query on the city column
     *
     * @param     mixed $city The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByCity($city = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientAddressTableMap::COL_CITY, $city, $comparison);
    }

    /**
     * Filter the query on the main column
     *
     * Example usage:
     * <code>
     * $query->filterByIsMain(1234); // WHERE main = 1234
     * $query->filterByIsMain(array(12, 34)); // WHERE main IN (12, 34)
     * $query->filterByIsMain(array('min' => 12)); // WHERE main > 12
     * </code>
     *
     * @param     mixed $isMain The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByIsMain($isMain = null, $comparison = null)
    {
        if (is_array($isMain)) {
            $useMinMax = false;
            if (isset($isMain['min'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_MAIN, $isMain['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isMain['max'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_MAIN, $isMain['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientAddressTableMap::COL_MAIN, $isMain, $comparison);
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
     * @see       filterByCountry()
     *
     * @param     mixed $countryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByCountryId($countryId = null, $comparison = null)
    {
        if (is_array($countryId)) {
            $useMinMax = false;
            if (isset($countryId['min'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_COUNTRY_ID, $countryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($countryId['max'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_COUNTRY_ID, $countryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientAddressTableMap::COL_COUNTRY_ID, $countryId, $comparison);
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
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByClientType($clientType = null, $comparison = null)
    {
        if (is_array($clientType)) {
            $useMinMax = false;
            if (isset($clientType['min'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_CLIENT_TYPE, $clientType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientType['max'])) {
                $this->addUsingAlias(ClientAddressTableMap::COL_CLIENT_TYPE, $clientType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientAddressTableMap::COL_CLIENT_TYPE, $clientType, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Client\Model\ORM\Client object
     *
     * @param \Gekosale\Plugin\Client\Model\ORM\Client|ObjectCollection $client The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByClient($client, $comparison = null)
    {
        if ($client instanceof \Gekosale\Plugin\Client\Model\ORM\Client) {
            return $this
                ->addUsingAlias(ClientAddressTableMap::COL_CLIENT_ID, $client->getId(), $comparison);
        } elseif ($client instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClientAddressTableMap::COL_CLIENT_ID, $client->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByClient() only accepts arguments of type \Gekosale\Plugin\Client\Model\ORM\Client or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Client relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function joinClient($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Client');

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
            $this->addJoinObject($join, 'Client');
        }

        return $this;
    }

    /**
     * Use the Client relation Client object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientQuery A secondary query class using the current class as primary query
     */
    public function useClientQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClient($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Client', '\Gekosale\Plugin\Client\Model\ORM\ClientQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Country\Model\ORM\Country object
     *
     * @param \Gekosale\Plugin\Country\Model\ORM\Country|ObjectCollection $country The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function filterByCountry($country, $comparison = null)
    {
        if ($country instanceof \Gekosale\Plugin\Country\Model\ORM\Country) {
            return $this
                ->addUsingAlias(ClientAddressTableMap::COL_COUNTRY_ID, $country->getId(), $comparison);
        } elseif ($country instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClientAddressTableMap::COL_COUNTRY_ID, $country->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCountry() only accepts arguments of type \Gekosale\Plugin\Country\Model\ORM\Country or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Country relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function joinCountry($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Country');

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
            $this->addJoinObject($join, 'Country');
        }

        return $this;
    }

    /**
     * Use the Country relation Country object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Country\Model\ORM\CountryQuery A secondary query class using the current class as primary query
     */
    public function useCountryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCountry($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Country', '\Gekosale\Plugin\Country\Model\ORM\CountryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildClientAddress $clientAddress Object to remove from the list of results
     *
     * @return ChildClientAddressQuery The current query, for fluid interface
     */
    public function prune($clientAddress = null)
    {
        if ($clientAddress) {
            $this->addUsingAlias(ClientAddressTableMap::COL_ID, $clientAddress->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the client_address table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientAddressTableMap::DATABASE_NAME);
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
            ClientAddressTableMap::clearInstancePool();
            ClientAddressTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildClientAddress or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildClientAddress object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientAddressTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ClientAddressTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ClientAddressTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ClientAddressTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ClientAddressQuery
