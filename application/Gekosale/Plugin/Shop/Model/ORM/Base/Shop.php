<?php

namespace Gekosale\Plugin\Shop\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\Blog\Model\ORM\BlogShopQuery;
use Gekosale\Plugin\Blog\Model\ORM\BlogShop as ChildBlogShop;
use Gekosale\Plugin\Blog\Model\ORM\Base\BlogShop;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleShopQuery;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop as ChildCartRuleShop;
use Gekosale\Plugin\CartRule\Model\ORM\Base\CartRuleShop;
use Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery;
use Gekosale\Plugin\Category\Model\ORM\CategoryShop as ChildCategoryShop;
use Gekosale\Plugin\Category\Model\ORM\Base\CategoryShop;
use Gekosale\Plugin\Client\Model\ORM\Client as ChildClient;
use Gekosale\Plugin\Client\Model\ORM\ClientQuery;
use Gekosale\Plugin\Client\Model\ORM\Base\Client;
use Gekosale\Plugin\Company\Model\ORM\Company as ChildCompany;
use Gekosale\Plugin\Company\Model\ORM\CompanyQuery;
use Gekosale\Plugin\Contact\Model\ORM\Contact as ChildContact;
use Gekosale\Plugin\Contact\Model\ORM\ContactShop as ChildContactShop;
use Gekosale\Plugin\Contact\Model\ORM\ContactQuery;
use Gekosale\Plugin\Contact\Model\ORM\ContactShopQuery;
use Gekosale\Plugin\Contact\Model\ORM\Base\ContactShop;
use Gekosale\Plugin\Currency\Model\ORM\Currency as ChildCurrency;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyShop as ChildCurrencyShop;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery;
use Gekosale\Plugin\Currency\Model\ORM\Base\CurrencyShop;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop as ChildDispatchMethodShop;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\Base\DispatchMethodShop;
use Gekosale\Plugin\Locale\Model\ORM\LocaleShop as ChildLocaleShop;
use Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery;
use Gekosale\Plugin\Locale\Model\ORM\Base\LocaleShop;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCart as ChildMissingCart;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct as ChildMissingCartProduct;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\Base\MissingCart;
use Gekosale\Plugin\MissingCart\Model\ORM\Base\MissingCartProduct;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroups as ChildOrderStatusGroups;
use Gekosale\Plugin\OrderStatus\Model\ORM\OrderStatusGroupsQuery;
use Gekosale\Plugin\Order\Model\ORM\Order as ChildOrder;
use Gekosale\Plugin\Order\Model\ORM\OrderQuery;
use Gekosale\Plugin\Order\Model\ORM\Base\Order;
use Gekosale\Plugin\Page\Model\ORM\PageShop as ChildPageShop;
use Gekosale\Plugin\Page\Model\ORM\PageShopQuery;
use Gekosale\Plugin\Page\Model\ORM\Base\PageShop;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop as ChildPaymentMethodShop;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShopQuery;
use Gekosale\Plugin\PaymentMethod\Model\ORM\Base\PaymentMethodShop;
use Gekosale\Plugin\Producer\Model\ORM\ProducerShop as ChildProducerShop;
use Gekosale\Plugin\Producer\Model\ORM\ProducerShopQuery;
use Gekosale\Plugin\Producer\Model\ORM\Base\ProducerShop;
use Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases as ChildProductSearchPhrases;
use Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrasesQuery;
use Gekosale\Plugin\Search\Model\ORM\Base\ProductSearchPhrases;
use Gekosale\Plugin\Shop\Model\ORM\Shop as ChildShop;
use Gekosale\Plugin\Shop\Model\ORM\ShopI18n as ChildShopI18n;
use Gekosale\Plugin\Shop\Model\ORM\ShopI18nQuery as ChildShopI18nQuery;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery as ChildShopQuery;
use Gekosale\Plugin\Shop\Model\ORM\Map\ShopTableMap;
use Gekosale\Plugin\User\Model\ORM\UserGroupShop as ChildUserGroupShop;
use Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery;
use Gekosale\Plugin\User\Model\ORM\Base\UserGroupShop;
use Gekosale\Plugin\Vat\Model\ORM\Vat as ChildVat;
use Gekosale\Plugin\Vat\Model\ORM\VatQuery;
use Gekosale\Plugin\Wishlist\Model\ORM\Wishlist as ChildWishlist;
use Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery;
use Gekosale\Plugin\Wishlist\Model\ORM\Base\Wishlist;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class Shop implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Map\\ShopTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the url field.
     * @var        string
     */
    protected $url;

    /**
     * The value for the company_id field.
     * @var        int
     */
    protected $company_id;

    /**
     * The value for the period_id field.
     * @var        int
     */
    protected $period_id;

    /**
     * The value for the www_redirection field.
     * @var        int
     */
    protected $www_redirection;

    /**
     * The value for the taxes field.
     * @var        int
     */
    protected $taxes;

    /**
     * The value for the photo_id field.
     * @var        string
     */
    protected $photo_id;

    /**
     * The value for the favicon field.
     * @var        string
     */
    protected $favicon;

    /**
     * The value for the offline field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $offline;

    /**
     * The value for the offline_text field.
     * @var        string
     */
    protected $offline_text;

    /**
     * The value for the cart_redirect field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $cart_redirect;

    /**
     * The value for the minimum_order_value field.
     * Note: this column has a database default value of: '0.0000'
     * @var        string
     */
    protected $minimum_order_value;

    /**
     * The value for the show_tax field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $show_tax;

    /**
     * The value for the enable_opinions field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $enable_opinions;

    /**
     * The value for the enable_tags field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $enable_tags;

    /**
     * The value for the catalog_mode field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $catalog_mode;

    /**
     * The value for the force_login field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $force_login;

    /**
     * The value for the enable_rss field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $enable_rss;

    /**
     * The value for the invoice_numeration_kind field.
     * Note: this column has a database default value of: 'ntmr'
     * @var        string
     */
    protected $invoice_numeration_kind;

    /**
     * The value for the invoice_default_payment_due field.
     * Note: this column has a database default value of: 7
     * @var        int
     */
    protected $invoice_default_payment_due;

    /**
     * The value for the confirm_registration field.
     * @var        boolean
     */
    protected $confirm_registration;

    /**
     * The value for the enable_registration field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $enable_registration;

    /**
     * The value for the currency_id field.
     * @var        int
     */
    protected $currency_id;

    /**
     * The value for the contact_id field.
     * @var        int
     */
    protected $contact_id;

    /**
     * The value for the default_vat_id field.
     * @var        int
     */
    protected $default_vat_id;

    /**
     * The value for the order_status_groups_id field.
     * @var        int
     */
    protected $order_status_groups_id;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        Contact
     */
    protected $aContact;

    /**
     * @var        Currency
     */
    protected $aCurrency;

    /**
     * @var        Vat
     */
    protected $aVat;

    /**
     * @var        OrderStatusGroups
     */
    protected $aOrderStatusGroups;

    /**
     * @var        Company
     */
    protected $aCompany;

    /**
     * @var        ObjectCollection|ChildClient[] Collection to store aggregation of ChildClient objects.
     */
    protected $collClients;
    protected $collClientsPartial;

    /**
     * @var        ObjectCollection|ChildMissingCart[] Collection to store aggregation of ChildMissingCart objects.
     */
    protected $collMissingCarts;
    protected $collMissingCartsPartial;

    /**
     * @var        ObjectCollection|ChildMissingCartProduct[] Collection to store aggregation of ChildMissingCartProduct objects.
     */
    protected $collMissingCartProducts;
    protected $collMissingCartProductsPartial;

    /**
     * @var        ObjectCollection|ChildOrder[] Collection to store aggregation of ChildOrder objects.
     */
    protected $collOrders;
    protected $collOrdersPartial;

    /**
     * @var        ObjectCollection|ChildProductSearchPhrases[] Collection to store aggregation of ChildProductSearchPhrases objects.
     */
    protected $collProductSearchPhrasess;
    protected $collProductSearchPhrasessPartial;

    /**
     * @var        ObjectCollection|ChildBlogShop[] Collection to store aggregation of ChildBlogShop objects.
     */
    protected $collBlogShops;
    protected $collBlogShopsPartial;

    /**
     * @var        ObjectCollection|ChildCartRuleShop[] Collection to store aggregation of ChildCartRuleShop objects.
     */
    protected $collCartRuleShops;
    protected $collCartRuleShopsPartial;

    /**
     * @var        ObjectCollection|ChildCategoryShop[] Collection to store aggregation of ChildCategoryShop objects.
     */
    protected $collCategoryShops;
    protected $collCategoryShopsPartial;

    /**
     * @var        ObjectCollection|ChildPageShop[] Collection to store aggregation of ChildPageShop objects.
     */
    protected $collPageShops;
    protected $collPageShopsPartial;

    /**
     * @var        ObjectCollection|ChildContactShop[] Collection to store aggregation of ChildContactShop objects.
     */
    protected $collContactShops;
    protected $collContactShopsPartial;

    /**
     * @var        ObjectCollection|ChildCurrencyShop[] Collection to store aggregation of ChildCurrencyShop objects.
     */
    protected $collCurrencyShops;
    protected $collCurrencyShopsPartial;

    /**
     * @var        ObjectCollection|ChildDispatchMethodShop[] Collection to store aggregation of ChildDispatchMethodShop objects.
     */
    protected $collDispatchMethodShops;
    protected $collDispatchMethodShopsPartial;

    /**
     * @var        ObjectCollection|ChildLocaleShop[] Collection to store aggregation of ChildLocaleShop objects.
     */
    protected $collLocaleShops;
    protected $collLocaleShopsPartial;

    /**
     * @var        ObjectCollection|ChildPaymentMethodShop[] Collection to store aggregation of ChildPaymentMethodShop objects.
     */
    protected $collPaymentMethodShops;
    protected $collPaymentMethodShopsPartial;

    /**
     * @var        ObjectCollection|ChildProducerShop[] Collection to store aggregation of ChildProducerShop objects.
     */
    protected $collProducerShops;
    protected $collProducerShopsPartial;

    /**
     * @var        ObjectCollection|ChildUserGroupShop[] Collection to store aggregation of ChildUserGroupShop objects.
     */
    protected $collUserGroupShops;
    protected $collUserGroupShopsPartial;

    /**
     * @var        ObjectCollection|ChildWishlist[] Collection to store aggregation of ChildWishlist objects.
     */
    protected $collWishlists;
    protected $collWishlistsPartial;

    /**
     * @var        ObjectCollection|ChildShopI18n[] Collection to store aggregation of ChildShopI18n objects.
     */
    protected $collShopI18ns;
    protected $collShopI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior
    
    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';
    
    /**
     * Current translation objects
     * @var        array[ChildShopI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $clientsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $missingCartsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $missingCartProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $ordersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productSearchPhrasessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $blogShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $cartRuleShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoryShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $pageShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $contactShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $currencyShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dispatchMethodShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $localeShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $paymentMethodShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $producerShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $userGroupShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $wishlistsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $shopI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->offline = 0;
        $this->cart_redirect = 1;
        $this->minimum_order_value = '0.0000';
        $this->show_tax = 1;
        $this->enable_opinions = 1;
        $this->enable_tags = 1;
        $this->catalog_mode = 0;
        $this->force_login = 0;
        $this->enable_rss = 1;
        $this->invoice_numeration_kind = 'ntmr';
        $this->invoice_default_payment_due = 7;
        $this->enable_registration = true;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Shop\Model\ORM\Base\Shop object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Shop</code> instance.  If
     * <code>obj</code> is an instance of <code>Shop</code>, delegates to
     * <code>equals(Shop)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Shop The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Shop The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     * 
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [url] column value.
     * 
     * @return   string
     */
    public function getUrl()
    {

        return $this->url;
    }

    /**
     * Get the [company_id] column value.
     * 
     * @return   int
     */
    public function getCompanyId()
    {

        return $this->company_id;
    }

    /**
     * Get the [period_id] column value.
     * 
     * @return   int
     */
    public function getPeriodId()
    {

        return $this->period_id;
    }

    /**
     * Get the [www_redirection] column value.
     * 
     * @return   int
     */
    public function getWwwRedirection()
    {

        return $this->www_redirection;
    }

    /**
     * Get the [taxes] column value.
     * 
     * @return   int
     */
    public function getTaxes()
    {

        return $this->taxes;
    }

    /**
     * Get the [photo_id] column value.
     * 
     * @return   string
     */
    public function getPhotoId()
    {

        return $this->photo_id;
    }

    /**
     * Get the [favicon] column value.
     * 
     * @return   string
     */
    public function getFavicon()
    {

        return $this->favicon;
    }

    /**
     * Get the [offline] column value.
     * 
     * @return   int
     */
    public function getOffline()
    {

        return $this->offline;
    }

    /**
     * Get the [offline_text] column value.
     * 
     * @return   string
     */
    public function getOfflineText()
    {

        return $this->offline_text;
    }

    /**
     * Get the [cart_redirect] column value.
     * 
     * @return   int
     */
    public function getCartRedirect()
    {

        return $this->cart_redirect;
    }

    /**
     * Get the [minimum_order_value] column value.
     * 
     * @return   string
     */
    public function getMinimumOrderValue()
    {

        return $this->minimum_order_value;
    }

    /**
     * Get the [show_tax] column value.
     * 
     * @return   int
     */
    public function getShowTax()
    {

        return $this->show_tax;
    }

    /**
     * Get the [enable_opinions] column value.
     * 
     * @return   int
     */
    public function getEnableOpinions()
    {

        return $this->enable_opinions;
    }

    /**
     * Get the [enable_tags] column value.
     * 
     * @return   int
     */
    public function getEnableTags()
    {

        return $this->enable_tags;
    }

    /**
     * Get the [catalog_mode] column value.
     * 
     * @return   int
     */
    public function getCatalogMode()
    {

        return $this->catalog_mode;
    }

    /**
     * Get the [force_login] column value.
     * 
     * @return   int
     */
    public function getForceLogin()
    {

        return $this->force_login;
    }

    /**
     * Get the [enable_rss] column value.
     * 
     * @return   int
     */
    public function getEnableRss()
    {

        return $this->enable_rss;
    }

    /**
     * Get the [invoice_numeration_kind] column value.
     * 
     * @return   string
     */
    public function getInvoiceNumerationKind()
    {

        return $this->invoice_numeration_kind;
    }

    /**
     * Get the [invoice_default_payment_due] column value.
     * 
     * @return   int
     */
    public function getInvoiceDefaultPaymentDue()
    {

        return $this->invoice_default_payment_due;
    }

    /**
     * Get the [confirm_registration] column value.
     * 
     * @return   boolean
     */
    public function getConfirmRegistration()
    {

        return $this->confirm_registration;
    }

    /**
     * Get the [enable_registration] column value.
     * 
     * @return   boolean
     */
    public function getEnableRegistration()
    {

        return $this->enable_registration;
    }

    /**
     * Get the [currency_id] column value.
     * 
     * @return   int
     */
    public function getCurrencyId()
    {

        return $this->currency_id;
    }

    /**
     * Get the [contact_id] column value.
     * 
     * @return   int
     */
    public function getContactId()
    {

        return $this->contact_id;
    }

    /**
     * Get the [default_vat_id] column value.
     * 
     * @return   int
     */
    public function getDefaultVatId()
    {

        return $this->default_vat_id;
    }

    /**
     * Get the [order_status_groups_id] column value.
     * 
     * @return   int
     */
    public function getOrderStatusGroupsId()
    {

        return $this->order_status_groups_id;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ShopTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [url] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[ShopTableMap::COL_URL] = true;
        }


        return $this;
    } // setUrl()

    /**
     * Set the value of [company_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setCompanyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->company_id !== $v) {
            $this->company_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_COMPANY_ID] = true;
        }

        if ($this->aCompany !== null && $this->aCompany->getId() !== $v) {
            $this->aCompany = null;
        }


        return $this;
    } // setCompanyId()

    /**
     * Set the value of [period_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setPeriodId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->period_id !== $v) {
            $this->period_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_PERIOD_ID] = true;
        }


        return $this;
    } // setPeriodId()

    /**
     * Set the value of [www_redirection] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setWwwRedirection($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->www_redirection !== $v) {
            $this->www_redirection = $v;
            $this->modifiedColumns[ShopTableMap::COL_WWW_REDIRECTION] = true;
        }


        return $this;
    } // setWwwRedirection()

    /**
     * Set the value of [taxes] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setTaxes($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->taxes !== $v) {
            $this->taxes = $v;
            $this->modifiedColumns[ShopTableMap::COL_TAXES] = true;
        }


        return $this;
    } // setTaxes()

    /**
     * Set the value of [photo_id] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setPhotoId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->photo_id !== $v) {
            $this->photo_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_PHOTO_ID] = true;
        }


        return $this;
    } // setPhotoId()

    /**
     * Set the value of [favicon] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setFavicon($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->favicon !== $v) {
            $this->favicon = $v;
            $this->modifiedColumns[ShopTableMap::COL_FAVICON] = true;
        }


        return $this;
    } // setFavicon()

    /**
     * Set the value of [offline] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setOffline($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->offline !== $v) {
            $this->offline = $v;
            $this->modifiedColumns[ShopTableMap::COL_OFFLINE] = true;
        }


        return $this;
    } // setOffline()

    /**
     * Set the value of [offline_text] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setOfflineText($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->offline_text !== $v) {
            $this->offline_text = $v;
            $this->modifiedColumns[ShopTableMap::COL_OFFLINE_TEXT] = true;
        }


        return $this;
    } // setOfflineText()

    /**
     * Set the value of [cart_redirect] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setCartRedirect($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cart_redirect !== $v) {
            $this->cart_redirect = $v;
            $this->modifiedColumns[ShopTableMap::COL_CART_REDIRECT] = true;
        }


        return $this;
    } // setCartRedirect()

    /**
     * Set the value of [minimum_order_value] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setMinimumOrderValue($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->minimum_order_value !== $v) {
            $this->minimum_order_value = $v;
            $this->modifiedColumns[ShopTableMap::COL_MINIMUM_ORDER_VALUE] = true;
        }


        return $this;
    } // setMinimumOrderValue()

    /**
     * Set the value of [show_tax] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setShowTax($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->show_tax !== $v) {
            $this->show_tax = $v;
            $this->modifiedColumns[ShopTableMap::COL_SHOW_TAX] = true;
        }


        return $this;
    } // setShowTax()

    /**
     * Set the value of [enable_opinions] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setEnableOpinions($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->enable_opinions !== $v) {
            $this->enable_opinions = $v;
            $this->modifiedColumns[ShopTableMap::COL_ENABLE_OPINIONS] = true;
        }


        return $this;
    } // setEnableOpinions()

    /**
     * Set the value of [enable_tags] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setEnableTags($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->enable_tags !== $v) {
            $this->enable_tags = $v;
            $this->modifiedColumns[ShopTableMap::COL_ENABLE_TAGS] = true;
        }


        return $this;
    } // setEnableTags()

    /**
     * Set the value of [catalog_mode] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setCatalogMode($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->catalog_mode !== $v) {
            $this->catalog_mode = $v;
            $this->modifiedColumns[ShopTableMap::COL_CATALOG_MODE] = true;
        }


        return $this;
    } // setCatalogMode()

    /**
     * Set the value of [force_login] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setForceLogin($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->force_login !== $v) {
            $this->force_login = $v;
            $this->modifiedColumns[ShopTableMap::COL_FORCE_LOGIN] = true;
        }


        return $this;
    } // setForceLogin()

    /**
     * Set the value of [enable_rss] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setEnableRss($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->enable_rss !== $v) {
            $this->enable_rss = $v;
            $this->modifiedColumns[ShopTableMap::COL_ENABLE_RSS] = true;
        }


        return $this;
    } // setEnableRss()

    /**
     * Set the value of [invoice_numeration_kind] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setInvoiceNumerationKind($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->invoice_numeration_kind !== $v) {
            $this->invoice_numeration_kind = $v;
            $this->modifiedColumns[ShopTableMap::COL_INVOICE_NUMERATION_KIND] = true;
        }


        return $this;
    } // setInvoiceNumerationKind()

    /**
     * Set the value of [invoice_default_payment_due] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setInvoiceDefaultPaymentDue($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->invoice_default_payment_due !== $v) {
            $this->invoice_default_payment_due = $v;
            $this->modifiedColumns[ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE] = true;
        }


        return $this;
    } // setInvoiceDefaultPaymentDue()

    /**
     * Sets the value of the [confirm_registration] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * 
     * @param      boolean|integer|string $v The new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setConfirmRegistration($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->confirm_registration !== $v) {
            $this->confirm_registration = $v;
            $this->modifiedColumns[ShopTableMap::COL_CONFIRM_REGISTRATION] = true;
        }


        return $this;
    } // setConfirmRegistration()

    /**
     * Sets the value of the [enable_registration] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * 
     * @param      boolean|integer|string $v The new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setEnableRegistration($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->enable_registration !== $v) {
            $this->enable_registration = $v;
            $this->modifiedColumns[ShopTableMap::COL_ENABLE_REGISTRATION] = true;
        }


        return $this;
    } // setEnableRegistration()

    /**
     * Set the value of [currency_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->currency_id !== $v) {
            $this->currency_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_CURRENCY_ID] = true;
        }

        if ($this->aCurrency !== null && $this->aCurrency->getId() !== $v) {
            $this->aCurrency = null;
        }


        return $this;
    } // setCurrencyId()

    /**
     * Set the value of [contact_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setContactId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->contact_id !== $v) {
            $this->contact_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_CONTACT_ID] = true;
        }

        if ($this->aContact !== null && $this->aContact->getId() !== $v) {
            $this->aContact = null;
        }


        return $this;
    } // setContactId()

    /**
     * Set the value of [default_vat_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setDefaultVatId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->default_vat_id !== $v) {
            $this->default_vat_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_DEFAULT_VAT_ID] = true;
        }

        if ($this->aVat !== null && $this->aVat->getId() !== $v) {
            $this->aVat = null;
        }


        return $this;
    } // setDefaultVatId()

    /**
     * Set the value of [order_status_groups_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setOrderStatusGroupsId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_status_groups_id !== $v) {
            $this->order_status_groups_id = $v;
            $this->modifiedColumns[ShopTableMap::COL_ORDER_STATUS_GROUPS_ID] = true;
        }

        if ($this->aOrderStatusGroups !== null && $this->aOrderStatusGroups->getId() !== $v) {
            $this->aOrderStatusGroups = null;
        }


        return $this;
    } // setOrderStatusGroupsId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[ShopTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[ShopTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->offline !== 0) {
                return false;
            }

            if ($this->cart_redirect !== 1) {
                return false;
            }

            if ($this->minimum_order_value !== '0.0000') {
                return false;
            }

            if ($this->show_tax !== 1) {
                return false;
            }

            if ($this->enable_opinions !== 1) {
                return false;
            }

            if ($this->enable_tags !== 1) {
                return false;
            }

            if ($this->catalog_mode !== 0) {
                return false;
            }

            if ($this->force_login !== 0) {
                return false;
            }

            if ($this->enable_rss !== 1) {
                return false;
            }

            if ($this->invoice_numeration_kind !== 'ntmr') {
                return false;
            }

            if ($this->invoice_default_payment_due !== 7) {
                return false;
            }

            if ($this->enable_registration !== true) {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ShopTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ShopTableMap::translateFieldName('Url', TableMap::TYPE_PHPNAME, $indexType)];
            $this->url = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ShopTableMap::translateFieldName('CompanyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->company_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ShopTableMap::translateFieldName('PeriodId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->period_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ShopTableMap::translateFieldName('WwwRedirection', TableMap::TYPE_PHPNAME, $indexType)];
            $this->www_redirection = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ShopTableMap::translateFieldName('Taxes', TableMap::TYPE_PHPNAME, $indexType)];
            $this->taxes = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ShopTableMap::translateFieldName('PhotoId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->photo_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : ShopTableMap::translateFieldName('Favicon', TableMap::TYPE_PHPNAME, $indexType)];
            $this->favicon = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : ShopTableMap::translateFieldName('Offline', TableMap::TYPE_PHPNAME, $indexType)];
            $this->offline = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : ShopTableMap::translateFieldName('OfflineText', TableMap::TYPE_PHPNAME, $indexType)];
            $this->offline_text = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : ShopTableMap::translateFieldName('CartRedirect', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cart_redirect = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : ShopTableMap::translateFieldName('MinimumOrderValue', TableMap::TYPE_PHPNAME, $indexType)];
            $this->minimum_order_value = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : ShopTableMap::translateFieldName('ShowTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->show_tax = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : ShopTableMap::translateFieldName('EnableOpinions', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enable_opinions = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : ShopTableMap::translateFieldName('EnableTags', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enable_tags = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : ShopTableMap::translateFieldName('CatalogMode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->catalog_mode = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : ShopTableMap::translateFieldName('ForceLogin', TableMap::TYPE_PHPNAME, $indexType)];
            $this->force_login = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : ShopTableMap::translateFieldName('EnableRss', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enable_rss = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 18 + $startcol : ShopTableMap::translateFieldName('InvoiceNumerationKind', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_numeration_kind = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 19 + $startcol : ShopTableMap::translateFieldName('InvoiceDefaultPaymentDue', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_default_payment_due = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 20 + $startcol : ShopTableMap::translateFieldName('ConfirmRegistration', TableMap::TYPE_PHPNAME, $indexType)];
            $this->confirm_registration = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 21 + $startcol : ShopTableMap::translateFieldName('EnableRegistration', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enable_registration = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 22 + $startcol : ShopTableMap::translateFieldName('CurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 23 + $startcol : ShopTableMap::translateFieldName('ContactId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->contact_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 24 + $startcol : ShopTableMap::translateFieldName('DefaultVatId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->default_vat_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 25 + $startcol : ShopTableMap::translateFieldName('OrderStatusGroupsId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_status_groups_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 26 + $startcol : ShopTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 27 + $startcol : ShopTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 28; // 28 = ShopTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Shop\Model\ORM\Shop object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aCompany !== null && $this->company_id !== $this->aCompany->getId()) {
            $this->aCompany = null;
        }
        if ($this->aCurrency !== null && $this->currency_id !== $this->aCurrency->getId()) {
            $this->aCurrency = null;
        }
        if ($this->aContact !== null && $this->contact_id !== $this->aContact->getId()) {
            $this->aContact = null;
        }
        if ($this->aVat !== null && $this->default_vat_id !== $this->aVat->getId()) {
            $this->aVat = null;
        }
        if ($this->aOrderStatusGroups !== null && $this->order_status_groups_id !== $this->aOrderStatusGroups->getId()) {
            $this->aOrderStatusGroups = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ShopTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildShopQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aContact = null;
            $this->aCurrency = null;
            $this->aVat = null;
            $this->aOrderStatusGroups = null;
            $this->aCompany = null;
            $this->collClients = null;

            $this->collMissingCarts = null;

            $this->collMissingCartProducts = null;

            $this->collOrders = null;

            $this->collProductSearchPhrasess = null;

            $this->collBlogShops = null;

            $this->collCartRuleShops = null;

            $this->collCategoryShops = null;

            $this->collPageShops = null;

            $this->collContactShops = null;

            $this->collCurrencyShops = null;

            $this->collDispatchMethodShops = null;

            $this->collLocaleShops = null;

            $this->collPaymentMethodShops = null;

            $this->collProducerShops = null;

            $this->collUserGroupShops = null;

            $this->collWishlists = null;

            $this->collShopI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Shop::setDeleted()
     * @see Shop::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShopTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildShopQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShopTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(ShopTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ShopTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ShopTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ShopTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aContact !== null) {
                if ($this->aContact->isModified() || $this->aContact->isNew()) {
                    $affectedRows += $this->aContact->save($con);
                }
                $this->setContact($this->aContact);
            }

            if ($this->aCurrency !== null) {
                if ($this->aCurrency->isModified() || $this->aCurrency->isNew()) {
                    $affectedRows += $this->aCurrency->save($con);
                }
                $this->setCurrency($this->aCurrency);
            }

            if ($this->aVat !== null) {
                if ($this->aVat->isModified() || $this->aVat->isNew()) {
                    $affectedRows += $this->aVat->save($con);
                }
                $this->setVat($this->aVat);
            }

            if ($this->aOrderStatusGroups !== null) {
                if ($this->aOrderStatusGroups->isModified() || $this->aOrderStatusGroups->isNew()) {
                    $affectedRows += $this->aOrderStatusGroups->save($con);
                }
                $this->setOrderStatusGroups($this->aOrderStatusGroups);
            }

            if ($this->aCompany !== null) {
                if ($this->aCompany->isModified() || $this->aCompany->isNew()) {
                    $affectedRows += $this->aCompany->save($con);
                }
                $this->setCompany($this->aCompany);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->clientsScheduledForDeletion !== null) {
                if (!$this->clientsScheduledForDeletion->isEmpty()) {
                    foreach ($this->clientsScheduledForDeletion as $client) {
                        // need to save related object because we set the relation to null
                        $client->save($con);
                    }
                    $this->clientsScheduledForDeletion = null;
                }
            }

                if ($this->collClients !== null) {
            foreach ($this->collClients as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->missingCartsScheduledForDeletion !== null) {
                if (!$this->missingCartsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery::create()
                        ->filterByPrimaryKeys($this->missingCartsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->missingCartsScheduledForDeletion = null;
                }
            }

                if ($this->collMissingCarts !== null) {
            foreach ($this->collMissingCarts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->missingCartProductsScheduledForDeletion !== null) {
                if (!$this->missingCartProductsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery::create()
                        ->filterByPrimaryKeys($this->missingCartProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->missingCartProductsScheduledForDeletion = null;
                }
            }

                if ($this->collMissingCartProducts !== null) {
            foreach ($this->collMissingCartProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ordersScheduledForDeletion !== null) {
                if (!$this->ordersScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderQuery::create()
                        ->filterByPrimaryKeys($this->ordersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ordersScheduledForDeletion = null;
                }
            }

                if ($this->collOrders !== null) {
            foreach ($this->collOrders as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productSearchPhrasessScheduledForDeletion !== null) {
                if (!$this->productSearchPhrasessScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrasesQuery::create()
                        ->filterByPrimaryKeys($this->productSearchPhrasessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productSearchPhrasessScheduledForDeletion = null;
                }
            }

                if ($this->collProductSearchPhrasess !== null) {
            foreach ($this->collProductSearchPhrasess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->blogShopsScheduledForDeletion !== null) {
                if (!$this->blogShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Blog\Model\ORM\BlogShopQuery::create()
                        ->filterByPrimaryKeys($this->blogShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->blogShopsScheduledForDeletion = null;
                }
            }

                if ($this->collBlogShops !== null) {
            foreach ($this->collBlogShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->cartRuleShopsScheduledForDeletion !== null) {
                if (!$this->cartRuleShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShopQuery::create()
                        ->filterByPrimaryKeys($this->cartRuleShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cartRuleShopsScheduledForDeletion = null;
                }
            }

                if ($this->collCartRuleShops !== null) {
            foreach ($this->collCartRuleShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->categoryShopsScheduledForDeletion !== null) {
                if (!$this->categoryShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery::create()
                        ->filterByPrimaryKeys($this->categoryShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoryShopsScheduledForDeletion = null;
                }
            }

                if ($this->collCategoryShops !== null) {
            foreach ($this->collCategoryShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->pageShopsScheduledForDeletion !== null) {
                if (!$this->pageShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Page\Model\ORM\PageShopQuery::create()
                        ->filterByPrimaryKeys($this->pageShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->pageShopsScheduledForDeletion = null;
                }
            }

                if ($this->collPageShops !== null) {
            foreach ($this->collPageShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->contactShopsScheduledForDeletion !== null) {
                if (!$this->contactShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Contact\Model\ORM\ContactShopQuery::create()
                        ->filterByPrimaryKeys($this->contactShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->contactShopsScheduledForDeletion = null;
                }
            }

                if ($this->collContactShops !== null) {
            foreach ($this->collContactShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->currencyShopsScheduledForDeletion !== null) {
                if (!$this->currencyShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery::create()
                        ->filterByPrimaryKeys($this->currencyShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->currencyShopsScheduledForDeletion = null;
                }
            }

                if ($this->collCurrencyShops !== null) {
            foreach ($this->collCurrencyShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dispatchMethodShopsScheduledForDeletion !== null) {
                if (!$this->dispatchMethodShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery::create()
                        ->filterByPrimaryKeys($this->dispatchMethodShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dispatchMethodShopsScheduledForDeletion = null;
                }
            }

                if ($this->collDispatchMethodShops !== null) {
            foreach ($this->collDispatchMethodShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->localeShopsScheduledForDeletion !== null) {
                if (!$this->localeShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery::create()
                        ->filterByPrimaryKeys($this->localeShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->localeShopsScheduledForDeletion = null;
                }
            }

                if ($this->collLocaleShops !== null) {
            foreach ($this->collLocaleShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->paymentMethodShopsScheduledForDeletion !== null) {
                if (!$this->paymentMethodShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShopQuery::create()
                        ->filterByPrimaryKeys($this->paymentMethodShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->paymentMethodShopsScheduledForDeletion = null;
                }
            }

                if ($this->collPaymentMethodShops !== null) {
            foreach ($this->collPaymentMethodShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->producerShopsScheduledForDeletion !== null) {
                if (!$this->producerShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Producer\Model\ORM\ProducerShopQuery::create()
                        ->filterByPrimaryKeys($this->producerShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->producerShopsScheduledForDeletion = null;
                }
            }

                if ($this->collProducerShops !== null) {
            foreach ($this->collProducerShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userGroupShopsScheduledForDeletion !== null) {
                if (!$this->userGroupShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery::create()
                        ->filterByPrimaryKeys($this->userGroupShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userGroupShopsScheduledForDeletion = null;
                }
            }

                if ($this->collUserGroupShops !== null) {
            foreach ($this->collUserGroupShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->wishlistsScheduledForDeletion !== null) {
                if (!$this->wishlistsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery::create()
                        ->filterByPrimaryKeys($this->wishlistsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->wishlistsScheduledForDeletion = null;
                }
            }

                if ($this->collWishlists !== null) {
            foreach ($this->collWishlists as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->shopI18nsScheduledForDeletion !== null) {
                if (!$this->shopI18nsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Shop\Model\ORM\ShopI18nQuery::create()
                        ->filterByPrimaryKeys($this->shopI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->shopI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collShopI18ns !== null) {
            foreach ($this->collShopI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[ShopTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ShopTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ShopTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_URL)) {
            $modifiedColumns[':p' . $index++]  = 'URL';
        }
        if ($this->isColumnModified(ShopTableMap::COL_COMPANY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'COMPANY_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_PERIOD_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PERIOD_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_WWW_REDIRECTION)) {
            $modifiedColumns[':p' . $index++]  = 'WWW_REDIRECTION';
        }
        if ($this->isColumnModified(ShopTableMap::COL_TAXES)) {
            $modifiedColumns[':p' . $index++]  = 'TAXES';
        }
        if ($this->isColumnModified(ShopTableMap::COL_PHOTO_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PHOTO_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_FAVICON)) {
            $modifiedColumns[':p' . $index++]  = 'FAVICON';
        }
        if ($this->isColumnModified(ShopTableMap::COL_OFFLINE)) {
            $modifiedColumns[':p' . $index++]  = 'OFFLINE';
        }
        if ($this->isColumnModified(ShopTableMap::COL_OFFLINE_TEXT)) {
            $modifiedColumns[':p' . $index++]  = 'OFFLINE_TEXT';
        }
        if ($this->isColumnModified(ShopTableMap::COL_CART_REDIRECT)) {
            $modifiedColumns[':p' . $index++]  = 'CART_REDIRECT';
        }
        if ($this->isColumnModified(ShopTableMap::COL_MINIMUM_ORDER_VALUE)) {
            $modifiedColumns[':p' . $index++]  = 'MINIMUM_ORDER_VALUE';
        }
        if ($this->isColumnModified(ShopTableMap::COL_SHOW_TAX)) {
            $modifiedColumns[':p' . $index++]  = 'SHOW_TAX';
        }
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_OPINIONS)) {
            $modifiedColumns[':p' . $index++]  = 'ENABLE_OPINIONS';
        }
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_TAGS)) {
            $modifiedColumns[':p' . $index++]  = 'ENABLE_TAGS';
        }
        if ($this->isColumnModified(ShopTableMap::COL_CATALOG_MODE)) {
            $modifiedColumns[':p' . $index++]  = 'CATALOG_MODE';
        }
        if ($this->isColumnModified(ShopTableMap::COL_FORCE_LOGIN)) {
            $modifiedColumns[':p' . $index++]  = 'FORCE_LOGIN';
        }
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_RSS)) {
            $modifiedColumns[':p' . $index++]  = 'ENABLE_RSS';
        }
        if ($this->isColumnModified(ShopTableMap::COL_INVOICE_NUMERATION_KIND)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_NUMERATION_KIND';
        }
        if ($this->isColumnModified(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_DEFAULT_PAYMENT_DUE';
        }
        if ($this->isColumnModified(ShopTableMap::COL_CONFIRM_REGISTRATION)) {
            $modifiedColumns[':p' . $index++]  = 'CONFIRM_REGISTRATION';
        }
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_REGISTRATION)) {
            $modifiedColumns[':p' . $index++]  = 'ENABLE_REGISTRATION';
        }
        if ($this->isColumnModified(ShopTableMap::COL_CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_CONTACT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CONTACT_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_DEFAULT_VAT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'DEFAULT_VAT_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_STATUS_GROUPS_ID';
        }
        if ($this->isColumnModified(ShopTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(ShopTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO shop (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':                        
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'URL':                        
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case 'COMPANY_ID':                        
                        $stmt->bindValue($identifier, $this->company_id, PDO::PARAM_INT);
                        break;
                    case 'PERIOD_ID':                        
                        $stmt->bindValue($identifier, $this->period_id, PDO::PARAM_INT);
                        break;
                    case 'WWW_REDIRECTION':                        
                        $stmt->bindValue($identifier, $this->www_redirection, PDO::PARAM_INT);
                        break;
                    case 'TAXES':                        
                        $stmt->bindValue($identifier, $this->taxes, PDO::PARAM_INT);
                        break;
                    case 'PHOTO_ID':                        
                        $stmt->bindValue($identifier, $this->photo_id, PDO::PARAM_STR);
                        break;
                    case 'FAVICON':                        
                        $stmt->bindValue($identifier, $this->favicon, PDO::PARAM_STR);
                        break;
                    case 'OFFLINE':                        
                        $stmt->bindValue($identifier, $this->offline, PDO::PARAM_INT);
                        break;
                    case 'OFFLINE_TEXT':                        
                        $stmt->bindValue($identifier, $this->offline_text, PDO::PARAM_STR);
                        break;
                    case 'CART_REDIRECT':                        
                        $stmt->bindValue($identifier, $this->cart_redirect, PDO::PARAM_INT);
                        break;
                    case 'MINIMUM_ORDER_VALUE':                        
                        $stmt->bindValue($identifier, $this->minimum_order_value, PDO::PARAM_STR);
                        break;
                    case 'SHOW_TAX':                        
                        $stmt->bindValue($identifier, $this->show_tax, PDO::PARAM_INT);
                        break;
                    case 'ENABLE_OPINIONS':                        
                        $stmt->bindValue($identifier, $this->enable_opinions, PDO::PARAM_INT);
                        break;
                    case 'ENABLE_TAGS':                        
                        $stmt->bindValue($identifier, $this->enable_tags, PDO::PARAM_INT);
                        break;
                    case 'CATALOG_MODE':                        
                        $stmt->bindValue($identifier, $this->catalog_mode, PDO::PARAM_INT);
                        break;
                    case 'FORCE_LOGIN':                        
                        $stmt->bindValue($identifier, $this->force_login, PDO::PARAM_INT);
                        break;
                    case 'ENABLE_RSS':                        
                        $stmt->bindValue($identifier, $this->enable_rss, PDO::PARAM_INT);
                        break;
                    case 'INVOICE_NUMERATION_KIND':                        
                        $stmt->bindValue($identifier, $this->invoice_numeration_kind, PDO::PARAM_STR);
                        break;
                    case 'INVOICE_DEFAULT_PAYMENT_DUE':                        
                        $stmt->bindValue($identifier, $this->invoice_default_payment_due, PDO::PARAM_INT);
                        break;
                    case 'CONFIRM_REGISTRATION':
                        $stmt->bindValue($identifier, (int) $this->confirm_registration, PDO::PARAM_INT);
                        break;
                    case 'ENABLE_REGISTRATION':
                        $stmt->bindValue($identifier, (int) $this->enable_registration, PDO::PARAM_INT);
                        break;
                    case 'CURRENCY_ID':                        
                        $stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
                        break;
                    case 'CONTACT_ID':                        
                        $stmt->bindValue($identifier, $this->contact_id, PDO::PARAM_INT);
                        break;
                    case 'DEFAULT_VAT_ID':                        
                        $stmt->bindValue($identifier, $this->default_vat_id, PDO::PARAM_INT);
                        break;
                    case 'ORDER_STATUS_GROUPS_ID':                        
                        $stmt->bindValue($identifier, $this->order_status_groups_id, PDO::PARAM_INT);
                        break;
                    case 'CREATED_AT':                        
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':                        
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ShopTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getUrl();
                break;
            case 2:
                return $this->getCompanyId();
                break;
            case 3:
                return $this->getPeriodId();
                break;
            case 4:
                return $this->getWwwRedirection();
                break;
            case 5:
                return $this->getTaxes();
                break;
            case 6:
                return $this->getPhotoId();
                break;
            case 7:
                return $this->getFavicon();
                break;
            case 8:
                return $this->getOffline();
                break;
            case 9:
                return $this->getOfflineText();
                break;
            case 10:
                return $this->getCartRedirect();
                break;
            case 11:
                return $this->getMinimumOrderValue();
                break;
            case 12:
                return $this->getShowTax();
                break;
            case 13:
                return $this->getEnableOpinions();
                break;
            case 14:
                return $this->getEnableTags();
                break;
            case 15:
                return $this->getCatalogMode();
                break;
            case 16:
                return $this->getForceLogin();
                break;
            case 17:
                return $this->getEnableRss();
                break;
            case 18:
                return $this->getInvoiceNumerationKind();
                break;
            case 19:
                return $this->getInvoiceDefaultPaymentDue();
                break;
            case 20:
                return $this->getConfirmRegistration();
                break;
            case 21:
                return $this->getEnableRegistration();
                break;
            case 22:
                return $this->getCurrencyId();
                break;
            case 23:
                return $this->getContactId();
                break;
            case 24:
                return $this->getDefaultVatId();
                break;
            case 25:
                return $this->getOrderStatusGroupsId();
                break;
            case 26:
                return $this->getCreatedAt();
                break;
            case 27:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Shop'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Shop'][$this->getPrimaryKey()] = true;
        $keys = ShopTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUrl(),
            $keys[2] => $this->getCompanyId(),
            $keys[3] => $this->getPeriodId(),
            $keys[4] => $this->getWwwRedirection(),
            $keys[5] => $this->getTaxes(),
            $keys[6] => $this->getPhotoId(),
            $keys[7] => $this->getFavicon(),
            $keys[8] => $this->getOffline(),
            $keys[9] => $this->getOfflineText(),
            $keys[10] => $this->getCartRedirect(),
            $keys[11] => $this->getMinimumOrderValue(),
            $keys[12] => $this->getShowTax(),
            $keys[13] => $this->getEnableOpinions(),
            $keys[14] => $this->getEnableTags(),
            $keys[15] => $this->getCatalogMode(),
            $keys[16] => $this->getForceLogin(),
            $keys[17] => $this->getEnableRss(),
            $keys[18] => $this->getInvoiceNumerationKind(),
            $keys[19] => $this->getInvoiceDefaultPaymentDue(),
            $keys[20] => $this->getConfirmRegistration(),
            $keys[21] => $this->getEnableRegistration(),
            $keys[22] => $this->getCurrencyId(),
            $keys[23] => $this->getContactId(),
            $keys[24] => $this->getDefaultVatId(),
            $keys[25] => $this->getOrderStatusGroupsId(),
            $keys[26] => $this->getCreatedAt(),
            $keys[27] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aContact) {
                $result['Contact'] = $this->aContact->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCurrency) {
                $result['Currency'] = $this->aCurrency->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aVat) {
                $result['Vat'] = $this->aVat->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aOrderStatusGroups) {
                $result['OrderStatusGroups'] = $this->aOrderStatusGroups->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCompany) {
                $result['Company'] = $this->aCompany->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collClients) {
                $result['Clients'] = $this->collClients->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMissingCarts) {
                $result['MissingCarts'] = $this->collMissingCarts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMissingCartProducts) {
                $result['MissingCartProducts'] = $this->collMissingCartProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrders) {
                $result['Orders'] = $this->collOrders->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductSearchPhrasess) {
                $result['ProductSearchPhrasess'] = $this->collProductSearchPhrasess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collBlogShops) {
                $result['BlogShops'] = $this->collBlogShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCartRuleShops) {
                $result['CartRuleShops'] = $this->collCartRuleShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategoryShops) {
                $result['CategoryShops'] = $this->collCategoryShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPageShops) {
                $result['PageShops'] = $this->collPageShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collContactShops) {
                $result['ContactShops'] = $this->collContactShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCurrencyShops) {
                $result['CurrencyShops'] = $this->collCurrencyShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDispatchMethodShops) {
                $result['DispatchMethodShops'] = $this->collDispatchMethodShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collLocaleShops) {
                $result['LocaleShops'] = $this->collLocaleShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPaymentMethodShops) {
                $result['PaymentMethodShops'] = $this->collPaymentMethodShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProducerShops) {
                $result['ProducerShops'] = $this->collProducerShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserGroupShops) {
                $result['UserGroupShops'] = $this->collUserGroupShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWishlists) {
                $result['Wishlists'] = $this->collWishlists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collShopI18ns) {
                $result['ShopI18ns'] = $this->collShopI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ShopTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUrl($value);
                break;
            case 2:
                $this->setCompanyId($value);
                break;
            case 3:
                $this->setPeriodId($value);
                break;
            case 4:
                $this->setWwwRedirection($value);
                break;
            case 5:
                $this->setTaxes($value);
                break;
            case 6:
                $this->setPhotoId($value);
                break;
            case 7:
                $this->setFavicon($value);
                break;
            case 8:
                $this->setOffline($value);
                break;
            case 9:
                $this->setOfflineText($value);
                break;
            case 10:
                $this->setCartRedirect($value);
                break;
            case 11:
                $this->setMinimumOrderValue($value);
                break;
            case 12:
                $this->setShowTax($value);
                break;
            case 13:
                $this->setEnableOpinions($value);
                break;
            case 14:
                $this->setEnableTags($value);
                break;
            case 15:
                $this->setCatalogMode($value);
                break;
            case 16:
                $this->setForceLogin($value);
                break;
            case 17:
                $this->setEnableRss($value);
                break;
            case 18:
                $this->setInvoiceNumerationKind($value);
                break;
            case 19:
                $this->setInvoiceDefaultPaymentDue($value);
                break;
            case 20:
                $this->setConfirmRegistration($value);
                break;
            case 21:
                $this->setEnableRegistration($value);
                break;
            case 22:
                $this->setCurrencyId($value);
                break;
            case 23:
                $this->setContactId($value);
                break;
            case 24:
                $this->setDefaultVatId($value);
                break;
            case 25:
                $this->setOrderStatusGroupsId($value);
                break;
            case 26:
                $this->setCreatedAt($value);
                break;
            case 27:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = ShopTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUrl($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCompanyId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPeriodId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setWwwRedirection($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setTaxes($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setPhotoId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setFavicon($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setOffline($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setOfflineText($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setCartRedirect($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setMinimumOrderValue($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setShowTax($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setEnableOpinions($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setEnableTags($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setCatalogMode($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setForceLogin($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setEnableRss($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setInvoiceNumerationKind($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setInvoiceDefaultPaymentDue($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setConfirmRegistration($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setEnableRegistration($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setCurrencyId($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setContactId($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setDefaultVatId($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setOrderStatusGroupsId($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setCreatedAt($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setUpdatedAt($arr[$keys[27]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ShopTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ShopTableMap::COL_ID)) $criteria->add(ShopTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(ShopTableMap::COL_URL)) $criteria->add(ShopTableMap::COL_URL, $this->url);
        if ($this->isColumnModified(ShopTableMap::COL_COMPANY_ID)) $criteria->add(ShopTableMap::COL_COMPANY_ID, $this->company_id);
        if ($this->isColumnModified(ShopTableMap::COL_PERIOD_ID)) $criteria->add(ShopTableMap::COL_PERIOD_ID, $this->period_id);
        if ($this->isColumnModified(ShopTableMap::COL_WWW_REDIRECTION)) $criteria->add(ShopTableMap::COL_WWW_REDIRECTION, $this->www_redirection);
        if ($this->isColumnModified(ShopTableMap::COL_TAXES)) $criteria->add(ShopTableMap::COL_TAXES, $this->taxes);
        if ($this->isColumnModified(ShopTableMap::COL_PHOTO_ID)) $criteria->add(ShopTableMap::COL_PHOTO_ID, $this->photo_id);
        if ($this->isColumnModified(ShopTableMap::COL_FAVICON)) $criteria->add(ShopTableMap::COL_FAVICON, $this->favicon);
        if ($this->isColumnModified(ShopTableMap::COL_OFFLINE)) $criteria->add(ShopTableMap::COL_OFFLINE, $this->offline);
        if ($this->isColumnModified(ShopTableMap::COL_OFFLINE_TEXT)) $criteria->add(ShopTableMap::COL_OFFLINE_TEXT, $this->offline_text);
        if ($this->isColumnModified(ShopTableMap::COL_CART_REDIRECT)) $criteria->add(ShopTableMap::COL_CART_REDIRECT, $this->cart_redirect);
        if ($this->isColumnModified(ShopTableMap::COL_MINIMUM_ORDER_VALUE)) $criteria->add(ShopTableMap::COL_MINIMUM_ORDER_VALUE, $this->minimum_order_value);
        if ($this->isColumnModified(ShopTableMap::COL_SHOW_TAX)) $criteria->add(ShopTableMap::COL_SHOW_TAX, $this->show_tax);
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_OPINIONS)) $criteria->add(ShopTableMap::COL_ENABLE_OPINIONS, $this->enable_opinions);
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_TAGS)) $criteria->add(ShopTableMap::COL_ENABLE_TAGS, $this->enable_tags);
        if ($this->isColumnModified(ShopTableMap::COL_CATALOG_MODE)) $criteria->add(ShopTableMap::COL_CATALOG_MODE, $this->catalog_mode);
        if ($this->isColumnModified(ShopTableMap::COL_FORCE_LOGIN)) $criteria->add(ShopTableMap::COL_FORCE_LOGIN, $this->force_login);
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_RSS)) $criteria->add(ShopTableMap::COL_ENABLE_RSS, $this->enable_rss);
        if ($this->isColumnModified(ShopTableMap::COL_INVOICE_NUMERATION_KIND)) $criteria->add(ShopTableMap::COL_INVOICE_NUMERATION_KIND, $this->invoice_numeration_kind);
        if ($this->isColumnModified(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE)) $criteria->add(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE, $this->invoice_default_payment_due);
        if ($this->isColumnModified(ShopTableMap::COL_CONFIRM_REGISTRATION)) $criteria->add(ShopTableMap::COL_CONFIRM_REGISTRATION, $this->confirm_registration);
        if ($this->isColumnModified(ShopTableMap::COL_ENABLE_REGISTRATION)) $criteria->add(ShopTableMap::COL_ENABLE_REGISTRATION, $this->enable_registration);
        if ($this->isColumnModified(ShopTableMap::COL_CURRENCY_ID)) $criteria->add(ShopTableMap::COL_CURRENCY_ID, $this->currency_id);
        if ($this->isColumnModified(ShopTableMap::COL_CONTACT_ID)) $criteria->add(ShopTableMap::COL_CONTACT_ID, $this->contact_id);
        if ($this->isColumnModified(ShopTableMap::COL_DEFAULT_VAT_ID)) $criteria->add(ShopTableMap::COL_DEFAULT_VAT_ID, $this->default_vat_id);
        if ($this->isColumnModified(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID)) $criteria->add(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, $this->order_status_groups_id);
        if ($this->isColumnModified(ShopTableMap::COL_CREATED_AT)) $criteria->add(ShopTableMap::COL_CREATED_AT, $this->created_at);
        if ($this->isColumnModified(ShopTableMap::COL_UPDATED_AT)) $criteria->add(ShopTableMap::COL_UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(ShopTableMap::DATABASE_NAME);
        $criteria->add(ShopTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Gekosale\Plugin\Shop\Model\ORM\Shop (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUrl($this->getUrl());
        $copyObj->setCompanyId($this->getCompanyId());
        $copyObj->setPeriodId($this->getPeriodId());
        $copyObj->setWwwRedirection($this->getWwwRedirection());
        $copyObj->setTaxes($this->getTaxes());
        $copyObj->setPhotoId($this->getPhotoId());
        $copyObj->setFavicon($this->getFavicon());
        $copyObj->setOffline($this->getOffline());
        $copyObj->setOfflineText($this->getOfflineText());
        $copyObj->setCartRedirect($this->getCartRedirect());
        $copyObj->setMinimumOrderValue($this->getMinimumOrderValue());
        $copyObj->setShowTax($this->getShowTax());
        $copyObj->setEnableOpinions($this->getEnableOpinions());
        $copyObj->setEnableTags($this->getEnableTags());
        $copyObj->setCatalogMode($this->getCatalogMode());
        $copyObj->setForceLogin($this->getForceLogin());
        $copyObj->setEnableRss($this->getEnableRss());
        $copyObj->setInvoiceNumerationKind($this->getInvoiceNumerationKind());
        $copyObj->setInvoiceDefaultPaymentDue($this->getInvoiceDefaultPaymentDue());
        $copyObj->setConfirmRegistration($this->getConfirmRegistration());
        $copyObj->setEnableRegistration($this->getEnableRegistration());
        $copyObj->setCurrencyId($this->getCurrencyId());
        $copyObj->setContactId($this->getContactId());
        $copyObj->setDefaultVatId($this->getDefaultVatId());
        $copyObj->setOrderStatusGroupsId($this->getOrderStatusGroupsId());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getClients() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClient($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMissingCarts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMissingCart($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMissingCartProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMissingCartProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrders() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrder($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductSearchPhrasess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductSearchPhrases($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getBlogShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBlogShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCartRuleShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCartRuleShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategoryShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPageShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPageShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getContactShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addContactShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCurrencyShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCurrencyShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDispatchMethodShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDispatchMethodShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getLocaleShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addLocaleShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPaymentMethodShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPaymentMethodShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProducerShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProducerShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserGroupShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserGroupShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWishlists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWishlist($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getShopI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addShopI18n($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Gekosale\Plugin\Shop\Model\ORM\Shop Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildContact object.
     *
     * @param                  ChildContact $v
     * @return                 \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     * @throws PropelException
     */
    public function setContact(ChildContact $v = null)
    {
        if ($v === null) {
            $this->setContactId(NULL);
        } else {
            $this->setContactId($v->getId());
        }

        $this->aContact = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildContact object, it will not be re-added.
        if ($v !== null) {
            $v->addShop($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildContact object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildContact The associated ChildContact object.
     * @throws PropelException
     */
    public function getContact(ConnectionInterface $con = null)
    {
        if ($this->aContact === null && ($this->contact_id !== null)) {
            $this->aContact = ContactQuery::create()->findPk($this->contact_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aContact->addShops($this);
             */
        }

        return $this->aContact;
    }

    /**
     * Declares an association between this object and a ChildCurrency object.
     *
     * @param                  ChildCurrency $v
     * @return                 \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCurrency(ChildCurrency $v = null)
    {
        if ($v === null) {
            $this->setCurrencyId(NULL);
        } else {
            $this->setCurrencyId($v->getId());
        }

        $this->aCurrency = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCurrency object, it will not be re-added.
        if ($v !== null) {
            $v->addShop($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCurrency object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCurrency The associated ChildCurrency object.
     * @throws PropelException
     */
    public function getCurrency(ConnectionInterface $con = null)
    {
        if ($this->aCurrency === null && ($this->currency_id !== null)) {
            $this->aCurrency = CurrencyQuery::create()->findPk($this->currency_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCurrency->addShops($this);
             */
        }

        return $this->aCurrency;
    }

    /**
     * Declares an association between this object and a ChildVat object.
     *
     * @param                  ChildVat $v
     * @return                 \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     * @throws PropelException
     */
    public function setVat(ChildVat $v = null)
    {
        if ($v === null) {
            $this->setDefaultVatId(NULL);
        } else {
            $this->setDefaultVatId($v->getId());
        }

        $this->aVat = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildVat object, it will not be re-added.
        if ($v !== null) {
            $v->addShop($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildVat object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildVat The associated ChildVat object.
     * @throws PropelException
     */
    public function getVat(ConnectionInterface $con = null)
    {
        if ($this->aVat === null && ($this->default_vat_id !== null)) {
            $this->aVat = VatQuery::create()->findPk($this->default_vat_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aVat->addShops($this);
             */
        }

        return $this->aVat;
    }

    /**
     * Declares an association between this object and a ChildOrderStatusGroups object.
     *
     * @param                  ChildOrderStatusGroups $v
     * @return                 \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrderStatusGroups(ChildOrderStatusGroups $v = null)
    {
        if ($v === null) {
            $this->setOrderStatusGroupsId(NULL);
        } else {
            $this->setOrderStatusGroupsId($v->getId());
        }

        $this->aOrderStatusGroups = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildOrderStatusGroups object, it will not be re-added.
        if ($v !== null) {
            $v->addShop($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildOrderStatusGroups object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildOrderStatusGroups The associated ChildOrderStatusGroups object.
     * @throws PropelException
     */
    public function getOrderStatusGroups(ConnectionInterface $con = null)
    {
        if ($this->aOrderStatusGroups === null && ($this->order_status_groups_id !== null)) {
            $this->aOrderStatusGroups = OrderStatusGroupsQuery::create()->findPk($this->order_status_groups_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aOrderStatusGroups->addShops($this);
             */
        }

        return $this->aOrderStatusGroups;
    }

    /**
     * Declares an association between this object and a ChildCompany object.
     *
     * @param                  ChildCompany $v
     * @return                 \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCompany(ChildCompany $v = null)
    {
        if ($v === null) {
            $this->setCompanyId(NULL);
        } else {
            $this->setCompanyId($v->getId());
        }

        $this->aCompany = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCompany object, it will not be re-added.
        if ($v !== null) {
            $v->addShop($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCompany object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCompany The associated ChildCompany object.
     * @throws PropelException
     */
    public function getCompany(ConnectionInterface $con = null)
    {
        if ($this->aCompany === null && ($this->company_id !== null)) {
            $this->aCompany = CompanyQuery::create()->findPk($this->company_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCompany->addShops($this);
             */
        }

        return $this->aCompany;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Client' == $relationName) {
            return $this->initClients();
        }
        if ('MissingCart' == $relationName) {
            return $this->initMissingCarts();
        }
        if ('MissingCartProduct' == $relationName) {
            return $this->initMissingCartProducts();
        }
        if ('Order' == $relationName) {
            return $this->initOrders();
        }
        if ('ProductSearchPhrases' == $relationName) {
            return $this->initProductSearchPhrasess();
        }
        if ('BlogShop' == $relationName) {
            return $this->initBlogShops();
        }
        if ('CartRuleShop' == $relationName) {
            return $this->initCartRuleShops();
        }
        if ('CategoryShop' == $relationName) {
            return $this->initCategoryShops();
        }
        if ('PageShop' == $relationName) {
            return $this->initPageShops();
        }
        if ('ContactShop' == $relationName) {
            return $this->initContactShops();
        }
        if ('CurrencyShop' == $relationName) {
            return $this->initCurrencyShops();
        }
        if ('DispatchMethodShop' == $relationName) {
            return $this->initDispatchMethodShops();
        }
        if ('LocaleShop' == $relationName) {
            return $this->initLocaleShops();
        }
        if ('PaymentMethodShop' == $relationName) {
            return $this->initPaymentMethodShops();
        }
        if ('ProducerShop' == $relationName) {
            return $this->initProducerShops();
        }
        if ('UserGroupShop' == $relationName) {
            return $this->initUserGroupShops();
        }
        if ('Wishlist' == $relationName) {
            return $this->initWishlists();
        }
        if ('ShopI18n' == $relationName) {
            return $this->initShopI18ns();
        }
    }

    /**
     * Clears out the collClients collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addClients()
     */
    public function clearClients()
    {
        $this->collClients = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collClients collection loaded partially.
     */
    public function resetPartialClients($v = true)
    {
        $this->collClientsPartial = $v;
    }

    /**
     * Initializes the collClients collection.
     *
     * By default this just sets the collClients collection to an empty array (like clearcollClients());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initClients($overrideExisting = true)
    {
        if (null !== $this->collClients && !$overrideExisting) {
            return;
        }
        $this->collClients = new ObjectCollection();
        $this->collClients->setModel('\Gekosale\Plugin\Client\Model\ORM\Client');
    }

    /**
     * Gets an array of ChildClient objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildClient[] List of ChildClient objects
     * @throws PropelException
     */
    public function getClients($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collClientsPartial && !$this->isNew();
        if (null === $this->collClients || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collClients) {
                // return empty collection
                $this->initClients();
            } else {
                $collClients = ClientQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collClientsPartial && count($collClients)) {
                        $this->initClients(false);

                        foreach ($collClients as $obj) {
                            if (false == $this->collClients->contains($obj)) {
                                $this->collClients->append($obj);
                            }
                        }

                        $this->collClientsPartial = true;
                    }

                    reset($collClients);

                    return $collClients;
                }

                if ($partial && $this->collClients) {
                    foreach ($this->collClients as $obj) {
                        if ($obj->isNew()) {
                            $collClients[] = $obj;
                        }
                    }
                }

                $this->collClients = $collClients;
                $this->collClientsPartial = false;
            }
        }

        return $this->collClients;
    }

    /**
     * Sets a collection of Client objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $clients A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setClients(Collection $clients, ConnectionInterface $con = null)
    {
        $clientsToDelete = $this->getClients(new Criteria(), $con)->diff($clients);

        
        $this->clientsScheduledForDeletion = $clientsToDelete;

        foreach ($clientsToDelete as $clientRemoved) {
            $clientRemoved->setShop(null);
        }

        $this->collClients = null;
        foreach ($clients as $client) {
            $this->addClient($client);
        }

        $this->collClients = $clients;
        $this->collClientsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Client objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Client objects.
     * @throws PropelException
     */
    public function countClients(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collClientsPartial && !$this->isNew();
        if (null === $this->collClients || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collClients) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getClients());
            }

            $query = ClientQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collClients);
    }

    /**
     * Method called to associate a ChildClient object to this object
     * through the ChildClient foreign key attribute.
     *
     * @param    ChildClient $l ChildClient
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addClient(ChildClient $l)
    {
        if ($this->collClients === null) {
            $this->initClients();
            $this->collClientsPartial = true;
        }

        if (!in_array($l, $this->collClients->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddClient($l);
        }

        return $this;
    }

    /**
     * @param Client $client The client object to add.
     */
    protected function doAddClient($client)
    {
        $this->collClients[]= $client;
        $client->setShop($this);
    }

    /**
     * @param  Client $client The client object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeClient($client)
    {
        if ($this->getClients()->contains($client)) {
            $this->collClients->remove($this->collClients->search($client));
            if (null === $this->clientsScheduledForDeletion) {
                $this->clientsScheduledForDeletion = clone $this->collClients;
                $this->clientsScheduledForDeletion->clear();
            }
            $this->clientsScheduledForDeletion[]= $client;
            $client->setShop(null);
        }

        return $this;
    }

    /**
     * Clears out the collMissingCarts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addMissingCarts()
     */
    public function clearMissingCarts()
    {
        $this->collMissingCarts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collMissingCarts collection loaded partially.
     */
    public function resetPartialMissingCarts($v = true)
    {
        $this->collMissingCartsPartial = $v;
    }

    /**
     * Initializes the collMissingCarts collection.
     *
     * By default this just sets the collMissingCarts collection to an empty array (like clearcollMissingCarts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMissingCarts($overrideExisting = true)
    {
        if (null !== $this->collMissingCarts && !$overrideExisting) {
            return;
        }
        $this->collMissingCarts = new ObjectCollection();
        $this->collMissingCarts->setModel('\Gekosale\Plugin\MissingCart\Model\ORM\MissingCart');
    }

    /**
     * Gets an array of ChildMissingCart objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildMissingCart[] List of ChildMissingCart objects
     * @throws PropelException
     */
    public function getMissingCarts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartsPartial && !$this->isNew();
        if (null === $this->collMissingCarts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMissingCarts) {
                // return empty collection
                $this->initMissingCarts();
            } else {
                $collMissingCarts = MissingCartQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collMissingCartsPartial && count($collMissingCarts)) {
                        $this->initMissingCarts(false);

                        foreach ($collMissingCarts as $obj) {
                            if (false == $this->collMissingCarts->contains($obj)) {
                                $this->collMissingCarts->append($obj);
                            }
                        }

                        $this->collMissingCartsPartial = true;
                    }

                    reset($collMissingCarts);

                    return $collMissingCarts;
                }

                if ($partial && $this->collMissingCarts) {
                    foreach ($this->collMissingCarts as $obj) {
                        if ($obj->isNew()) {
                            $collMissingCarts[] = $obj;
                        }
                    }
                }

                $this->collMissingCarts = $collMissingCarts;
                $this->collMissingCartsPartial = false;
            }
        }

        return $this->collMissingCarts;
    }

    /**
     * Sets a collection of MissingCart objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $missingCarts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setMissingCarts(Collection $missingCarts, ConnectionInterface $con = null)
    {
        $missingCartsToDelete = $this->getMissingCarts(new Criteria(), $con)->diff($missingCarts);

        
        $this->missingCartsScheduledForDeletion = $missingCartsToDelete;

        foreach ($missingCartsToDelete as $missingCartRemoved) {
            $missingCartRemoved->setShop(null);
        }

        $this->collMissingCarts = null;
        foreach ($missingCarts as $missingCart) {
            $this->addMissingCart($missingCart);
        }

        $this->collMissingCarts = $missingCarts;
        $this->collMissingCartsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MissingCart objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related MissingCart objects.
     * @throws PropelException
     */
    public function countMissingCarts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartsPartial && !$this->isNew();
        if (null === $this->collMissingCarts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMissingCarts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMissingCarts());
            }

            $query = MissingCartQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collMissingCarts);
    }

    /**
     * Method called to associate a ChildMissingCart object to this object
     * through the ChildMissingCart foreign key attribute.
     *
     * @param    ChildMissingCart $l ChildMissingCart
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addMissingCart(ChildMissingCart $l)
    {
        if ($this->collMissingCarts === null) {
            $this->initMissingCarts();
            $this->collMissingCartsPartial = true;
        }

        if (!in_array($l, $this->collMissingCarts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMissingCart($l);
        }

        return $this;
    }

    /**
     * @param MissingCart $missingCart The missingCart object to add.
     */
    protected function doAddMissingCart($missingCart)
    {
        $this->collMissingCarts[]= $missingCart;
        $missingCart->setShop($this);
    }

    /**
     * @param  MissingCart $missingCart The missingCart object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeMissingCart($missingCart)
    {
        if ($this->getMissingCarts()->contains($missingCart)) {
            $this->collMissingCarts->remove($this->collMissingCarts->search($missingCart));
            if (null === $this->missingCartsScheduledForDeletion) {
                $this->missingCartsScheduledForDeletion = clone $this->collMissingCarts;
                $this->missingCartsScheduledForDeletion->clear();
            }
            $this->missingCartsScheduledForDeletion[]= $missingCart;
            $missingCart->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related MissingCarts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildMissingCart[] List of ChildMissingCart objects
     */
    public function getMissingCartsJoinClient($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = MissingCartQuery::create(null, $criteria);
        $query->joinWith('Client', $joinBehavior);

        return $this->getMissingCarts($query, $con);
    }

    /**
     * Clears out the collMissingCartProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addMissingCartProducts()
     */
    public function clearMissingCartProducts()
    {
        $this->collMissingCartProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collMissingCartProducts collection loaded partially.
     */
    public function resetPartialMissingCartProducts($v = true)
    {
        $this->collMissingCartProductsPartial = $v;
    }

    /**
     * Initializes the collMissingCartProducts collection.
     *
     * By default this just sets the collMissingCartProducts collection to an empty array (like clearcollMissingCartProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMissingCartProducts($overrideExisting = true)
    {
        if (null !== $this->collMissingCartProducts && !$overrideExisting) {
            return;
        }
        $this->collMissingCartProducts = new ObjectCollection();
        $this->collMissingCartProducts->setModel('\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct');
    }

    /**
     * Gets an array of ChildMissingCartProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildMissingCartProduct[] List of ChildMissingCartProduct objects
     * @throws PropelException
     */
    public function getMissingCartProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartProductsPartial && !$this->isNew();
        if (null === $this->collMissingCartProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMissingCartProducts) {
                // return empty collection
                $this->initMissingCartProducts();
            } else {
                $collMissingCartProducts = MissingCartProductQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collMissingCartProductsPartial && count($collMissingCartProducts)) {
                        $this->initMissingCartProducts(false);

                        foreach ($collMissingCartProducts as $obj) {
                            if (false == $this->collMissingCartProducts->contains($obj)) {
                                $this->collMissingCartProducts->append($obj);
                            }
                        }

                        $this->collMissingCartProductsPartial = true;
                    }

                    reset($collMissingCartProducts);

                    return $collMissingCartProducts;
                }

                if ($partial && $this->collMissingCartProducts) {
                    foreach ($this->collMissingCartProducts as $obj) {
                        if ($obj->isNew()) {
                            $collMissingCartProducts[] = $obj;
                        }
                    }
                }

                $this->collMissingCartProducts = $collMissingCartProducts;
                $this->collMissingCartProductsPartial = false;
            }
        }

        return $this->collMissingCartProducts;
    }

    /**
     * Sets a collection of MissingCartProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $missingCartProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setMissingCartProducts(Collection $missingCartProducts, ConnectionInterface $con = null)
    {
        $missingCartProductsToDelete = $this->getMissingCartProducts(new Criteria(), $con)->diff($missingCartProducts);

        
        $this->missingCartProductsScheduledForDeletion = $missingCartProductsToDelete;

        foreach ($missingCartProductsToDelete as $missingCartProductRemoved) {
            $missingCartProductRemoved->setShop(null);
        }

        $this->collMissingCartProducts = null;
        foreach ($missingCartProducts as $missingCartProduct) {
            $this->addMissingCartProduct($missingCartProduct);
        }

        $this->collMissingCartProducts = $missingCartProducts;
        $this->collMissingCartProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MissingCartProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related MissingCartProduct objects.
     * @throws PropelException
     */
    public function countMissingCartProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartProductsPartial && !$this->isNew();
        if (null === $this->collMissingCartProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMissingCartProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMissingCartProducts());
            }

            $query = MissingCartProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collMissingCartProducts);
    }

    /**
     * Method called to associate a ChildMissingCartProduct object to this object
     * through the ChildMissingCartProduct foreign key attribute.
     *
     * @param    ChildMissingCartProduct $l ChildMissingCartProduct
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addMissingCartProduct(ChildMissingCartProduct $l)
    {
        if ($this->collMissingCartProducts === null) {
            $this->initMissingCartProducts();
            $this->collMissingCartProductsPartial = true;
        }

        if (!in_array($l, $this->collMissingCartProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMissingCartProduct($l);
        }

        return $this;
    }

    /**
     * @param MissingCartProduct $missingCartProduct The missingCartProduct object to add.
     */
    protected function doAddMissingCartProduct($missingCartProduct)
    {
        $this->collMissingCartProducts[]= $missingCartProduct;
        $missingCartProduct->setShop($this);
    }

    /**
     * @param  MissingCartProduct $missingCartProduct The missingCartProduct object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeMissingCartProduct($missingCartProduct)
    {
        if ($this->getMissingCartProducts()->contains($missingCartProduct)) {
            $this->collMissingCartProducts->remove($this->collMissingCartProducts->search($missingCartProduct));
            if (null === $this->missingCartProductsScheduledForDeletion) {
                $this->missingCartProductsScheduledForDeletion = clone $this->collMissingCartProducts;
                $this->missingCartProductsScheduledForDeletion->clear();
            }
            $this->missingCartProductsScheduledForDeletion[]= $missingCartProduct;
            $missingCartProduct->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related MissingCartProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildMissingCartProduct[] List of ChildMissingCartProduct objects
     */
    public function getMissingCartProductsJoinMissingCart($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = MissingCartProductQuery::create(null, $criteria);
        $query->joinWith('MissingCart', $joinBehavior);

        return $this->getMissingCartProducts($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related MissingCartProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildMissingCartProduct[] List of ChildMissingCartProduct objects
     */
    public function getMissingCartProductsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = MissingCartProductQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getMissingCartProducts($query, $con);
    }

    /**
     * Clears out the collOrders collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrders()
     */
    public function clearOrders()
    {
        $this->collOrders = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrders collection loaded partially.
     */
    public function resetPartialOrders($v = true)
    {
        $this->collOrdersPartial = $v;
    }

    /**
     * Initializes the collOrders collection.
     *
     * By default this just sets the collOrders collection to an empty array (like clearcollOrders());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrders($overrideExisting = true)
    {
        if (null !== $this->collOrders && !$overrideExisting) {
            return;
        }
        $this->collOrders = new ObjectCollection();
        $this->collOrders->setModel('\Gekosale\Plugin\Order\Model\ORM\Order');
    }

    /**
     * Gets an array of ChildOrder objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrder[] List of ChildOrder objects
     * @throws PropelException
     */
    public function getOrders($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrdersPartial && !$this->isNew();
        if (null === $this->collOrders || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrders) {
                // return empty collection
                $this->initOrders();
            } else {
                $collOrders = OrderQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrdersPartial && count($collOrders)) {
                        $this->initOrders(false);

                        foreach ($collOrders as $obj) {
                            if (false == $this->collOrders->contains($obj)) {
                                $this->collOrders->append($obj);
                            }
                        }

                        $this->collOrdersPartial = true;
                    }

                    reset($collOrders);

                    return $collOrders;
                }

                if ($partial && $this->collOrders) {
                    foreach ($this->collOrders as $obj) {
                        if ($obj->isNew()) {
                            $collOrders[] = $obj;
                        }
                    }
                }

                $this->collOrders = $collOrders;
                $this->collOrdersPartial = false;
            }
        }

        return $this->collOrders;
    }

    /**
     * Sets a collection of Order objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orders A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setOrders(Collection $orders, ConnectionInterface $con = null)
    {
        $ordersToDelete = $this->getOrders(new Criteria(), $con)->diff($orders);

        
        $this->ordersScheduledForDeletion = $ordersToDelete;

        foreach ($ordersToDelete as $orderRemoved) {
            $orderRemoved->setShop(null);
        }

        $this->collOrders = null;
        foreach ($orders as $order) {
            $this->addOrder($order);
        }

        $this->collOrders = $orders;
        $this->collOrdersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Order objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Order objects.
     * @throws PropelException
     */
    public function countOrders(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrdersPartial && !$this->isNew();
        if (null === $this->collOrders || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrders) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrders());
            }

            $query = OrderQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collOrders);
    }

    /**
     * Method called to associate a ChildOrder object to this object
     * through the ChildOrder foreign key attribute.
     *
     * @param    ChildOrder $l ChildOrder
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addOrder(ChildOrder $l)
    {
        if ($this->collOrders === null) {
            $this->initOrders();
            $this->collOrdersPartial = true;
        }

        if (!in_array($l, $this->collOrders->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrder($l);
        }

        return $this;
    }

    /**
     * @param Order $order The order object to add.
     */
    protected function doAddOrder($order)
    {
        $this->collOrders[]= $order;
        $order->setShop($this);
    }

    /**
     * @param  Order $order The order object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeOrder($order)
    {
        if ($this->getOrders()->contains($order)) {
            $this->collOrders->remove($this->collOrders->search($order));
            if (null === $this->ordersScheduledForDeletion) {
                $this->ordersScheduledForDeletion = clone $this->collOrders;
                $this->ordersScheduledForDeletion->clear();
            }
            $this->ordersScheduledForDeletion[]= $order;
            $order->setShop(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductSearchPhrasess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductSearchPhrasess()
     */
    public function clearProductSearchPhrasess()
    {
        $this->collProductSearchPhrasess = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductSearchPhrasess collection loaded partially.
     */
    public function resetPartialProductSearchPhrasess($v = true)
    {
        $this->collProductSearchPhrasessPartial = $v;
    }

    /**
     * Initializes the collProductSearchPhrasess collection.
     *
     * By default this just sets the collProductSearchPhrasess collection to an empty array (like clearcollProductSearchPhrasess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductSearchPhrasess($overrideExisting = true)
    {
        if (null !== $this->collProductSearchPhrasess && !$overrideExisting) {
            return;
        }
        $this->collProductSearchPhrasess = new ObjectCollection();
        $this->collProductSearchPhrasess->setModel('\Gekosale\Plugin\Search\Model\ORM\ProductSearchPhrases');
    }

    /**
     * Gets an array of ChildProductSearchPhrases objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductSearchPhrases[] List of ChildProductSearchPhrases objects
     * @throws PropelException
     */
    public function getProductSearchPhrasess($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductSearchPhrasessPartial && !$this->isNew();
        if (null === $this->collProductSearchPhrasess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductSearchPhrasess) {
                // return empty collection
                $this->initProductSearchPhrasess();
            } else {
                $collProductSearchPhrasess = ProductSearchPhrasesQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductSearchPhrasessPartial && count($collProductSearchPhrasess)) {
                        $this->initProductSearchPhrasess(false);

                        foreach ($collProductSearchPhrasess as $obj) {
                            if (false == $this->collProductSearchPhrasess->contains($obj)) {
                                $this->collProductSearchPhrasess->append($obj);
                            }
                        }

                        $this->collProductSearchPhrasessPartial = true;
                    }

                    reset($collProductSearchPhrasess);

                    return $collProductSearchPhrasess;
                }

                if ($partial && $this->collProductSearchPhrasess) {
                    foreach ($this->collProductSearchPhrasess as $obj) {
                        if ($obj->isNew()) {
                            $collProductSearchPhrasess[] = $obj;
                        }
                    }
                }

                $this->collProductSearchPhrasess = $collProductSearchPhrasess;
                $this->collProductSearchPhrasessPartial = false;
            }
        }

        return $this->collProductSearchPhrasess;
    }

    /**
     * Sets a collection of ProductSearchPhrases objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productSearchPhrasess A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setProductSearchPhrasess(Collection $productSearchPhrasess, ConnectionInterface $con = null)
    {
        $productSearchPhrasessToDelete = $this->getProductSearchPhrasess(new Criteria(), $con)->diff($productSearchPhrasess);

        
        $this->productSearchPhrasessScheduledForDeletion = $productSearchPhrasessToDelete;

        foreach ($productSearchPhrasessToDelete as $productSearchPhrasesRemoved) {
            $productSearchPhrasesRemoved->setShop(null);
        }

        $this->collProductSearchPhrasess = null;
        foreach ($productSearchPhrasess as $productSearchPhrases) {
            $this->addProductSearchPhrases($productSearchPhrases);
        }

        $this->collProductSearchPhrasess = $productSearchPhrasess;
        $this->collProductSearchPhrasessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductSearchPhrases objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductSearchPhrases objects.
     * @throws PropelException
     */
    public function countProductSearchPhrasess(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductSearchPhrasessPartial && !$this->isNew();
        if (null === $this->collProductSearchPhrasess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductSearchPhrasess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductSearchPhrasess());
            }

            $query = ProductSearchPhrasesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collProductSearchPhrasess);
    }

    /**
     * Method called to associate a ChildProductSearchPhrases object to this object
     * through the ChildProductSearchPhrases foreign key attribute.
     *
     * @param    ChildProductSearchPhrases $l ChildProductSearchPhrases
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addProductSearchPhrases(ChildProductSearchPhrases $l)
    {
        if ($this->collProductSearchPhrasess === null) {
            $this->initProductSearchPhrasess();
            $this->collProductSearchPhrasessPartial = true;
        }

        if (!in_array($l, $this->collProductSearchPhrasess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductSearchPhrases($l);
        }

        return $this;
    }

    /**
     * @param ProductSearchPhrases $productSearchPhrases The productSearchPhrases object to add.
     */
    protected function doAddProductSearchPhrases($productSearchPhrases)
    {
        $this->collProductSearchPhrasess[]= $productSearchPhrases;
        $productSearchPhrases->setShop($this);
    }

    /**
     * @param  ProductSearchPhrases $productSearchPhrases The productSearchPhrases object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeProductSearchPhrases($productSearchPhrases)
    {
        if ($this->getProductSearchPhrasess()->contains($productSearchPhrases)) {
            $this->collProductSearchPhrasess->remove($this->collProductSearchPhrasess->search($productSearchPhrases));
            if (null === $this->productSearchPhrasessScheduledForDeletion) {
                $this->productSearchPhrasessScheduledForDeletion = clone $this->collProductSearchPhrasess;
                $this->productSearchPhrasessScheduledForDeletion->clear();
            }
            $this->productSearchPhrasessScheduledForDeletion[]= clone $productSearchPhrases;
            $productSearchPhrases->setShop(null);
        }

        return $this;
    }

    /**
     * Clears out the collBlogShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addBlogShops()
     */
    public function clearBlogShops()
    {
        $this->collBlogShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collBlogShops collection loaded partially.
     */
    public function resetPartialBlogShops($v = true)
    {
        $this->collBlogShopsPartial = $v;
    }

    /**
     * Initializes the collBlogShops collection.
     *
     * By default this just sets the collBlogShops collection to an empty array (like clearcollBlogShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBlogShops($overrideExisting = true)
    {
        if (null !== $this->collBlogShops && !$overrideExisting) {
            return;
        }
        $this->collBlogShops = new ObjectCollection();
        $this->collBlogShops->setModel('\Gekosale\Plugin\Blog\Model\ORM\BlogShop');
    }

    /**
     * Gets an array of ChildBlogShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildBlogShop[] List of ChildBlogShop objects
     * @throws PropelException
     */
    public function getBlogShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collBlogShopsPartial && !$this->isNew();
        if (null === $this->collBlogShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBlogShops) {
                // return empty collection
                $this->initBlogShops();
            } else {
                $collBlogShops = BlogShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collBlogShopsPartial && count($collBlogShops)) {
                        $this->initBlogShops(false);

                        foreach ($collBlogShops as $obj) {
                            if (false == $this->collBlogShops->contains($obj)) {
                                $this->collBlogShops->append($obj);
                            }
                        }

                        $this->collBlogShopsPartial = true;
                    }

                    reset($collBlogShops);

                    return $collBlogShops;
                }

                if ($partial && $this->collBlogShops) {
                    foreach ($this->collBlogShops as $obj) {
                        if ($obj->isNew()) {
                            $collBlogShops[] = $obj;
                        }
                    }
                }

                $this->collBlogShops = $collBlogShops;
                $this->collBlogShopsPartial = false;
            }
        }

        return $this->collBlogShops;
    }

    /**
     * Sets a collection of BlogShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $blogShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setBlogShops(Collection $blogShops, ConnectionInterface $con = null)
    {
        $blogShopsToDelete = $this->getBlogShops(new Criteria(), $con)->diff($blogShops);

        
        $this->blogShopsScheduledForDeletion = $blogShopsToDelete;

        foreach ($blogShopsToDelete as $blogShopRemoved) {
            $blogShopRemoved->setShop(null);
        }

        $this->collBlogShops = null;
        foreach ($blogShops as $blogShop) {
            $this->addBlogShop($blogShop);
        }

        $this->collBlogShops = $blogShops;
        $this->collBlogShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BlogShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related BlogShop objects.
     * @throws PropelException
     */
    public function countBlogShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collBlogShopsPartial && !$this->isNew();
        if (null === $this->collBlogShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBlogShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBlogShops());
            }

            $query = BlogShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collBlogShops);
    }

    /**
     * Method called to associate a ChildBlogShop object to this object
     * through the ChildBlogShop foreign key attribute.
     *
     * @param    ChildBlogShop $l ChildBlogShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addBlogShop(ChildBlogShop $l)
    {
        if ($this->collBlogShops === null) {
            $this->initBlogShops();
            $this->collBlogShopsPartial = true;
        }

        if (!in_array($l, $this->collBlogShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddBlogShop($l);
        }

        return $this;
    }

    /**
     * @param BlogShop $blogShop The blogShop object to add.
     */
    protected function doAddBlogShop($blogShop)
    {
        $this->collBlogShops[]= $blogShop;
        $blogShop->setShop($this);
    }

    /**
     * @param  BlogShop $blogShop The blogShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeBlogShop($blogShop)
    {
        if ($this->getBlogShops()->contains($blogShop)) {
            $this->collBlogShops->remove($this->collBlogShops->search($blogShop));
            if (null === $this->blogShopsScheduledForDeletion) {
                $this->blogShopsScheduledForDeletion = clone $this->collBlogShops;
                $this->blogShopsScheduledForDeletion->clear();
            }
            $this->blogShopsScheduledForDeletion[]= clone $blogShop;
            $blogShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related BlogShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildBlogShop[] List of ChildBlogShop objects
     */
    public function getBlogShopsJoinBlog($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = BlogShopQuery::create(null, $criteria);
        $query->joinWith('Blog', $joinBehavior);

        return $this->getBlogShops($query, $con);
    }

    /**
     * Clears out the collCartRuleShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCartRuleShops()
     */
    public function clearCartRuleShops()
    {
        $this->collCartRuleShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCartRuleShops collection loaded partially.
     */
    public function resetPartialCartRuleShops($v = true)
    {
        $this->collCartRuleShopsPartial = $v;
    }

    /**
     * Initializes the collCartRuleShops collection.
     *
     * By default this just sets the collCartRuleShops collection to an empty array (like clearcollCartRuleShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCartRuleShops($overrideExisting = true)
    {
        if (null !== $this->collCartRuleShops && !$overrideExisting) {
            return;
        }
        $this->collCartRuleShops = new ObjectCollection();
        $this->collCartRuleShops->setModel('\Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop');
    }

    /**
     * Gets an array of ChildCartRuleShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCartRuleShop[] List of ChildCartRuleShop objects
     * @throws PropelException
     */
    public function getCartRuleShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCartRuleShopsPartial && !$this->isNew();
        if (null === $this->collCartRuleShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCartRuleShops) {
                // return empty collection
                $this->initCartRuleShops();
            } else {
                $collCartRuleShops = CartRuleShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCartRuleShopsPartial && count($collCartRuleShops)) {
                        $this->initCartRuleShops(false);

                        foreach ($collCartRuleShops as $obj) {
                            if (false == $this->collCartRuleShops->contains($obj)) {
                                $this->collCartRuleShops->append($obj);
                            }
                        }

                        $this->collCartRuleShopsPartial = true;
                    }

                    reset($collCartRuleShops);

                    return $collCartRuleShops;
                }

                if ($partial && $this->collCartRuleShops) {
                    foreach ($this->collCartRuleShops as $obj) {
                        if ($obj->isNew()) {
                            $collCartRuleShops[] = $obj;
                        }
                    }
                }

                $this->collCartRuleShops = $collCartRuleShops;
                $this->collCartRuleShopsPartial = false;
            }
        }

        return $this->collCartRuleShops;
    }

    /**
     * Sets a collection of CartRuleShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $cartRuleShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setCartRuleShops(Collection $cartRuleShops, ConnectionInterface $con = null)
    {
        $cartRuleShopsToDelete = $this->getCartRuleShops(new Criteria(), $con)->diff($cartRuleShops);

        
        $this->cartRuleShopsScheduledForDeletion = $cartRuleShopsToDelete;

        foreach ($cartRuleShopsToDelete as $cartRuleShopRemoved) {
            $cartRuleShopRemoved->setShop(null);
        }

        $this->collCartRuleShops = null;
        foreach ($cartRuleShops as $cartRuleShop) {
            $this->addCartRuleShop($cartRuleShop);
        }

        $this->collCartRuleShops = $cartRuleShops;
        $this->collCartRuleShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CartRuleShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CartRuleShop objects.
     * @throws PropelException
     */
    public function countCartRuleShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCartRuleShopsPartial && !$this->isNew();
        if (null === $this->collCartRuleShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCartRuleShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCartRuleShops());
            }

            $query = CartRuleShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collCartRuleShops);
    }

    /**
     * Method called to associate a ChildCartRuleShop object to this object
     * through the ChildCartRuleShop foreign key attribute.
     *
     * @param    ChildCartRuleShop $l ChildCartRuleShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addCartRuleShop(ChildCartRuleShop $l)
    {
        if ($this->collCartRuleShops === null) {
            $this->initCartRuleShops();
            $this->collCartRuleShopsPartial = true;
        }

        if (!in_array($l, $this->collCartRuleShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCartRuleShop($l);
        }

        return $this;
    }

    /**
     * @param CartRuleShop $cartRuleShop The cartRuleShop object to add.
     */
    protected function doAddCartRuleShop($cartRuleShop)
    {
        $this->collCartRuleShops[]= $cartRuleShop;
        $cartRuleShop->setShop($this);
    }

    /**
     * @param  CartRuleShop $cartRuleShop The cartRuleShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeCartRuleShop($cartRuleShop)
    {
        if ($this->getCartRuleShops()->contains($cartRuleShop)) {
            $this->collCartRuleShops->remove($this->collCartRuleShops->search($cartRuleShop));
            if (null === $this->cartRuleShopsScheduledForDeletion) {
                $this->cartRuleShopsScheduledForDeletion = clone $this->collCartRuleShops;
                $this->cartRuleShopsScheduledForDeletion->clear();
            }
            $this->cartRuleShopsScheduledForDeletion[]= clone $cartRuleShop;
            $cartRuleShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related CartRuleShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCartRuleShop[] List of ChildCartRuleShop objects
     */
    public function getCartRuleShopsJoinCartRule($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CartRuleShopQuery::create(null, $criteria);
        $query->joinWith('CartRule', $joinBehavior);

        return $this->getCartRuleShops($query, $con);
    }

    /**
     * Clears out the collCategoryShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategoryShops()
     */
    public function clearCategoryShops()
    {
        $this->collCategoryShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategoryShops collection loaded partially.
     */
    public function resetPartialCategoryShops($v = true)
    {
        $this->collCategoryShopsPartial = $v;
    }

    /**
     * Initializes the collCategoryShops collection.
     *
     * By default this just sets the collCategoryShops collection to an empty array (like clearcollCategoryShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoryShops($overrideExisting = true)
    {
        if (null !== $this->collCategoryShops && !$overrideExisting) {
            return;
        }
        $this->collCategoryShops = new ObjectCollection();
        $this->collCategoryShops->setModel('\Gekosale\Plugin\Category\Model\ORM\CategoryShop');
    }

    /**
     * Gets an array of ChildCategoryShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategoryShop[] List of ChildCategoryShop objects
     * @throws PropelException
     */
    public function getCategoryShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryShopsPartial && !$this->isNew();
        if (null === $this->collCategoryShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoryShops) {
                // return empty collection
                $this->initCategoryShops();
            } else {
                $collCategoryShops = CategoryShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoryShopsPartial && count($collCategoryShops)) {
                        $this->initCategoryShops(false);

                        foreach ($collCategoryShops as $obj) {
                            if (false == $this->collCategoryShops->contains($obj)) {
                                $this->collCategoryShops->append($obj);
                            }
                        }

                        $this->collCategoryShopsPartial = true;
                    }

                    reset($collCategoryShops);

                    return $collCategoryShops;
                }

                if ($partial && $this->collCategoryShops) {
                    foreach ($this->collCategoryShops as $obj) {
                        if ($obj->isNew()) {
                            $collCategoryShops[] = $obj;
                        }
                    }
                }

                $this->collCategoryShops = $collCategoryShops;
                $this->collCategoryShopsPartial = false;
            }
        }

        return $this->collCategoryShops;
    }

    /**
     * Sets a collection of CategoryShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categoryShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setCategoryShops(Collection $categoryShops, ConnectionInterface $con = null)
    {
        $categoryShopsToDelete = $this->getCategoryShops(new Criteria(), $con)->diff($categoryShops);

        
        $this->categoryShopsScheduledForDeletion = $categoryShopsToDelete;

        foreach ($categoryShopsToDelete as $categoryShopRemoved) {
            $categoryShopRemoved->setShop(null);
        }

        $this->collCategoryShops = null;
        foreach ($categoryShops as $categoryShop) {
            $this->addCategoryShop($categoryShop);
        }

        $this->collCategoryShops = $categoryShops;
        $this->collCategoryShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CategoryShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CategoryShop objects.
     * @throws PropelException
     */
    public function countCategoryShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryShopsPartial && !$this->isNew();
        if (null === $this->collCategoryShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoryShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategoryShops());
            }

            $query = CategoryShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collCategoryShops);
    }

    /**
     * Method called to associate a ChildCategoryShop object to this object
     * through the ChildCategoryShop foreign key attribute.
     *
     * @param    ChildCategoryShop $l ChildCategoryShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addCategoryShop(ChildCategoryShop $l)
    {
        if ($this->collCategoryShops === null) {
            $this->initCategoryShops();
            $this->collCategoryShopsPartial = true;
        }

        if (!in_array($l, $this->collCategoryShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryShop($l);
        }

        return $this;
    }

    /**
     * @param CategoryShop $categoryShop The categoryShop object to add.
     */
    protected function doAddCategoryShop($categoryShop)
    {
        $this->collCategoryShops[]= $categoryShop;
        $categoryShop->setShop($this);
    }

    /**
     * @param  CategoryShop $categoryShop The categoryShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeCategoryShop($categoryShop)
    {
        if ($this->getCategoryShops()->contains($categoryShop)) {
            $this->collCategoryShops->remove($this->collCategoryShops->search($categoryShop));
            if (null === $this->categoryShopsScheduledForDeletion) {
                $this->categoryShopsScheduledForDeletion = clone $this->collCategoryShops;
                $this->categoryShopsScheduledForDeletion->clear();
            }
            $this->categoryShopsScheduledForDeletion[]= clone $categoryShop;
            $categoryShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related CategoryShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryShop[] List of ChildCategoryShop objects
     */
    public function getCategoryShopsJoinCategory($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CategoryShopQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getCategoryShops($query, $con);
    }

    /**
     * Clears out the collPageShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPageShops()
     */
    public function clearPageShops()
    {
        $this->collPageShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPageShops collection loaded partially.
     */
    public function resetPartialPageShops($v = true)
    {
        $this->collPageShopsPartial = $v;
    }

    /**
     * Initializes the collPageShops collection.
     *
     * By default this just sets the collPageShops collection to an empty array (like clearcollPageShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPageShops($overrideExisting = true)
    {
        if (null !== $this->collPageShops && !$overrideExisting) {
            return;
        }
        $this->collPageShops = new ObjectCollection();
        $this->collPageShops->setModel('\Gekosale\Plugin\Page\Model\ORM\PageShop');
    }

    /**
     * Gets an array of ChildPageShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildPageShop[] List of ChildPageShop objects
     * @throws PropelException
     */
    public function getPageShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPageShopsPartial && !$this->isNew();
        if (null === $this->collPageShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPageShops) {
                // return empty collection
                $this->initPageShops();
            } else {
                $collPageShops = PageShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPageShopsPartial && count($collPageShops)) {
                        $this->initPageShops(false);

                        foreach ($collPageShops as $obj) {
                            if (false == $this->collPageShops->contains($obj)) {
                                $this->collPageShops->append($obj);
                            }
                        }

                        $this->collPageShopsPartial = true;
                    }

                    reset($collPageShops);

                    return $collPageShops;
                }

                if ($partial && $this->collPageShops) {
                    foreach ($this->collPageShops as $obj) {
                        if ($obj->isNew()) {
                            $collPageShops[] = $obj;
                        }
                    }
                }

                $this->collPageShops = $collPageShops;
                $this->collPageShopsPartial = false;
            }
        }

        return $this->collPageShops;
    }

    /**
     * Sets a collection of PageShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $pageShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setPageShops(Collection $pageShops, ConnectionInterface $con = null)
    {
        $pageShopsToDelete = $this->getPageShops(new Criteria(), $con)->diff($pageShops);

        
        $this->pageShopsScheduledForDeletion = $pageShopsToDelete;

        foreach ($pageShopsToDelete as $pageShopRemoved) {
            $pageShopRemoved->setShop(null);
        }

        $this->collPageShops = null;
        foreach ($pageShops as $pageShop) {
            $this->addPageShop($pageShop);
        }

        $this->collPageShops = $pageShops;
        $this->collPageShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PageShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PageShop objects.
     * @throws PropelException
     */
    public function countPageShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPageShopsPartial && !$this->isNew();
        if (null === $this->collPageShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPageShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPageShops());
            }

            $query = PageShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collPageShops);
    }

    /**
     * Method called to associate a ChildPageShop object to this object
     * through the ChildPageShop foreign key attribute.
     *
     * @param    ChildPageShop $l ChildPageShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addPageShop(ChildPageShop $l)
    {
        if ($this->collPageShops === null) {
            $this->initPageShops();
            $this->collPageShopsPartial = true;
        }

        if (!in_array($l, $this->collPageShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPageShop($l);
        }

        return $this;
    }

    /**
     * @param PageShop $pageShop The pageShop object to add.
     */
    protected function doAddPageShop($pageShop)
    {
        $this->collPageShops[]= $pageShop;
        $pageShop->setShop($this);
    }

    /**
     * @param  PageShop $pageShop The pageShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removePageShop($pageShop)
    {
        if ($this->getPageShops()->contains($pageShop)) {
            $this->collPageShops->remove($this->collPageShops->search($pageShop));
            if (null === $this->pageShopsScheduledForDeletion) {
                $this->pageShopsScheduledForDeletion = clone $this->collPageShops;
                $this->pageShopsScheduledForDeletion->clear();
            }
            $this->pageShopsScheduledForDeletion[]= clone $pageShop;
            $pageShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related PageShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildPageShop[] List of ChildPageShop objects
     */
    public function getPageShopsJoinPage($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = PageShopQuery::create(null, $criteria);
        $query->joinWith('Page', $joinBehavior);

        return $this->getPageShops($query, $con);
    }

    /**
     * Clears out the collContactShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addContactShops()
     */
    public function clearContactShops()
    {
        $this->collContactShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collContactShops collection loaded partially.
     */
    public function resetPartialContactShops($v = true)
    {
        $this->collContactShopsPartial = $v;
    }

    /**
     * Initializes the collContactShops collection.
     *
     * By default this just sets the collContactShops collection to an empty array (like clearcollContactShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initContactShops($overrideExisting = true)
    {
        if (null !== $this->collContactShops && !$overrideExisting) {
            return;
        }
        $this->collContactShops = new ObjectCollection();
        $this->collContactShops->setModel('\Gekosale\Plugin\Contact\Model\ORM\ContactShop');
    }

    /**
     * Gets an array of ChildContactShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildContactShop[] List of ChildContactShop objects
     * @throws PropelException
     */
    public function getContactShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collContactShopsPartial && !$this->isNew();
        if (null === $this->collContactShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collContactShops) {
                // return empty collection
                $this->initContactShops();
            } else {
                $collContactShops = ContactShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collContactShopsPartial && count($collContactShops)) {
                        $this->initContactShops(false);

                        foreach ($collContactShops as $obj) {
                            if (false == $this->collContactShops->contains($obj)) {
                                $this->collContactShops->append($obj);
                            }
                        }

                        $this->collContactShopsPartial = true;
                    }

                    reset($collContactShops);

                    return $collContactShops;
                }

                if ($partial && $this->collContactShops) {
                    foreach ($this->collContactShops as $obj) {
                        if ($obj->isNew()) {
                            $collContactShops[] = $obj;
                        }
                    }
                }

                $this->collContactShops = $collContactShops;
                $this->collContactShopsPartial = false;
            }
        }

        return $this->collContactShops;
    }

    /**
     * Sets a collection of ContactShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $contactShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setContactShops(Collection $contactShops, ConnectionInterface $con = null)
    {
        $contactShopsToDelete = $this->getContactShops(new Criteria(), $con)->diff($contactShops);

        
        $this->contactShopsScheduledForDeletion = $contactShopsToDelete;

        foreach ($contactShopsToDelete as $contactShopRemoved) {
            $contactShopRemoved->setShop(null);
        }

        $this->collContactShops = null;
        foreach ($contactShops as $contactShop) {
            $this->addContactShop($contactShop);
        }

        $this->collContactShops = $contactShops;
        $this->collContactShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ContactShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ContactShop objects.
     * @throws PropelException
     */
    public function countContactShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collContactShopsPartial && !$this->isNew();
        if (null === $this->collContactShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collContactShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getContactShops());
            }

            $query = ContactShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collContactShops);
    }

    /**
     * Method called to associate a ChildContactShop object to this object
     * through the ChildContactShop foreign key attribute.
     *
     * @param    ChildContactShop $l ChildContactShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addContactShop(ChildContactShop $l)
    {
        if ($this->collContactShops === null) {
            $this->initContactShops();
            $this->collContactShopsPartial = true;
        }

        if (!in_array($l, $this->collContactShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddContactShop($l);
        }

        return $this;
    }

    /**
     * @param ContactShop $contactShop The contactShop object to add.
     */
    protected function doAddContactShop($contactShop)
    {
        $this->collContactShops[]= $contactShop;
        $contactShop->setShop($this);
    }

    /**
     * @param  ContactShop $contactShop The contactShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeContactShop($contactShop)
    {
        if ($this->getContactShops()->contains($contactShop)) {
            $this->collContactShops->remove($this->collContactShops->search($contactShop));
            if (null === $this->contactShopsScheduledForDeletion) {
                $this->contactShopsScheduledForDeletion = clone $this->collContactShops;
                $this->contactShopsScheduledForDeletion->clear();
            }
            $this->contactShopsScheduledForDeletion[]= clone $contactShop;
            $contactShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related ContactShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildContactShop[] List of ChildContactShop objects
     */
    public function getContactShopsJoinContact($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ContactShopQuery::create(null, $criteria);
        $query->joinWith('Contact', $joinBehavior);

        return $this->getContactShops($query, $con);
    }

    /**
     * Clears out the collCurrencyShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCurrencyShops()
     */
    public function clearCurrencyShops()
    {
        $this->collCurrencyShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCurrencyShops collection loaded partially.
     */
    public function resetPartialCurrencyShops($v = true)
    {
        $this->collCurrencyShopsPartial = $v;
    }

    /**
     * Initializes the collCurrencyShops collection.
     *
     * By default this just sets the collCurrencyShops collection to an empty array (like clearcollCurrencyShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCurrencyShops($overrideExisting = true)
    {
        if (null !== $this->collCurrencyShops && !$overrideExisting) {
            return;
        }
        $this->collCurrencyShops = new ObjectCollection();
        $this->collCurrencyShops->setModel('\Gekosale\Plugin\Currency\Model\ORM\CurrencyShop');
    }

    /**
     * Gets an array of ChildCurrencyShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCurrencyShop[] List of ChildCurrencyShop objects
     * @throws PropelException
     */
    public function getCurrencyShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCurrencyShopsPartial && !$this->isNew();
        if (null === $this->collCurrencyShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCurrencyShops) {
                // return empty collection
                $this->initCurrencyShops();
            } else {
                $collCurrencyShops = CurrencyShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCurrencyShopsPartial && count($collCurrencyShops)) {
                        $this->initCurrencyShops(false);

                        foreach ($collCurrencyShops as $obj) {
                            if (false == $this->collCurrencyShops->contains($obj)) {
                                $this->collCurrencyShops->append($obj);
                            }
                        }

                        $this->collCurrencyShopsPartial = true;
                    }

                    reset($collCurrencyShops);

                    return $collCurrencyShops;
                }

                if ($partial && $this->collCurrencyShops) {
                    foreach ($this->collCurrencyShops as $obj) {
                        if ($obj->isNew()) {
                            $collCurrencyShops[] = $obj;
                        }
                    }
                }

                $this->collCurrencyShops = $collCurrencyShops;
                $this->collCurrencyShopsPartial = false;
            }
        }

        return $this->collCurrencyShops;
    }

    /**
     * Sets a collection of CurrencyShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $currencyShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setCurrencyShops(Collection $currencyShops, ConnectionInterface $con = null)
    {
        $currencyShopsToDelete = $this->getCurrencyShops(new Criteria(), $con)->diff($currencyShops);

        
        $this->currencyShopsScheduledForDeletion = $currencyShopsToDelete;

        foreach ($currencyShopsToDelete as $currencyShopRemoved) {
            $currencyShopRemoved->setShop(null);
        }

        $this->collCurrencyShops = null;
        foreach ($currencyShops as $currencyShop) {
            $this->addCurrencyShop($currencyShop);
        }

        $this->collCurrencyShops = $currencyShops;
        $this->collCurrencyShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CurrencyShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CurrencyShop objects.
     * @throws PropelException
     */
    public function countCurrencyShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCurrencyShopsPartial && !$this->isNew();
        if (null === $this->collCurrencyShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCurrencyShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCurrencyShops());
            }

            $query = CurrencyShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collCurrencyShops);
    }

    /**
     * Method called to associate a ChildCurrencyShop object to this object
     * through the ChildCurrencyShop foreign key attribute.
     *
     * @param    ChildCurrencyShop $l ChildCurrencyShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addCurrencyShop(ChildCurrencyShop $l)
    {
        if ($this->collCurrencyShops === null) {
            $this->initCurrencyShops();
            $this->collCurrencyShopsPartial = true;
        }

        if (!in_array($l, $this->collCurrencyShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCurrencyShop($l);
        }

        return $this;
    }

    /**
     * @param CurrencyShop $currencyShop The currencyShop object to add.
     */
    protected function doAddCurrencyShop($currencyShop)
    {
        $this->collCurrencyShops[]= $currencyShop;
        $currencyShop->setShop($this);
    }

    /**
     * @param  CurrencyShop $currencyShop The currencyShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeCurrencyShop($currencyShop)
    {
        if ($this->getCurrencyShops()->contains($currencyShop)) {
            $this->collCurrencyShops->remove($this->collCurrencyShops->search($currencyShop));
            if (null === $this->currencyShopsScheduledForDeletion) {
                $this->currencyShopsScheduledForDeletion = clone $this->collCurrencyShops;
                $this->currencyShopsScheduledForDeletion->clear();
            }
            $this->currencyShopsScheduledForDeletion[]= clone $currencyShop;
            $currencyShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related CurrencyShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCurrencyShop[] List of ChildCurrencyShop objects
     */
    public function getCurrencyShopsJoinCurrency($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CurrencyShopQuery::create(null, $criteria);
        $query->joinWith('Currency', $joinBehavior);

        return $this->getCurrencyShops($query, $con);
    }

    /**
     * Clears out the collDispatchMethodShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDispatchMethodShops()
     */
    public function clearDispatchMethodShops()
    {
        $this->collDispatchMethodShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDispatchMethodShops collection loaded partially.
     */
    public function resetPartialDispatchMethodShops($v = true)
    {
        $this->collDispatchMethodShopsPartial = $v;
    }

    /**
     * Initializes the collDispatchMethodShops collection.
     *
     * By default this just sets the collDispatchMethodShops collection to an empty array (like clearcollDispatchMethodShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDispatchMethodShops($overrideExisting = true)
    {
        if (null !== $this->collDispatchMethodShops && !$overrideExisting) {
            return;
        }
        $this->collDispatchMethodShops = new ObjectCollection();
        $this->collDispatchMethodShops->setModel('\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop');
    }

    /**
     * Gets an array of ChildDispatchMethodShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDispatchMethodShop[] List of ChildDispatchMethodShop objects
     * @throws PropelException
     */
    public function getDispatchMethodShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodShopsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodShops) {
                // return empty collection
                $this->initDispatchMethodShops();
            } else {
                $collDispatchMethodShops = DispatchMethodShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDispatchMethodShopsPartial && count($collDispatchMethodShops)) {
                        $this->initDispatchMethodShops(false);

                        foreach ($collDispatchMethodShops as $obj) {
                            if (false == $this->collDispatchMethodShops->contains($obj)) {
                                $this->collDispatchMethodShops->append($obj);
                            }
                        }

                        $this->collDispatchMethodShopsPartial = true;
                    }

                    reset($collDispatchMethodShops);

                    return $collDispatchMethodShops;
                }

                if ($partial && $this->collDispatchMethodShops) {
                    foreach ($this->collDispatchMethodShops as $obj) {
                        if ($obj->isNew()) {
                            $collDispatchMethodShops[] = $obj;
                        }
                    }
                }

                $this->collDispatchMethodShops = $collDispatchMethodShops;
                $this->collDispatchMethodShopsPartial = false;
            }
        }

        return $this->collDispatchMethodShops;
    }

    /**
     * Sets a collection of DispatchMethodShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dispatchMethodShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setDispatchMethodShops(Collection $dispatchMethodShops, ConnectionInterface $con = null)
    {
        $dispatchMethodShopsToDelete = $this->getDispatchMethodShops(new Criteria(), $con)->diff($dispatchMethodShops);

        
        $this->dispatchMethodShopsScheduledForDeletion = $dispatchMethodShopsToDelete;

        foreach ($dispatchMethodShopsToDelete as $dispatchMethodShopRemoved) {
            $dispatchMethodShopRemoved->setShop(null);
        }

        $this->collDispatchMethodShops = null;
        foreach ($dispatchMethodShops as $dispatchMethodShop) {
            $this->addDispatchMethodShop($dispatchMethodShop);
        }

        $this->collDispatchMethodShops = $dispatchMethodShops;
        $this->collDispatchMethodShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DispatchMethodShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DispatchMethodShop objects.
     * @throws PropelException
     */
    public function countDispatchMethodShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodShopsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDispatchMethodShops());
            }

            $query = DispatchMethodShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collDispatchMethodShops);
    }

    /**
     * Method called to associate a ChildDispatchMethodShop object to this object
     * through the ChildDispatchMethodShop foreign key attribute.
     *
     * @param    ChildDispatchMethodShop $l ChildDispatchMethodShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addDispatchMethodShop(ChildDispatchMethodShop $l)
    {
        if ($this->collDispatchMethodShops === null) {
            $this->initDispatchMethodShops();
            $this->collDispatchMethodShopsPartial = true;
        }

        if (!in_array($l, $this->collDispatchMethodShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDispatchMethodShop($l);
        }

        return $this;
    }

    /**
     * @param DispatchMethodShop $dispatchMethodShop The dispatchMethodShop object to add.
     */
    protected function doAddDispatchMethodShop($dispatchMethodShop)
    {
        $this->collDispatchMethodShops[]= $dispatchMethodShop;
        $dispatchMethodShop->setShop($this);
    }

    /**
     * @param  DispatchMethodShop $dispatchMethodShop The dispatchMethodShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeDispatchMethodShop($dispatchMethodShop)
    {
        if ($this->getDispatchMethodShops()->contains($dispatchMethodShop)) {
            $this->collDispatchMethodShops->remove($this->collDispatchMethodShops->search($dispatchMethodShop));
            if (null === $this->dispatchMethodShopsScheduledForDeletion) {
                $this->dispatchMethodShopsScheduledForDeletion = clone $this->collDispatchMethodShops;
                $this->dispatchMethodShopsScheduledForDeletion->clear();
            }
            $this->dispatchMethodShopsScheduledForDeletion[]= clone $dispatchMethodShop;
            $dispatchMethodShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related DispatchMethodShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDispatchMethodShop[] List of ChildDispatchMethodShop objects
     */
    public function getDispatchMethodShopsJoinDispatchMethod($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = DispatchMethodShopQuery::create(null, $criteria);
        $query->joinWith('DispatchMethod', $joinBehavior);

        return $this->getDispatchMethodShops($query, $con);
    }

    /**
     * Clears out the collLocaleShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addLocaleShops()
     */
    public function clearLocaleShops()
    {
        $this->collLocaleShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collLocaleShops collection loaded partially.
     */
    public function resetPartialLocaleShops($v = true)
    {
        $this->collLocaleShopsPartial = $v;
    }

    /**
     * Initializes the collLocaleShops collection.
     *
     * By default this just sets the collLocaleShops collection to an empty array (like clearcollLocaleShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initLocaleShops($overrideExisting = true)
    {
        if (null !== $this->collLocaleShops && !$overrideExisting) {
            return;
        }
        $this->collLocaleShops = new ObjectCollection();
        $this->collLocaleShops->setModel('\Gekosale\Plugin\Locale\Model\ORM\LocaleShop');
    }

    /**
     * Gets an array of ChildLocaleShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildLocaleShop[] List of ChildLocaleShop objects
     * @throws PropelException
     */
    public function getLocaleShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collLocaleShopsPartial && !$this->isNew();
        if (null === $this->collLocaleShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collLocaleShops) {
                // return empty collection
                $this->initLocaleShops();
            } else {
                $collLocaleShops = LocaleShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collLocaleShopsPartial && count($collLocaleShops)) {
                        $this->initLocaleShops(false);

                        foreach ($collLocaleShops as $obj) {
                            if (false == $this->collLocaleShops->contains($obj)) {
                                $this->collLocaleShops->append($obj);
                            }
                        }

                        $this->collLocaleShopsPartial = true;
                    }

                    reset($collLocaleShops);

                    return $collLocaleShops;
                }

                if ($partial && $this->collLocaleShops) {
                    foreach ($this->collLocaleShops as $obj) {
                        if ($obj->isNew()) {
                            $collLocaleShops[] = $obj;
                        }
                    }
                }

                $this->collLocaleShops = $collLocaleShops;
                $this->collLocaleShopsPartial = false;
            }
        }

        return $this->collLocaleShops;
    }

    /**
     * Sets a collection of LocaleShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $localeShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setLocaleShops(Collection $localeShops, ConnectionInterface $con = null)
    {
        $localeShopsToDelete = $this->getLocaleShops(new Criteria(), $con)->diff($localeShops);

        
        $this->localeShopsScheduledForDeletion = $localeShopsToDelete;

        foreach ($localeShopsToDelete as $localeShopRemoved) {
            $localeShopRemoved->setShop(null);
        }

        $this->collLocaleShops = null;
        foreach ($localeShops as $localeShop) {
            $this->addLocaleShop($localeShop);
        }

        $this->collLocaleShops = $localeShops;
        $this->collLocaleShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related LocaleShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related LocaleShop objects.
     * @throws PropelException
     */
    public function countLocaleShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collLocaleShopsPartial && !$this->isNew();
        if (null === $this->collLocaleShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collLocaleShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getLocaleShops());
            }

            $query = LocaleShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collLocaleShops);
    }

    /**
     * Method called to associate a ChildLocaleShop object to this object
     * through the ChildLocaleShop foreign key attribute.
     *
     * @param    ChildLocaleShop $l ChildLocaleShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addLocaleShop(ChildLocaleShop $l)
    {
        if ($this->collLocaleShops === null) {
            $this->initLocaleShops();
            $this->collLocaleShopsPartial = true;
        }

        if (!in_array($l, $this->collLocaleShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddLocaleShop($l);
        }

        return $this;
    }

    /**
     * @param LocaleShop $localeShop The localeShop object to add.
     */
    protected function doAddLocaleShop($localeShop)
    {
        $this->collLocaleShops[]= $localeShop;
        $localeShop->setShop($this);
    }

    /**
     * @param  LocaleShop $localeShop The localeShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeLocaleShop($localeShop)
    {
        if ($this->getLocaleShops()->contains($localeShop)) {
            $this->collLocaleShops->remove($this->collLocaleShops->search($localeShop));
            if (null === $this->localeShopsScheduledForDeletion) {
                $this->localeShopsScheduledForDeletion = clone $this->collLocaleShops;
                $this->localeShopsScheduledForDeletion->clear();
            }
            $this->localeShopsScheduledForDeletion[]= clone $localeShop;
            $localeShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related LocaleShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildLocaleShop[] List of ChildLocaleShop objects
     */
    public function getLocaleShopsJoinLocale($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = LocaleShopQuery::create(null, $criteria);
        $query->joinWith('Locale', $joinBehavior);

        return $this->getLocaleShops($query, $con);
    }

    /**
     * Clears out the collPaymentMethodShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPaymentMethodShops()
     */
    public function clearPaymentMethodShops()
    {
        $this->collPaymentMethodShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPaymentMethodShops collection loaded partially.
     */
    public function resetPartialPaymentMethodShops($v = true)
    {
        $this->collPaymentMethodShopsPartial = $v;
    }

    /**
     * Initializes the collPaymentMethodShops collection.
     *
     * By default this just sets the collPaymentMethodShops collection to an empty array (like clearcollPaymentMethodShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPaymentMethodShops($overrideExisting = true)
    {
        if (null !== $this->collPaymentMethodShops && !$overrideExisting) {
            return;
        }
        $this->collPaymentMethodShops = new ObjectCollection();
        $this->collPaymentMethodShops->setModel('\Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop');
    }

    /**
     * Gets an array of ChildPaymentMethodShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildPaymentMethodShop[] List of ChildPaymentMethodShop objects
     * @throws PropelException
     */
    public function getPaymentMethodShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPaymentMethodShopsPartial && !$this->isNew();
        if (null === $this->collPaymentMethodShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPaymentMethodShops) {
                // return empty collection
                $this->initPaymentMethodShops();
            } else {
                $collPaymentMethodShops = PaymentMethodShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPaymentMethodShopsPartial && count($collPaymentMethodShops)) {
                        $this->initPaymentMethodShops(false);

                        foreach ($collPaymentMethodShops as $obj) {
                            if (false == $this->collPaymentMethodShops->contains($obj)) {
                                $this->collPaymentMethodShops->append($obj);
                            }
                        }

                        $this->collPaymentMethodShopsPartial = true;
                    }

                    reset($collPaymentMethodShops);

                    return $collPaymentMethodShops;
                }

                if ($partial && $this->collPaymentMethodShops) {
                    foreach ($this->collPaymentMethodShops as $obj) {
                        if ($obj->isNew()) {
                            $collPaymentMethodShops[] = $obj;
                        }
                    }
                }

                $this->collPaymentMethodShops = $collPaymentMethodShops;
                $this->collPaymentMethodShopsPartial = false;
            }
        }

        return $this->collPaymentMethodShops;
    }

    /**
     * Sets a collection of PaymentMethodShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $paymentMethodShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setPaymentMethodShops(Collection $paymentMethodShops, ConnectionInterface $con = null)
    {
        $paymentMethodShopsToDelete = $this->getPaymentMethodShops(new Criteria(), $con)->diff($paymentMethodShops);

        
        $this->paymentMethodShopsScheduledForDeletion = $paymentMethodShopsToDelete;

        foreach ($paymentMethodShopsToDelete as $paymentMethodShopRemoved) {
            $paymentMethodShopRemoved->setShop(null);
        }

        $this->collPaymentMethodShops = null;
        foreach ($paymentMethodShops as $paymentMethodShop) {
            $this->addPaymentMethodShop($paymentMethodShop);
        }

        $this->collPaymentMethodShops = $paymentMethodShops;
        $this->collPaymentMethodShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PaymentMethodShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PaymentMethodShop objects.
     * @throws PropelException
     */
    public function countPaymentMethodShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPaymentMethodShopsPartial && !$this->isNew();
        if (null === $this->collPaymentMethodShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPaymentMethodShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPaymentMethodShops());
            }

            $query = PaymentMethodShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collPaymentMethodShops);
    }

    /**
     * Method called to associate a ChildPaymentMethodShop object to this object
     * through the ChildPaymentMethodShop foreign key attribute.
     *
     * @param    ChildPaymentMethodShop $l ChildPaymentMethodShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addPaymentMethodShop(ChildPaymentMethodShop $l)
    {
        if ($this->collPaymentMethodShops === null) {
            $this->initPaymentMethodShops();
            $this->collPaymentMethodShopsPartial = true;
        }

        if (!in_array($l, $this->collPaymentMethodShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPaymentMethodShop($l);
        }

        return $this;
    }

    /**
     * @param PaymentMethodShop $paymentMethodShop The paymentMethodShop object to add.
     */
    protected function doAddPaymentMethodShop($paymentMethodShop)
    {
        $this->collPaymentMethodShops[]= $paymentMethodShop;
        $paymentMethodShop->setShop($this);
    }

    /**
     * @param  PaymentMethodShop $paymentMethodShop The paymentMethodShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removePaymentMethodShop($paymentMethodShop)
    {
        if ($this->getPaymentMethodShops()->contains($paymentMethodShop)) {
            $this->collPaymentMethodShops->remove($this->collPaymentMethodShops->search($paymentMethodShop));
            if (null === $this->paymentMethodShopsScheduledForDeletion) {
                $this->paymentMethodShopsScheduledForDeletion = clone $this->collPaymentMethodShops;
                $this->paymentMethodShopsScheduledForDeletion->clear();
            }
            $this->paymentMethodShopsScheduledForDeletion[]= clone $paymentMethodShop;
            $paymentMethodShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related PaymentMethodShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildPaymentMethodShop[] List of ChildPaymentMethodShop objects
     */
    public function getPaymentMethodShopsJoinPaymentMethod($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = PaymentMethodShopQuery::create(null, $criteria);
        $query->joinWith('PaymentMethod', $joinBehavior);

        return $this->getPaymentMethodShops($query, $con);
    }

    /**
     * Clears out the collProducerShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProducerShops()
     */
    public function clearProducerShops()
    {
        $this->collProducerShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProducerShops collection loaded partially.
     */
    public function resetPartialProducerShops($v = true)
    {
        $this->collProducerShopsPartial = $v;
    }

    /**
     * Initializes the collProducerShops collection.
     *
     * By default this just sets the collProducerShops collection to an empty array (like clearcollProducerShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProducerShops($overrideExisting = true)
    {
        if (null !== $this->collProducerShops && !$overrideExisting) {
            return;
        }
        $this->collProducerShops = new ObjectCollection();
        $this->collProducerShops->setModel('\Gekosale\Plugin\Producer\Model\ORM\ProducerShop');
    }

    /**
     * Gets an array of ChildProducerShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProducerShop[] List of ChildProducerShop objects
     * @throws PropelException
     */
    public function getProducerShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProducerShopsPartial && !$this->isNew();
        if (null === $this->collProducerShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProducerShops) {
                // return empty collection
                $this->initProducerShops();
            } else {
                $collProducerShops = ProducerShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProducerShopsPartial && count($collProducerShops)) {
                        $this->initProducerShops(false);

                        foreach ($collProducerShops as $obj) {
                            if (false == $this->collProducerShops->contains($obj)) {
                                $this->collProducerShops->append($obj);
                            }
                        }

                        $this->collProducerShopsPartial = true;
                    }

                    reset($collProducerShops);

                    return $collProducerShops;
                }

                if ($partial && $this->collProducerShops) {
                    foreach ($this->collProducerShops as $obj) {
                        if ($obj->isNew()) {
                            $collProducerShops[] = $obj;
                        }
                    }
                }

                $this->collProducerShops = $collProducerShops;
                $this->collProducerShopsPartial = false;
            }
        }

        return $this->collProducerShops;
    }

    /**
     * Sets a collection of ProducerShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $producerShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setProducerShops(Collection $producerShops, ConnectionInterface $con = null)
    {
        $producerShopsToDelete = $this->getProducerShops(new Criteria(), $con)->diff($producerShops);

        
        $this->producerShopsScheduledForDeletion = $producerShopsToDelete;

        foreach ($producerShopsToDelete as $producerShopRemoved) {
            $producerShopRemoved->setShop(null);
        }

        $this->collProducerShops = null;
        foreach ($producerShops as $producerShop) {
            $this->addProducerShop($producerShop);
        }

        $this->collProducerShops = $producerShops;
        $this->collProducerShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProducerShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProducerShop objects.
     * @throws PropelException
     */
    public function countProducerShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProducerShopsPartial && !$this->isNew();
        if (null === $this->collProducerShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProducerShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProducerShops());
            }

            $query = ProducerShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collProducerShops);
    }

    /**
     * Method called to associate a ChildProducerShop object to this object
     * through the ChildProducerShop foreign key attribute.
     *
     * @param    ChildProducerShop $l ChildProducerShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addProducerShop(ChildProducerShop $l)
    {
        if ($this->collProducerShops === null) {
            $this->initProducerShops();
            $this->collProducerShopsPartial = true;
        }

        if (!in_array($l, $this->collProducerShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProducerShop($l);
        }

        return $this;
    }

    /**
     * @param ProducerShop $producerShop The producerShop object to add.
     */
    protected function doAddProducerShop($producerShop)
    {
        $this->collProducerShops[]= $producerShop;
        $producerShop->setShop($this);
    }

    /**
     * @param  ProducerShop $producerShop The producerShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeProducerShop($producerShop)
    {
        if ($this->getProducerShops()->contains($producerShop)) {
            $this->collProducerShops->remove($this->collProducerShops->search($producerShop));
            if (null === $this->producerShopsScheduledForDeletion) {
                $this->producerShopsScheduledForDeletion = clone $this->collProducerShops;
                $this->producerShopsScheduledForDeletion->clear();
            }
            $this->producerShopsScheduledForDeletion[]= clone $producerShop;
            $producerShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related ProducerShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProducerShop[] List of ChildProducerShop objects
     */
    public function getProducerShopsJoinProducer($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProducerShopQuery::create(null, $criteria);
        $query->joinWith('Producer', $joinBehavior);

        return $this->getProducerShops($query, $con);
    }

    /**
     * Clears out the collUserGroupShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserGroupShops()
     */
    public function clearUserGroupShops()
    {
        $this->collUserGroupShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserGroupShops collection loaded partially.
     */
    public function resetPartialUserGroupShops($v = true)
    {
        $this->collUserGroupShopsPartial = $v;
    }

    /**
     * Initializes the collUserGroupShops collection.
     *
     * By default this just sets the collUserGroupShops collection to an empty array (like clearcollUserGroupShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserGroupShops($overrideExisting = true)
    {
        if (null !== $this->collUserGroupShops && !$overrideExisting) {
            return;
        }
        $this->collUserGroupShops = new ObjectCollection();
        $this->collUserGroupShops->setModel('\Gekosale\Plugin\User\Model\ORM\UserGroupShop');
    }

    /**
     * Gets an array of ChildUserGroupShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUserGroupShop[] List of ChildUserGroupShop objects
     * @throws PropelException
     */
    public function getUserGroupShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserGroupShopsPartial && !$this->isNew();
        if (null === $this->collUserGroupShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserGroupShops) {
                // return empty collection
                $this->initUserGroupShops();
            } else {
                $collUserGroupShops = UserGroupShopQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserGroupShopsPartial && count($collUserGroupShops)) {
                        $this->initUserGroupShops(false);

                        foreach ($collUserGroupShops as $obj) {
                            if (false == $this->collUserGroupShops->contains($obj)) {
                                $this->collUserGroupShops->append($obj);
                            }
                        }

                        $this->collUserGroupShopsPartial = true;
                    }

                    reset($collUserGroupShops);

                    return $collUserGroupShops;
                }

                if ($partial && $this->collUserGroupShops) {
                    foreach ($this->collUserGroupShops as $obj) {
                        if ($obj->isNew()) {
                            $collUserGroupShops[] = $obj;
                        }
                    }
                }

                $this->collUserGroupShops = $collUserGroupShops;
                $this->collUserGroupShopsPartial = false;
            }
        }

        return $this->collUserGroupShops;
    }

    /**
     * Sets a collection of UserGroupShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userGroupShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setUserGroupShops(Collection $userGroupShops, ConnectionInterface $con = null)
    {
        $userGroupShopsToDelete = $this->getUserGroupShops(new Criteria(), $con)->diff($userGroupShops);

        
        $this->userGroupShopsScheduledForDeletion = $userGroupShopsToDelete;

        foreach ($userGroupShopsToDelete as $userGroupShopRemoved) {
            $userGroupShopRemoved->setShop(null);
        }

        $this->collUserGroupShops = null;
        foreach ($userGroupShops as $userGroupShop) {
            $this->addUserGroupShop($userGroupShop);
        }

        $this->collUserGroupShops = $userGroupShops;
        $this->collUserGroupShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserGroupShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserGroupShop objects.
     * @throws PropelException
     */
    public function countUserGroupShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserGroupShopsPartial && !$this->isNew();
        if (null === $this->collUserGroupShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserGroupShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserGroupShops());
            }

            $query = UserGroupShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collUserGroupShops);
    }

    /**
     * Method called to associate a ChildUserGroupShop object to this object
     * through the ChildUserGroupShop foreign key attribute.
     *
     * @param    ChildUserGroupShop $l ChildUserGroupShop
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addUserGroupShop(ChildUserGroupShop $l)
    {
        if ($this->collUserGroupShops === null) {
            $this->initUserGroupShops();
            $this->collUserGroupShopsPartial = true;
        }

        if (!in_array($l, $this->collUserGroupShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserGroupShop($l);
        }

        return $this;
    }

    /**
     * @param UserGroupShop $userGroupShop The userGroupShop object to add.
     */
    protected function doAddUserGroupShop($userGroupShop)
    {
        $this->collUserGroupShops[]= $userGroupShop;
        $userGroupShop->setShop($this);
    }

    /**
     * @param  UserGroupShop $userGroupShop The userGroupShop object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeUserGroupShop($userGroupShop)
    {
        if ($this->getUserGroupShops()->contains($userGroupShop)) {
            $this->collUserGroupShops->remove($this->collUserGroupShops->search($userGroupShop));
            if (null === $this->userGroupShopsScheduledForDeletion) {
                $this->userGroupShopsScheduledForDeletion = clone $this->collUserGroupShops;
                $this->userGroupShopsScheduledForDeletion->clear();
            }
            $this->userGroupShopsScheduledForDeletion[]= clone $userGroupShop;
            $userGroupShop->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related UserGroupShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserGroupShop[] List of ChildUserGroupShop objects
     */
    public function getUserGroupShopsJoinUserGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = UserGroupShopQuery::create(null, $criteria);
        $query->joinWith('UserGroup', $joinBehavior);

        return $this->getUserGroupShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related UserGroupShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserGroupShop[] List of ChildUserGroupShop objects
     */
    public function getUserGroupShopsJoinUser($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = UserGroupShopQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getUserGroupShops($query, $con);
    }

    /**
     * Clears out the collWishlists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addWishlists()
     */
    public function clearWishlists()
    {
        $this->collWishlists = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collWishlists collection loaded partially.
     */
    public function resetPartialWishlists($v = true)
    {
        $this->collWishlistsPartial = $v;
    }

    /**
     * Initializes the collWishlists collection.
     *
     * By default this just sets the collWishlists collection to an empty array (like clearcollWishlists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWishlists($overrideExisting = true)
    {
        if (null !== $this->collWishlists && !$overrideExisting) {
            return;
        }
        $this->collWishlists = new ObjectCollection();
        $this->collWishlists->setModel('\Gekosale\Plugin\Wishlist\Model\ORM\Wishlist');
    }

    /**
     * Gets an array of ChildWishlist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     * @throws PropelException
     */
    public function getWishlists($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collWishlistsPartial && !$this->isNew();
        if (null === $this->collWishlists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWishlists) {
                // return empty collection
                $this->initWishlists();
            } else {
                $collWishlists = WishlistQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collWishlistsPartial && count($collWishlists)) {
                        $this->initWishlists(false);

                        foreach ($collWishlists as $obj) {
                            if (false == $this->collWishlists->contains($obj)) {
                                $this->collWishlists->append($obj);
                            }
                        }

                        $this->collWishlistsPartial = true;
                    }

                    reset($collWishlists);

                    return $collWishlists;
                }

                if ($partial && $this->collWishlists) {
                    foreach ($this->collWishlists as $obj) {
                        if ($obj->isNew()) {
                            $collWishlists[] = $obj;
                        }
                    }
                }

                $this->collWishlists = $collWishlists;
                $this->collWishlistsPartial = false;
            }
        }

        return $this->collWishlists;
    }

    /**
     * Sets a collection of Wishlist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $wishlists A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setWishlists(Collection $wishlists, ConnectionInterface $con = null)
    {
        $wishlistsToDelete = $this->getWishlists(new Criteria(), $con)->diff($wishlists);

        
        $this->wishlistsScheduledForDeletion = $wishlistsToDelete;

        foreach ($wishlistsToDelete as $wishlistRemoved) {
            $wishlistRemoved->setShop(null);
        }

        $this->collWishlists = null;
        foreach ($wishlists as $wishlist) {
            $this->addWishlist($wishlist);
        }

        $this->collWishlists = $wishlists;
        $this->collWishlistsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Wishlist objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Wishlist objects.
     * @throws PropelException
     */
    public function countWishlists(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collWishlistsPartial && !$this->isNew();
        if (null === $this->collWishlists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWishlists) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWishlists());
            }

            $query = WishlistQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collWishlists);
    }

    /**
     * Method called to associate a ChildWishlist object to this object
     * through the ChildWishlist foreign key attribute.
     *
     * @param    ChildWishlist $l ChildWishlist
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addWishlist(ChildWishlist $l)
    {
        if ($this->collWishlists === null) {
            $this->initWishlists();
            $this->collWishlistsPartial = true;
        }

        if (!in_array($l, $this->collWishlists->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWishlist($l);
        }

        return $this;
    }

    /**
     * @param Wishlist $wishlist The wishlist object to add.
     */
    protected function doAddWishlist($wishlist)
    {
        $this->collWishlists[]= $wishlist;
        $wishlist->setShop($this);
    }

    /**
     * @param  Wishlist $wishlist The wishlist object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeWishlist($wishlist)
    {
        if ($this->getWishlists()->contains($wishlist)) {
            $this->collWishlists->remove($this->collWishlists->search($wishlist));
            if (null === $this->wishlistsScheduledForDeletion) {
                $this->wishlistsScheduledForDeletion = clone $this->collWishlists;
                $this->wishlistsScheduledForDeletion->clear();
            }
            $this->wishlistsScheduledForDeletion[]= $wishlist;
            $wishlist->setShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related Wishlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     */
    public function getWishlistsJoinClient($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = WishlistQuery::create(null, $criteria);
        $query->joinWith('Client', $joinBehavior);

        return $this->getWishlists($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Shop is new, it will return
     * an empty collection; or if this Shop has previously
     * been saved, it will retrieve related Wishlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Shop.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     */
    public function getWishlistsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = WishlistQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getWishlists($query, $con);
    }

    /**
     * Clears out the collShopI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addShopI18ns()
     */
    public function clearShopI18ns()
    {
        $this->collShopI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collShopI18ns collection loaded partially.
     */
    public function resetPartialShopI18ns($v = true)
    {
        $this->collShopI18nsPartial = $v;
    }

    /**
     * Initializes the collShopI18ns collection.
     *
     * By default this just sets the collShopI18ns collection to an empty array (like clearcollShopI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initShopI18ns($overrideExisting = true)
    {
        if (null !== $this->collShopI18ns && !$overrideExisting) {
            return;
        }
        $this->collShopI18ns = new ObjectCollection();
        $this->collShopI18ns->setModel('\Gekosale\Plugin\Shop\Model\ORM\ShopI18n');
    }

    /**
     * Gets an array of ChildShopI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildShop is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildShopI18n[] List of ChildShopI18n objects
     * @throws PropelException
     */
    public function getShopI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collShopI18nsPartial && !$this->isNew();
        if (null === $this->collShopI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collShopI18ns) {
                // return empty collection
                $this->initShopI18ns();
            } else {
                $collShopI18ns = ChildShopI18nQuery::create(null, $criteria)
                    ->filterByShop($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collShopI18nsPartial && count($collShopI18ns)) {
                        $this->initShopI18ns(false);

                        foreach ($collShopI18ns as $obj) {
                            if (false == $this->collShopI18ns->contains($obj)) {
                                $this->collShopI18ns->append($obj);
                            }
                        }

                        $this->collShopI18nsPartial = true;
                    }

                    reset($collShopI18ns);

                    return $collShopI18ns;
                }

                if ($partial && $this->collShopI18ns) {
                    foreach ($this->collShopI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collShopI18ns[] = $obj;
                        }
                    }
                }

                $this->collShopI18ns = $collShopI18ns;
                $this->collShopI18nsPartial = false;
            }
        }

        return $this->collShopI18ns;
    }

    /**
     * Sets a collection of ShopI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $shopI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildShop The current object (for fluent API support)
     */
    public function setShopI18ns(Collection $shopI18ns, ConnectionInterface $con = null)
    {
        $shopI18nsToDelete = $this->getShopI18ns(new Criteria(), $con)->diff($shopI18ns);

        
        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->shopI18nsScheduledForDeletion = clone $shopI18nsToDelete;

        foreach ($shopI18nsToDelete as $shopI18nRemoved) {
            $shopI18nRemoved->setShop(null);
        }

        $this->collShopI18ns = null;
        foreach ($shopI18ns as $shopI18n) {
            $this->addShopI18n($shopI18n);
        }

        $this->collShopI18ns = $shopI18ns;
        $this->collShopI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ShopI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ShopI18n objects.
     * @throws PropelException
     */
    public function countShopI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collShopI18nsPartial && !$this->isNew();
        if (null === $this->collShopI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collShopI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getShopI18ns());
            }

            $query = ChildShopI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByShop($this)
                ->count($con);
        }

        return count($this->collShopI18ns);
    }

    /**
     * Method called to associate a ChildShopI18n object to this object
     * through the ChildShopI18n foreign key attribute.
     *
     * @param    ChildShopI18n $l ChildShopI18n
     * @return   \Gekosale\Plugin\Shop\Model\ORM\Shop The current object (for fluent API support)
     */
    public function addShopI18n(ChildShopI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collShopI18ns === null) {
            $this->initShopI18ns();
            $this->collShopI18nsPartial = true;
        }

        if (!in_array($l, $this->collShopI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddShopI18n($l);
        }

        return $this;
    }

    /**
     * @param ShopI18n $shopI18n The shopI18n object to add.
     */
    protected function doAddShopI18n($shopI18n)
    {
        $this->collShopI18ns[]= $shopI18n;
        $shopI18n->setShop($this);
    }

    /**
     * @param  ShopI18n $shopI18n The shopI18n object to remove.
     * @return ChildShop The current object (for fluent API support)
     */
    public function removeShopI18n($shopI18n)
    {
        if ($this->getShopI18ns()->contains($shopI18n)) {
            $this->collShopI18ns->remove($this->collShopI18ns->search($shopI18n));
            if (null === $this->shopI18nsScheduledForDeletion) {
                $this->shopI18nsScheduledForDeletion = clone $this->collShopI18ns;
                $this->shopI18nsScheduledForDeletion->clear();
            }
            $this->shopI18nsScheduledForDeletion[]= clone $shopI18n;
            $shopI18n->setShop(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->url = null;
        $this->company_id = null;
        $this->period_id = null;
        $this->www_redirection = null;
        $this->taxes = null;
        $this->photo_id = null;
        $this->favicon = null;
        $this->offline = null;
        $this->offline_text = null;
        $this->cart_redirect = null;
        $this->minimum_order_value = null;
        $this->show_tax = null;
        $this->enable_opinions = null;
        $this->enable_tags = null;
        $this->catalog_mode = null;
        $this->force_login = null;
        $this->enable_rss = null;
        $this->invoice_numeration_kind = null;
        $this->invoice_default_payment_due = null;
        $this->confirm_registration = null;
        $this->enable_registration = null;
        $this->currency_id = null;
        $this->contact_id = null;
        $this->default_vat_id = null;
        $this->order_status_groups_id = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collClients) {
                foreach ($this->collClients as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMissingCarts) {
                foreach ($this->collMissingCarts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMissingCartProducts) {
                foreach ($this->collMissingCartProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrders) {
                foreach ($this->collOrders as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductSearchPhrasess) {
                foreach ($this->collProductSearchPhrasess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collBlogShops) {
                foreach ($this->collBlogShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCartRuleShops) {
                foreach ($this->collCartRuleShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategoryShops) {
                foreach ($this->collCategoryShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPageShops) {
                foreach ($this->collPageShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collContactShops) {
                foreach ($this->collContactShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCurrencyShops) {
                foreach ($this->collCurrencyShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDispatchMethodShops) {
                foreach ($this->collDispatchMethodShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collLocaleShops) {
                foreach ($this->collLocaleShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPaymentMethodShops) {
                foreach ($this->collPaymentMethodShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProducerShops) {
                foreach ($this->collProducerShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserGroupShops) {
                foreach ($this->collUserGroupShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWishlists) {
                foreach ($this->collWishlists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collShopI18ns) {
                foreach ($this->collShopI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collClients = null;
        $this->collMissingCarts = null;
        $this->collMissingCartProducts = null;
        $this->collOrders = null;
        $this->collProductSearchPhrasess = null;
        $this->collBlogShops = null;
        $this->collCartRuleShops = null;
        $this->collCategoryShops = null;
        $this->collPageShops = null;
        $this->collContactShops = null;
        $this->collCurrencyShops = null;
        $this->collDispatchMethodShops = null;
        $this->collLocaleShops = null;
        $this->collPaymentMethodShops = null;
        $this->collProducerShops = null;
        $this->collUserGroupShops = null;
        $this->collWishlists = null;
        $this->collShopI18ns = null;
        $this->aContact = null;
        $this->aCurrency = null;
        $this->aVat = null;
        $this->aOrderStatusGroups = null;
        $this->aCompany = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ShopTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior
    
    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildShop The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;
    
        return $this;
    }
    
    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }
    
    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildShopI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collShopI18ns) {
                foreach ($this->collShopI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;
    
                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildShopI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildShopI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addShopI18n($translation);
        }
    
        return $this->currentTranslations[$locale];
    }
    
    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildShop The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildShopI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collShopI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collShopI18ns[$key]);
                break;
            }
        }
    
        return $this;
    }
    
    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildShopI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }
    
    
        /**
         * Get the [name] column value.
         * 
         * @return   string
         */
        public function getName()
        {
        return $this->getCurrentTranslation()->getName();
    }
    
    
        /**
         * Set the value of [name] column.
         * 
         * @param      string $v new value
         * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopI18n The current object (for fluent API support)
         */
        public function setName($v)
        {    $this->getCurrentTranslation()->setName($v);
    
        return $this;
    }
    
    
        /**
         * Get the [meta_title] column value.
         * 
         * @return   string
         */
        public function getMetaTitle()
        {
        return $this->getCurrentTranslation()->getMetaTitle();
    }
    
    
        /**
         * Set the value of [meta_title] column.
         * 
         * @param      string $v new value
         * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopI18n The current object (for fluent API support)
         */
        public function setMetaTitle($v)
        {    $this->getCurrentTranslation()->setMetaTitle($v);
    
        return $this;
    }
    
    
        /**
         * Get the [meta_keyword] column value.
         * 
         * @return   string
         */
        public function getMetaKeyword()
        {
        return $this->getCurrentTranslation()->getMetaKeyword();
    }
    
    
        /**
         * Set the value of [meta_keyword] column.
         * 
         * @param      string $v new value
         * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopI18n The current object (for fluent API support)
         */
        public function setMetaKeyword($v)
        {    $this->getCurrentTranslation()->setMetaKeyword($v);
    
        return $this;
    }
    
    
        /**
         * Get the [meta_description] column value.
         * 
         * @return   string
         */
        public function getMetaDescription()
        {
        return $this->getCurrentTranslation()->getMetaDescription();
    }
    
    
        /**
         * Set the value of [meta_description] column.
         * 
         * @param      string $v new value
         * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopI18n The current object (for fluent API support)
         */
        public function setMetaDescription($v)
        {    $this->getCurrentTranslation()->setMetaDescription($v);
    
        return $this;
    }

    // timestampable behavior
    
    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildShop The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[ShopTableMap::COL_UPDATED_AT] = true;
    
        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
