<?php

namespace Gekosale\Plugin\Invoice\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Invoice\Model\ORM\Invoice as ChildInvoice;
use Gekosale\Plugin\Invoice\Model\ORM\InvoiceQuery as ChildInvoiceQuery;
use Gekosale\Plugin\Invoice\Model\ORM\Map\InvoiceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'invoice' table.
 *
 * 
 *
 * @method     ChildInvoiceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildInvoiceQuery orderBySymbol($order = Criteria::ASC) Order by the symbol column
 * @method     ChildInvoiceQuery orderByInvoiceDate($order = Criteria::ASC) Order by the invoice_date column
 * @method     ChildInvoiceQuery orderBySalesDate($order = Criteria::ASC) Order by the sales_date column
 * @method     ChildInvoiceQuery orderByPaymentDueDate($order = Criteria::ASC) Order by the payment_due_date column
 * @method     ChildInvoiceQuery orderBySalesPerson($order = Criteria::ASC) Order by the sales_person column
 * @method     ChildInvoiceQuery orderByInvoiceType($order = Criteria::ASC) Order by the invoice_type column
 * @method     ChildInvoiceQuery orderByComment($order = Criteria::ASC) Order by the comment column
 * @method     ChildInvoiceQuery orderByContentOriginal($order = Criteria::ASC) Order by the content_original column
 * @method     ChildInvoiceQuery orderByContentCopy($order = Criteria::ASC) Order by the content_copy column
 * @method     ChildInvoiceQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildInvoiceQuery orderByTotalPayed($order = Criteria::ASC) Order by the total_payed column
 * @method     ChildInvoiceQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 * @method     ChildInvoiceQuery orderByExternalId($order = Criteria::ASC) Order by the external_id column
 * @method     ChildInvoiceQuery orderByContentType($order = Criteria::ASC) Order by the content_type column
 *
 * @method     ChildInvoiceQuery groupById() Group by the id column
 * @method     ChildInvoiceQuery groupBySymbol() Group by the symbol column
 * @method     ChildInvoiceQuery groupByInvoiceDate() Group by the invoice_date column
 * @method     ChildInvoiceQuery groupBySalesDate() Group by the sales_date column
 * @method     ChildInvoiceQuery groupByPaymentDueDate() Group by the payment_due_date column
 * @method     ChildInvoiceQuery groupBySalesPerson() Group by the sales_person column
 * @method     ChildInvoiceQuery groupByInvoiceType() Group by the invoice_type column
 * @method     ChildInvoiceQuery groupByComment() Group by the comment column
 * @method     ChildInvoiceQuery groupByContentOriginal() Group by the content_original column
 * @method     ChildInvoiceQuery groupByContentCopy() Group by the content_copy column
 * @method     ChildInvoiceQuery groupByOrderId() Group by the order_id column
 * @method     ChildInvoiceQuery groupByTotalPayed() Group by the total_payed column
 * @method     ChildInvoiceQuery groupByShopId() Group by the shop_id column
 * @method     ChildInvoiceQuery groupByExternalId() Group by the external_id column
 * @method     ChildInvoiceQuery groupByContentType() Group by the content_type column
 *
 * @method     ChildInvoiceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildInvoiceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildInvoiceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildInvoice findOne(ConnectionInterface $con = null) Return the first ChildInvoice matching the query
 * @method     ChildInvoice findOneOrCreate(ConnectionInterface $con = null) Return the first ChildInvoice matching the query, or a new ChildInvoice object populated from the query conditions when no match is found
 *
 * @method     ChildInvoice findOneById(int $id) Return the first ChildInvoice filtered by the id column
 * @method     ChildInvoice findOneBySymbol(string $symbol) Return the first ChildInvoice filtered by the symbol column
 * @method     ChildInvoice findOneByInvoiceDate(string $invoice_date) Return the first ChildInvoice filtered by the invoice_date column
 * @method     ChildInvoice findOneBySalesDate(string $sales_date) Return the first ChildInvoice filtered by the sales_date column
 * @method     ChildInvoice findOneByPaymentDueDate(string $payment_due_date) Return the first ChildInvoice filtered by the payment_due_date column
 * @method     ChildInvoice findOneBySalesPerson(string $sales_person) Return the first ChildInvoice filtered by the sales_person column
 * @method     ChildInvoice findOneByInvoiceType(int $invoice_type) Return the first ChildInvoice filtered by the invoice_type column
 * @method     ChildInvoice findOneByComment(string $comment) Return the first ChildInvoice filtered by the comment column
 * @method     ChildInvoice findOneByContentOriginal(resource $content_original) Return the first ChildInvoice filtered by the content_original column
 * @method     ChildInvoice findOneByContentCopy(resource $content_copy) Return the first ChildInvoice filtered by the content_copy column
 * @method     ChildInvoice findOneByOrderId(int $order_id) Return the first ChildInvoice filtered by the order_id column
 * @method     ChildInvoice findOneByTotalPayed(string $total_payed) Return the first ChildInvoice filtered by the total_payed column
 * @method     ChildInvoice findOneByShopId(int $shop_id) Return the first ChildInvoice filtered by the shop_id column
 * @method     ChildInvoice findOneByExternalId(int $external_id) Return the first ChildInvoice filtered by the external_id column
 * @method     ChildInvoice findOneByContentType(string $content_type) Return the first ChildInvoice filtered by the content_type column
 *
 * @method     array findById(int $id) Return ChildInvoice objects filtered by the id column
 * @method     array findBySymbol(string $symbol) Return ChildInvoice objects filtered by the symbol column
 * @method     array findByInvoiceDate(string $invoice_date) Return ChildInvoice objects filtered by the invoice_date column
 * @method     array findBySalesDate(string $sales_date) Return ChildInvoice objects filtered by the sales_date column
 * @method     array findByPaymentDueDate(string $payment_due_date) Return ChildInvoice objects filtered by the payment_due_date column
 * @method     array findBySalesPerson(string $sales_person) Return ChildInvoice objects filtered by the sales_person column
 * @method     array findByInvoiceType(int $invoice_type) Return ChildInvoice objects filtered by the invoice_type column
 * @method     array findByComment(string $comment) Return ChildInvoice objects filtered by the comment column
 * @method     array findByContentOriginal(resource $content_original) Return ChildInvoice objects filtered by the content_original column
 * @method     array findByContentCopy(resource $content_copy) Return ChildInvoice objects filtered by the content_copy column
 * @method     array findByOrderId(int $order_id) Return ChildInvoice objects filtered by the order_id column
 * @method     array findByTotalPayed(string $total_payed) Return ChildInvoice objects filtered by the total_payed column
 * @method     array findByShopId(int $shop_id) Return ChildInvoice objects filtered by the shop_id column
 * @method     array findByExternalId(int $external_id) Return ChildInvoice objects filtered by the external_id column
 * @method     array findByContentType(string $content_type) Return ChildInvoice objects filtered by the content_type column
 *
 */
