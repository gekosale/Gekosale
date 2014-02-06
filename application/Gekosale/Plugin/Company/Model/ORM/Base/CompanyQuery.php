<?php

namespace Gekosale\Plugin\Company\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Company\Model\ORM\Company as ChildCompany;
use Gekosale\Plugin\Company\Model\ORM\CompanyQuery as ChildCompanyQuery;
use Gekosale\Plugin\Company\Model\ORM\Map\CompanyTableMap;
use Gekosale\Plugin\Controller\Model\ORM\ControllerPermission;
use Gekosale\Plugin\Country\Model\ORM\Country;
use Gekosale\Plugin\File\Model\ORM\File;
use Gekosale\Plugin\Shop\Model\ORM\Shop;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'company' table.
 *
 * 
 *
 * @method     ChildCompanyQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCompanyQuery orderByCountryId($order = Criteria::ASC) Order by the country_id column
 * @method     ChildCompanyQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method     ChildCompanyQuery orderByBankName($order = Criteria::ASC) Order by the bank_name column
 * @method     ChildCompanyQuery orderByBankAccountNo($order = Criteria::ASC) Order by the bank_account_no column
 * @method     ChildCompanyQuery orderByTaxId($order = Criteria::ASC) Order by the tax_id column
 * @method     ChildCompanyQuery orderByCompanyName($order = Criteria::ASC) Order by the company_name column
 * @method     ChildCompanyQuery orderByShortCompanyName($order = Criteria::ASC) Order by the short_company_name column
 * @method     ChildCompanyQuery orderByPostCode($order = Criteria::ASC) Order by the post_code column
 * @method     ChildCompanyQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method     ChildCompanyQuery orderByStreet($order = Criteria::ASC) Order by the street column
 * @method     ChildCompanyQuery orderByStreetNo($order = Criteria::ASC) Order by the street_no column
 * @method     ChildCompanyQuery orderByPlaceNo($order = Criteria::ASC) Order by the place_no column
 * @method     ChildCompanyQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCompanyQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCompanyQuery groupById() Group by the id column
 * @method     ChildCompanyQuery groupByCountryId() Group by the country_id column
 * @method     ChildCompanyQuery groupByPhotoId() Group by the photo_id column
 * @method     ChildCompanyQuery groupByBankName() Group by the bank_name column
 * @method     ChildCompanyQuery groupByBankAccountNo() Group by the bank_account_no column
 * @method     ChildCompanyQuery groupByTaxId() Group by the tax_id column
 * @method     ChildCompanyQuery groupByCompanyName() Group by the company_name column
 * @method     ChildCompanyQuery groupByShortCompanyName() Group by the short_company_name column
 * @method     ChildCompanyQuery groupByPostCode() Group by the post_code column
 * @method     ChildCompanyQuery groupByCity() Group by the city column
 * @method     ChildCompanyQuery groupByStreet() Group by the street column
 * @method     ChildCompanyQuery groupByStreetNo() Group by the street_no column
 * @method     ChildCompanyQuery groupByPlaceNo() Group by the place_no column
 * @method     ChildCompanyQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCompanyQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCompanyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCompanyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCompanyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCompanyQuery leftJoinCountry($relationAlias = null) Adds a LEFT JOIN clause to the query using the Country relation
 * @method     ChildCompanyQuery rightJoinCountry($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Country relation
 * @method     ChildCompanyQuery innerJoinCountry($relationAlias = null) Adds a INNER JOIN clause to the query using the Country relation
 *
 * @method     ChildCompanyQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method     ChildCompanyQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method     ChildCompanyQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method     ChildCompanyQuery leftJoinControllerPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the ControllerPermission relation
 * @method     ChildCompanyQuery rightJoinControllerPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ControllerPermission relation
 * @method     ChildCompanyQuery innerJoinControllerPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the ControllerPermission relation
 *
 * @method     ChildCompanyQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildCompanyQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildCompanyQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildCompany findOne(ConnectionInterface $con = null) Return the first ChildCompany matching the query
 * @method     ChildCompany findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCompany matching the query, or a new ChildCompany object populated from the query conditions when no match is found
 *
 * @method     ChildCompany findOneById(int $id) Return the first ChildCompany filtered by the id column
 * @method     ChildCompany findOneByCountryId(int $country_id) Return the first ChildCompany filtered by the country_id column
 * @method     ChildCompany findOneByPhotoId(int $photo_id) Return the first ChildCompany filtered by the photo_id column
 * @method     ChildCompany findOneByBankName(string $bank_name) Return the first ChildCompany filtered by the bank_name column
 * @method     ChildCompany findOneByBankAccountNo(string $bank_account_no) Return the first ChildCompany filtered by the bank_account_no column
 * @method     ChildCompany findOneByTaxId(string $tax_id) Return the first ChildCompany filtered by the tax_id column
 * @method     ChildCompany findOneByCompanyName(string $company_name) Return the first ChildCompany filtered by the company_name column
 * @method     ChildCompany findOneByShortCompanyName(string $short_company_name) Return the first ChildCompany filtered by the short_company_name column
 * @method     ChildCompany findOneByPostCode(string $post_code) Return the first ChildCompany filtered by the post_code column
 * @method     ChildCompany findOneByCity(string $city) Return the first ChildCompany filtered by the city column
 * @method     ChildCompany findOneByStreet(string $street) Return the first ChildCompany filtered by the street column
 * @method     ChildCompany findOneByStreetNo(string $street_no) Return the first ChildCompany filtered by the street_no column
 * @method     ChildCompany findOneByPlaceNo(string $place_no) Return the first ChildCompany filtered by the place_no column
 * @method     ChildCompany findOneByCreatedAt(string $created_at) Return the first ChildCompany filtered by the created_at column
 * @method     ChildCompany findOneByUpdatedAt(string $updated_at) Return the first ChildCompany filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildCompany objects filtered by the id column
 * @method     array findByCountryId(int $country_id) Return ChildCompany objects filtered by the country_id column
 * @method     array findByPhotoId(int $photo_id) Return ChildCompany objects filtered by the photo_id column
 * @method     array findByBankName(string $bank_name) Return ChildCompany objects filtered by the bank_name column
 * @method     array findByBankAccountNo(string $bank_account_no) Return ChildCompany objects filtered by the bank_account_no column
 * @method     array findByTaxId(string $tax_id) Return ChildCompany objects filtered by the tax_id column
 * @method     array findByCompanyName(string $company_name) Return ChildCompany objects filtered by the company_name column
 * @method     array findByShortCompanyName(string $short_company_name) Return ChildCompany objects filtered by the short_company_name column
 * @method     array findByPostCode(string $post_code) Return ChildCompany objects filtered by the post_code column
 * @method     array findByCity(string $city) Return ChildCompany objects filtered by the city column
 * @method     array findByStreet(string $street) Return ChildCompany objects filtered by the street column
 * @method     array findByStreetNo(string $street_no) Return ChildCompany objects filtered by the street_no column
 * @method     array findByPlaceNo(string $place_no) Return ChildCompany objects filtered by the place_no column
 * @method     array findByCreatedAt(string $created_at) Return ChildCompany objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildCompany objects filtered by the updated_at column
 *
 */
abstract class CompanyQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Company\Model\ORM\Base\CompanyQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Company\\Model\\ORM\\Company', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCompanyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCompanyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Company\Model\ORM\CompanyQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Company\Model\ORM\CompanyQuery();
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
     * @return ChildCompany|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CompanyTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CompanyTableMap::DATABASE_NAME);
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
     * @return   ChildCompany A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, COUNTRY_ID, PHOTO_ID, BANK_NAME, BANK_ACCOUNT_NO, TAX_ID, COMPANY_NAME, SHORT_COMPANY_NAME, POST_CODE, CITY, STREET, STREET_NO, PLACE_NO, CREATED_AT, UPDATED_AT FROM company WHERE ID = :p0';
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
            $obj = new ChildCompany();
            $obj->hydrate($row);
            CompanyTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCompany|array|mixed the result, formatted by the current formatter
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
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CompanyTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CompanyTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CompanyTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CompanyTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_ID, $id, $comparison);
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
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByCountryId($countryId = null, $comparison = null)
    {
        if (is_array($countryId)) {
            $useMinMax = false;
            if (isset($countryId['min'])) {
                $this->addUsingAlias(CompanyTableMap::COL_COUNTRY_ID, $countryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($countryId['max'])) {
                $this->addUsingAlias(CompanyTableMap::COL_COUNTRY_ID, $countryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_COUNTRY_ID, $countryId, $comparison);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId(1234); // WHERE photo_id = 1234
     * $query->filterByPhotoId(array(12, 34)); // WHERE photo_id IN (12, 34)
     * $query->filterByPhotoId(array('min' => 12)); // WHERE photo_id > 12
     * </code>
     *
     * @see       filterByFile()
     *
     * @param     mixed $photoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(CompanyTableMap::COL_PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(CompanyTableMap::COL_PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query on the bank_name column
     *
     * Example usage:
     * <code>
     * $query->filterByBankName('fooValue');   // WHERE bank_name = 'fooValue'
     * $query->filterByBankName('%fooValue%'); // WHERE bank_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $bankName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByBankName($bankName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($bankName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $bankName)) {
                $bankName = str_replace('*', '%', $bankName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_BANK_NAME, $bankName, $comparison);
    }

    /**
     * Filter the query on the bank_account_no column
     *
     * Example usage:
     * <code>
     * $query->filterByBankAccountNo('fooValue');   // WHERE bank_account_no = 'fooValue'
     * $query->filterByBankAccountNo('%fooValue%'); // WHERE bank_account_no LIKE '%fooValue%'
     * </code>
     *
     * @param     string $bankAccountNo The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByBankAccountNo($bankAccountNo = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($bankAccountNo)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $bankAccountNo)) {
                $bankAccountNo = str_replace('*', '%', $bankAccountNo);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_BANK_ACCOUNT_NO, $bankAccountNo, $comparison);
    }

    /**
     * Filter the query on the tax_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTaxId('fooValue');   // WHERE tax_id = 'fooValue'
     * $query->filterByTaxId('%fooValue%'); // WHERE tax_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $taxId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByTaxId($taxId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($taxId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $taxId)) {
                $taxId = str_replace('*', '%', $taxId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_TAX_ID, $taxId, $comparison);
    }

    /**
     * Filter the query on the company_name column
     *
     * Example usage:
     * <code>
     * $query->filterByCompanyName('fooValue');   // WHERE company_name = 'fooValue'
     * $query->filterByCompanyName('%fooValue%'); // WHERE company_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $companyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByCompanyName($companyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($companyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $companyName)) {
                $companyName = str_replace('*', '%', $companyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_COMPANY_NAME, $companyName, $comparison);
    }

    /**
     * Filter the query on the short_company_name column
     *
     * Example usage:
     * <code>
     * $query->filterByShortCompanyName('fooValue');   // WHERE short_company_name = 'fooValue'
     * $query->filterByShortCompanyName('%fooValue%'); // WHERE short_company_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $shortCompanyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByShortCompanyName($shortCompanyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($shortCompanyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $shortCompanyName)) {
                $shortCompanyName = str_replace('*', '%', $shortCompanyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_SHORT_COMPANY_NAME, $shortCompanyName, $comparison);
    }

    /**
     * Filter the query on the post_code column
     *
     * Example usage:
     * <code>
     * $query->filterByPostCode('fooValue');   // WHERE post_code = 'fooValue'
     * $query->filterByPostCode('%fooValue%'); // WHERE post_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $postCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByPostCode($postCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($postCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $postCode)) {
                $postCode = str_replace('*', '%', $postCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_POST_CODE, $postCode, $comparison);
    }

    /**
     * Filter the query on the city column
     *
     * Example usage:
     * <code>
     * $query->filterByCity('fooValue');   // WHERE city = 'fooValue'
     * $query->filterByCity('%fooValue%'); // WHERE city LIKE '%fooValue%'
     * </code>
     *
     * @param     string $city The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByCity($city = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($city)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $city)) {
                $city = str_replace('*', '%', $city);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_CITY, $city, $comparison);
    }

    /**
     * Filter the query on the street column
     *
     * Example usage:
     * <code>
     * $query->filterByStreet('fooValue');   // WHERE street = 'fooValue'
     * $query->filterByStreet('%fooValue%'); // WHERE street LIKE '%fooValue%'
     * </code>
     *
     * @param     string $street The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByStreet($street = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($street)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $street)) {
                $street = str_replace('*', '%', $street);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_STREET, $street, $comparison);
    }

    /**
     * Filter the query on the street_no column
     *
     * Example usage:
     * <code>
     * $query->filterByStreetNo('fooValue');   // WHERE street_no = 'fooValue'
     * $query->filterByStreetNo('%fooValue%'); // WHERE street_no LIKE '%fooValue%'
     * </code>
     *
     * @param     string $streetNo The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByStreetNo($streetNo = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($streetNo)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $streetNo)) {
                $streetNo = str_replace('*', '%', $streetNo);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_STREET_NO, $streetNo, $comparison);
    }

    /**
     * Filter the query on the place_no column
     *
     * Example usage:
     * <code>
     * $query->filterByPlaceNo('fooValue');   // WHERE place_no = 'fooValue'
     * $query->filterByPlaceNo('%fooValue%'); // WHERE place_no LIKE '%fooValue%'
     * </code>
     *
     * @param     string $placeNo The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByPlaceNo($placeNo = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($placeNo)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $placeNo)) {
                $placeNo = str_replace('*', '%', $placeNo);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_PLACE_NO, $placeNo, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CompanyTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CompanyTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CompanyTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CompanyTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CompanyTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Country\Model\ORM\Country object
     *
     * @param \Gekosale\Plugin\Country\Model\ORM\Country|ObjectCollection $country The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByCountry($country, $comparison = null)
    {
        if ($country instanceof \Gekosale\Plugin\Country\Model\ORM\Country) {
            return $this
                ->addUsingAlias(CompanyTableMap::COL_COUNTRY_ID, $country->getId(), $comparison);
        } elseif ($country instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CompanyTableMap::COL_COUNTRY_ID, $country->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCompanyQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\File\Model\ORM\File object
     *
     * @param \Gekosale\Plugin\File\Model\ORM\File|ObjectCollection $file The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof \Gekosale\Plugin\File\Model\ORM\File) {
            return $this
                ->addUsingAlias(CompanyTableMap::COL_PHOTO_ID, $file->getId(), $comparison);
        } elseif ($file instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CompanyTableMap::COL_PHOTO_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFile() only accepts arguments of type \Gekosale\Plugin\File\Model\ORM\File or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the File relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function joinFile($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('File');

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
            $this->addJoinObject($join, 'File');
        }

        return $this;
    }

    /**
     * Use the File relation File object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\File\Model\ORM\FileQuery A secondary query class using the current class as primary query
     */
    public function useFileQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'File', '\Gekosale\Plugin\File\Model\ORM\FileQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission object
     *
     * @param \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission|ObjectCollection $controllerPermission  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByControllerPermission($controllerPermission, $comparison = null)
    {
        if ($controllerPermission instanceof \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission) {
            return $this
                ->addUsingAlias(CompanyTableMap::COL_ID, $controllerPermission->getCompanyId(), $comparison);
        } elseif ($controllerPermission instanceof ObjectCollection) {
            return $this
                ->useControllerPermissionQuery()
                ->filterByPrimaryKeys($controllerPermission->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByControllerPermission() only accepts arguments of type \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ControllerPermission relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function joinControllerPermission($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ControllerPermission');

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
            $this->addJoinObject($join, 'ControllerPermission');
        }

        return $this;
    }

    /**
     * Use the ControllerPermission relation ControllerPermission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery A secondary query class using the current class as primary query
     */
    public function useControllerPermissionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinControllerPermission($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ControllerPermission', '\Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(CompanyTableMap::COL_ID, $shop->getCompanyId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            return $this
                ->useShopQuery()
                ->filterByPrimaryKeys($shop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByShop() only accepts arguments of type \Gekosale\Plugin\Shop\Model\ORM\Shop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Shop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function joinShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Shop');

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
            $this->addJoinObject($join, 'Shop');
        }

        return $this;
    }

    /**
     * Use the Shop relation Shop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopQuery A secondary query class using the current class as primary query
     */
    public function useShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Shop', '\Gekosale\Plugin\Shop\Model\ORM\ShopQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCompany $company Object to remove from the list of results
     *
     * @return ChildCompanyQuery The current query, for fluid interface
     */
    public function prune($company = null)
    {
        if ($company) {
            $this->addUsingAlias(CompanyTableMap::COL_ID, $company->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the company table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CompanyTableMap::DATABASE_NAME);
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
            CompanyTableMap::clearInstancePool();
            CompanyTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCompany or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCompany object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CompanyTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CompanyTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CompanyTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CompanyTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildCompanyQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CompanyTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildCompanyQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CompanyTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildCompanyQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CompanyTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildCompanyQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CompanyTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildCompanyQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CompanyTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildCompanyQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CompanyTableMap::COL_CREATED_AT);
    }

} // CompanyQuery
