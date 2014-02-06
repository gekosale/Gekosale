<?php

namespace Gekosale\Plugin\Shop\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Blog\Model\ORM\BlogShop;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop;
use Gekosale\Plugin\Category\Model\ORM\CategoryShop;
use Gekosale\Plugin\Client\Model\ORM\Client;
use Gekosale\Plugin\Company\Model\ORM\Company;
use Gekosale\Plugin\Contact\Model\ORM\Contact;
use Gekosale\Plugin\Contact\Model\ORM\ContactShop;
use Gekosale\Plugin\Currency\Model\ORM\Currency;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyShop;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop;
use Gekosale\Plugin\Locale\Model\ORM\LocaleShop;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCart;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups;
use Gekosale\Plugin\Order\Model\ORM\Order;
use Gekosale\Plugin\Page\Model\ORM\PageShop;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop;
use Gekosale\Plugin\Producer\Model\ORM\ProducerShop;
use Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases;
use Gekosale\Plugin\Shop\Model\ORM\Shop as ChildShop;
use Gekosale\Plugin\Shop\Model\ORM\ShopI18nQuery as ChildShopI18nQuery;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery as ChildShopQuery;
use Gekosale\Plugin\Shop\Model\ORM\Map\ShopTableMap;
use Gekosale\Plugin\User\Model\ORM\UserGroupShop;
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
 * Base class that represents a query for the 'shop' table.
 *
 * 
 *
 * @method     ChildShopQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildShopQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     ChildShopQuery orderByCompanyId($order = Criteria::ASC) Order by the company_id column
 * @method     ChildShopQuery orderByPeriodId($order = Criteria::ASC) Order by the period_id column
 * @method     ChildShopQuery orderByWwwRedirection($order = Criteria::ASC) Order by the www_redirection column
 * @method     ChildShopQuery orderByTaxes($order = Criteria::ASC) Order by the taxes column
 * @method     ChildShopQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method     ChildShopQuery orderByFavicon($order = Criteria::ASC) Order by the favicon column
 * @method     ChildShopQuery orderByOffline($order = Criteria::ASC) Order by the offline column
 * @method     ChildShopQuery orderByOfflineText($order = Criteria::ASC) Order by the offline_text column
 * @method     ChildShopQuery orderByCartRedirect($order = Criteria::ASC) Order by the cart_redirect column
 * @method     ChildShopQuery orderByMinimumOrderValue($order = Criteria::ASC) Order by the minimum_order_value column
 * @method     ChildShopQuery orderByShowTax($order = Criteria::ASC) Order by the show_tax column
 * @method     ChildShopQuery orderByEnableOpinions($order = Criteria::ASC) Order by the enable_opinions column
 * @method     ChildShopQuery orderByEnableTags($order = Criteria::ASC) Order by the enable_tags column
 * @method     ChildShopQuery orderByCatalogMode($order = Criteria::ASC) Order by the catalog_mode column
 * @method     ChildShopQuery orderByForceLogin($order = Criteria::ASC) Order by the force_login column
 * @method     ChildShopQuery orderByEnableRss($order = Criteria::ASC) Order by the enable_rss column
 * @method     ChildShopQuery orderByInvoiceNumerationKind($order = Criteria::ASC) Order by the invoice_numeration_kind column
 * @method     ChildShopQuery orderByInvoiceDefaultPaymentDue($order = Criteria::ASC) Order by the invoice_default_payment_due column
 * @method     ChildShopQuery orderByConfirmRegistration($order = Criteria::ASC) Order by the confirm_registration column
 * @method     ChildShopQuery orderByEnableRegistration($order = Criteria::ASC) Order by the enable_registration column
 * @method     ChildShopQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildShopQuery orderByContactId($order = Criteria::ASC) Order by the contact_id column
 * @method     ChildShopQuery orderByDefaultVatId($order = Criteria::ASC) Order by the default_vat_id column
 * @method     ChildShopQuery orderByOrderStatusGroupsId($order = Criteria::ASC) Order by the order_status_groups_id column
 * @method     ChildShopQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildShopQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildShopQuery groupById() Group by the id column
 * @method     ChildShopQuery groupByUrl() Group by the url column
 * @method     ChildShopQuery groupByCompanyId() Group by the company_id column
 * @method     ChildShopQuery groupByPeriodId() Group by the period_id column
 * @method     ChildShopQuery groupByWwwRedirection() Group by the www_redirection column
 * @method     ChildShopQuery groupByTaxes() Group by the taxes column
 * @method     ChildShopQuery groupByPhotoId() Group by the photo_id column
 * @method     ChildShopQuery groupByFavicon() Group by the favicon column
 * @method     ChildShopQuery groupByOffline() Group by the offline column
 * @method     ChildShopQuery groupByOfflineText() Group by the offline_text column
 * @method     ChildShopQuery groupByCartRedirect() Group by the cart_redirect column
 * @method     ChildShopQuery groupByMinimumOrderValue() Group by the minimum_order_value column
 * @method     ChildShopQuery groupByShowTax() Group by the show_tax column
 * @method     ChildShopQuery groupByEnableOpinions() Group by the enable_opinions column
 * @method     ChildShopQuery groupByEnableTags() Group by the enable_tags column
 * @method     ChildShopQuery groupByCatalogMode() Group by the catalog_mode column
 * @method     ChildShopQuery groupByForceLogin() Group by the force_login column
 * @method     ChildShopQuery groupByEnableRss() Group by the enable_rss column
 * @method     ChildShopQuery groupByInvoiceNumerationKind() Group by the invoice_numeration_kind column
 * @method     ChildShopQuery groupByInvoiceDefaultPaymentDue() Group by the invoice_default_payment_due column
 * @method     ChildShopQuery groupByConfirmRegistration() Group by the confirm_registration column
 * @method     ChildShopQuery groupByEnableRegistration() Group by the enable_registration column
 * @method     ChildShopQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildShopQuery groupByContactId() Group by the contact_id column
 * @method     ChildShopQuery groupByDefaultVatId() Group by the default_vat_id column
 * @method     ChildShopQuery groupByOrderStatusGroupsId() Group by the order_status_groups_id column
 * @method     ChildShopQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildShopQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildShopQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildShopQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildShopQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildShopQuery leftJoinContact($relationAlias = null) Adds a LEFT JOIN clause to the query using the Contact relation
 * @method     ChildShopQuery rightJoinContact($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Contact relation
 * @method     ChildShopQuery innerJoinContact($relationAlias = null) Adds a INNER JOIN clause to the query using the Contact relation
 *
 * @method     ChildShopQuery leftJoinCurrency($relationAlias = null) Adds a LEFT JOIN clause to the query using the Currency relation
 * @method     ChildShopQuery rightJoinCurrency($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Currency relation
 * @method     ChildShopQuery innerJoinCurrency($relationAlias = null) Adds a INNER JOIN clause to the query using the Currency relation
 *
 * @method     ChildShopQuery leftJoinVat($relationAlias = null) Adds a LEFT JOIN clause to the query using the Vat relation
 * @method     ChildShopQuery rightJoinVat($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Vat relation
 * @method     ChildShopQuery innerJoinVat($relationAlias = null) Adds a INNER JOIN clause to the query using the Vat relation
 *
 * @method     ChildShopQuery leftJoinOrderStatusGroups($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderStatusGroups relation
 * @method     ChildShopQuery rightJoinOrderStatusGroups($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderStatusGroups relation
 * @method     ChildShopQuery innerJoinOrderStatusGroups($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderStatusGroups relation
 *
 * @method     ChildShopQuery leftJoinCompany($relationAlias = null) Adds a LEFT JOIN clause to the query using the Company relation
 * @method     ChildShopQuery rightJoinCompany($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Company relation
 * @method     ChildShopQuery innerJoinCompany($relationAlias = null) Adds a INNER JOIN clause to the query using the Company relation
 *
 * @method     ChildShopQuery leftJoinClient($relationAlias = null) Adds a LEFT JOIN clause to the query using the Client relation
 * @method     ChildShopQuery rightJoinClient($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Client relation
 * @method     ChildShopQuery innerJoinClient($relationAlias = null) Adds a INNER JOIN clause to the query using the Client relation
 *
 * @method     ChildShopQuery leftJoinMissingCart($relationAlias = null) Adds a LEFT JOIN clause to the query using the MissingCart relation
 * @method     ChildShopQuery rightJoinMissingCart($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MissingCart relation
 * @method     ChildShopQuery innerJoinMissingCart($relationAlias = null) Adds a INNER JOIN clause to the query using the MissingCart relation
 *
 * @method     ChildShopQuery leftJoinMissingCartProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the MissingCartProduct relation
 * @method     ChildShopQuery rightJoinMissingCartProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MissingCartProduct relation
 * @method     ChildShopQuery innerJoinMissingCartProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the MissingCartProduct relation
 *
 * @method     ChildShopQuery leftJoinOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the Order relation
 * @method     ChildShopQuery rightJoinOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Order relation
 * @method     ChildShopQuery innerJoinOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the Order relation
 *
 * @method     ChildShopQuery leftJoinProductSearchPhrases($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductSearchPhrases relation
 * @method     ChildShopQuery rightJoinProductSearchPhrases($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductSearchPhrases relation
 * @method     ChildShopQuery innerJoinProductSearchPhrases($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductSearchPhrases relation
 *
 * @method     ChildShopQuery leftJoinBlogShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the BlogShop relation
 * @method     ChildShopQuery rightJoinBlogShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BlogShop relation
 * @method     ChildShopQuery innerJoinBlogShop($relationAlias = null) Adds a INNER JOIN clause to the query using the BlogShop relation
 *
 * @method     ChildShopQuery leftJoinCartRuleShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRuleShop relation
 * @method     ChildShopQuery rightJoinCartRuleShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRuleShop relation
 * @method     ChildShopQuery innerJoinCartRuleShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRuleShop relation
 *
 * @method     ChildShopQuery leftJoinCategoryShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryShop relation
 * @method     ChildShopQuery rightJoinCategoryShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryShop relation
 * @method     ChildShopQuery innerJoinCategoryShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryShop relation
 *
 * @method     ChildShopQuery leftJoinPageShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageShop relation
 * @method     ChildShopQuery rightJoinPageShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageShop relation
 * @method     ChildShopQuery innerJoinPageShop($relationAlias = null) Adds a INNER JOIN clause to the query using the PageShop relation
 *
 * @method     ChildShopQuery leftJoinContactShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the ContactShop relation
 * @method     ChildShopQuery rightJoinContactShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ContactShop relation
 * @method     ChildShopQuery innerJoinContactShop($relationAlias = null) Adds a INNER JOIN clause to the query using the ContactShop relation
 *
 * @method     ChildShopQuery leftJoinCurrencyShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CurrencyShop relation
 * @method     ChildShopQuery rightJoinCurrencyShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CurrencyShop relation
 * @method     ChildShopQuery innerJoinCurrencyShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CurrencyShop relation
 *
 * @method     ChildShopQuery leftJoinDispatchMethodShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodShop relation
 * @method     ChildShopQuery rightJoinDispatchMethodShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodShop relation
 * @method     ChildShopQuery innerJoinDispatchMethodShop($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodShop relation
 *
 * @method     ChildShopQuery leftJoinLocaleShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the LocaleShop relation
 * @method     ChildShopQuery rightJoinLocaleShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LocaleShop relation
 * @method     ChildShopQuery innerJoinLocaleShop($relationAlias = null) Adds a INNER JOIN clause to the query using the LocaleShop relation
 *
 * @method     ChildShopQuery leftJoinPaymentMethodShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the PaymentMethodShop relation
 * @method     ChildShopQuery rightJoinPaymentMethodShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PaymentMethodShop relation
 * @method     ChildShopQuery innerJoinPaymentMethodShop($relationAlias = null) Adds a INNER JOIN clause to the query using the PaymentMethodShop relation
 *
 * @method     ChildShopQuery leftJoinProducerShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProducerShop relation
 * @method     ChildShopQuery rightJoinProducerShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProducerShop relation
 * @method     ChildShopQuery innerJoinProducerShop($relationAlias = null) Adds a INNER JOIN clause to the query using the ProducerShop relation
 *
 * @method     ChildShopQuery leftJoinUserGroupShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserGroupShop relation
 * @method     ChildShopQuery rightJoinUserGroupShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserGroupShop relation
 * @method     ChildShopQuery innerJoinUserGroupShop($relationAlias = null) Adds a INNER JOIN clause to the query using the UserGroupShop relation
 *
 * @method     ChildShopQuery leftJoinWishlist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Wishlist relation
 * @method     ChildShopQuery rightJoinWishlist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Wishlist relation
 * @method     ChildShopQuery innerJoinWishlist($relationAlias = null) Adds a INNER JOIN clause to the query using the Wishlist relation
 *
 * @method     ChildShopQuery leftJoinShopI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the ShopI18n relation
 * @method     ChildShopQuery rightJoinShopI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ShopI18n relation
 * @method     ChildShopQuery innerJoinShopI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the ShopI18n relation
 *
 * @method     ChildShop findOne(ConnectionInterface $con = null) Return the first ChildShop matching the query
 * @method     ChildShop findOneOrCreate(ConnectionInterface $con = null) Return the first ChildShop matching the query, or a new ChildShop object populated from the query conditions when no match is found
 *
 * @method     ChildShop findOneById(int $id) Return the first ChildShop filtered by the id column
 * @method     ChildShop findOneByUrl(string $url) Return the first ChildShop filtered by the url column
 * @method     ChildShop findOneByCompanyId(int $company_id) Return the first ChildShop filtered by the company_id column
 * @method     ChildShop findOneByPeriodId(int $period_id) Return the first ChildShop filtered by the period_id column
 * @method     ChildShop findOneByWwwRedirection(int $www_redirection) Return the first ChildShop filtered by the www_redirection column
 * @method     ChildShop findOneByTaxes(int $taxes) Return the first ChildShop filtered by the taxes column
 * @method     ChildShop findOneByPhotoId(string $photo_id) Return the first ChildShop filtered by the photo_id column
 * @method     ChildShop findOneByFavicon(string $favicon) Return the first ChildShop filtered by the favicon column
 * @method     ChildShop findOneByOffline(int $offline) Return the first ChildShop filtered by the offline column
 * @method     ChildShop findOneByOfflineText(string $offline_text) Return the first ChildShop filtered by the offline_text column
 * @method     ChildShop findOneByCartRedirect(int $cart_redirect) Return the first ChildShop filtered by the cart_redirect column
 * @method     ChildShop findOneByMinimumOrderValue(string $minimum_order_value) Return the first ChildShop filtered by the minimum_order_value column
 * @method     ChildShop findOneByShowTax(int $show_tax) Return the first ChildShop filtered by the show_tax column
 * @method     ChildShop findOneByEnableOpinions(int $enable_opinions) Return the first ChildShop filtered by the enable_opinions column
 * @method     ChildShop findOneByEnableTags(int $enable_tags) Return the first ChildShop filtered by the enable_tags column
 * @method     ChildShop findOneByCatalogMode(int $catalog_mode) Return the first ChildShop filtered by the catalog_mode column
 * @method     ChildShop findOneByForceLogin(int $force_login) Return the first ChildShop filtered by the force_login column
 * @method     ChildShop findOneByEnableRss(int $enable_rss) Return the first ChildShop filtered by the enable_rss column
 * @method     ChildShop findOneByInvoiceNumerationKind(string $invoice_numeration_kind) Return the first ChildShop filtered by the invoice_numeration_kind column
 * @method     ChildShop findOneByInvoiceDefaultPaymentDue(int $invoice_default_payment_due) Return the first ChildShop filtered by the invoice_default_payment_due column
 * @method     ChildShop findOneByConfirmRegistration(boolean $confirm_registration) Return the first ChildShop filtered by the confirm_registration column
 * @method     ChildShop findOneByEnableRegistration(boolean $enable_registration) Return the first ChildShop filtered by the enable_registration column
 * @method     ChildShop findOneByCurrencyId(int $currency_id) Return the first ChildShop filtered by the currency_id column
 * @method     ChildShop findOneByContactId(int $contact_id) Return the first ChildShop filtered by the contact_id column
 * @method     ChildShop findOneByDefaultVatId(int $default_vat_id) Return the first ChildShop filtered by the default_vat_id column
 * @method     ChildShop findOneByOrderStatusGroupsId(int $order_status_groups_id) Return the first ChildShop filtered by the order_status_groups_id column
 * @method     ChildShop findOneByCreatedAt(string $created_at) Return the first ChildShop filtered by the created_at column
 * @method     ChildShop findOneByUpdatedAt(string $updated_at) Return the first ChildShop filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildShop objects filtered by the id column
 * @method     array findByUrl(string $url) Return ChildShop objects filtered by the url column
 * @method     array findByCompanyId(int $company_id) Return ChildShop objects filtered by the company_id column
 * @method     array findByPeriodId(int $period_id) Return ChildShop objects filtered by the period_id column
 * @method     array findByWwwRedirection(int $www_redirection) Return ChildShop objects filtered by the www_redirection column
 * @method     array findByTaxes(int $taxes) Return ChildShop objects filtered by the taxes column
 * @method     array findByPhotoId(string $photo_id) Return ChildShop objects filtered by the photo_id column
 * @method     array findByFavicon(string $favicon) Return ChildShop objects filtered by the favicon column
 * @method     array findByOffline(int $offline) Return ChildShop objects filtered by the offline column
 * @method     array findByOfflineText(string $offline_text) Return ChildShop objects filtered by the offline_text column
 * @method     array findByCartRedirect(int $cart_redirect) Return ChildShop objects filtered by the cart_redirect column
 * @method     array findByMinimumOrderValue(string $minimum_order_value) Return ChildShop objects filtered by the minimum_order_value column
 * @method     array findByShowTax(int $show_tax) Return ChildShop objects filtered by the show_tax column
 * @method     array findByEnableOpinions(int $enable_opinions) Return ChildShop objects filtered by the enable_opinions column
 * @method     array findByEnableTags(int $enable_tags) Return ChildShop objects filtered by the enable_tags column
 * @method     array findByCatalogMode(int $catalog_mode) Return ChildShop objects filtered by the catalog_mode column
 * @method     array findByForceLogin(int $force_login) Return ChildShop objects filtered by the force_login column
 * @method     array findByEnableRss(int $enable_rss) Return ChildShop objects filtered by the enable_rss column
 * @method     array findByInvoiceNumerationKind(string $invoice_numeration_kind) Return ChildShop objects filtered by the invoice_numeration_kind column
 * @method     array findByInvoiceDefaultPaymentDue(int $invoice_default_payment_due) Return ChildShop objects filtered by the invoice_default_payment_due column
 * @method     array findByConfirmRegistration(boolean $confirm_registration) Return ChildShop objects filtered by the confirm_registration column
 * @method     array findByEnableRegistration(boolean $enable_registration) Return ChildShop objects filtered by the enable_registration column
 * @method     array findByCurrencyId(int $currency_id) Return ChildShop objects filtered by the currency_id column
 * @method     array findByContactId(int $contact_id) Return ChildShop objects filtered by the contact_id column
 * @method     array findByDefaultVatId(int $default_vat_id) Return ChildShop objects filtered by the default_vat_id column
 * @method     array findByOrderStatusGroupsId(int $order_status_groups_id) Return ChildShop objects filtered by the order_status_groups_id column
 * @method     array findByCreatedAt(string $created_at) Return ChildShop objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildShop objects filtered by the updated_at column
 *
 */
abstract class ShopQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Shop\Model\ORM\Base\ShopQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildShopQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildShopQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Shop\Model\ORM\ShopQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Shop\Model\ORM\ShopQuery();
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
     * @return ChildShop|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ShopTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ShopTableMap::DATABASE_NAME);
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
     * @return   ChildShop A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, URL, COMPANY_ID, PERIOD_ID, WWW_REDIRECTION, TAXES, PHOTO_ID, FAVICON, OFFLINE, OFFLINE_TEXT, CART_REDIRECT, MINIMUM_ORDER_VALUE, SHOW_TAX, ENABLE_OPINIONS, ENABLE_TAGS, CATALOG_MODE, FORCE_LOGIN, ENABLE_RSS, INVOICE_NUMERATION_KIND, INVOICE_DEFAULT_PAYMENT_DUE, CONFIRM_REGISTRATION, ENABLE_REGISTRATION, CURRENCY_ID, CONTACT_ID, DEFAULT_VAT_ID, ORDER_STATUS_GROUPS_ID, CREATED_AT, UPDATED_AT FROM shop WHERE ID = :p0';
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
            $obj = new ChildShop();
            $obj->hydrate($row);
            ShopTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildShop|array|mixed the result, formatted by the current formatter
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ShopTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ShopTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $url)) {
                $url = str_replace('*', '%', $url);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_URL, $url, $comparison);
    }

    /**
     * Filter the query on the company_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCompanyId(1234); // WHERE company_id = 1234
     * $query->filterByCompanyId(array(12, 34)); // WHERE company_id IN (12, 34)
     * $query->filterByCompanyId(array('min' => 12)); // WHERE company_id > 12
     * </code>
     *
     * @see       filterByCompany()
     *
     * @param     mixed $companyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCompanyId($companyId = null, $comparison = null)
    {
        if (is_array($companyId)) {
            $useMinMax = false;
            if (isset($companyId['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_COMPANY_ID, $companyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($companyId['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_COMPANY_ID, $companyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_COMPANY_ID, $companyId, $comparison);
    }

    /**
     * Filter the query on the period_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPeriodId(1234); // WHERE period_id = 1234
     * $query->filterByPeriodId(array(12, 34)); // WHERE period_id IN (12, 34)
     * $query->filterByPeriodId(array('min' => 12)); // WHERE period_id > 12
     * </code>
     *
     * @param     mixed $periodId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByPeriodId($periodId = null, $comparison = null)
    {
        if (is_array($periodId)) {
            $useMinMax = false;
            if (isset($periodId['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_PERIOD_ID, $periodId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($periodId['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_PERIOD_ID, $periodId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_PERIOD_ID, $periodId, $comparison);
    }

    /**
     * Filter the query on the www_redirection column
     *
     * Example usage:
     * <code>
     * $query->filterByWwwRedirection(1234); // WHERE www_redirection = 1234
     * $query->filterByWwwRedirection(array(12, 34)); // WHERE www_redirection IN (12, 34)
     * $query->filterByWwwRedirection(array('min' => 12)); // WHERE www_redirection > 12
     * </code>
     *
     * @param     mixed $wwwRedirection The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByWwwRedirection($wwwRedirection = null, $comparison = null)
    {
        if (is_array($wwwRedirection)) {
            $useMinMax = false;
            if (isset($wwwRedirection['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_WWW_REDIRECTION, $wwwRedirection['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($wwwRedirection['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_WWW_REDIRECTION, $wwwRedirection['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_WWW_REDIRECTION, $wwwRedirection, $comparison);
    }

    /**
     * Filter the query on the taxes column
     *
     * Example usage:
     * <code>
     * $query->filterByTaxes(1234); // WHERE taxes = 1234
     * $query->filterByTaxes(array(12, 34)); // WHERE taxes IN (12, 34)
     * $query->filterByTaxes(array('min' => 12)); // WHERE taxes > 12
     * </code>
     *
     * @param     mixed $taxes The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByTaxes($taxes = null, $comparison = null)
    {
        if (is_array($taxes)) {
            $useMinMax = false;
            if (isset($taxes['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_TAXES, $taxes['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($taxes['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_TAXES, $taxes['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_TAXES, $taxes, $comparison);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId('fooValue');   // WHERE photo_id = 'fooValue'
     * $query->filterByPhotoId('%fooValue%'); // WHERE photo_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $photoId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($photoId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $photoId)) {
                $photoId = str_replace('*', '%', $photoId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query on the favicon column
     *
     * Example usage:
     * <code>
     * $query->filterByFavicon('fooValue');   // WHERE favicon = 'fooValue'
     * $query->filterByFavicon('%fooValue%'); // WHERE favicon LIKE '%fooValue%'
     * </code>
     *
     * @param     string $favicon The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByFavicon($favicon = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($favicon)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $favicon)) {
                $favicon = str_replace('*', '%', $favicon);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_FAVICON, $favicon, $comparison);
    }

    /**
     * Filter the query on the offline column
     *
     * Example usage:
     * <code>
     * $query->filterByOffline(1234); // WHERE offline = 1234
     * $query->filterByOffline(array(12, 34)); // WHERE offline IN (12, 34)
     * $query->filterByOffline(array('min' => 12)); // WHERE offline > 12
     * </code>
     *
     * @param     mixed $offline The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByOffline($offline = null, $comparison = null)
    {
        if (is_array($offline)) {
            $useMinMax = false;
            if (isset($offline['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_OFFLINE, $offline['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($offline['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_OFFLINE, $offline['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_OFFLINE, $offline, $comparison);
    }

    /**
     * Filter the query on the offline_text column
     *
     * Example usage:
     * <code>
     * $query->filterByOfflineText('fooValue');   // WHERE offline_text = 'fooValue'
     * $query->filterByOfflineText('%fooValue%'); // WHERE offline_text LIKE '%fooValue%'
     * </code>
     *
     * @param     string $offlineText The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByOfflineText($offlineText = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($offlineText)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $offlineText)) {
                $offlineText = str_replace('*', '%', $offlineText);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_OFFLINE_TEXT, $offlineText, $comparison);
    }

    /**
     * Filter the query on the cart_redirect column
     *
     * Example usage:
     * <code>
     * $query->filterByCartRedirect(1234); // WHERE cart_redirect = 1234
     * $query->filterByCartRedirect(array(12, 34)); // WHERE cart_redirect IN (12, 34)
     * $query->filterByCartRedirect(array('min' => 12)); // WHERE cart_redirect > 12
     * </code>
     *
     * @param     mixed $cartRedirect The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCartRedirect($cartRedirect = null, $comparison = null)
    {
        if (is_array($cartRedirect)) {
            $useMinMax = false;
            if (isset($cartRedirect['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_CART_REDIRECT, $cartRedirect['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cartRedirect['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_CART_REDIRECT, $cartRedirect['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_CART_REDIRECT, $cartRedirect, $comparison);
    }

    /**
     * Filter the query on the minimum_order_value column
     *
     * Example usage:
     * <code>
     * $query->filterByMinimumOrderValue(1234); // WHERE minimum_order_value = 1234
     * $query->filterByMinimumOrderValue(array(12, 34)); // WHERE minimum_order_value IN (12, 34)
     * $query->filterByMinimumOrderValue(array('min' => 12)); // WHERE minimum_order_value > 12
     * </code>
     *
     * @param     mixed $minimumOrderValue The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByMinimumOrderValue($minimumOrderValue = null, $comparison = null)
    {
        if (is_array($minimumOrderValue)) {
            $useMinMax = false;
            if (isset($minimumOrderValue['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_MINIMUM_ORDER_VALUE, $minimumOrderValue['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($minimumOrderValue['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_MINIMUM_ORDER_VALUE, $minimumOrderValue['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_MINIMUM_ORDER_VALUE, $minimumOrderValue, $comparison);
    }

    /**
     * Filter the query on the show_tax column
     *
     * Example usage:
     * <code>
     * $query->filterByShowTax(1234); // WHERE show_tax = 1234
     * $query->filterByShowTax(array(12, 34)); // WHERE show_tax IN (12, 34)
     * $query->filterByShowTax(array('min' => 12)); // WHERE show_tax > 12
     * </code>
     *
     * @param     mixed $showTax The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByShowTax($showTax = null, $comparison = null)
    {
        if (is_array($showTax)) {
            $useMinMax = false;
            if (isset($showTax['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_SHOW_TAX, $showTax['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($showTax['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_SHOW_TAX, $showTax['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_SHOW_TAX, $showTax, $comparison);
    }

    /**
     * Filter the query on the enable_opinions column
     *
     * Example usage:
     * <code>
     * $query->filterByEnableOpinions(1234); // WHERE enable_opinions = 1234
     * $query->filterByEnableOpinions(array(12, 34)); // WHERE enable_opinions IN (12, 34)
     * $query->filterByEnableOpinions(array('min' => 12)); // WHERE enable_opinions > 12
     * </code>
     *
     * @param     mixed $enableOpinions The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByEnableOpinions($enableOpinions = null, $comparison = null)
    {
        if (is_array($enableOpinions)) {
            $useMinMax = false;
            if (isset($enableOpinions['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_ENABLE_OPINIONS, $enableOpinions['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enableOpinions['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_ENABLE_OPINIONS, $enableOpinions['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_ENABLE_OPINIONS, $enableOpinions, $comparison);
    }

    /**
     * Filter the query on the enable_tags column
     *
     * Example usage:
     * <code>
     * $query->filterByEnableTags(1234); // WHERE enable_tags = 1234
     * $query->filterByEnableTags(array(12, 34)); // WHERE enable_tags IN (12, 34)
     * $query->filterByEnableTags(array('min' => 12)); // WHERE enable_tags > 12
     * </code>
     *
     * @param     mixed $enableTags The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByEnableTags($enableTags = null, $comparison = null)
    {
        if (is_array($enableTags)) {
            $useMinMax = false;
            if (isset($enableTags['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_ENABLE_TAGS, $enableTags['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enableTags['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_ENABLE_TAGS, $enableTags['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_ENABLE_TAGS, $enableTags, $comparison);
    }

    /**
     * Filter the query on the catalog_mode column
     *
     * Example usage:
     * <code>
     * $query->filterByCatalogMode(1234); // WHERE catalog_mode = 1234
     * $query->filterByCatalogMode(array(12, 34)); // WHERE catalog_mode IN (12, 34)
     * $query->filterByCatalogMode(array('min' => 12)); // WHERE catalog_mode > 12
     * </code>
     *
     * @param     mixed $catalogMode The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCatalogMode($catalogMode = null, $comparison = null)
    {
        if (is_array($catalogMode)) {
            $useMinMax = false;
            if (isset($catalogMode['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_CATALOG_MODE, $catalogMode['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($catalogMode['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_CATALOG_MODE, $catalogMode['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_CATALOG_MODE, $catalogMode, $comparison);
    }

    /**
     * Filter the query on the force_login column
     *
     * Example usage:
     * <code>
     * $query->filterByForceLogin(1234); // WHERE force_login = 1234
     * $query->filterByForceLogin(array(12, 34)); // WHERE force_login IN (12, 34)
     * $query->filterByForceLogin(array('min' => 12)); // WHERE force_login > 12
     * </code>
     *
     * @param     mixed $forceLogin The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByForceLogin($forceLogin = null, $comparison = null)
    {
        if (is_array($forceLogin)) {
            $useMinMax = false;
            if (isset($forceLogin['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_FORCE_LOGIN, $forceLogin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($forceLogin['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_FORCE_LOGIN, $forceLogin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_FORCE_LOGIN, $forceLogin, $comparison);
    }

    /**
     * Filter the query on the enable_rss column
     *
     * Example usage:
     * <code>
     * $query->filterByEnableRss(1234); // WHERE enable_rss = 1234
     * $query->filterByEnableRss(array(12, 34)); // WHERE enable_rss IN (12, 34)
     * $query->filterByEnableRss(array('min' => 12)); // WHERE enable_rss > 12
     * </code>
     *
     * @param     mixed $enableRss The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByEnableRss($enableRss = null, $comparison = null)
    {
        if (is_array($enableRss)) {
            $useMinMax = false;
            if (isset($enableRss['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_ENABLE_RSS, $enableRss['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enableRss['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_ENABLE_RSS, $enableRss['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_ENABLE_RSS, $enableRss, $comparison);
    }

    /**
     * Filter the query on the invoice_numeration_kind column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceNumerationKind('fooValue');   // WHERE invoice_numeration_kind = 'fooValue'
     * $query->filterByInvoiceNumerationKind('%fooValue%'); // WHERE invoice_numeration_kind LIKE '%fooValue%'
     * </code>
     *
     * @param     string $invoiceNumerationKind The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByInvoiceNumerationKind($invoiceNumerationKind = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($invoiceNumerationKind)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $invoiceNumerationKind)) {
                $invoiceNumerationKind = str_replace('*', '%', $invoiceNumerationKind);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_INVOICE_NUMERATION_KIND, $invoiceNumerationKind, $comparison);
    }

    /**
     * Filter the query on the invoice_default_payment_due column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceDefaultPaymentDue(1234); // WHERE invoice_default_payment_due = 1234
     * $query->filterByInvoiceDefaultPaymentDue(array(12, 34)); // WHERE invoice_default_payment_due IN (12, 34)
     * $query->filterByInvoiceDefaultPaymentDue(array('min' => 12)); // WHERE invoice_default_payment_due > 12
     * </code>
     *
     * @param     mixed $invoiceDefaultPaymentDue The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByInvoiceDefaultPaymentDue($invoiceDefaultPaymentDue = null, $comparison = null)
    {
        if (is_array($invoiceDefaultPaymentDue)) {
            $useMinMax = false;
            if (isset($invoiceDefaultPaymentDue['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE, $invoiceDefaultPaymentDue['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($invoiceDefaultPaymentDue['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE, $invoiceDefaultPaymentDue['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE, $invoiceDefaultPaymentDue, $comparison);
    }

    /**
     * Filter the query on the confirm_registration column
     *
     * Example usage:
     * <code>
     * $query->filterByConfirmRegistration(true); // WHERE confirm_registration = true
     * $query->filterByConfirmRegistration('yes'); // WHERE confirm_registration = true
     * </code>
     *
     * @param     boolean|string $confirmRegistration The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByConfirmRegistration($confirmRegistration = null, $comparison = null)
    {
        if (is_string($confirmRegistration)) {
            $confirm_registration = in_array(strtolower($confirmRegistration), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ShopTableMap::COL_CONFIRM_REGISTRATION, $confirmRegistration, $comparison);
    }

    /**
     * Filter the query on the enable_registration column
     *
     * Example usage:
     * <code>
     * $query->filterByEnableRegistration(true); // WHERE enable_registration = true
     * $query->filterByEnableRegistration('yes'); // WHERE enable_registration = true
     * </code>
     *
     * @param     boolean|string $enableRegistration The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByEnableRegistration($enableRegistration = null, $comparison = null)
    {
        if (is_string($enableRegistration)) {
            $enable_registration = in_array(strtolower($enableRegistration), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ShopTableMap::COL_ENABLE_REGISTRATION, $enableRegistration, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id > 12
     * </code>
     *
     * @see       filterByCurrency()
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the contact_id column
     *
     * Example usage:
     * <code>
     * $query->filterByContactId(1234); // WHERE contact_id = 1234
     * $query->filterByContactId(array(12, 34)); // WHERE contact_id IN (12, 34)
     * $query->filterByContactId(array('min' => 12)); // WHERE contact_id > 12
     * </code>
     *
     * @see       filterByContact()
     *
     * @param     mixed $contactId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByContactId($contactId = null, $comparison = null)
    {
        if (is_array($contactId)) {
            $useMinMax = false;
            if (isset($contactId['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_CONTACT_ID, $contactId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($contactId['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_CONTACT_ID, $contactId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_CONTACT_ID, $contactId, $comparison);
    }

    /**
     * Filter the query on the default_vat_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDefaultVatId(1234); // WHERE default_vat_id = 1234
     * $query->filterByDefaultVatId(array(12, 34)); // WHERE default_vat_id IN (12, 34)
     * $query->filterByDefaultVatId(array('min' => 12)); // WHERE default_vat_id > 12
     * </code>
     *
     * @see       filterByVat()
     *
     * @param     mixed $defaultVatId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByDefaultVatId($defaultVatId = null, $comparison = null)
    {
        if (is_array($defaultVatId)) {
            $useMinMax = false;
            if (isset($defaultVatId['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_DEFAULT_VAT_ID, $defaultVatId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($defaultVatId['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_DEFAULT_VAT_ID, $defaultVatId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_DEFAULT_VAT_ID, $defaultVatId, $comparison);
    }

    /**
     * Filter the query on the order_status_groups_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderStatusGroupsId(1234); // WHERE order_status_groups_id = 1234
     * $query->filterByOrderStatusGroupsId(array(12, 34)); // WHERE order_status_groups_id IN (12, 34)
     * $query->filterByOrderStatusGroupsId(array('min' => 12)); // WHERE order_status_groups_id > 12
     * </code>
     *
     * @see       filterByOrderStatusGroups()
     *
     * @param     mixed $orderStatusGroupsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByOrderStatusGroupsId($orderStatusGroupsId = null, $comparison = null)
    {
        if (is_array($orderStatusGroupsId)) {
            $useMinMax = false;
            if (isset($orderStatusGroupsId['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroupsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderStatusGroupsId['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroupsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroupsId, $comparison);
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ShopTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ShopTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ShopTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Contact\Model\ORM\Contact object
     *
     * @param \Gekosale\Plugin\Contact\Model\ORM\Contact|ObjectCollection $contact The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByContact($contact, $comparison = null)
    {
        if ($contact instanceof \Gekosale\Plugin\Contact\Model\ORM\Contact) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_CONTACT_ID, $contact->getId(), $comparison);
        } elseif ($contact instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ShopTableMap::COL_CONTACT_ID, $contact->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByContact() only accepts arguments of type \Gekosale\Plugin\Contact\Model\ORM\Contact or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Contact relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinContact($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Contact');

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
            $this->addJoinObject($join, 'Contact');
        }

        return $this;
    }

    /**
     * Use the Contact relation Contact object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Contact\Model\ORM\ContactQuery A secondary query class using the current class as primary query
     */
    public function useContactQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinContact($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Contact', '\Gekosale\Plugin\Contact\Model\ORM\ContactQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\Currency object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\Currency|ObjectCollection $currency The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCurrency($currency, $comparison = null)
    {
        if ($currency instanceof \Gekosale\Plugin\Currency\Model\ORM\Currency) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_CURRENCY_ID, $currency->getId(), $comparison);
        } elseif ($currency instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ShopTableMap::COL_CURRENCY_ID, $currency->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCurrency() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\Currency or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Currency relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinCurrency($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Currency');

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
            $this->addJoinObject($join, 'Currency');
        }

        return $this;
    }

    /**
     * Use the Currency relation Currency object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCurrency($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Currency', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Vat\Model\ORM\Vat object
     *
     * @param \Gekosale\Plugin\Vat\Model\ORM\Vat|ObjectCollection $vat The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByVat($vat, $comparison = null)
    {
        if ($vat instanceof \Gekosale\Plugin\Vat\Model\ORM\Vat) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_DEFAULT_VAT_ID, $vat->getId(), $comparison);
        } elseif ($vat instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ShopTableMap::COL_DEFAULT_VAT_ID, $vat->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinVat($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useVatQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinVat($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Vat', '\Gekosale\Plugin\Vat\Model\ORM\VatQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups object
     *
     * @param \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups|ObjectCollection $orderStatusGroups The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByOrderStatusGroups($orderStatusGroups, $comparison = null)
    {
        if ($orderStatusGroups instanceof \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroups->getId(), $comparison);
        } elseif ($orderStatusGroups instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, $orderStatusGroups->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrderStatusGroups() only accepts arguments of type \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderStatusGroups relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinOrderStatusGroups($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderStatusGroups');

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
            $this->addJoinObject($join, 'OrderStatusGroups');
        }

        return $this;
    }

    /**
     * Use the OrderStatusGroups relation OrderStatusGroups object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroupsQuery A secondary query class using the current class as primary query
     */
    public function useOrderStatusGroupsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinOrderStatusGroups($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderStatusGroups', '\Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroupsQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Company\Model\ORM\Company object
     *
     * @param \Gekosale\Plugin\Company\Model\ORM\Company|ObjectCollection $company The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCompany($company, $comparison = null)
    {
        if ($company instanceof \Gekosale\Plugin\Company\Model\ORM\Company) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_COMPANY_ID, $company->getId(), $comparison);
        } elseif ($company instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ShopTableMap::COL_COMPANY_ID, $company->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCompany() only accepts arguments of type \Gekosale\Plugin\Company\Model\ORM\Company or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Company relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinCompany($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Company');

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
            $this->addJoinObject($join, 'Company');
        }

        return $this;
    }

    /**
     * Use the Company relation Company object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Company\Model\ORM\CompanyQuery A secondary query class using the current class as primary query
     */
    public function useCompanyQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCompany($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Company', '\Gekosale\Plugin\Company\Model\ORM\CompanyQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Client\Model\ORM\Client object
     *
     * @param \Gekosale\Plugin\Client\Model\ORM\Client|ObjectCollection $client  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByClient($client, $comparison = null)
    {
        if ($client instanceof \Gekosale\Plugin\Client\Model\ORM\Client) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $client->getShopId(), $comparison);
        } elseif ($client instanceof ObjectCollection) {
            return $this
                ->useClientQuery()
                ->filterByPrimaryKeys($client->getPrimaryKeys())
                ->endUse();
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinClient($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useClientQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinClient($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Client', '\Gekosale\Plugin\Client\Model\ORM\ClientQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart object
     *
     * @param \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart|ObjectCollection $missingCart  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByMissingCart($missingCart, $comparison = null)
    {
        if ($missingCart instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $missingCart->getShopId(), $comparison);
        } elseif ($missingCart instanceof ObjectCollection) {
            return $this
                ->useMissingCartQuery()
                ->filterByPrimaryKeys($missingCart->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMissingCart() only accepts arguments of type \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MissingCart relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinMissingCart($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MissingCart');

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
            $this->addJoinObject($join, 'MissingCart');
        }

        return $this;
    }

    /**
     * Use the MissingCart relation MissingCart object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery A secondary query class using the current class as primary query
     */
    public function useMissingCartQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMissingCart($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MissingCart', '\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct object
     *
     * @param \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct|ObjectCollection $missingCartProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByMissingCartProduct($missingCartProduct, $comparison = null)
    {
        if ($missingCartProduct instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $missingCartProduct->getShopId(), $comparison);
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinMissingCartProduct($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useMissingCartProductQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMissingCartProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MissingCartProduct', '\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\Order object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\Order|ObjectCollection $order  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByOrder($order, $comparison = null)
    {
        if ($order instanceof \Gekosale\Plugin\Order\Model\ORM\Order) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $order->getShopId(), $comparison);
        } elseif ($order instanceof ObjectCollection) {
            return $this
                ->useOrderQuery()
                ->filterByPrimaryKeys($order->getPrimaryKeys())
                ->endUse();
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinOrder($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useOrderQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Order', '\Gekosale\Plugin\Order\Model\ORM\OrderQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases object
     *
     * @param \Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases|ObjectCollection $productSearchPhrases  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByProductSearchPhrases($productSearchPhrases, $comparison = null)
    {
        if ($productSearchPhrases instanceof \Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $productSearchPhrases->getShopId(), $comparison);
        } elseif ($productSearchPhrases instanceof ObjectCollection) {
            return $this
                ->useProductSearchPhrasesQuery()
                ->filterByPrimaryKeys($productSearchPhrases->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductSearchPhrases() only accepts arguments of type \Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductSearchPhrases relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinProductSearchPhrases($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductSearchPhrases');

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
            $this->addJoinObject($join, 'ProductSearchPhrases');
        }

        return $this;
    }

    /**
     * Use the ProductSearchPhrases relation ProductSearchPhrases object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrasesQuery A secondary query class using the current class as primary query
     */
    public function useProductSearchPhrasesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductSearchPhrases($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductSearchPhrases', '\Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrasesQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Blog\Model\ORM\BlogShop object
     *
     * @param \Gekosale\Plugin\Blog\Model\ORM\BlogShop|ObjectCollection $blogShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByBlogShop($blogShop, $comparison = null)
    {
        if ($blogShop instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $blogShop->getShopId(), $comparison);
        } elseif ($blogShop instanceof ObjectCollection) {
            return $this
                ->useBlogShopQuery()
                ->filterByPrimaryKeys($blogShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBlogShop() only accepts arguments of type \Gekosale\Plugin\Blog\Model\ORM\BlogShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BlogShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinBlogShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BlogShop');

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
            $this->addJoinObject($join, 'BlogShop');
        }

        return $this;
    }

    /**
     * Use the BlogShop relation BlogShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Blog\Model\ORM\BlogShopQuery A secondary query class using the current class as primary query
     */
    public function useBlogShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBlogShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BlogShop', '\Gekosale\Plugin\Blog\Model\ORM\BlogShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop|ObjectCollection $cartRuleShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCartRuleShop($cartRuleShop, $comparison = null)
    {
        if ($cartRuleShop instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $cartRuleShop->getShopId(), $comparison);
        } elseif ($cartRuleShop instanceof ObjectCollection) {
            return $this
                ->useCartRuleShopQuery()
                ->filterByPrimaryKeys($cartRuleShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCartRuleShop() only accepts arguments of type \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CartRuleShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinCartRuleShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CartRuleShop');

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
            $this->addJoinObject($join, 'CartRuleShop');
        }

        return $this;
    }

    /**
     * Use the CartRuleShop relation CartRuleShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShopQuery A secondary query class using the current class as primary query
     */
    public function useCartRuleShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCartRuleShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CartRuleShop', '\Gekosale\Plugin\CartRule\Model\ORM\CartRuleShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\CategoryShop object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\CategoryShop|ObjectCollection $categoryShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCategoryShop($categoryShop, $comparison = null)
    {
        if ($categoryShop instanceof \Gekosale\Plugin\Category\Model\ORM\CategoryShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $categoryShop->getShopId(), $comparison);
        } elseif ($categoryShop instanceof ObjectCollection) {
            return $this
                ->useCategoryShopQuery()
                ->filterByPrimaryKeys($categoryShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryShop() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\CategoryShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinCategoryShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryShop');

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
            $this->addJoinObject($join, 'CategoryShop');
        }

        return $this;
    }

    /**
     * Use the CategoryShop relation CategoryShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery A secondary query class using the current class as primary query
     */
    public function useCategoryShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategoryShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryShop', '\Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Page\Model\ORM\PageShop object
     *
     * @param \Gekosale\Plugin\Page\Model\ORM\PageShop|ObjectCollection $pageShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByPageShop($pageShop, $comparison = null)
    {
        if ($pageShop instanceof \Gekosale\Plugin\Page\Model\ORM\PageShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $pageShop->getShopId(), $comparison);
        } elseif ($pageShop instanceof ObjectCollection) {
            return $this
                ->usePageShopQuery()
                ->filterByPrimaryKeys($pageShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPageShop() only accepts arguments of type \Gekosale\Plugin\Page\Model\ORM\PageShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinPageShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageShop');

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
            $this->addJoinObject($join, 'PageShop');
        }

        return $this;
    }

    /**
     * Use the PageShop relation PageShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Page\Model\ORM\PageShopQuery A secondary query class using the current class as primary query
     */
    public function usePageShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPageShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageShop', '\Gekosale\Plugin\Page\Model\ORM\PageShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Contact\Model\ORM\ContactShop object
     *
     * @param \Gekosale\Plugin\Contact\Model\ORM\ContactShop|ObjectCollection $contactShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByContactShop($contactShop, $comparison = null)
    {
        if ($contactShop instanceof \Gekosale\Plugin\Contact\Model\ORM\ContactShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $contactShop->getShopId(), $comparison);
        } elseif ($contactShop instanceof ObjectCollection) {
            return $this
                ->useContactShopQuery()
                ->filterByPrimaryKeys($contactShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByContactShop() only accepts arguments of type \Gekosale\Plugin\Contact\Model\ORM\ContactShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ContactShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinContactShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ContactShop');

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
            $this->addJoinObject($join, 'ContactShop');
        }

        return $this;
    }

    /**
     * Use the ContactShop relation ContactShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Contact\Model\ORM\ContactShopQuery A secondary query class using the current class as primary query
     */
    public function useContactShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinContactShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ContactShop', '\Gekosale\Plugin\Contact\Model\ORM\ContactShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop|ObjectCollection $currencyShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByCurrencyShop($currencyShop, $comparison = null)
    {
        if ($currencyShop instanceof \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $currencyShop->getShopId(), $comparison);
        } elseif ($currencyShop instanceof ObjectCollection) {
            return $this
                ->useCurrencyShopQuery()
                ->filterByPrimaryKeys($currencyShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCurrencyShop() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CurrencyShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinCurrencyShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CurrencyShop');

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
            $this->addJoinObject($join, 'CurrencyShop');
        }

        return $this;
    }

    /**
     * Use the CurrencyShop relation CurrencyShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCurrencyShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CurrencyShop', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop|ObjectCollection $dispatchMethodShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodShop($dispatchMethodShop, $comparison = null)
    {
        if ($dispatchMethodShop instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $dispatchMethodShop->getShopId(), $comparison);
        } elseif ($dispatchMethodShop instanceof ObjectCollection) {
            return $this
                ->useDispatchMethodShopQuery()
                ->filterByPrimaryKeys($dispatchMethodShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDispatchMethodShop() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethodShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinDispatchMethodShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethodShop');

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
            $this->addJoinObject($join, 'DispatchMethodShop');
        }

        return $this;
    }

    /**
     * Use the DispatchMethodShop relation DispatchMethodShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDispatchMethodShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodShop', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Locale\Model\ORM\LocaleShop object
     *
     * @param \Gekosale\Plugin\Locale\Model\ORM\LocaleShop|ObjectCollection $localeShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByLocaleShop($localeShop, $comparison = null)
    {
        if ($localeShop instanceof \Gekosale\Plugin\Locale\Model\ORM\LocaleShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $localeShop->getShopId(), $comparison);
        } elseif ($localeShop instanceof ObjectCollection) {
            return $this
                ->useLocaleShopQuery()
                ->filterByPrimaryKeys($localeShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByLocaleShop() only accepts arguments of type \Gekosale\Plugin\Locale\Model\ORM\LocaleShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LocaleShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinLocaleShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LocaleShop');

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
            $this->addJoinObject($join, 'LocaleShop');
        }

        return $this;
    }

    /**
     * Use the LocaleShop relation LocaleShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery A secondary query class using the current class as primary query
     */
    public function useLocaleShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLocaleShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LocaleShop', '\Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop object
     *
     * @param \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop|ObjectCollection $paymentMethodShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByPaymentMethodShop($paymentMethodShop, $comparison = null)
    {
        if ($paymentMethodShop instanceof \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $paymentMethodShop->getShopId(), $comparison);
        } elseif ($paymentMethodShop instanceof ObjectCollection) {
            return $this
                ->usePaymentMethodShopQuery()
                ->filterByPrimaryKeys($paymentMethodShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPaymentMethodShop() only accepts arguments of type \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PaymentMethodShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinPaymentMethodShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PaymentMethodShop');

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
            $this->addJoinObject($join, 'PaymentMethodShop');
        }

        return $this;
    }

    /**
     * Use the PaymentMethodShop relation PaymentMethodShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShopQuery A secondary query class using the current class as primary query
     */
    public function usePaymentMethodShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPaymentMethodShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PaymentMethodShop', '\Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Producer\Model\ORM\ProducerShop object
     *
     * @param \Gekosale\Plugin\Producer\Model\ORM\ProducerShop|ObjectCollection $producerShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByProducerShop($producerShop, $comparison = null)
    {
        if ($producerShop instanceof \Gekosale\Plugin\Producer\Model\ORM\ProducerShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $producerShop->getShopId(), $comparison);
        } elseif ($producerShop instanceof ObjectCollection) {
            return $this
                ->useProducerShopQuery()
                ->filterByPrimaryKeys($producerShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProducerShop() only accepts arguments of type \Gekosale\Plugin\Producer\Model\ORM\ProducerShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProducerShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinProducerShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProducerShop');

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
            $this->addJoinObject($join, 'ProducerShop');
        }

        return $this;
    }

    /**
     * Use the ProducerShop relation ProducerShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Producer\Model\ORM\ProducerShopQuery A secondary query class using the current class as primary query
     */
    public function useProducerShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProducerShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProducerShop', '\Gekosale\Plugin\Producer\Model\ORM\ProducerShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\User\Model\ORM\UserGroupShop object
     *
     * @param \Gekosale\Plugin\User\Model\ORM\UserGroupShop|ObjectCollection $userGroupShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByUserGroupShop($userGroupShop, $comparison = null)
    {
        if ($userGroupShop instanceof \Gekosale\Plugin\User\Model\ORM\UserGroupShop) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $userGroupShop->getShopId(), $comparison);
        } elseif ($userGroupShop instanceof ObjectCollection) {
            return $this
                ->useUserGroupShopQuery()
                ->filterByPrimaryKeys($userGroupShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserGroupShop() only accepts arguments of type \Gekosale\Plugin\User\Model\ORM\UserGroupShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserGroupShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinUserGroupShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserGroupShop');

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
            $this->addJoinObject($join, 'UserGroupShop');
        }

        return $this;
    }

    /**
     * Use the UserGroupShop relation UserGroupShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery A secondary query class using the current class as primary query
     */
    public function useUserGroupShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserGroupShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserGroupShop', '\Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist object
     *
     * @param \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist|ObjectCollection $wishlist  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByWishlist($wishlist, $comparison = null)
    {
        if ($wishlist instanceof \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $wishlist->getShopId(), $comparison);
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
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinWishlist($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useWishlistQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinWishlist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Wishlist', '\Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\ShopI18n object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\ShopI18n|ObjectCollection $shopI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function filterByShopI18n($shopI18n, $comparison = null)
    {
        if ($shopI18n instanceof \Gekosale\Plugin\Shop\Model\ORM\ShopI18n) {
            return $this
                ->addUsingAlias(ShopTableMap::COL_ID, $shopI18n->getId(), $comparison);
        } elseif ($shopI18n instanceof ObjectCollection) {
            return $this
                ->useShopI18nQuery()
                ->filterByPrimaryKeys($shopI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByShopI18n() only accepts arguments of type \Gekosale\Plugin\Shop\Model\ORM\ShopI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ShopI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function joinShopI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ShopI18n');

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
            $this->addJoinObject($join, 'ShopI18n');
        }

        return $this;
    }

    /**
     * Use the ShopI18n relation ShopI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopI18nQuery A secondary query class using the current class as primary query
     */
    public function useShopI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinShopI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ShopI18n', '\Gekosale\Plugin\Shop\Model\ORM\ShopI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildShop $shop Object to remove from the list of results
     *
     * @return ChildShopQuery The current query, for fluid interface
     */
    public function prune($shop = null)
    {
        if ($shop) {
            $this->addUsingAlias(ShopTableMap::COL_ID, $shop->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the shop table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShopTableMap::DATABASE_NAME);
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
            ShopTableMap::clearInstancePool();
            ShopTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildShop or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildShop object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ShopTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ShopTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ShopTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ShopTableMap::clearRelatedInstancePool();
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
     * @return    ChildShopQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'ShopI18n';
    
        return $this
            ->joinShopI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildShopQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('ShopI18n');
        $this->with['ShopI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildShopI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ShopI18n', '\Gekosale\Plugin\Shop\Model\ORM\ShopI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildShopQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ShopTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildShopQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ShopTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildShopQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ShopTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildShopQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ShopTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildShopQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ShopTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildShopQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ShopTableMap::COL_CREATED_AT);
    }

} // ShopQuery