abstract class InvoiceQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Invoice\Model\ORM\Base\InvoiceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Invoice\\Model\\ORM\\Invoice', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildInvoiceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildInvoiceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Invoice\Model\ORM\InvoiceQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Invoice\Model\ORM\InvoiceQuery();
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
     * @return ChildInvoice|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = InvoiceTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(InvoiceTableMap::DATABASE_NAME);
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
     * @return   ChildInvoice A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, SYMBOL, INVOICE_DATE, SALES_DATE, PAYMENT_DUE_DATE, SALES_PERSON, INVOICE_TYPE, COMMENT, CONTENT_ORIGINAL, CONTENT_COPY, ORDER_ID, TOTAL_PAYED, SHOP_ID, EXTERNAL_ID, CONTENT_TYPE FROM invoice WHERE ID = :p0';
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
            $obj = new ChildInvoice();
            $obj->hydrate($row);
            InvoiceTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildInvoice|array|mixed the result, formatted by the current formatter
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
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(InvoiceTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(InvoiceTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_ID, $id, $comparison);
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
     * @return ChildInvoiceQuery The current query, for fluid interface
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

        return $this->addUsingAlias(InvoiceTableMap::COL_SYMBOL, $symbol, $comparison);
    }

    /**
     * Filter the query on the invoice_date column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceDate('2011-03-14'); // WHERE invoice_date = '2011-03-14'
     * $query->filterByInvoiceDate('now'); // WHERE invoice_date = '2011-03-14'
     * $query->filterByInvoiceDate(array('max' => 'yesterday')); // WHERE invoice_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $invoiceDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByInvoiceDate($invoiceDate = null, $comparison = null)
    {
        if (is_array($invoiceDate)) {
            $useMinMax = false;
            if (isset($invoiceDate['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_INVOICE_DATE, $invoiceDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($invoiceDate['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_INVOICE_DATE, $invoiceDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_INVOICE_DATE, $invoiceDate, $comparison);
    }

    /**
     * Filter the query on the sales_date column
     *
     * Example usage:
     * <code>
     * $query->filterBySalesDate('2011-03-14'); // WHERE sales_date = '2011-03-14'
     * $query->filterBySalesDate('now'); // WHERE sales_date = '2011-03-14'
     * $query->filterBySalesDate(array('max' => 'yesterday')); // WHERE sales_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $salesDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterBySalesDate($salesDate = null, $comparison = null)
    {
        if (is_array($salesDate)) {
            $useMinMax = false;
            if (isset($salesDate['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_SALES_DATE, $salesDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($salesDate['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_SALES_DATE, $salesDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_SALES_DATE, $salesDate, $comparison);
    }

    /**
     * Filter the query on the payment_due_date column
     *
     * Example usage:
     * <code>
     * $query->filterByPaymentDueDate('2011-03-14'); // WHERE payment_due_date = '2011-03-14'
     * $query->filterByPaymentDueDate('now'); // WHERE payment_due_date = '2011-03-14'
     * $query->filterByPaymentDueDate(array('max' => 'yesterday')); // WHERE payment_due_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $paymentDueDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByPaymentDueDate($paymentDueDate = null, $comparison = null)
    {
        if (is_array($paymentDueDate)) {
            $useMinMax = false;
            if (isset($paymentDueDate['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_PAYMENT_DUE_DATE, $paymentDueDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($paymentDueDate['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_PAYMENT_DUE_DATE, $paymentDueDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_PAYMENT_DUE_DATE, $paymentDueDate, $comparison);
    }

    /**
     * Filter the query on the sales_person column
     *
     * Example usage:
     * <code>
     * $query->filterBySalesPerson('fooValue');   // WHERE sales_person = 'fooValue'
     * $query->filterBySalesPerson('%fooValue%'); // WHERE sales_person LIKE '%fooValue%'
     * </code>
     *
     * @param     string $salesPerson The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterBySalesPerson($salesPerson = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($salesPerson)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $salesPerson)) {
                $salesPerson = str_replace('*', '%', $salesPerson);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_SALES_PERSON, $salesPerson, $comparison);
    }

    /**
     * Filter the query on the invoice_type column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceType(1234); // WHERE invoice_type = 1234
     * $query->filterByInvoiceType(array(12, 34)); // WHERE invoice_type IN (12, 34)
     * $query->filterByInvoiceType(array('min' => 12)); // WHERE invoice_type > 12
     * </code>
     *
     * @param     mixed $invoiceType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByInvoiceType($invoiceType = null, $comparison = null)
    {
        if (is_array($invoiceType)) {
            $useMinMax = false;
            if (isset($invoiceType['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_INVOICE_TYPE, $invoiceType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($invoiceType['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_INVOICE_TYPE, $invoiceType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_INVOICE_TYPE, $invoiceType, $comparison);
    }

    /**
     * Filter the query on the comment column
     *
     * Example usage:
     * <code>
     * $query->filterByComment('fooValue');   // WHERE comment = 'fooValue'
     * $query->filterByComment('%fooValue%'); // WHERE comment LIKE '%fooValue%'
     * </code>
     *
     * @param     string $comment The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByComment($comment = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($comment)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $comment)) {
                $comment = str_replace('*', '%', $comment);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_COMMENT, $comment, $comparison);
    }

    /**
     * Filter the query on the content_original column
     *
     * @param     mixed $contentOriginal The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByContentOriginal($contentOriginal = null, $comparison = null)
    {

        return $this->addUsingAlias(InvoiceTableMap::COL_CONTENT_ORIGINAL, $contentOriginal, $comparison);
    }

    /**
     * Filter the query on the content_copy column
     *
     * @param     mixed $contentCopy The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByContentCopy($contentCopy = null, $comparison = null)
    {

        return $this->addUsingAlias(InvoiceTableMap::COL_CONTENT_COPY, $contentCopy, $comparison);
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
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the total_payed column
     *
     * Example usage:
     * <code>
     * $query->filterByTotalPayed(1234); // WHERE total_payed = 1234
     * $query->filterByTotalPayed(array(12, 34)); // WHERE total_payed IN (12, 34)
     * $query->filterByTotalPayed(array('min' => 12)); // WHERE total_payed > 12
     * </code>
     *
     * @param     mixed $totalPayed The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByTotalPayed($totalPayed = null, $comparison = null)
    {
        if (is_array($totalPayed)) {
            $useMinMax = false;
            if (isset($totalPayed['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_TOTAL_PAYED, $totalPayed['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($totalPayed['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_TOTAL_PAYED, $totalPayed['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_TOTAL_PAYED, $totalPayed, $comparison);
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
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query on the external_id column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalId(1234); // WHERE external_id = 1234
     * $query->filterByExternalId(array(12, 34)); // WHERE external_id IN (12, 34)
     * $query->filterByExternalId(array('min' => 12)); // WHERE external_id > 12
     * </code>
     *
     * @param     mixed $externalId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByExternalId($externalId = null, $comparison = null)
    {
        if (is_array($externalId)) {
            $useMinMax = false;
            if (isset($externalId['min'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_EXTERNAL_ID, $externalId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($externalId['max'])) {
                $this->addUsingAlias(InvoiceTableMap::COL_EXTERNAL_ID, $externalId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_EXTERNAL_ID, $externalId, $comparison);
    }

    /**
     * Filter the query on the content_type column
     *
     * Example usage:
     * <code>
     * $query->filterByContentType('fooValue');   // WHERE content_type = 'fooValue'
     * $query->filterByContentType('%fooValue%'); // WHERE content_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $contentType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function filterByContentType($contentType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($contentType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $contentType)) {
                $contentType = str_replace('*', '%', $contentType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(InvoiceTableMap::COL_CONTENT_TYPE, $contentType, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildInvoice $invoice Object to remove from the list of results
     *
     * @return ChildInvoiceQuery The current query, for fluid interface
     */
    public function prune($invoice = null)
    {
        if ($invoice) {
            $this->addUsingAlias(InvoiceTableMap::COL_ID, $invoice->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the invoice table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(InvoiceTableMap::DATABASE_NAME);
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
            InvoiceTableMap::clearInstancePool();
            InvoiceTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildInvoice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildInvoice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(InvoiceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(InvoiceTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        InvoiceTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            InvoiceTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // InvoiceQuery
