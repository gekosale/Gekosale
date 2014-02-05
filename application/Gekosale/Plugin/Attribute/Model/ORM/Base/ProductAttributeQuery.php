<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute as ChildProductAttribute;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery as ChildProductAttributeQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Map\ProductAttributeTableMap;
use Gekosale\Plugin\Availability\Model\ORM\Availability;
use Gekosale\Plugin\File\Model\ORM\File;
use Gekosale\Plugin\Product\Model\ORM\Product;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'product_attribute' table.
 *
 * 
 *
 * @method     ChildProductAttributeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProductAttributeQuery orderByProductId($order = Criteria::ASC) Order by the product_id column
 * @method     ChildProductAttributeQuery orderBySuffixTypeId($order = Criteria::ASC) Order by the suffix_type_id column
 * @method     ChildProductAttributeQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method     ChildProductAttributeQuery orderByAddDate($order = Criteria::ASC) Order by the add_date column
 * @method     ChildProductAttributeQuery orderByStock($order = Criteria::ASC) Order by the stock column
 * @method     ChildProductAttributeQuery orderByAttributeGroupNameId($order = Criteria::ASC) Order by the attribute_group_name_id column
 * @method     ChildProductAttributeQuery orderByAttributePrice($order = Criteria::ASC) Order by the attribute_price column
 * @method     ChildProductAttributeQuery orderByDiscountPrice($order = Criteria::ASC) Order by the discount_price column
 * @method     ChildProductAttributeQuery orderBySymbol($order = Criteria::ASC) Order by the symbol column
 * @method     ChildProductAttributeQuery orderByWeight($order = Criteria::ASC) Order by the weight column
 * @method     ChildProductAttributeQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     ChildProductAttributeQuery orderByAvailabilityId($order = Criteria::ASC) Order by the availability_id column
 * @method     ChildProductAttributeQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method     ChildProductAttributeQuery orderByFirmesId($order = Criteria::ASC) Order by the firmes_id column
 *
 * @method     ChildProductAttributeQuery groupById() Group by the id column
 * @method     ChildProductAttributeQuery groupByProductId() Group by the product_id column
 * @method     ChildProductAttributeQuery groupBySuffixTypeId() Group by the suffix_type_id column
 * @method     ChildProductAttributeQuery groupByValue() Group by the value column
 * @method     ChildProductAttributeQuery groupByAddDate() Group by the add_date column
 * @method     ChildProductAttributeQuery groupByStock() Group by the stock column
 * @method     ChildProductAttributeQuery groupByAttributeGroupNameId() Group by the attribute_group_name_id column
 * @method     ChildProductAttributeQuery groupByAttributePrice() Group by the attribute_price column
 * @method     ChildProductAttributeQuery groupByDiscountPrice() Group by the discount_price column
 * @method     ChildProductAttributeQuery groupBySymbol() Group by the symbol column
 * @method     ChildProductAttributeQuery groupByWeight() Group by the weight column
 * @method     ChildProductAttributeQuery groupByStatus() Group by the status column
 * @method     ChildProductAttributeQuery groupByAvailabilityId() Group by the availability_id column
 * @method     ChildProductAttributeQuery groupByPhotoId() Group by the photo_id column
 * @method     ChildProductAttributeQuery groupByFirmesId() Group by the firmes_id column
 *
 * @method     ChildProductAttributeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProductAttributeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProductAttributeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProductAttributeQuery leftJoinAttributeGroupName($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeGroupName relation
 * @method     ChildProductAttributeQuery rightJoinAttributeGroupName($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeGroupName relation
 * @method     ChildProductAttributeQuery innerJoinAttributeGroupName($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeGroupName relation
 *
 * @method     ChildProductAttributeQuery leftJoinAvailability($relationAlias = null) Adds a LEFT JOIN clause to the query using the Availability relation
 * @method     ChildProductAttributeQuery rightJoinAvailability($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Availability relation
 * @method     ChildProductAttributeQuery innerJoinAvailability($relationAlias = null) Adds a INNER JOIN clause to the query using the Availability relation
 *
 * @method     ChildProductAttributeQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method     ChildProductAttributeQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method     ChildProductAttributeQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method     ChildProductAttributeQuery leftJoinProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the Product relation
 * @method     ChildProductAttributeQuery rightJoinProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Product relation
 * @method     ChildProductAttributeQuery innerJoinProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the Product relation
 *
 * @method     ChildProductAttributeQuery leftJoinProductAttributeValueSet($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAttributeValueSet relation
 * @method     ChildProductAttributeQuery rightJoinProductAttributeValueSet($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAttributeValueSet relation
 * @method     ChildProductAttributeQuery innerJoinProductAttributeValueSet($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAttributeValueSet relation
 *
 * @method     ChildProductAttribute findOne(ConnectionInterface $con = null) Return the first ChildProductAttribute matching the query
 * @method     ChildProductAttribute findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProductAttribute matching the query, or a new ChildProductAttribute object populated from the query conditions when no match is found
 *
 * @method     ChildProductAttribute findOneById(int $id) Return the first ChildProductAttribute filtered by the id column
 * @method     ChildProductAttribute findOneByProductId(int $product_id) Return the first ChildProductAttribute filtered by the product_id column
 * @method     ChildProductAttribute findOneBySuffixTypeId(int $suffix_type_id) Return the first ChildProductAttribute filtered by the suffix_type_id column
 * @method     ChildProductAttribute findOneByValue(string $value) Return the first ChildProductAttribute filtered by the value column
 * @method     ChildProductAttribute findOneByAddDate(string $add_date) Return the first ChildProductAttribute filtered by the add_date column
 * @method     ChildProductAttribute findOneByStock(int $stock) Return the first ChildProductAttribute filtered by the stock column
 * @method     ChildProductAttribute findOneByAttributeGroupNameId(int $attribute_group_name_id) Return the first ChildProductAttribute filtered by the attribute_group_name_id column
 * @method     ChildProductAttribute findOneByAttributePrice(string $attribute_price) Return the first ChildProductAttribute filtered by the attribute_price column
 * @method     ChildProductAttribute findOneByDiscountPrice(string $discount_price) Return the first ChildProductAttribute filtered by the discount_price column
 * @method     ChildProductAttribute findOneBySymbol(string $symbol) Return the first ChildProductAttribute filtered by the symbol column
 * @method     ChildProductAttribute findOneByWeight(string $weight) Return the first ChildProductAttribute filtered by the weight column
 * @method     ChildProductAttribute findOneByStatus(boolean $status) Return the first ChildProductAttribute filtered by the status column
 * @method     ChildProductAttribute findOneByAvailabilityId(int $availability_id) Return the first ChildProductAttribute filtered by the availability_id column
 * @method     ChildProductAttribute findOneByPhotoId(int $photo_id) Return the first ChildProductAttribute filtered by the photo_id column
 * @method     ChildProductAttribute findOneByFirmesId(int $firmes_id) Return the first ChildProductAttribute filtered by the firmes_id column
 *
 * @method     array findById(int $id) Return ChildProductAttribute objects filtered by the id column
 * @method     array findByProductId(int $product_id) Return ChildProductAttribute objects filtered by the product_id column
 * @method     array findBySuffixTypeId(int $suffix_type_id) Return ChildProductAttribute objects filtered by the suffix_type_id column
 * @method     array findByValue(string $value) Return ChildProductAttribute objects filtered by the value column
 * @method     array findByAddDate(string $add_date) Return ChildProductAttribute objects filtered by the add_date column
 * @method     array findByStock(int $stock) Return ChildProductAttribute objects filtered by the stock column
 * @method     array findByAttributeGroupNameId(int $attribute_group_name_id) Return ChildProductAttribute objects filtered by the attribute_group_name_id column
 * @method     array findByAttributePrice(string $attribute_price) Return ChildProductAttribute objects filtered by the attribute_price column
 * @method     array findByDiscountPrice(string $discount_price) Return ChildProductAttribute objects filtered by the discount_price column
 * @method     array findBySymbol(string $symbol) Return ChildProductAttribute objects filtered by the symbol column
 * @method     array findByWeight(string $weight) Return ChildProductAttribute objects filtered by the weight column
 * @method     array findByStatus(boolean $status) Return ChildProductAttribute objects filtered by the status column
 * @method     array findByAvailabilityId(int $availability_id) Return ChildProductAttribute objects filtered by the availability_id column
 * @method     array findByPhotoId(int $photo_id) Return ChildProductAttribute objects filtered by the photo_id column
 * @method     array findByFirmesId(int $firmes_id) Return ChildProductAttribute objects filtered by the firmes_id column
 *
 */
abstract class ProductAttributeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Attribute\Model\ORM\Base\ProductAttributeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttribute', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProductAttributeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProductAttributeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery();
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
     * @return ChildProductAttribute|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductAttributeTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductAttributeTableMap::DATABASE_NAME);
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
     * @return   ChildProductAttribute A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PRODUCT_ID, SUFFIX_TYPE_ID, VALUE, ADD_DATE, STOCK, ATTRIBUTE_GROUP_NAME_ID, ATTRIBUTE_PRICE, DISCOUNT_PRICE, SYMBOL, WEIGHT, STATUS, AVAILABILITY_ID, PHOTO_ID, FIRMES_ID FROM product_attribute WHERE ID = :p0';
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
            $obj = new ChildProductAttribute();
            $obj->hydrate($row);
            ProductAttributeTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProductAttribute|array|mixed the result, formatted by the current formatter
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
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProductAttributeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProductAttributeTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the product_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductId(1234); // WHERE product_id = 1234
     * $query->filterByProductId(array(12, 34)); // WHERE product_id IN (12, 34)
     * $query->filterByProductId(array('min' => 12)); // WHERE product_id > 12
     * </code>
     *
     * @see       filterByProduct()
     *
     * @param     mixed $productId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByProductId($productId = null, $comparison = null)
    {
        if (is_array($productId)) {
            $useMinMax = false;
            if (isset($productId['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_PRODUCT_ID, $productId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productId['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_PRODUCT_ID, $productId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_PRODUCT_ID, $productId, $comparison);
    }

    /**
     * Filter the query on the suffix_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySuffixTypeId(1234); // WHERE suffix_type_id = 1234
     * $query->filterBySuffixTypeId(array(12, 34)); // WHERE suffix_type_id IN (12, 34)
     * $query->filterBySuffixTypeId(array('min' => 12)); // WHERE suffix_type_id > 12
     * </code>
     *
     * @param     mixed $suffixTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterBySuffixTypeId($suffixTypeId = null, $comparison = null)
    {
        if (is_array($suffixTypeId)) {
            $useMinMax = false;
            if (isset($suffixTypeId['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($suffixTypeId['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue(1234); // WHERE value = 1234
     * $query->filterByValue(array(12, 34)); // WHERE value IN (12, 34)
     * $query->filterByValue(array('min' => 12)); // WHERE value > 12
     * </code>
     *
     * @param     mixed $value The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (is_array($value)) {
            $useMinMax = false;
            if (isset($value['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_VALUE, $value['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($value['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_VALUE, $value['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_VALUE, $value, $comparison);
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
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByAddDate($addDate = null, $comparison = null)
    {
        if (is_array($addDate)) {
            $useMinMax = false;
            if (isset($addDate['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ADD_DATE, $addDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($addDate['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ADD_DATE, $addDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_ADD_DATE, $addDate, $comparison);
    }

    /**
     * Filter the query on the stock column
     *
     * Example usage:
     * <code>
     * $query->filterByStock(1234); // WHERE stock = 1234
     * $query->filterByStock(array(12, 34)); // WHERE stock IN (12, 34)
     * $query->filterByStock(array('min' => 12)); // WHERE stock > 12
     * </code>
     *
     * @param     mixed $stock The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByStock($stock = null, $comparison = null)
    {
        if (is_array($stock)) {
            $useMinMax = false;
            if (isset($stock['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_STOCK, $stock['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($stock['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_STOCK, $stock['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_STOCK, $stock, $comparison);
    }

    /**
     * Filter the query on the attribute_group_name_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAttributeGroupNameId(1234); // WHERE attribute_group_name_id = 1234
     * $query->filterByAttributeGroupNameId(array(12, 34)); // WHERE attribute_group_name_id IN (12, 34)
     * $query->filterByAttributeGroupNameId(array('min' => 12)); // WHERE attribute_group_name_id > 12
     * </code>
     *
     * @see       filterByAttributeGroupName()
     *
     * @param     mixed $attributeGroupNameId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByAttributeGroupNameId($attributeGroupNameId = null, $comparison = null)
    {
        if (is_array($attributeGroupNameId)) {
            $useMinMax = false;
            if (isset($attributeGroupNameId['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupNameId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attributeGroupNameId['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupNameId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupNameId, $comparison);
    }

    /**
     * Filter the query on the attribute_price column
     *
     * Example usage:
     * <code>
     * $query->filterByAttributePrice(1234); // WHERE attribute_price = 1234
     * $query->filterByAttributePrice(array(12, 34)); // WHERE attribute_price IN (12, 34)
     * $query->filterByAttributePrice(array('min' => 12)); // WHERE attribute_price > 12
     * </code>
     *
     * @param     mixed $attributePrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByAttributePrice($attributePrice = null, $comparison = null)
    {
        if (is_array($attributePrice)) {
            $useMinMax = false;
            if (isset($attributePrice['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_PRICE, $attributePrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attributePrice['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_PRICE, $attributePrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_PRICE, $attributePrice, $comparison);
    }

    /**
     * Filter the query on the discount_price column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscountPrice(1234); // WHERE discount_price = 1234
     * $query->filterByDiscountPrice(array(12, 34)); // WHERE discount_price IN (12, 34)
     * $query->filterByDiscountPrice(array('min' => 12)); // WHERE discount_price > 12
     * </code>
     *
     * @param     mixed $discountPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByDiscountPrice($discountPrice = null, $comparison = null)
    {
        if (is_array($discountPrice)) {
            $useMinMax = false;
            if (isset($discountPrice['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_DISCOUNT_PRICE, $discountPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountPrice['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_DISCOUNT_PRICE, $discountPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_DISCOUNT_PRICE, $discountPrice, $comparison);
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
     * @return ChildProductAttributeQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ProductAttributeTableMap::COL_SYMBOL, $symbol, $comparison);
    }

    /**
     * Filter the query on the weight column
     *
     * Example usage:
     * <code>
     * $query->filterByWeight(1234); // WHERE weight = 1234
     * $query->filterByWeight(array(12, 34)); // WHERE weight IN (12, 34)
     * $query->filterByWeight(array('min' => 12)); // WHERE weight > 12
     * </code>
     *
     * @param     mixed $weight The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByWeight($weight = null, $comparison = null)
    {
        if (is_array($weight)) {
            $useMinMax = false;
            if (isset($weight['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_WEIGHT, $weight['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($weight['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_WEIGHT, $weight['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_WEIGHT, $weight, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus(true); // WHERE status = true
     * $query->filterByStatus('yes'); // WHERE status = true
     * </code>
     *
     * @param     boolean|string $status The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (is_string($status)) {
            $status = in_array(strtolower($status), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_STATUS, $status, $comparison);
    }

    /**
     * Filter the query on the availability_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAvailabilityId(1234); // WHERE availability_id = 1234
     * $query->filterByAvailabilityId(array(12, 34)); // WHERE availability_id IN (12, 34)
     * $query->filterByAvailabilityId(array('min' => 12)); // WHERE availability_id > 12
     * </code>
     *
     * @see       filterByAvailability()
     *
     * @param     mixed $availabilityId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByAvailabilityId($availabilityId = null, $comparison = null)
    {
        if (is_array($availabilityId)) {
            $useMinMax = false;
            if (isset($availabilityId['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_AVAILABILITY_ID, $availabilityId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($availabilityId['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_AVAILABILITY_ID, $availabilityId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_AVAILABILITY_ID, $availabilityId, $comparison);
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
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query on the firmes_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFirmesId(1234); // WHERE firmes_id = 1234
     * $query->filterByFirmesId(array(12, 34)); // WHERE firmes_id IN (12, 34)
     * $query->filterByFirmesId(array('min' => 12)); // WHERE firmes_id > 12
     * </code>
     *
     * @param     mixed $firmesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByFirmesId($firmesId = null, $comparison = null)
    {
        if (is_array($firmesId)) {
            $useMinMax = false;
            if (isset($firmesId['min'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_FIRMES_ID, $firmesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($firmesId['max'])) {
                $this->addUsingAlias(ProductAttributeTableMap::COL_FIRMES_ID, $firmesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductAttributeTableMap::COL_FIRMES_ID, $firmesId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName|ObjectCollection $attributeGroupName The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByAttributeGroupName($attributeGroupName, $comparison = null)
    {
        if ($attributeGroupName instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName) {
            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupName->getId(), $comparison);
        } elseif ($attributeGroupName instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupName->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAttributeGroupName() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeGroupName relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function joinAttributeGroupName($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeGroupName');

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
            $this->addJoinObject($join, 'AttributeGroupName');
        }

        return $this;
    }

    /**
     * Use the AttributeGroupName relation AttributeGroupName object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery A secondary query class using the current class as primary query
     */
    public function useAttributeGroupNameQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAttributeGroupName($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeGroupName', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Availability\Model\ORM\Availability object
     *
     * @param \Gekosale\Plugin\Availability\Model\ORM\Availability|ObjectCollection $availability The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByAvailability($availability, $comparison = null)
    {
        if ($availability instanceof \Gekosale\Plugin\Availability\Model\ORM\Availability) {
            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_AVAILABILITY_ID, $availability->getId(), $comparison);
        } elseif ($availability instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_AVAILABILITY_ID, $availability->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAvailability() only accepts arguments of type \Gekosale\Plugin\Availability\Model\ORM\Availability or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Availability relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function joinAvailability($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Availability');

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
            $this->addJoinObject($join, 'Availability');
        }

        return $this;
    }

    /**
     * Use the Availability relation Availability object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Availability\Model\ORM\AvailabilityQuery A secondary query class using the current class as primary query
     */
    public function useAvailabilityQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAvailability($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Availability', '\Gekosale\Plugin\Availability\Model\ORM\AvailabilityQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\File\Model\ORM\File object
     *
     * @param \Gekosale\Plugin\File\Model\ORM\File|ObjectCollection $file The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof \Gekosale\Plugin\File\Model\ORM\File) {
            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_PHOTO_ID, $file->getId(), $comparison);
        } elseif ($file instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_PHOTO_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildProductAttributeQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\Product object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\Product|ObjectCollection $product The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByProduct($product, $comparison = null)
    {
        if ($product instanceof \Gekosale\Plugin\Product\Model\ORM\Product) {
            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_PRODUCT_ID, $product->getId(), $comparison);
        } elseif ($product instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_PRODUCT_ID, $product->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProduct() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\Product or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Product relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function joinProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Product');

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
            $this->addJoinObject($join, 'Product');
        }

        return $this;
    }

    /**
     * Use the Product relation Product object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductQuery A secondary query class using the current class as primary query
     */
    public function useProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Product', '\Gekosale\Plugin\Product\Model\ORM\ProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet|ObjectCollection $productAttributeValueSet  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function filterByProductAttributeValueSet($productAttributeValueSet, $comparison = null)
    {
        if ($productAttributeValueSet instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet) {
            return $this
                ->addUsingAlias(ProductAttributeTableMap::COL_ID, $productAttributeValueSet->getProductAttributeId(), $comparison);
        } elseif ($productAttributeValueSet instanceof ObjectCollection) {
            return $this
                ->useProductAttributeValueSetQuery()
                ->filterByPrimaryKeys($productAttributeValueSet->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductAttributeValueSet() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSet or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductAttributeValueSet relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function joinProductAttributeValueSet($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductAttributeValueSet');

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
            $this->addJoinObject($join, 'ProductAttributeValueSet');
        }

        return $this;
    }

    /**
     * Use the ProductAttributeValueSet relation ProductAttributeValueSet object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery A secondary query class using the current class as primary query
     */
    public function useProductAttributeValueSetQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductAttributeValueSet($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAttributeValueSet', '\Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeValueSetQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProductAttribute $productAttribute Object to remove from the list of results
     *
     * @return ChildProductAttributeQuery The current query, for fluid interface
     */
    public function prune($productAttribute = null)
    {
        if ($productAttribute) {
            $this->addUsingAlias(ProductAttributeTableMap::COL_ID, $productAttribute->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the product_attribute table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductAttributeTableMap::DATABASE_NAME);
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
            ProductAttributeTableMap::clearInstancePool();
            ProductAttributeTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProductAttribute or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProductAttribute object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductAttributeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProductAttributeTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProductAttributeTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProductAttributeTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ProductAttributeQuery
