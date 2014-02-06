<?php

namespace Gekosale\Plugin\Shipment\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Shipment\Model\ORM\Shipment as ChildShipment;
use Gekosale\Plugin\Shipment\Model\ORM\ShipmentQuery as ChildShipmentQuery;
use Gekosale\Plugin\Shipment\Model\ORM\Map\ShipmentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'shipment' table.
 *
 * 
 *
 * @method     ChildShipmentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildShipmentQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildShipmentQuery orderByGuid($order = Criteria::ASC) Order by the guid column
 * @method     ChildShipmentQuery orderByPackageNumber($order = Criteria::ASC) Order by the package_number column
 * @method     ChildShipmentQuery orderByLabel($order = Criteria::ASC) Order by the label column
 * @method     ChildShipmentQuery orderByOrderData($order = Criteria::ASC) Order by the order_data column
 * @method     ChildShipmentQuery orderByFormData($order = Criteria::ASC) Order by the form_data column
 * @method     ChildShipmentQuery orderByModel($order = Criteria::ASC) Order by the model column
 * @method     ChildShipmentQuery orderByIsSent($order = Criteria::ASC) Order by the sent column
 * @method     ChildShipmentQuery orderByEnvelopeId($order = Criteria::ASC) Order by the envelope_id column
 *
 * @method     ChildShipmentQuery groupById() Group by the id column
 * @method     ChildShipmentQuery groupByOrderId() Group by the order_id column
 * @method     ChildShipmentQuery groupByGuid() Group by the guid column
 * @method     ChildShipmentQuery groupByPackageNumber() Group by the package_number column
 * @method     ChildShipmentQuery groupByLabel() Group by the label column
 * @method     ChildShipmentQuery groupByOrderData() Group by the order_data column
 * @method     ChildShipmentQuery groupByFormData() Group by the form_data column
 * @method     ChildShipmentQuery groupByModel() Group by the model column
 * @method     ChildShipmentQuery groupByIsSent() Group by the sent column
 * @method     ChildShipmentQuery groupByEnvelopeId() Group by the envelope_id column
 *
 * @method     ChildShipmentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildShipmentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildShipmentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildShipment findOne(ConnectionInterface $con = null) Return the first ChildShipment matching the query
 * @method     ChildShipment findOneOrCreate(ConnectionInterface $con = null) Return the first ChildShipment matching the query, or a new ChildShipment object populated from the query conditions when no match is found
 *
 * @method     ChildShipment findOneById(int $id) Return the first ChildShipment filtered by the id column
 * @method     ChildShipment findOneByOrderId(int $order_id) Return the first ChildShipment filtered by the order_id column
 * @method     ChildShipment findOneByGuid(string $guid) Return the first ChildShipment filtered by the guid column
 * @method     ChildShipment findOneByPackageNumber(string $package_number) Return the first ChildShipment filtered by the package_number column
 * @method     ChildShipment findOneByLabel(resource $label) Return the first ChildShipment filtered by the label column
 * @method     ChildShipment findOneByOrderData(string $order_data) Return the first ChildShipment filtered by the order_data column
 * @method     ChildShipment findOneByFormData(string $form_data) Return the first ChildShipment filtered by the form_data column
 * @method     ChildShipment findOneByModel(string $model) Return the first ChildShipment filtered by the model column
 * @method     ChildShipment findOneByIsSent(int $sent) Return the first ChildShipment filtered by the sent column
 * @method     ChildShipment findOneByEnvelopeId(string $envelope_id) Return the first ChildShipment filtered by the envelope_id column
 *
 * @method     array findById(int $id) Return ChildShipment objects filtered by the id column
 * @method     array findByOrderId(int $order_id) Return ChildShipment objects filtered by the order_id column
 * @method     array findByGuid(string $guid) Return ChildShipment objects filtered by the guid column
 * @method     array findByPackageNumber(string $package_number) Return ChildShipment objects filtered by the package_number column
 * @method     array findByLabel(resource $label) Return ChildShipment objects filtered by the label column
 * @method     array findByOrderData(string $order_data) Return ChildShipment objects filtered by the order_data column
 * @method     array findByFormData(string $form_data) Return ChildShipment objects filtered by the form_data column
 * @method     array findByModel(string $model) Return ChildShipment objects filtered by the model column
 * @method     array findByIsSent(int $sent) Return ChildShipment objects filtered by the sent column
 * @method     array findByEnvelopeId(string $envelope_id) Return ChildShipment objects filtered by the envelope_id column
 *
 */
