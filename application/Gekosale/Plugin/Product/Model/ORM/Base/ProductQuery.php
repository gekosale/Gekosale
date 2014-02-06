<?php

namespace Gekosale\Plugin\Product\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute;
use Gekosale\Plugin\Availability\Model\ORM\Availability;
use Gekosale\Plugin\Crosssell\Model\ORM\Crosssell;
use Gekosale\Plugin\Currency\Model\ORM\Currency;
use Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProduct;
use Gekosale\Plugin\Producer\Model\ORM\Producer;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice;
use Gekosale\Plugin\ProductNew\Model\ORM\ProductNew;
use Gekosale\Plugin\Product\Model\ORM\Product as ChildProduct;
use Gekosale\Plugin\Product\Model\ORM\ProductI18nQuery as ChildProductI18nQuery;
use Gekosale\Plugin\Product\Model\ORM\ProductQuery as ChildProductQuery;
use Gekosale\Plugin\Product\Model\ORM\Map\ProductTableMap;
use Gekosale\Plugin\Similar\Model\ORM\Similar;
use Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet;
use Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasure;
use Gekosale\Plugin\Upsell\Model\ORM\Upsell;
use Gekosale\Plugin\Vat\Model\ORM\Vat;
use Gekosale\Plugin\Wishlist\Model\ORM\Wishlist;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'product' table.
 *
 * 
 *
 * @method     ChildProductQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProductQuery orderByDelivelerCode($order = Criteria::ASC) Order by the deliveler_code column
 * @method     ChildProductQuery orderByEan($order = Criteria::ASC) Order by the ean column
 * @method     ChildProductQuery orderByBarcode($order = Criteria::ASC) Order by the barcode column
 * @method     ChildProductQuery orderByBuyPrice($order = Criteria::ASC) Order by the buy_price column
 * @method     ChildProductQuery orderBySellPrice($order = Criteria::ASC) Order by the sell_price column
 * @method     ChildProductQuery orderByProducerId($order = Criteria::ASC) Order by the producer_id column
 * @method     ChildProductQuery orderByVatId($order = Criteria::ASC) Order by the vat_id column
 * @method     ChildProductQuery orderByStock($order = Criteria::ASC) Order by the stock column
 * @method     ChildProductQuery orderByAddDate($order = Criteria::ASC) Order by the add_date column
 * @method     ChildProductQuery orderByWeight($order = Criteria::ASC) Order by the weight column
 * @method     ChildProductQuery orderByBuyCurrencyId($order = Criteria::ASC) Order by the buy_currency_id column
 * @method     ChildProductQuery orderBySellCurrencyId($order = Criteria::ASC) Order by the sell_currency_id column
 * @method     ChildProductQuery orderByTechnicalDataSetId($order = Criteria::ASC) Order by the technical_data_set_id column
 * @method     ChildProductQuery orderByTrackStock($order = Criteria::ASC) Order by the track_stock column
 * @method     ChildProductQuery orderByEnable($order = Criteria::ASC) Order by the enable column
 * @method     ChildProductQuery orderByPromotion($order = Criteria::ASC) Order by the promotion column
 * @method     ChildProductQuery orderByDiscountPrice($order = Criteria::ASC) Order by the discount_price column
 * @method     ChildProductQuery orderByPromotionStart($order = Criteria::ASC) Order by the promotion_start column
 * @method     ChildProductQuery orderByPromotionEnd($order = Criteria::ASC) Order by the promotion_end column
 * @method     ChildProductQuery orderByShoped($order = Criteria::ASC) Order by the shoped column
 * @method     ChildProductQuery orderByWidth($order = Criteria::ASC) Order by the width column
 * @method     ChildProductQuery orderByHeight($order = Criteria::ASC) Order by the height column
 * @method     ChildProductQuery orderByDeepth($order = Criteria::ASC) Order by the deepth column
 * @method     ChildProductQuery orderByUnitMeasureId($order = Criteria::ASC) Order by the unit_measure_id column
 * @method     ChildProductQuery orderByDisableAtStock($order = Criteria::ASC) Order by the disable_at_stock column
 * @method     ChildProductQuery orderByAvailabilityId($order = Criteria::ASC) Order by the availability_id column
 * @method     ChildProductQuery orderByHierarchy($order = Criteria::ASC) Order by the hierarchy column
 * @method     ChildProductQuery orderByPackageSize($order = Criteria::ASC) Order by the package_size column
 * @method     ChildProductQuery orderByDisableAtStockEnabled($order = Criteria::ASC) Order by the disable_at_stock_enabled column
 * @method     ChildProductQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildProductQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildProductQuery groupById() Group by the id column
 * @method     ChildProductQuery groupByDelivelerCode() Group by the deliveler_code column
 * @method     ChildProductQuery groupByEan() Group by the ean column
 * @method     ChildProductQuery groupByBarcode() Group by the barcode column
 * @method     ChildProductQuery groupByBuyPrice() Group by the buy_price column
 * @method     ChildProductQuery groupBySellPrice() Group by the sell_price column
 * @method     ChildProductQuery groupByProducerId() Group by the producer_id column
 * @method     ChildProductQuery groupByVatId() Group by the vat_id column
 * @method     ChildProductQuery groupByStock() Group by the stock column
 * @method     ChildProductQuery groupByAddDate() Group by the add_date column
 * @method     ChildProductQuery groupByWeight() Group by the weight column
 * @method     ChildProductQuery groupByBuyCurrencyId() Group by the buy_currency_id column
 * @method     ChildProductQuery groupBySellCurrencyId() Group by the sell_currency_id column
 * @method     ChildProductQuery groupByTechnicalDataSetId() Group by the technical_data_set_id column
 * @method     ChildProductQuery groupByTrackStock() Group by the track_stock column
 * @method     ChildProductQuery groupByEnable() Group by the enable column
 * @method     ChildProductQuery groupByPromotion() Group by the promotion column
 * @method     ChildProductQuery groupByDiscountPrice() Group by the discount_price column
 * @method     ChildProductQuery groupByPromotionStart() Group by the promotion_start column
 * @method     ChildProductQuery groupByPromotionEnd() Group by the promotion_end column
 * @method     ChildProductQuery groupByShoped() Group by the shoped column
 * @method     ChildProductQuery groupByWidth() Group by the width column
 * @method     ChildProductQuery groupByHeight() Group by the height column
 * @method     ChildProductQuery groupByDeepth() Group by the deepth column
 * @method     ChildProductQuery groupByUnitMeasureId() Group by the unit_measure_id column
 * @method     ChildProductQuery groupByDisableAtStock() Group by the disable_at_stock column
 * @method     ChildProductQuery groupByAvailabilityId() Group by the availability_id column
 * @method     ChildProductQuery groupByHierarchy() Group by the hierarchy column
 * @method     ChildProductQuery groupByPackageSize() Group by the package_size column
 * @method     ChildProductQuery groupByDisableAtStockEnabled() Group by the disable_at_stock_enabled column
 * @method     ChildProductQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildProductQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildProductQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProductQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProductQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProductQuery leftJoinAvailability($relationAlias = null) Adds a LEFT JOIN clause to the query using the Availability relation
 * @method     ChildProductQuery rightJoinAvailability($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Availability relation
 * @method     ChildProductQuery innerJoinAvailability($relationAlias = null) Adds a INNER JOIN clause to the query using the Availability relation
 *
 * @method     ChildProductQuery leftJoinCurrencyRelatedByBuyCurrencyId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CurrencyRelatedByBuyCurrencyId relation
 * @method     ChildProductQuery rightJoinCurrencyRelatedByBuyCurrencyId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CurrencyRelatedByBuyCurrencyId relation
 * @method     ChildProductQuery innerJoinCurrencyRelatedByBuyCurrencyId($relationAlias = null) Adds a INNER JOIN clause to the query using the CurrencyRelatedByBuyCurrencyId relation
 *
 * @method     ChildProductQuery leftJoinProducer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Producer relation
 * @method     ChildProductQuery rightJoinProducer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Producer relation
 * @method     ChildProductQuery innerJoinProducer($relationAlias = null) Adds a INNER JOIN clause to the query using the Producer relation
 *
 * @method     ChildProductQuery leftJoinCurrencyRelatedBySellCurrencyId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CurrencyRelatedBySellCurrencyId relation
 * @method     ChildProductQuery rightJoinCurrencyRelatedBySellCurrencyId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CurrencyRelatedBySellCurrencyId relation
 * @method     ChildProductQuery innerJoinCurrencyRelatedBySellCurrencyId($relationAlias = null) Adds a INNER JOIN clause to the query using the CurrencyRelatedBySellCurrencyId relation
 *
 * @method     ChildProductQuery leftJoinUnitMeasure($relationAlias = null) Adds a LEFT JOIN clause to the query using the UnitMeasure relation
 * @method     ChildProductQuery rightJoinUnitMeasure($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UnitMeasure relation
 * @method     ChildProductQuery innerJoinUnitMeasure($relationAlias = null) Adds a INNER JOIN clause to the query using the UnitMeasure relation
 *
 * @method     ChildProductQuery leftJoinVat($relationAlias = null) Adds a LEFT JOIN clause to the query using the Vat relation
 * @method     ChildProductQuery rightJoinVat($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Vat relation
 * @method     ChildProductQuery innerJoinVat($relationAlias = null) Adds a INNER JOIN clause to the query using the Vat relation
 *
 * @method     ChildProductQuery leftJoinTechnicalDataSet($relationAlias = null) Adds a LEFT JOIN clause to the query using the TechnicalDataSet relation
 * @method     ChildProductQuery rightJoinTechnicalDataSet($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TechnicalDataSet relation
 * @method     ChildProductQuery innerJoinTechnicalDataSet($relationAlias = null) Adds a INNER JOIN clause to the query using the TechnicalDataSet relation
 *
 * @method     ChildProductQuery leftJoinMissingCartProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the MissingCartProduct relation
 * @method     ChildProductQuery rightJoinMissingCartProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MissingCartProduct relation
 * @method     ChildProductQuery innerJoinMissingCartProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the MissingCartProduct relation
 *
 * @method     ChildProductQuery leftJoinOrderProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderProduct relation
 * @method     ChildProductQuery rightJoinOrderProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderProduct relation
 * @method     ChildProductQuery innerJoinOrderProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderProduct relation
 *
 * @method     ChildProductQuery leftJoinProductAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildProductQuery rightJoinProductAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildProductQuery innerJoinProductAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAttribute relation
 *
 * @method     ChildProductQuery leftJoinProductCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductCategory relation
 * @method     ChildProductQuery rightJoinProductCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductCategory relation
 * @method     ChildProductQuery innerJoinProductCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductCategory relation
 *
 * @method     ChildProductQuery leftJoinDelivererProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the DelivererProduct relation
 * @method     ChildProductQuery rightJoinDelivererProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DelivererProduct relation
 * @method     ChildProductQuery innerJoinDelivererProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the DelivererProduct relation
 *
 * @method     ChildProductQuery leftJoinProductFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductFile relation
 * @method     ChildProductQuery rightJoinProductFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductFile relation
 * @method     ChildProductQuery innerJoinProductFile($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductFile relation
 *
 * @method     ChildProductQuery leftJoinProductGroupPrice($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductGroupPrice relation
 * @method     ChildProductQuery rightJoinProductGroupPrice($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductGroupPrice relation
 * @method     ChildProductQuery innerJoinProductGroupPrice($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductGroupPrice relation
 *
 * @method     ChildProductQuery leftJoinProductNew($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductNew relation
 * @method     ChildProductQuery rightJoinProductNew($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductNew relation
 * @method     ChildProductQuery innerJoinProductNew($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductNew relation
 *
 * @method     ChildProductQuery leftJoinProductPhoto($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductPhoto relation
 * @method     ChildProductQuery rightJoinProductPhoto($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductPhoto relation
 * @method     ChildProductQuery innerJoinProductPhoto($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductPhoto relation
 *
 * @method     ChildProductQuery leftJoinCrosssellRelatedByProductId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CrosssellRelatedByProductId relation
 * @method     ChildProductQuery rightJoinCrosssellRelatedByProductId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CrosssellRelatedByProductId relation
 * @method     ChildProductQuery innerJoinCrosssellRelatedByProductId($relationAlias = null) Adds a INNER JOIN clause to the query using the CrosssellRelatedByProductId relation
 *
 * @method     ChildProductQuery leftJoinCrosssellRelatedByRelatedProductId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CrosssellRelatedByRelatedProductId relation
 * @method     ChildProductQuery rightJoinCrosssellRelatedByRelatedProductId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CrosssellRelatedByRelatedProductId relation
 * @method     ChildProductQuery innerJoinCrosssellRelatedByRelatedProductId($relationAlias = null) Adds a INNER JOIN clause to the query using the CrosssellRelatedByRelatedProductId relation
 *
 * @method     ChildProductQuery leftJoinSimilarRelatedByProductId($relationAlias = null) Adds a LEFT JOIN clause to the query using the SimilarRelatedByProductId relation
 * @method     ChildProductQuery rightJoinSimilarRelatedByProductId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SimilarRelatedByProductId relation
 * @method     ChildProductQuery innerJoinSimilarRelatedByProductId($relationAlias = null) Adds a INNER JOIN clause to the query using the SimilarRelatedByProductId relation
 *
 * @method     ChildProductQuery leftJoinSimilarRelatedByRelatedProductId($relationAlias = null) Adds a LEFT JOIN clause to the query using the SimilarRelatedByRelatedProductId relation
 * @method     ChildProductQuery rightJoinSimilarRelatedByRelatedProductId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SimilarRelatedByRelatedProductId relation
 * @method     ChildProductQuery innerJoinSimilarRelatedByRelatedProductId($relationAlias = null) Adds a INNER JOIN clause to the query using the SimilarRelatedByRelatedProductId relation
 *
 * @method     ChildProductQuery leftJoinUpsellRelatedByProductId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UpsellRelatedByProductId relation
 * @method     ChildProductQuery rightJoinUpsellRelatedByProductId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UpsellRelatedByProductId relation
 * @method     ChildProductQuery innerJoinUpsellRelatedByProductId($relationAlias = null) Adds a INNER JOIN clause to the query using the UpsellRelatedByProductId relation
 *
 * @method     ChildProductQuery leftJoinUpsellRelatedByRelatedProductId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UpsellRelatedByRelatedProductId relation
 * @method     ChildProductQuery rightJoinUpsellRelatedByRelatedProductId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UpsellRelatedByRelatedProductId relation
 * @method     ChildProductQuery innerJoinUpsellRelatedByRelatedProductId($relationAlias = null) Adds a INNER JOIN clause to the query using the UpsellRelatedByRelatedProductId relation
 *
 * @method     ChildProductQuery leftJoinProductTechnicalDataGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductTechnicalDataGroup relation
 * @method     ChildProductQuery rightJoinProductTechnicalDataGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductTechnicalDataGroup relation
 * @method     ChildProductQuery innerJoinProductTechnicalDataGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductTechnicalDataGroup relation
 *
 * @method     ChildProductQuery leftJoinWishlist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Wishlist relation
 * @method     ChildProductQuery rightJoinWishlist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Wishlist relation
 * @method     ChildProductQuery innerJoinWishlist($relationAlias = null) Adds a INNER JOIN clause to the query using the Wishlist relation
 *
 * @method     ChildProductQuery leftJoinProductI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductI18n relation
 * @method     ChildProductQuery rightJoinProductI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductI18n relation
 * @method     ChildProductQuery innerJoinProductI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductI18n relation
 *
 * @method     ChildProduct findOne(ConnectionInterface $con = null) Return the first ChildProduct matching the query
 * @method     ChildProduct findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProduct matching the query, or a new ChildProduct object populated from the query conditions when no match is found
 *
 * @method     ChildProduct findOneById(int $id) Return the first ChildProduct filtered by the id column
 * @method     ChildProduct findOneByDelivelerCode(string $deliveler_code) Return the first ChildProduct filtered by the deliveler_code column
 * @method     ChildProduct findOneByEan(string $ean) Return the first ChildProduct filtered by the ean column
 * @method     ChildProduct findOneByBarcode(string $barcode) Return the first ChildProduct filtered by the barcode column
 * @method     ChildProduct findOneByBuyPrice(string $buy_price) Return the first ChildProduct filtered by the buy_price column
 * @method     ChildProduct findOneBySellPrice(string $sell_price) Return the first ChildProduct filtered by the sell_price column
 * @method     ChildProduct findOneByProducerId(int $producer_id) Return the first ChildProduct filtered by the producer_id column
 * @method     ChildProduct findOneByVatId(int $vat_id) Return the first ChildProduct filtered by the vat_id column
 * @method     ChildProduct findOneByStock(int $stock) Return the first ChildProduct filtered by the stock column
 * @method     ChildProduct findOneByAddDate(string $add_date) Return the first ChildProduct filtered by the add_date column
 * @method     ChildProduct findOneByWeight(string $weight) Return the first ChildProduct filtered by the weight column
 * @method     ChildProduct findOneByBuyCurrencyId(int $buy_currency_id) Return the first ChildProduct filtered by the buy_currency_id column
 * @method     ChildProduct findOneBySellCurrencyId(int $sell_currency_id) Return the first ChildProduct filtered by the sell_currency_id column
 * @method     ChildProduct findOneByTechnicalDataSetId(int $technical_data_set_id) Return the first ChildProduct filtered by the technical_data_set_id column
 * @method     ChildProduct findOneByTrackStock(int $track_stock) Return the first ChildProduct filtered by the track_stock column
 * @method     ChildProduct findOneByEnable(int $enable) Return the first ChildProduct filtered by the enable column
 * @method     ChildProduct findOneByPromotion(int $promotion) Return the first ChildProduct filtered by the promotion column
 * @method     ChildProduct findOneByDiscountPrice(string $discount_price) Return the first ChildProduct filtered by the discount_price column
 * @method     ChildProduct findOneByPromotionStart(string $promotion_start) Return the first ChildProduct filtered by the promotion_start column
 * @method     ChildProduct findOneByPromotionEnd(string $promotion_end) Return the first ChildProduct filtered by the promotion_end column
 * @method     ChildProduct findOneByShoped(int $shoped) Return the first ChildProduct filtered by the shoped column
 * @method     ChildProduct findOneByWidth(string $width) Return the first ChildProduct filtered by the width column
 * @method     ChildProduct findOneByHeight(string $height) Return the first ChildProduct filtered by the height column
 * @method     ChildProduct findOneByDeepth(string $deepth) Return the first ChildProduct filtered by the deepth column
 * @method     ChildProduct findOneByUnitMeasureId(int $unit_measure_id) Return the first ChildProduct filtered by the unit_measure_id column
 * @method     ChildProduct findOneByDisableAtStock(int $disable_at_stock) Return the first ChildProduct filtered by the disable_at_stock column
 * @method     ChildProduct findOneByAvailabilityId(int $availability_id) Return the first ChildProduct filtered by the availability_id column
 * @method     ChildProduct findOneByHierarchy(int $hierarchy) Return the first ChildProduct filtered by the hierarchy column
 * @method     ChildProduct findOneByPackageSize(string $package_size) Return the first ChildProduct filtered by the package_size column
 * @method     ChildProduct findOneByDisableAtStockEnabled(int $disable_at_stock_enabled) Return the first ChildProduct filtered by the disable_at_stock_enabled column
 * @method     ChildProduct findOneByCreatedAt(string $created_at) Return the first ChildProduct filtered by the created_at column
 * @method     ChildProduct findOneByUpdatedAt(string $updated_at) Return the first ChildProduct filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildProduct objects filtered by the id column
 * @method     array findByDelivelerCode(string $deliveler_code) Return ChildProduct objects filtered by the deliveler_code column
 * @method     array findByEan(string $ean) Return ChildProduct objects filtered by the ean column
 * @method     array findByBarcode(string $barcode) Return ChildProduct objects filtered by the barcode column
 * @method     array findByBuyPrice(string $buy_price) Return ChildProduct objects filtered by the buy_price column
 * @method     array findBySellPrice(string $sell_price) Return ChildProduct objects filtered by the sell_price column
 * @method     array findByProducerId(int $producer_id) Return ChildProduct objects filtered by the producer_id column
 * @method     array findByVatId(int $vat_id) Return ChildProduct objects filtered by the vat_id column
 * @method     array findByStock(int $stock) Return ChildProduct objects filtered by the stock column
 * @method     array findByAddDate(string $add_date) Return ChildProduct objects filtered by the add_date column
 * @method     array findByWeight(string $weight) Return ChildProduct objects filtered by the weight column
 * @method     array findByBuyCurrencyId(int $buy_currency_id) Return ChildProduct objects filtered by the buy_currency_id column
 * @method     array findBySellCurrencyId(int $sell_currency_id) Return ChildProduct objects filtered by the sell_currency_id column
 * @method     array findByTechnicalDataSetId(int $technical_data_set_id) Return ChildProduct objects filtered by the technical_data_set_id column
 * @method     array findByTrackStock(int $track_stock) Return ChildProduct objects filtered by the track_stock column
 * @method     array findByEnable(int $enable) Return ChildProduct objects filtered by the enable column
 * @method     array findByPromotion(int $promotion) Return ChildProduct objects filtered by the promotion column
 * @method     array findByDiscountPrice(string $discount_price) Return ChildProduct objects filtered by the discount_price column
 * @method     array findByPromotionStart(string $promotion_start) Return ChildProduct objects filtered by the promotion_start column
 * @method     array findByPromotionEnd(string $promotion_end) Return ChildProduct objects filtered by the promotion_end column
 * @method     array findByShoped(int $shoped) Return ChildProduct objects filtered by the shoped column
 * @method     array findByWidth(string $width) Return ChildProduct objects filtered by the width column
 * @method     array findByHeight(string $height) Return ChildProduct objects filtered by the height column
 * @method     array findByDeepth(string $deepth) Return ChildProduct objects filtered by the deepth column
 * @method     array findByUnitMeasureId(int $unit_measure_id) Return ChildProduct objects filtered by the unit_measure_id column
 * @method     array findByDisableAtStock(int $disable_at_stock) Return ChildProduct objects filtered by the disable_at_stock column
 * @method     array findByAvailabilityId(int $availability_id) Return ChildProduct objects filtered by the availability_id column
 * @method     array findByHierarchy(int $hierarchy) Return ChildProduct objects filtered by the hierarchy column
 * @method     array findByPackageSize(string $package_size) Return ChildProduct objects filtered by the package_size column
 * @method     array findByDisableAtStockEnabled(int $disable_at_stock_enabled) Return ChildProduct objects filtered by the disable_at_stock_enabled column
 * @method     array findByCreatedAt(string $created_at) Return ChildProduct objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildProduct objects filtered by the updated_at column
 *
 */
abstract class ProductQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Product\Model\ORM\Base\ProductQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProductQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProductQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Product\Model\ORM\ProductQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Product\Model\ORM\ProductQuery();
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
     * @return ChildProduct|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductTableMap::DATABASE_NAME);
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
     * @return   ChildProduct A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DELIVELER_CODE, EAN, BARCODE, BUY_PRICE, SELL_PRICE, PRODUCER_ID, VAT_ID, STOCK, ADD_DATE, WEIGHT, BUY_CURRENCY_ID, SELL_CURRENCY_ID, TECHNICAL_DATA_SET_ID, TRACK_STOCK, ENABLE, PROMOTION, DISCOUNT_PRICE, PROMOTION_START, PROMOTION_END, SHOPED, WIDTH, HEIGHT, DEEPTH, UNIT_MEASURE_ID, DISABLE_AT_STOCK, AVAILABILITY_ID, HIERARCHY, PACKAGE_SIZE, DISABLE_AT_STOCK_ENABLED, CREATED_AT, UPDATED_AT FROM product WHERE ID = :p0';
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
            $obj = new ChildProduct();
            $obj->hydrate($row);
            ProductTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProduct|array|mixed the result, formatted by the current formatter
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProductTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProductTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the deliveler_code column
     *
     * Example usage:
     * <code>
     * $query->filterByDelivelerCode('fooValue');   // WHERE deliveler_code = 'fooValue'
     * $query->filterByDelivelerCode('%fooValue%'); // WHERE deliveler_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $delivelerCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByDelivelerCode($delivelerCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($delivelerCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $delivelerCode)) {
                $delivelerCode = str_replace('*', '%', $delivelerCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_DELIVELER_CODE, $delivelerCode, $comparison);
    }

    /**
     * Filter the query on the ean column
     *
     * Example usage:
     * <code>
     * $query->filterByEan('fooValue');   // WHERE ean = 'fooValue'
     * $query->filterByEan('%fooValue%'); // WHERE ean LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ean The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByEan($ean = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ean)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ean)) {
                $ean = str_replace('*', '%', $ean);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_EAN, $ean, $comparison);
    }

    /**
     * Filter the query on the barcode column
     *
     * Example usage:
     * <code>
     * $query->filterByBarcode('fooValue');   // WHERE barcode = 'fooValue'
     * $query->filterByBarcode('%fooValue%'); // WHERE barcode LIKE '%fooValue%'
     * </code>
     *
     * @param     string $barcode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByBarcode($barcode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($barcode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $barcode)) {
                $barcode = str_replace('*', '%', $barcode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_BARCODE, $barcode, $comparison);
    }

    /**
     * Filter the query on the buy_price column
     *
     * Example usage:
     * <code>
     * $query->filterByBuyPrice(1234); // WHERE buy_price = 1234
     * $query->filterByBuyPrice(array(12, 34)); // WHERE buy_price IN (12, 34)
     * $query->filterByBuyPrice(array('min' => 12)); // WHERE buy_price > 12
     * </code>
     *
     * @param     mixed $buyPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByBuyPrice($buyPrice = null, $comparison = null)
    {
        if (is_array($buyPrice)) {
            $useMinMax = false;
            if (isset($buyPrice['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_BUY_PRICE, $buyPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($buyPrice['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_BUY_PRICE, $buyPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_BUY_PRICE, $buyPrice, $comparison);
    }

    /**
     * Filter the query on the sell_price column
     *
     * Example usage:
     * <code>
     * $query->filterBySellPrice(1234); // WHERE sell_price = 1234
     * $query->filterBySellPrice(array(12, 34)); // WHERE sell_price IN (12, 34)
     * $query->filterBySellPrice(array('min' => 12)); // WHERE sell_price > 12
     * </code>
     *
     * @param     mixed $sellPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterBySellPrice($sellPrice = null, $comparison = null)
    {
        if (is_array($sellPrice)) {
            $useMinMax = false;
            if (isset($sellPrice['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_SELL_PRICE, $sellPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sellPrice['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_SELL_PRICE, $sellPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_SELL_PRICE, $sellPrice, $comparison);
    }

    /**
     * Filter the query on the producer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProducerId(1234); // WHERE producer_id = 1234
     * $query->filterByProducerId(array(12, 34)); // WHERE producer_id IN (12, 34)
     * $query->filterByProducerId(array('min' => 12)); // WHERE producer_id > 12
     * </code>
     *
     * @see       filterByProducer()
     *
     * @param     mixed $producerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProducerId($producerId = null, $comparison = null)
    {
        if (is_array($producerId)) {
            $useMinMax = false;
            if (isset($producerId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_PRODUCER_ID, $producerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($producerId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_PRODUCER_ID, $producerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_PRODUCER_ID, $producerId, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByVatId($vatId = null, $comparison = null)
    {
        if (is_array($vatId)) {
            $useMinMax = false;
            if (isset($vatId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_VAT_ID, $vatId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vatId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_VAT_ID, $vatId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_VAT_ID, $vatId, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByStock($stock = null, $comparison = null)
    {
        if (is_array($stock)) {
            $useMinMax = false;
            if (isset($stock['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_STOCK, $stock['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($stock['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_STOCK, $stock['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_STOCK, $stock, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByAddDate($addDate = null, $comparison = null)
    {
        if (is_array($addDate)) {
            $useMinMax = false;
            if (isset($addDate['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_ADD_DATE, $addDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($addDate['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_ADD_DATE, $addDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_ADD_DATE, $addDate, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByWeight($weight = null, $comparison = null)
    {
        if (is_array($weight)) {
            $useMinMax = false;
            if (isset($weight['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_WEIGHT, $weight['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($weight['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_WEIGHT, $weight['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_WEIGHT, $weight, $comparison);
    }

    /**
     * Filter the query on the buy_currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBuyCurrencyId(1234); // WHERE buy_currency_id = 1234
     * $query->filterByBuyCurrencyId(array(12, 34)); // WHERE buy_currency_id IN (12, 34)
     * $query->filterByBuyCurrencyId(array('min' => 12)); // WHERE buy_currency_id > 12
     * </code>
     *
     * @see       filterByCurrencyRelatedByBuyCurrencyId()
     *
     * @param     mixed $buyCurrencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByBuyCurrencyId($buyCurrencyId = null, $comparison = null)
    {
        if (is_array($buyCurrencyId)) {
            $useMinMax = false;
            if (isset($buyCurrencyId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_BUY_CURRENCY_ID, $buyCurrencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($buyCurrencyId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_BUY_CURRENCY_ID, $buyCurrencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_BUY_CURRENCY_ID, $buyCurrencyId, $comparison);
    }

    /**
     * Filter the query on the sell_currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySellCurrencyId(1234); // WHERE sell_currency_id = 1234
     * $query->filterBySellCurrencyId(array(12, 34)); // WHERE sell_currency_id IN (12, 34)
     * $query->filterBySellCurrencyId(array('min' => 12)); // WHERE sell_currency_id > 12
     * </code>
     *
     * @see       filterByCurrencyRelatedBySellCurrencyId()
     *
     * @param     mixed $sellCurrencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterBySellCurrencyId($sellCurrencyId = null, $comparison = null)
    {
        if (is_array($sellCurrencyId)) {
            $useMinMax = false;
            if (isset($sellCurrencyId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_SELL_CURRENCY_ID, $sellCurrencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sellCurrencyId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_SELL_CURRENCY_ID, $sellCurrencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_SELL_CURRENCY_ID, $sellCurrencyId, $comparison);
    }

    /**
     * Filter the query on the technical_data_set_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTechnicalDataSetId(1234); // WHERE technical_data_set_id = 1234
     * $query->filterByTechnicalDataSetId(array(12, 34)); // WHERE technical_data_set_id IN (12, 34)
     * $query->filterByTechnicalDataSetId(array('min' => 12)); // WHERE technical_data_set_id > 12
     * </code>
     *
     * @see       filterByTechnicalDataSet()
     *
     * @param     mixed $technicalDataSetId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSetId($technicalDataSetId = null, $comparison = null)
    {
        if (is_array($technicalDataSetId)) {
            $useMinMax = false;
            if (isset($technicalDataSetId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSetId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($technicalDataSetId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSetId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSetId, $comparison);
    }

    /**
     * Filter the query on the track_stock column
     *
     * Example usage:
     * <code>
     * $query->filterByTrackStock(1234); // WHERE track_stock = 1234
     * $query->filterByTrackStock(array(12, 34)); // WHERE track_stock IN (12, 34)
     * $query->filterByTrackStock(array('min' => 12)); // WHERE track_stock > 12
     * </code>
     *
     * @param     mixed $trackStock The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByTrackStock($trackStock = null, $comparison = null)
    {
        if (is_array($trackStock)) {
            $useMinMax = false;
            if (isset($trackStock['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_TRACK_STOCK, $trackStock['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($trackStock['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_TRACK_STOCK, $trackStock['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_TRACK_STOCK, $trackStock, $comparison);
    }

    /**
     * Filter the query on the enable column
     *
     * Example usage:
     * <code>
     * $query->filterByEnable(1234); // WHERE enable = 1234
     * $query->filterByEnable(array(12, 34)); // WHERE enable IN (12, 34)
     * $query->filterByEnable(array('min' => 12)); // WHERE enable > 12
     * </code>
     *
     * @param     mixed $enable The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByEnable($enable = null, $comparison = null)
    {
        if (is_array($enable)) {
            $useMinMax = false;
            if (isset($enable['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_ENABLE, $enable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enable['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_ENABLE, $enable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_ENABLE, $enable, $comparison);
    }

    /**
     * Filter the query on the promotion column
     *
     * Example usage:
     * <code>
     * $query->filterByPromotion(1234); // WHERE promotion = 1234
     * $query->filterByPromotion(array(12, 34)); // WHERE promotion IN (12, 34)
     * $query->filterByPromotion(array('min' => 12)); // WHERE promotion > 12
     * </code>
     *
     * @param     mixed $promotion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByPromotion($promotion = null, $comparison = null)
    {
        if (is_array($promotion)) {
            $useMinMax = false;
            if (isset($promotion['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_PROMOTION, $promotion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promotion['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_PROMOTION, $promotion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_PROMOTION, $promotion, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByDiscountPrice($discountPrice = null, $comparison = null)
    {
        if (is_array($discountPrice)) {
            $useMinMax = false;
            if (isset($discountPrice['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_DISCOUNT_PRICE, $discountPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountPrice['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_DISCOUNT_PRICE, $discountPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_DISCOUNT_PRICE, $discountPrice, $comparison);
    }

    /**
     * Filter the query on the promotion_start column
     *
     * Example usage:
     * <code>
     * $query->filterByPromotionStart('2011-03-14'); // WHERE promotion_start = '2011-03-14'
     * $query->filterByPromotionStart('now'); // WHERE promotion_start = '2011-03-14'
     * $query->filterByPromotionStart(array('max' => 'yesterday')); // WHERE promotion_start > '2011-03-13'
     * </code>
     *
     * @param     mixed $promotionStart The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByPromotionStart($promotionStart = null, $comparison = null)
    {
        if (is_array($promotionStart)) {
            $useMinMax = false;
            if (isset($promotionStart['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_PROMOTION_START, $promotionStart['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promotionStart['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_PROMOTION_START, $promotionStart['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_PROMOTION_START, $promotionStart, $comparison);
    }

    /**
     * Filter the query on the promotion_end column
     *
     * Example usage:
     * <code>
     * $query->filterByPromotionEnd('2011-03-14'); // WHERE promotion_end = '2011-03-14'
     * $query->filterByPromotionEnd('now'); // WHERE promotion_end = '2011-03-14'
     * $query->filterByPromotionEnd(array('max' => 'yesterday')); // WHERE promotion_end > '2011-03-13'
     * </code>
     *
     * @param     mixed $promotionEnd The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByPromotionEnd($promotionEnd = null, $comparison = null)
    {
        if (is_array($promotionEnd)) {
            $useMinMax = false;
            if (isset($promotionEnd['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_PROMOTION_END, $promotionEnd['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promotionEnd['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_PROMOTION_END, $promotionEnd['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_PROMOTION_END, $promotionEnd, $comparison);
    }

    /**
     * Filter the query on the shoped column
     *
     * Example usage:
     * <code>
     * $query->filterByShoped(1234); // WHERE shoped = 1234
     * $query->filterByShoped(array(12, 34)); // WHERE shoped IN (12, 34)
     * $query->filterByShoped(array('min' => 12)); // WHERE shoped > 12
     * </code>
     *
     * @param     mixed $shoped The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByShoped($shoped = null, $comparison = null)
    {
        if (is_array($shoped)) {
            $useMinMax = false;
            if (isset($shoped['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_SHOPED, $shoped['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shoped['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_SHOPED, $shoped['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_SHOPED, $shoped, $comparison);
    }

    /**
     * Filter the query on the width column
     *
     * Example usage:
     * <code>
     * $query->filterByWidth(1234); // WHERE width = 1234
     * $query->filterByWidth(array(12, 34)); // WHERE width IN (12, 34)
     * $query->filterByWidth(array('min' => 12)); // WHERE width > 12
     * </code>
     *
     * @param     mixed $width The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByWidth($width = null, $comparison = null)
    {
        if (is_array($width)) {
            $useMinMax = false;
            if (isset($width['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_WIDTH, $width['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($width['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_WIDTH, $width['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_WIDTH, $width, $comparison);
    }

    /**
     * Filter the query on the height column
     *
     * Example usage:
     * <code>
     * $query->filterByHeight(1234); // WHERE height = 1234
     * $query->filterByHeight(array(12, 34)); // WHERE height IN (12, 34)
     * $query->filterByHeight(array('min' => 12)); // WHERE height > 12
     * </code>
     *
     * @param     mixed $height The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByHeight($height = null, $comparison = null)
    {
        if (is_array($height)) {
            $useMinMax = false;
            if (isset($height['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_HEIGHT, $height['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($height['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_HEIGHT, $height['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_HEIGHT, $height, $comparison);
    }

    /**
     * Filter the query on the deepth column
     *
     * Example usage:
     * <code>
     * $query->filterByDeepth(1234); // WHERE deepth = 1234
     * $query->filterByDeepth(array(12, 34)); // WHERE deepth IN (12, 34)
     * $query->filterByDeepth(array('min' => 12)); // WHERE deepth > 12
     * </code>
     *
     * @param     mixed $deepth The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByDeepth($deepth = null, $comparison = null)
    {
        if (is_array($deepth)) {
            $useMinMax = false;
            if (isset($deepth['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_DEEPTH, $deepth['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($deepth['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_DEEPTH, $deepth['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_DEEPTH, $deepth, $comparison);
    }

    /**
     * Filter the query on the unit_measure_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUnitMeasureId(1234); // WHERE unit_measure_id = 1234
     * $query->filterByUnitMeasureId(array(12, 34)); // WHERE unit_measure_id IN (12, 34)
     * $query->filterByUnitMeasureId(array('min' => 12)); // WHERE unit_measure_id > 12
     * </code>
     *
     * @see       filterByUnitMeasure()
     *
     * @param     mixed $unitMeasureId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByUnitMeasureId($unitMeasureId = null, $comparison = null)
    {
        if (is_array($unitMeasureId)) {
            $useMinMax = false;
            if (isset($unitMeasureId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_UNIT_MEASURE_ID, $unitMeasureId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unitMeasureId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_UNIT_MEASURE_ID, $unitMeasureId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_UNIT_MEASURE_ID, $unitMeasureId, $comparison);
    }

    /**
     * Filter the query on the disable_at_stock column
     *
     * Example usage:
     * <code>
     * $query->filterByDisableAtStock(1234); // WHERE disable_at_stock = 1234
     * $query->filterByDisableAtStock(array(12, 34)); // WHERE disable_at_stock IN (12, 34)
     * $query->filterByDisableAtStock(array('min' => 12)); // WHERE disable_at_stock > 12
     * </code>
     *
     * @param     mixed $disableAtStock The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByDisableAtStock($disableAtStock = null, $comparison = null)
    {
        if (is_array($disableAtStock)) {
            $useMinMax = false;
            if (isset($disableAtStock['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_DISABLE_AT_STOCK, $disableAtStock['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($disableAtStock['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_DISABLE_AT_STOCK, $disableAtStock['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_DISABLE_AT_STOCK, $disableAtStock, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByAvailabilityId($availabilityId = null, $comparison = null)
    {
        if (is_array($availabilityId)) {
            $useMinMax = false;
            if (isset($availabilityId['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_AVAILABILITY_ID, $availabilityId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($availabilityId['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_AVAILABILITY_ID, $availabilityId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_AVAILABILITY_ID, $availabilityId, $comparison);
    }

    /**
     * Filter the query on the hierarchy column
     *
     * Example usage:
     * <code>
     * $query->filterByHierarchy(1234); // WHERE hierarchy = 1234
     * $query->filterByHierarchy(array(12, 34)); // WHERE hierarchy IN (12, 34)
     * $query->filterByHierarchy(array('min' => 12)); // WHERE hierarchy > 12
     * </code>
     *
     * @param     mixed $hierarchy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByHierarchy($hierarchy = null, $comparison = null)
    {
        if (is_array($hierarchy)) {
            $useMinMax = false;
            if (isset($hierarchy['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_HIERARCHY, $hierarchy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hierarchy['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_HIERARCHY, $hierarchy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_HIERARCHY, $hierarchy, $comparison);
    }

    /**
     * Filter the query on the package_size column
     *
     * Example usage:
     * <code>
     * $query->filterByPackageSize(1234); // WHERE package_size = 1234
     * $query->filterByPackageSize(array(12, 34)); // WHERE package_size IN (12, 34)
     * $query->filterByPackageSize(array('min' => 12)); // WHERE package_size > 12
     * </code>
     *
     * @param     mixed $packageSize The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByPackageSize($packageSize = null, $comparison = null)
    {
        if (is_array($packageSize)) {
            $useMinMax = false;
            if (isset($packageSize['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_PACKAGE_SIZE, $packageSize['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($packageSize['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_PACKAGE_SIZE, $packageSize['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_PACKAGE_SIZE, $packageSize, $comparison);
    }

    /**
     * Filter the query on the disable_at_stock_enabled column
     *
     * Example usage:
     * <code>
     * $query->filterByDisableAtStockEnabled(1234); // WHERE disable_at_stock_enabled = 1234
     * $query->filterByDisableAtStockEnabled(array(12, 34)); // WHERE disable_at_stock_enabled IN (12, 34)
     * $query->filterByDisableAtStockEnabled(array('min' => 12)); // WHERE disable_at_stock_enabled > 12
     * </code>
     *
     * @param     mixed $disableAtStockEnabled The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByDisableAtStockEnabled($disableAtStockEnabled = null, $comparison = null)
    {
        if (is_array($disableAtStockEnabled)) {
            $useMinMax = false;
            if (isset($disableAtStockEnabled['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED, $disableAtStockEnabled['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($disableAtStockEnabled['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED, $disableAtStockEnabled['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED, $disableAtStockEnabled, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ProductTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ProductTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Availability\Model\ORM\Availability object
     *
     * @param \Gekosale\Plugin\Availability\Model\ORM\Availability|ObjectCollection $availability The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByAvailability($availability, $comparison = null)
    {
        if ($availability instanceof \Gekosale\Plugin\Availability\Model\ORM\Availability) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_AVAILABILITY_ID, $availability->getId(), $comparison);
        } elseif ($availability instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_AVAILABILITY_ID, $availability->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\Currency object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\Currency|ObjectCollection $currency The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByCurrencyRelatedByBuyCurrencyId($currency, $comparison = null)
    {
        if ($currency instanceof \Gekosale\Plugin\Currency\Model\ORM\Currency) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_BUY_CURRENCY_ID, $currency->getId(), $comparison);
        } elseif ($currency instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_BUY_CURRENCY_ID, $currency->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCurrencyRelatedByBuyCurrencyId() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\Currency or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CurrencyRelatedByBuyCurrencyId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinCurrencyRelatedByBuyCurrencyId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CurrencyRelatedByBuyCurrencyId');

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
            $this->addJoinObject($join, 'CurrencyRelatedByBuyCurrencyId');
        }

        return $this;
    }

    /**
     * Use the CurrencyRelatedByBuyCurrencyId relation Currency object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyRelatedByBuyCurrencyIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCurrencyRelatedByBuyCurrencyId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CurrencyRelatedByBuyCurrencyId', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Producer\Model\ORM\Producer object
     *
     * @param \Gekosale\Plugin\Producer\Model\ORM\Producer|ObjectCollection $producer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProducer($producer, $comparison = null)
    {
        if ($producer instanceof \Gekosale\Plugin\Producer\Model\ORM\Producer) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_PRODUCER_ID, $producer->getId(), $comparison);
        } elseif ($producer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_PRODUCER_ID, $producer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProducer() only accepts arguments of type \Gekosale\Plugin\Producer\Model\ORM\Producer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Producer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProducer($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Producer');

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
            $this->addJoinObject($join, 'Producer');
        }

        return $this;
    }

    /**
     * Use the Producer relation Producer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Producer\Model\ORM\ProducerQuery A secondary query class using the current class as primary query
     */
    public function useProducerQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProducer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Producer', '\Gekosale\Plugin\Producer\Model\ORM\ProducerQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\Currency object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\Currency|ObjectCollection $currency The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByCurrencyRelatedBySellCurrencyId($currency, $comparison = null)
    {
        if ($currency instanceof \Gekosale\Plugin\Currency\Model\ORM\Currency) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_SELL_CURRENCY_ID, $currency->getId(), $comparison);
        } elseif ($currency instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_SELL_CURRENCY_ID, $currency->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCurrencyRelatedBySellCurrencyId() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\Currency or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CurrencyRelatedBySellCurrencyId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinCurrencyRelatedBySellCurrencyId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CurrencyRelatedBySellCurrencyId');

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
            $this->addJoinObject($join, 'CurrencyRelatedBySellCurrencyId');
        }

        return $this;
    }

    /**
     * Use the CurrencyRelatedBySellCurrencyId relation Currency object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyRelatedBySellCurrencyIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCurrencyRelatedBySellCurrencyId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CurrencyRelatedBySellCurrencyId', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasure object
     *
     * @param \Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasure|ObjectCollection $unitMeasure The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByUnitMeasure($unitMeasure, $comparison = null)
    {
        if ($unitMeasure instanceof \Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasure) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_UNIT_MEASURE_ID, $unitMeasure->getId(), $comparison);
        } elseif ($unitMeasure instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_UNIT_MEASURE_ID, $unitMeasure->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUnitMeasure() only accepts arguments of type \Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasure or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UnitMeasure relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinUnitMeasure($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UnitMeasure');

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
            $this->addJoinObject($join, 'UnitMeasure');
        }

        return $this;
    }

    /**
     * Use the UnitMeasure relation UnitMeasure object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasureQuery A secondary query class using the current class as primary query
     */
    public function useUnitMeasureQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUnitMeasure($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UnitMeasure', '\Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasureQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Vat\Model\ORM\Vat object
     *
     * @param \Gekosale\Plugin\Vat\Model\ORM\Vat|ObjectCollection $vat The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByVat($vat, $comparison = null)
    {
        if ($vat instanceof \Gekosale\Plugin\Vat\Model\ORM\Vat) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_VAT_ID, $vat->getId(), $comparison);
        } elseif ($vat instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_VAT_ID, $vat->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildProductQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet|ObjectCollection $technicalDataSet The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByTechnicalDataSet($technicalDataSet, $comparison = null)
    {
        if ($technicalDataSet instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSet->getId(), $comparison);
        } elseif ($technicalDataSet instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductTableMap::COL_TECHNICAL_DATA_SET_ID, $technicalDataSet->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTechnicalDataSet() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TechnicalDataSet relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinTechnicalDataSet($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TechnicalDataSet');

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
            $this->addJoinObject($join, 'TechnicalDataSet');
        }

        return $this;
    }

    /**
     * Use the TechnicalDataSet relation TechnicalDataSet object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetQuery A secondary query class using the current class as primary query
     */
    public function useTechnicalDataSetQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTechnicalDataSet($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TechnicalDataSet', '\Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct object
     *
     * @param \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct|ObjectCollection $missingCartProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByMissingCartProduct($missingCartProduct, $comparison = null)
    {
        if ($missingCartProduct instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $missingCartProduct->getProductId(), $comparison);
        } elseif ($missingCartProduct instanceof ObjectCollection) {
            return $this
                ->useMissingCartProductQuery()
                ->filterByPrimaryKeys($missingCartProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMissingCartProduct() only accepts arguments of type \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MissingCartProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinMissingCartProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MissingCartProduct');

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
            $this->addJoinObject($join, 'MissingCartProduct');
        }

        return $this;
    }

    /**
     * Use the MissingCartProduct relation MissingCartProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery A secondary query class using the current class as primary query
     */
    public function useMissingCartProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMissingCartProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MissingCartProduct', '\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderProduct object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderProduct|ObjectCollection $orderProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByOrderProduct($orderProduct, $comparison = null)
    {
        if ($orderProduct instanceof \Gekosale\Plugin\Order\Model\ORM\OrderProduct) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $orderProduct->getProductId(), $comparison);
        } elseif ($orderProduct instanceof ObjectCollection) {
            return $this
                ->useOrderProductQuery()
                ->filterByPrimaryKeys($orderProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderProduct() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinOrderProduct($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderProduct');

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
            $this->addJoinObject($join, 'OrderProduct');
        }

        return $this;
    }

    /**
     * Use the OrderProduct relation OrderProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProductQuery A secondary query class using the current class as primary query
     */
    public function useOrderProductQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinOrderProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderProduct', '\Gekosale\Plugin\Order\Model\ORM\OrderProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute|ObjectCollection $productAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductAttribute($productAttribute, $comparison = null)
    {
        if ($productAttribute instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productAttribute->getProductId(), $comparison);
        } elseif ($productAttribute instanceof ObjectCollection) {
            return $this
                ->useProductAttributeQuery()
                ->filterByPrimaryKeys($productAttribute->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductAttribute() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductAttribute($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductAttribute');

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
            $this->addJoinObject($join, 'ProductAttribute');
        }

        return $this;
    }

    /**
     * Use the ProductAttribute relation ProductAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery A secondary query class using the current class as primary query
     */
    public function useProductAttributeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAttribute', '\Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductCategory object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductCategory|ObjectCollection $productCategory  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductCategory($productCategory, $comparison = null)
    {
        if ($productCategory instanceof \Gekosale\Plugin\Product\Model\ORM\ProductCategory) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productCategory->getProductId(), $comparison);
        } elseif ($productCategory instanceof ObjectCollection) {
            return $this
                ->useProductCategoryQuery()
                ->filterByPrimaryKeys($productCategory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductCategory() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductCategory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductCategory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductCategory');

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
            $this->addJoinObject($join, 'ProductCategory');
        }

        return $this;
    }

    /**
     * Use the ProductCategory relation ProductCategory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery A secondary query class using the current class as primary query
     */
    public function useProductCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductCategory', '\Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct object
     *
     * @param \Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct|ObjectCollection $delivererProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByDelivererProduct($delivererProduct, $comparison = null)
    {
        if ($delivererProduct instanceof \Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $delivererProduct->getProductId(), $comparison);
        } elseif ($delivererProduct instanceof ObjectCollection) {
            return $this
                ->useDelivererProductQuery()
                ->filterByPrimaryKeys($delivererProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDelivererProduct() only accepts arguments of type \Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DelivererProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinDelivererProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DelivererProduct');

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
            $this->addJoinObject($join, 'DelivererProduct');
        }

        return $this;
    }

    /**
     * Use the DelivererProduct relation DelivererProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Deliverer\Model\ORM\DelivererProductQuery A secondary query class using the current class as primary query
     */
    public function useDelivererProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDelivererProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DelivererProduct', '\Gekosale\Plugin\Deliverer\Model\ORM\DelivererProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductFile object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductFile|ObjectCollection $productFile  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductFile($productFile, $comparison = null)
    {
        if ($productFile instanceof \Gekosale\Plugin\Product\Model\ORM\ProductFile) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productFile->getProductId(), $comparison);
        } elseif ($productFile instanceof ObjectCollection) {
            return $this
                ->useProductFileQuery()
                ->filterByPrimaryKeys($productFile->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductFile() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductFile or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductFile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductFile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductFile');

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
            $this->addJoinObject($join, 'ProductFile');
        }

        return $this;
    }

    /**
     * Use the ProductFile relation ProductFile object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductFileQuery A secondary query class using the current class as primary query
     */
    public function useProductFileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductFile', '\Gekosale\Plugin\Product\Model\ORM\ProductFileQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice object
     *
     * @param \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice|ObjectCollection $productGroupPrice  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductGroupPrice($productGroupPrice, $comparison = null)
    {
        if ($productGroupPrice instanceof \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productGroupPrice->getProductId(), $comparison);
        } elseif ($productGroupPrice instanceof ObjectCollection) {
            return $this
                ->useProductGroupPriceQuery()
                ->filterByPrimaryKeys($productGroupPrice->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductGroupPrice() only accepts arguments of type \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductGroupPrice relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductGroupPrice($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductGroupPrice');

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
            $this->addJoinObject($join, 'ProductGroupPrice');
        }

        return $this;
    }

    /**
     * Use the ProductGroupPrice relation ProductGroupPrice object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery A secondary query class using the current class as primary query
     */
    public function useProductGroupPriceQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductGroupPrice($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductGroupPrice', '\Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\ProductNew\Model\ORM\ProductNew object
     *
     * @param \Gekosale\Plugin\ProductNew\Model\ORM\ProductNew|ObjectCollection $productNew  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductNew($productNew, $comparison = null)
    {
        if ($productNew instanceof \Gekosale\Plugin\ProductNew\Model\ORM\ProductNew) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productNew->getProductId(), $comparison);
        } elseif ($productNew instanceof ObjectCollection) {
            return $this
                ->useProductNewQuery()
                ->filterByPrimaryKeys($productNew->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductNew() only accepts arguments of type \Gekosale\Plugin\ProductNew\Model\ORM\ProductNew or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductNew relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductNew($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductNew');

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
            $this->addJoinObject($join, 'ProductNew');
        }

        return $this;
    }

    /**
     * Use the ProductNew relation ProductNew object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\ProductNew\Model\ORM\ProductNewQuery A secondary query class using the current class as primary query
     */
    public function useProductNewQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductNew($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductNew', '\Gekosale\Plugin\ProductNew\Model\ORM\ProductNewQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductPhoto object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductPhoto|ObjectCollection $productPhoto  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductPhoto($productPhoto, $comparison = null)
    {
        if ($productPhoto instanceof \Gekosale\Plugin\Product\Model\ORM\ProductPhoto) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productPhoto->getProductId(), $comparison);
        } elseif ($productPhoto instanceof ObjectCollection) {
            return $this
                ->useProductPhotoQuery()
                ->filterByPrimaryKeys($productPhoto->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductPhoto() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductPhoto or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductPhoto relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductPhoto($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductPhoto');

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
            $this->addJoinObject($join, 'ProductPhoto');
        }

        return $this;
    }

    /**
     * Use the ProductPhoto relation ProductPhoto object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery A secondary query class using the current class as primary query
     */
    public function useProductPhotoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductPhoto($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductPhoto', '\Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell object
     *
     * @param \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell|ObjectCollection $crosssell  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByCrosssellRelatedByProductId($crosssell, $comparison = null)
    {
        if ($crosssell instanceof \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $crosssell->getProductId(), $comparison);
        } elseif ($crosssell instanceof ObjectCollection) {
            return $this
                ->useCrosssellRelatedByProductIdQuery()
                ->filterByPrimaryKeys($crosssell->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCrosssellRelatedByProductId() only accepts arguments of type \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CrosssellRelatedByProductId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinCrosssellRelatedByProductId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CrosssellRelatedByProductId');

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
            $this->addJoinObject($join, 'CrosssellRelatedByProductId');
        }

        return $this;
    }

    /**
     * Use the CrosssellRelatedByProductId relation Crosssell object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery A secondary query class using the current class as primary query
     */
    public function useCrosssellRelatedByProductIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCrosssellRelatedByProductId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CrosssellRelatedByProductId', '\Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell object
     *
     * @param \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell|ObjectCollection $crosssell  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByCrosssellRelatedByRelatedProductId($crosssell, $comparison = null)
    {
        if ($crosssell instanceof \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $crosssell->getRelatedProductId(), $comparison);
        } elseif ($crosssell instanceof ObjectCollection) {
            return $this
                ->useCrosssellRelatedByRelatedProductIdQuery()
                ->filterByPrimaryKeys($crosssell->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCrosssellRelatedByRelatedProductId() only accepts arguments of type \Gekosale\Plugin\Crosssell\Model\ORM\Crosssell or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CrosssellRelatedByRelatedProductId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinCrosssellRelatedByRelatedProductId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CrosssellRelatedByRelatedProductId');

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
            $this->addJoinObject($join, 'CrosssellRelatedByRelatedProductId');
        }

        return $this;
    }

    /**
     * Use the CrosssellRelatedByRelatedProductId relation Crosssell object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery A secondary query class using the current class as primary query
     */
    public function useCrosssellRelatedByRelatedProductIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCrosssellRelatedByRelatedProductId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CrosssellRelatedByRelatedProductId', '\Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Similar\Model\ORM\Similar object
     *
     * @param \Gekosale\Plugin\Similar\Model\ORM\Similar|ObjectCollection $similar  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterBySimilarRelatedByProductId($similar, $comparison = null)
    {
        if ($similar instanceof \Gekosale\Plugin\Similar\Model\ORM\Similar) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $similar->getProductId(), $comparison);
        } elseif ($similar instanceof ObjectCollection) {
            return $this
                ->useSimilarRelatedByProductIdQuery()
                ->filterByPrimaryKeys($similar->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySimilarRelatedByProductId() only accepts arguments of type \Gekosale\Plugin\Similar\Model\ORM\Similar or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SimilarRelatedByProductId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinSimilarRelatedByProductId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SimilarRelatedByProductId');

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
            $this->addJoinObject($join, 'SimilarRelatedByProductId');
        }

        return $this;
    }

    /**
     * Use the SimilarRelatedByProductId relation Similar object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Similar\Model\ORM\SimilarQuery A secondary query class using the current class as primary query
     */
    public function useSimilarRelatedByProductIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSimilarRelatedByProductId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SimilarRelatedByProductId', '\Gekosale\Plugin\Similar\Model\ORM\SimilarQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Similar\Model\ORM\Similar object
     *
     * @param \Gekosale\Plugin\Similar\Model\ORM\Similar|ObjectCollection $similar  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterBySimilarRelatedByRelatedProductId($similar, $comparison = null)
    {
        if ($similar instanceof \Gekosale\Plugin\Similar\Model\ORM\Similar) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $similar->getRelatedProductId(), $comparison);
        } elseif ($similar instanceof ObjectCollection) {
            return $this
                ->useSimilarRelatedByRelatedProductIdQuery()
                ->filterByPrimaryKeys($similar->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySimilarRelatedByRelatedProductId() only accepts arguments of type \Gekosale\Plugin\Similar\Model\ORM\Similar or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SimilarRelatedByRelatedProductId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinSimilarRelatedByRelatedProductId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SimilarRelatedByRelatedProductId');

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
            $this->addJoinObject($join, 'SimilarRelatedByRelatedProductId');
        }

        return $this;
    }

    /**
     * Use the SimilarRelatedByRelatedProductId relation Similar object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Similar\Model\ORM\SimilarQuery A secondary query class using the current class as primary query
     */
    public function useSimilarRelatedByRelatedProductIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSimilarRelatedByRelatedProductId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SimilarRelatedByRelatedProductId', '\Gekosale\Plugin\Similar\Model\ORM\SimilarQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Upsell\Model\ORM\Upsell object
     *
     * @param \Gekosale\Plugin\Upsell\Model\ORM\Upsell|ObjectCollection $upsell  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByUpsellRelatedByProductId($upsell, $comparison = null)
    {
        if ($upsell instanceof \Gekosale\Plugin\Upsell\Model\ORM\Upsell) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $upsell->getProductId(), $comparison);
        } elseif ($upsell instanceof ObjectCollection) {
            return $this
                ->useUpsellRelatedByProductIdQuery()
                ->filterByPrimaryKeys($upsell->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUpsellRelatedByProductId() only accepts arguments of type \Gekosale\Plugin\Upsell\Model\ORM\Upsell or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UpsellRelatedByProductId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinUpsellRelatedByProductId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UpsellRelatedByProductId');

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
            $this->addJoinObject($join, 'UpsellRelatedByProductId');
        }

        return $this;
    }

    /**
     * Use the UpsellRelatedByProductId relation Upsell object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery A secondary query class using the current class as primary query
     */
    public function useUpsellRelatedByProductIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUpsellRelatedByProductId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UpsellRelatedByProductId', '\Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Upsell\Model\ORM\Upsell object
     *
     * @param \Gekosale\Plugin\Upsell\Model\ORM\Upsell|ObjectCollection $upsell  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByUpsellRelatedByRelatedProductId($upsell, $comparison = null)
    {
        if ($upsell instanceof \Gekosale\Plugin\Upsell\Model\ORM\Upsell) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $upsell->getRelatedProductId(), $comparison);
        } elseif ($upsell instanceof ObjectCollection) {
            return $this
                ->useUpsellRelatedByRelatedProductIdQuery()
                ->filterByPrimaryKeys($upsell->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUpsellRelatedByRelatedProductId() only accepts arguments of type \Gekosale\Plugin\Upsell\Model\ORM\Upsell or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UpsellRelatedByRelatedProductId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinUpsellRelatedByRelatedProductId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UpsellRelatedByRelatedProductId');

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
            $this->addJoinObject($join, 'UpsellRelatedByRelatedProductId');
        }

        return $this;
    }

    /**
     * Use the UpsellRelatedByRelatedProductId relation Upsell object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery A secondary query class using the current class as primary query
     */
    public function useUpsellRelatedByRelatedProductIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUpsellRelatedByRelatedProductId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UpsellRelatedByRelatedProductId', '\Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup object
     *
     * @param \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup|ObjectCollection $productTechnicalDataGroup  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductTechnicalDataGroup($productTechnicalDataGroup, $comparison = null)
    {
        if ($productTechnicalDataGroup instanceof \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productTechnicalDataGroup->getProductId(), $comparison);
        } elseif ($productTechnicalDataGroup instanceof ObjectCollection) {
            return $this
                ->useProductTechnicalDataGroupQuery()
                ->filterByPrimaryKeys($productTechnicalDataGroup->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductTechnicalDataGroup() only accepts arguments of type \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductTechnicalDataGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductTechnicalDataGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductTechnicalDataGroup');

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
            $this->addJoinObject($join, 'ProductTechnicalDataGroup');
        }

        return $this;
    }

    /**
     * Use the ProductTechnicalDataGroup relation ProductTechnicalDataGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupQuery A secondary query class using the current class as primary query
     */
    public function useProductTechnicalDataGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductTechnicalDataGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductTechnicalDataGroup', '\Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist object
     *
     * @param \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist|ObjectCollection $wishlist  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByWishlist($wishlist, $comparison = null)
    {
        if ($wishlist instanceof \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $wishlist->getProductId(), $comparison);
        } elseif ($wishlist instanceof ObjectCollection) {
            return $this
                ->useWishlistQuery()
                ->filterByPrimaryKeys($wishlist->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByWishlist() only accepts arguments of type \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Wishlist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinWishlist($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Wishlist');

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
            $this->addJoinObject($join, 'Wishlist');
        }

        return $this;
    }

    /**
     * Use the Wishlist relation Wishlist object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery A secondary query class using the current class as primary query
     */
    public function useWishlistQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWishlist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Wishlist', '\Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductI18n object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductI18n|ObjectCollection $productI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function filterByProductI18n($productI18n, $comparison = null)
    {
        if ($productI18n instanceof \Gekosale\Plugin\Product\Model\ORM\ProductI18n) {
            return $this
                ->addUsingAlias(ProductTableMap::COL_ID, $productI18n->getId(), $comparison);
        } elseif ($productI18n instanceof ObjectCollection) {
            return $this
                ->useProductI18nQuery()
                ->filterByPrimaryKeys($productI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductI18n() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function joinProductI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductI18n');

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
            $this->addJoinObject($join, 'ProductI18n');
        }

        return $this;
    }

    /**
     * Use the ProductI18n relation ProductI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductI18nQuery A secondary query class using the current class as primary query
     */
    public function useProductI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinProductI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductI18n', '\Gekosale\Plugin\Product\Model\ORM\ProductI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProduct $product Object to remove from the list of results
     *
     * @return ChildProductQuery The current query, for fluid interface
     */
    public function prune($product = null)
    {
        if ($product) {
            $this->addUsingAlias(ProductTableMap::COL_ID, $product->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
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
            ProductTableMap::clearInstancePool();
            ProductTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProductTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProductTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProductTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior
    
    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildProductQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'ProductI18n';
    
        return $this
            ->joinProductI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildProductQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('ProductI18n');
        $this->with['ProductI18n']->setIsWithOneToMany(false);
    
        return $this;
    }
    
    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildProductI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductI18n', '\Gekosale\Plugin\Product\Model\ORM\ProductI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildProductQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ProductTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildProductQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ProductTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildProductQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProductTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildProductQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProductTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildProductQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProductTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildProductQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProductTableMap::COL_CREATED_AT);
    }

} // ProductQuery