abstract class ShipmentQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Shipment\Model\ORM\Base\ShipmentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Shipment\\Model\\ORM\\Shipment', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildShipmentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildShipmentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Shipment\Model\ORM\ShipmentQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Shipment\Model\ORM\ShipmentQuery();
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
     * @return ChildShipment|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ShipmentTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ShipmentTableMap::DATABASE_NAME);
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
     * @return   ChildShipment A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ORDER_ID, GUID, PACKAGE_NUMBER, LABEL, ORDER_DATA, FORM_DATA, MODEL, SENT, ENVELOPE_ID FROM shipment WHERE ID = :p0';
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
            $obj = new ChildShipment();
            $obj->hydrate($row);
            ShipmentTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildShipment|array|mixed the result, formatted by the current formatter
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
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ShipmentTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ShipmentTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ShipmentTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ShipmentTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_ID, $id, $comparison);
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
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(ShipmentTableMap::COL_ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(ShipmentTableMap::COL_ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the guid column
     *
     * Example usage:
     * <code>
     * $query->filterByGuid('fooValue');   // WHERE guid = 'fooValue'
     * $query->filterByGuid('%fooValue%'); // WHERE guid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $guid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByGuid($guid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($guid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $guid)) {
                $guid = str_replace('*', '%', $guid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_GUID, $guid, $comparison);
    }

    /**
     * Filter the query on the package_number column
     *
     * Example usage:
     * <code>
     * $query->filterByPackageNumber('fooValue');   // WHERE package_number = 'fooValue'
     * $query->filterByPackageNumber('%fooValue%'); // WHERE package_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $packageNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByPackageNumber($packageNumber = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($packageNumber)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $packageNumber)) {
                $packageNumber = str_replace('*', '%', $packageNumber);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_PACKAGE_NUMBER, $packageNumber, $comparison);
    }

    /**
     * Filter the query on the label column
     *
     * @param     mixed $label The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByLabel($label = null, $comparison = null)
    {

        return $this->addUsingAlias(ShipmentTableMap::COL_LABEL, $label, $comparison);
    }

    /**
     * Filter the query on the order_data column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderData('fooValue');   // WHERE order_data = 'fooValue'
     * $query->filterByOrderData('%fooValue%'); // WHERE order_data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $orderData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByOrderData($orderData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($orderData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $orderData)) {
                $orderData = str_replace('*', '%', $orderData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_ORDER_DATA, $orderData, $comparison);
    }

    /**
     * Filter the query on the form_data column
     *
     * Example usage:
     * <code>
     * $query->filterByFormData('fooValue');   // WHERE form_data = 'fooValue'
     * $query->filterByFormData('%fooValue%'); // WHERE form_data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $formData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByFormData($formData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($formData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $formData)) {
                $formData = str_replace('*', '%', $formData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_FORM_DATA, $formData, $comparison);
    }

    /**
     * Filter the query on the model column
     *
     * Example usage:
     * <code>
     * $query->filterByModel('fooValue');   // WHERE model = 'fooValue'
     * $query->filterByModel('%fooValue%'); // WHERE model LIKE '%fooValue%'
     * </code>
     *
     * @param     string $model The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByModel($model = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($model)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $model)) {
                $model = str_replace('*', '%', $model);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_MODEL, $model, $comparison);
    }

    /**
     * Filter the query on the sent column
     *
     * Example usage:
     * <code>
     * $query->filterByIsSent(1234); // WHERE sent = 1234
     * $query->filterByIsSent(array(12, 34)); // WHERE sent IN (12, 34)
     * $query->filterByIsSent(array('min' => 12)); // WHERE sent > 12
     * </code>
     *
     * @param     mixed $isSent The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByIsSent($isSent = null, $comparison = null)
    {
        if (is_array($isSent)) {
            $useMinMax = false;
            if (isset($isSent['min'])) {
                $this->addUsingAlias(ShipmentTableMap::COL_SENT, $isSent['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isSent['max'])) {
                $this->addUsingAlias(ShipmentTableMap::COL_SENT, $isSent['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_SENT, $isSent, $comparison);
    }

    /**
     * Filter the query on the envelope_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEnvelopeId('fooValue');   // WHERE envelope_id = 'fooValue'
     * $query->filterByEnvelopeId('%fooValue%'); // WHERE envelope_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $envelopeId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function filterByEnvelopeId($envelopeId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($envelopeId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $envelopeId)) {
                $envelopeId = str_replace('*', '%', $envelopeId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShipmentTableMap::COL_ENVELOPE_ID, $envelopeId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildShipment $shipment Object to remove from the list of results
     *
     * @return ChildShipmentQuery The current query, for fluid interface
     */
    public function prune($shipment = null)
    {
        if ($shipment) {
            $this->addUsingAlias(ShipmentTableMap::COL_ID, $shipment->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shipment table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShipmentTableMap::DATABASE_NAME);
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
            ShipmentTableMap::clearInstancePool();
            ShipmentTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildShipment or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildShipment object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ShipmentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ShipmentTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ShipmentTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ShipmentTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ShipmentQuery
