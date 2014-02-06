<?php

namespace Gekosale\Plugin\Shop\Model\ORM\Map;

use Gekosale\Plugin\Blog\Model\ORM\Map\BlogShopTableMap;
use Gekosale\Plugin\CartRule\Model\ORM\Map\CartRuleShopTableMap;
use Gekosale\Plugin\Category\Model\ORM\Map\CategoryShopTableMap;
use Gekosale\Plugin\Contact\Model\ORM\Map\ContactShopTableMap;
use Gekosale\Plugin\Currency\Model\ORM\Map\CurrencyShopTableMap;
use Gekosale\Plugin\DispatchMethod\Model\ORM\Map\DispatchMethodShopTableMap;
use Gekosale\Plugin\Locale\Model\ORM\Map\LocaleShopTableMap;
use Gekosale\Plugin\MissingCart\Model\ORM\Map\MissingCartProductTableMap;
use Gekosale\Plugin\MissingCart\Model\ORM\Map\MissingCartTableMap;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderTableMap;
use Gekosale\Plugin\Page\Model\ORM\Map\PageShopTableMap;
use Gekosale\Plugin\PaymentMethod\Model\ORM\Map\PaymentMethodShopTableMap;
use Gekosale\Plugin\Producer\Model\ORM\Map\ProducerShopTableMap;
use Gekosale\Plugin\Search\Model\ORM\Map\ProductSearchPhrasesTableMap;
use Gekosale\Plugin\Shop\Model\ORM\Shop;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery;
use Gekosale\Plugin\User\Model\ORM\Map\UserGroupShopTableMap;
use Gekosale\Plugin\Wishlist\Model\ORM\Map\WishlistTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'shop' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ShopTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Shop.Model.ORM.Map.ShopTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'shop';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Shop.Model.ORM.Shop';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 28;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 28;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'shop.ID';

    /**
     * the column name for the URL field
     */
    const COL_URL = 'shop.URL';

    /**
     * the column name for the COMPANY_ID field
     */
    const COL_COMPANY_ID = 'shop.COMPANY_ID';

    /**
     * the column name for the PERIOD_ID field
     */
    const COL_PERIOD_ID = 'shop.PERIOD_ID';

    /**
     * the column name for the WWW_REDIRECTION field
     */
    const COL_WWW_REDIRECTION = 'shop.WWW_REDIRECTION';

    /**
     * the column name for the TAXES field
     */
    const COL_TAXES = 'shop.TAXES';

    /**
     * the column name for the PHOTO_ID field
     */
    const COL_PHOTO_ID = 'shop.PHOTO_ID';

    /**
     * the column name for the FAVICON field
     */
    const COL_FAVICON = 'shop.FAVICON';

    /**
     * the column name for the OFFLINE field
     */
    const COL_OFFLINE = 'shop.OFFLINE';

    /**
     * the column name for the OFFLINE_TEXT field
     */
    const COL_OFFLINE_TEXT = 'shop.OFFLINE_TEXT';

    /**
     * the column name for the CART_REDIRECT field
     */
    const COL_CART_REDIRECT = 'shop.CART_REDIRECT';

    /**
     * the column name for the MINIMUM_ORDER_VALUE field
     */
    const COL_MINIMUM_ORDER_VALUE = 'shop.MINIMUM_ORDER_VALUE';

    /**
     * the column name for the SHOW_TAX field
     */
    const COL_SHOW_TAX = 'shop.SHOW_TAX';

    /**
     * the column name for the ENABLE_OPINIONS field
     */
    const COL_ENABLE_OPINIONS = 'shop.ENABLE_OPINIONS';

    /**
     * the column name for the ENABLE_TAGS field
     */
    const COL_ENABLE_TAGS = 'shop.ENABLE_TAGS';

    /**
     * the column name for the CATALOG_MODE field
     */
    const COL_CATALOG_MODE = 'shop.CATALOG_MODE';

    /**
     * the column name for the FORCE_LOGIN field
     */
    const COL_FORCE_LOGIN = 'shop.FORCE_LOGIN';

    /**
     * the column name for the ENABLE_RSS field
     */
    const COL_ENABLE_RSS = 'shop.ENABLE_RSS';

    /**
     * the column name for the INVOICE_NUMERATION_KIND field
     */
    const COL_INVOICE_NUMERATION_KIND = 'shop.INVOICE_NUMERATION_KIND';

    /**
     * the column name for the INVOICE_DEFAULT_PAYMENT_DUE field
     */
    const COL_INVOICE_DEFAULT_PAYMENT_DUE = 'shop.INVOICE_DEFAULT_PAYMENT_DUE';

    /**
     * the column name for the CONFIRM_REGISTRATION field
     */
    const COL_CONFIRM_REGISTRATION = 'shop.CONFIRM_REGISTRATION';

    /**
     * the column name for the ENABLE_REGISTRATION field
     */
    const COL_ENABLE_REGISTRATION = 'shop.ENABLE_REGISTRATION';

    /**
     * the column name for the CURRENCY_ID field
     */
    const COL_CURRENCY_ID = 'shop.CURRENCY_ID';

    /**
     * the column name for the CONTACT_ID field
     */
    const COL_CONTACT_ID = 'shop.CONTACT_ID';

    /**
     * the column name for the DEFAULT_VAT_ID field
     */
    const COL_DEFAULT_VAT_ID = 'shop.DEFAULT_VAT_ID';

    /**
     * the column name for the ORDER_STATUS_GROUPS_ID field
     */
    const COL_ORDER_STATUS_GROUPS_ID = 'shop.ORDER_STATUS_GROUPS_ID';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'shop.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'shop.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    // i18n behavior
    
    /**
     * The default locale to use for translations.
     *
     * @var string
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Url', 'CompanyId', 'PeriodId', 'WwwRedirection', 'Taxes', 'PhotoId', 'Favicon', 'Offline', 'OfflineText', 'CartRedirect', 'MinimumOrderValue', 'ShowTax', 'EnableOpinions', 'EnableTags', 'CatalogMode', 'ForceLogin', 'EnableRss', 'InvoiceNumerationKind', 'InvoiceDefaultPaymentDue', 'ConfirmRegistration', 'EnableRegistration', 'CurrencyId', 'ContactId', 'DefaultVatId', 'OrderStatusGroupsId', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'url', 'companyId', 'periodId', 'wwwRedirection', 'taxes', 'photoId', 'favicon', 'offline', 'offlineText', 'cartRedirect', 'minimumOrderValue', 'showTax', 'enableOpinions', 'enableTags', 'catalogMode', 'forceLogin', 'enableRss', 'invoiceNumerationKind', 'invoiceDefaultPaymentDue', 'confirmRegistration', 'enableRegistration', 'currencyId', 'contactId', 'defaultVatId', 'orderStatusGroupsId', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(ShopTableMap::COL_ID, ShopTableMap::COL_URL, ShopTableMap::COL_COMPANY_ID, ShopTableMap::COL_PERIOD_ID, ShopTableMap::COL_WWW_REDIRECTION, ShopTableMap::COL_TAXES, ShopTableMap::COL_PHOTO_ID, ShopTableMap::COL_FAVICON, ShopTableMap::COL_OFFLINE, ShopTableMap::COL_OFFLINE_TEXT, ShopTableMap::COL_CART_REDIRECT, ShopTableMap::COL_MINIMUM_ORDER_VALUE, ShopTableMap::COL_SHOW_TAX, ShopTableMap::COL_ENABLE_OPINIONS, ShopTableMap::COL_ENABLE_TAGS, ShopTableMap::COL_CATALOG_MODE, ShopTableMap::COL_FORCE_LOGIN, ShopTableMap::COL_ENABLE_RSS, ShopTableMap::COL_INVOICE_NUMERATION_KIND, ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE, ShopTableMap::COL_CONFIRM_REGISTRATION, ShopTableMap::COL_ENABLE_REGISTRATION, ShopTableMap::COL_CURRENCY_ID, ShopTableMap::COL_CONTACT_ID, ShopTableMap::COL_DEFAULT_VAT_ID, ShopTableMap::COL_ORDER_STATUS_GROUPS_ID, ShopTableMap::COL_CREATED_AT, ShopTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_URL', 'COL_COMPANY_ID', 'COL_PERIOD_ID', 'COL_WWW_REDIRECTION', 'COL_TAXES', 'COL_PHOTO_ID', 'COL_FAVICON', 'COL_OFFLINE', 'COL_OFFLINE_TEXT', 'COL_CART_REDIRECT', 'COL_MINIMUM_ORDER_VALUE', 'COL_SHOW_TAX', 'COL_ENABLE_OPINIONS', 'COL_ENABLE_TAGS', 'COL_CATALOG_MODE', 'COL_FORCE_LOGIN', 'COL_ENABLE_RSS', 'COL_INVOICE_NUMERATION_KIND', 'COL_INVOICE_DEFAULT_PAYMENT_DUE', 'COL_CONFIRM_REGISTRATION', 'COL_ENABLE_REGISTRATION', 'COL_CURRENCY_ID', 'COL_CONTACT_ID', 'COL_DEFAULT_VAT_ID', 'COL_ORDER_STATUS_GROUPS_ID', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'url', 'company_id', 'period_id', 'www_redirection', 'taxes', 'photo_id', 'favicon', 'offline', 'offline_text', 'cart_redirect', 'minimum_order_value', 'show_tax', 'enable_opinions', 'enable_tags', 'catalog_mode', 'force_login', 'enable_rss', 'invoice_numeration_kind', 'invoice_default_payment_due', 'confirm_registration', 'enable_registration', 'currency_id', 'contact_id', 'default_vat_id', 'order_status_groups_id', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Url' => 1, 'CompanyId' => 2, 'PeriodId' => 3, 'WwwRedirection' => 4, 'Taxes' => 5, 'PhotoId' => 6, 'Favicon' => 7, 'Offline' => 8, 'OfflineText' => 9, 'CartRedirect' => 10, 'MinimumOrderValue' => 11, 'ShowTax' => 12, 'EnableOpinions' => 13, 'EnableTags' => 14, 'CatalogMode' => 15, 'ForceLogin' => 16, 'EnableRss' => 17, 'InvoiceNumerationKind' => 18, 'InvoiceDefaultPaymentDue' => 19, 'ConfirmRegistration' => 20, 'EnableRegistration' => 21, 'CurrencyId' => 22, 'ContactId' => 23, 'DefaultVatId' => 24, 'OrderStatusGroupsId' => 25, 'CreatedAt' => 26, 'UpdatedAt' => 27, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'url' => 1, 'companyId' => 2, 'periodId' => 3, 'wwwRedirection' => 4, 'taxes' => 5, 'photoId' => 6, 'favicon' => 7, 'offline' => 8, 'offlineText' => 9, 'cartRedirect' => 10, 'minimumOrderValue' => 11, 'showTax' => 12, 'enableOpinions' => 13, 'enableTags' => 14, 'catalogMode' => 15, 'forceLogin' => 16, 'enableRss' => 17, 'invoiceNumerationKind' => 18, 'invoiceDefaultPaymentDue' => 19, 'confirmRegistration' => 20, 'enableRegistration' => 21, 'currencyId' => 22, 'contactId' => 23, 'defaultVatId' => 24, 'orderStatusGroupsId' => 25, 'createdAt' => 26, 'updatedAt' => 27, ),
        self::TYPE_COLNAME       => array(ShopTableMap::COL_ID => 0, ShopTableMap::COL_URL => 1, ShopTableMap::COL_COMPANY_ID => 2, ShopTableMap::COL_PERIOD_ID => 3, ShopTableMap::COL_WWW_REDIRECTION => 4, ShopTableMap::COL_TAXES => 5, ShopTableMap::COL_PHOTO_ID => 6, ShopTableMap::COL_FAVICON => 7, ShopTableMap::COL_OFFLINE => 8, ShopTableMap::COL_OFFLINE_TEXT => 9, ShopTableMap::COL_CART_REDIRECT => 10, ShopTableMap::COL_MINIMUM_ORDER_VALUE => 11, ShopTableMap::COL_SHOW_TAX => 12, ShopTableMap::COL_ENABLE_OPINIONS => 13, ShopTableMap::COL_ENABLE_TAGS => 14, ShopTableMap::COL_CATALOG_MODE => 15, ShopTableMap::COL_FORCE_LOGIN => 16, ShopTableMap::COL_ENABLE_RSS => 17, ShopTableMap::COL_INVOICE_NUMERATION_KIND => 18, ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE => 19, ShopTableMap::COL_CONFIRM_REGISTRATION => 20, ShopTableMap::COL_ENABLE_REGISTRATION => 21, ShopTableMap::COL_CURRENCY_ID => 22, ShopTableMap::COL_CONTACT_ID => 23, ShopTableMap::COL_DEFAULT_VAT_ID => 24, ShopTableMap::COL_ORDER_STATUS_GROUPS_ID => 25, ShopTableMap::COL_CREATED_AT => 26, ShopTableMap::COL_UPDATED_AT => 27, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_URL' => 1, 'COL_COMPANY_ID' => 2, 'COL_PERIOD_ID' => 3, 'COL_WWW_REDIRECTION' => 4, 'COL_TAXES' => 5, 'COL_PHOTO_ID' => 6, 'COL_FAVICON' => 7, 'COL_OFFLINE' => 8, 'COL_OFFLINE_TEXT' => 9, 'COL_CART_REDIRECT' => 10, 'COL_MINIMUM_ORDER_VALUE' => 11, 'COL_SHOW_TAX' => 12, 'COL_ENABLE_OPINIONS' => 13, 'COL_ENABLE_TAGS' => 14, 'COL_CATALOG_MODE' => 15, 'COL_FORCE_LOGIN' => 16, 'COL_ENABLE_RSS' => 17, 'COL_INVOICE_NUMERATION_KIND' => 18, 'COL_INVOICE_DEFAULT_PAYMENT_DUE' => 19, 'COL_CONFIRM_REGISTRATION' => 20, 'COL_ENABLE_REGISTRATION' => 21, 'COL_CURRENCY_ID' => 22, 'COL_CONTACT_ID' => 23, 'COL_DEFAULT_VAT_ID' => 24, 'COL_ORDER_STATUS_GROUPS_ID' => 25, 'COL_CREATED_AT' => 26, 'COL_UPDATED_AT' => 27, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'url' => 1, 'company_id' => 2, 'period_id' => 3, 'www_redirection' => 4, 'taxes' => 5, 'photo_id' => 6, 'favicon' => 7, 'offline' => 8, 'offline_text' => 9, 'cart_redirect' => 10, 'minimum_order_value' => 11, 'show_tax' => 12, 'enable_opinions' => 13, 'enable_tags' => 14, 'catalog_mode' => 15, 'force_login' => 16, 'enable_rss' => 17, 'invoice_numeration_kind' => 18, 'invoice_default_payment_due' => 19, 'confirm_registration' => 20, 'enable_registration' => 21, 'currency_id' => 22, 'contact_id' => 23, 'default_vat_id' => 24, 'order_status_groups_id' => 25, 'created_at' => 26, 'updated_at' => 27, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('shop');
        $this->setPhpName('Shop');
        $this->setClassName('\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop');
        $this->setPackage('Gekosale.Plugin.Shop.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('URL', 'Url', 'VARCHAR', true, 255, null);
        $this->addForeignKey('COMPANY_ID', 'CompanyId', 'INTEGER', 'company', 'ID', true, 10, null);
        $this->addColumn('PERIOD_ID', 'PeriodId', 'INTEGER', false, 10, null);
        $this->addColumn('WWW_REDIRECTION', 'WwwRedirection', 'SMALLINT', false, 1, null);
        $this->addColumn('TAXES', 'Taxes', 'INTEGER', false, 10, null);
        $this->addColumn('PHOTO_ID', 'PhotoId', 'VARCHAR', false, 255, null);
        $this->addColumn('FAVICON', 'Favicon', 'VARCHAR', false, 255, null);
        $this->addColumn('OFFLINE', 'Offline', 'INTEGER', true, 10, 0);
        $this->addColumn('OFFLINE_TEXT', 'OfflineText', 'LONGVARCHAR', false, null, null);
        $this->addColumn('CART_REDIRECT', 'CartRedirect', 'INTEGER', false, null, 1);
        $this->addColumn('MINIMUM_ORDER_VALUE', 'MinimumOrderValue', 'DECIMAL', true, null, 0);
        $this->addColumn('SHOW_TAX', 'ShowTax', 'INTEGER', false, 10, 1);
        $this->addColumn('ENABLE_OPINIONS', 'EnableOpinions', 'INTEGER', false, 10, 1);
        $this->addColumn('ENABLE_TAGS', 'EnableTags', 'INTEGER', false, 10, 1);
        $this->addColumn('CATALOG_MODE', 'CatalogMode', 'INTEGER', false, 10, 0);
        $this->addColumn('FORCE_LOGIN', 'ForceLogin', 'INTEGER', false, 10, 0);
        $this->addColumn('ENABLE_RSS', 'EnableRss', 'INTEGER', false, 10, 1);
        $this->addColumn('INVOICE_NUMERATION_KIND', 'InvoiceNumerationKind', 'VARCHAR', true, 4, 'ntmr');
        $this->addColumn('INVOICE_DEFAULT_PAYMENT_DUE', 'InvoiceDefaultPaymentDue', 'INTEGER', true, null, 7);
        $this->addColumn('CONFIRM_REGISTRATION', 'ConfirmRegistration', 'BOOLEAN', true, 1, null);
        $this->addColumn('ENABLE_REGISTRATION', 'EnableRegistration', 'BOOLEAN', true, 1, true);
        $this->addForeignKey('CURRENCY_ID', 'CurrencyId', 'INTEGER', 'currency', 'ID', false, 10, null);
        $this->addForeignKey('CONTACT_ID', 'ContactId', 'INTEGER', 'contact', 'ID', false, null, null);
        $this->addForeignKey('DEFAULT_VAT_ID', 'DefaultVatId', 'INTEGER', 'vat', 'ID', false, 10, null);
        $this->addForeignKey('ORDER_STATUS_GROUPS_ID', 'OrderStatusGroupsId', 'INTEGER', 'order_status_groups', 'ID', false, 10, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Contact', '\\Gekosale\\Plugin\\Contact\\Model\\ORM\\Contact', RelationMap::MANY_TO_ONE, array('contact_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('Currency', '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Currency', RelationMap::MANY_TO_ONE, array('currency_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('Vat', '\\Gekosale\\Plugin\\Vat\\Model\\ORM\\Vat', RelationMap::MANY_TO_ONE, array('default_vat_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('OrderStatusGroups', '\\Gekosale\\Plugin\\OrderStatus\\Model\\ORM\\OrderStatusGroups', RelationMap::MANY_TO_ONE, array('order_status_groups_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('Company', '\\Gekosale\\Plugin\\Company\\Model\\ORM\\Company', RelationMap::MANY_TO_ONE, array('company_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Client', '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Client', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), null, null, 'Clients');
        $this->addRelation('MissingCart', '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCart', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'MissingCarts');
        $this->addRelation('MissingCartProduct', '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCartProduct', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'MissingCartProducts');
        $this->addRelation('Order', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Order', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'Orders');
        $this->addRelation('ProductSearchPhrases', '\\Gekosale\\Plugin\\Search\\Model\\ORM\\ProductSearchPhrases', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'ProductSearchPhrasess');
        $this->addRelation('BlogShop', '\\Gekosale\\Plugin\\Blog\\Model\\ORM\\BlogShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'BlogShops');
        $this->addRelation('CartRuleShop', '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'CartRuleShops');
        $this->addRelation('CategoryShop', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\CategoryShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'CategoryShops');
        $this->addRelation('PageShop', '\\Gekosale\\Plugin\\Page\\Model\\ORM\\PageShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'PageShops');
        $this->addRelation('ContactShop', '\\Gekosale\\Plugin\\Contact\\Model\\ORM\\ContactShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'ContactShops');
        $this->addRelation('CurrencyShop', '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\CurrencyShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'CurrencyShops');
        $this->addRelation('DispatchMethodShop', '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'DispatchMethodShops');
        $this->addRelation('LocaleShop', '\\Gekosale\\Plugin\\Locale\\Model\\ORM\\LocaleShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'LocaleShops');
        $this->addRelation('PaymentMethodShop', '\\Gekosale\\Plugin\\PaymentMethod\\Model\\ORM\\PaymentMethodShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'PaymentMethodShops');
        $this->addRelation('ProducerShop', '\\Gekosale\\Plugin\\Producer\\Model\\ORM\\ProducerShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'ProducerShops');
        $this->addRelation('UserGroupShop', '\\Gekosale\\Plugin\\User\\Model\\ORM\\UserGroupShop', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'UserGroupShops');
        $this->addRelation('Wishlist', '\\Gekosale\\Plugin\\Wishlist\\Model\\ORM\\Wishlist', RelationMap::ONE_TO_MANY, array('id' => 'shop_id', ), 'CASCADE', null, 'Wishlists');
        $this->addRelation('ShopI18n', '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\ShopI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'ShopI18ns');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'name, meta_title, meta_keyword, meta_description', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => '', 'locale_alias' => '', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to shop     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                MissingCartTableMap::clearInstancePool();
                MissingCartProductTableMap::clearInstancePool();
                OrderTableMap::clearInstancePool();
                ProductSearchPhrasesTableMap::clearInstancePool();
                BlogShopTableMap::clearInstancePool();
                CartRuleShopTableMap::clearInstancePool();
                CategoryShopTableMap::clearInstancePool();
                PageShopTableMap::clearInstancePool();
                ContactShopTableMap::clearInstancePool();
                CurrencyShopTableMap::clearInstancePool();
                DispatchMethodShopTableMap::clearInstancePool();
                LocaleShopTableMap::clearInstancePool();
                PaymentMethodShopTableMap::clearInstancePool();
                ProducerShopTableMap::clearInstancePool();
                UserGroupShopTableMap::clearInstancePool();
                WishlistTableMap::clearInstancePool();
                ShopI18nTableMap::clearInstancePool();
            }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }
    
    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? ShopTableMap::CLASS_DEFAULT : ShopTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (Shop object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ShopTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ShopTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ShopTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ShopTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ShopTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ShopTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ShopTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ShopTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ShopTableMap::COL_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_URL);
            $criteria->addSelectColumn(ShopTableMap::COL_COMPANY_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_PERIOD_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_WWW_REDIRECTION);
            $criteria->addSelectColumn(ShopTableMap::COL_TAXES);
            $criteria->addSelectColumn(ShopTableMap::COL_PHOTO_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_FAVICON);
            $criteria->addSelectColumn(ShopTableMap::COL_OFFLINE);
            $criteria->addSelectColumn(ShopTableMap::COL_OFFLINE_TEXT);
            $criteria->addSelectColumn(ShopTableMap::COL_CART_REDIRECT);
            $criteria->addSelectColumn(ShopTableMap::COL_MINIMUM_ORDER_VALUE);
            $criteria->addSelectColumn(ShopTableMap::COL_SHOW_TAX);
            $criteria->addSelectColumn(ShopTableMap::COL_ENABLE_OPINIONS);
            $criteria->addSelectColumn(ShopTableMap::COL_ENABLE_TAGS);
            $criteria->addSelectColumn(ShopTableMap::COL_CATALOG_MODE);
            $criteria->addSelectColumn(ShopTableMap::COL_FORCE_LOGIN);
            $criteria->addSelectColumn(ShopTableMap::COL_ENABLE_RSS);
            $criteria->addSelectColumn(ShopTableMap::COL_INVOICE_NUMERATION_KIND);
            $criteria->addSelectColumn(ShopTableMap::COL_INVOICE_DEFAULT_PAYMENT_DUE);
            $criteria->addSelectColumn(ShopTableMap::COL_CONFIRM_REGISTRATION);
            $criteria->addSelectColumn(ShopTableMap::COL_ENABLE_REGISTRATION);
            $criteria->addSelectColumn(ShopTableMap::COL_CURRENCY_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_CONTACT_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_DEFAULT_VAT_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_ORDER_STATUS_GROUPS_ID);
            $criteria->addSelectColumn(ShopTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(ShopTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.URL');
            $criteria->addSelectColumn($alias . '.COMPANY_ID');
            $criteria->addSelectColumn($alias . '.PERIOD_ID');
            $criteria->addSelectColumn($alias . '.WWW_REDIRECTION');
            $criteria->addSelectColumn($alias . '.TAXES');
            $criteria->addSelectColumn($alias . '.PHOTO_ID');
            $criteria->addSelectColumn($alias . '.FAVICON');
            $criteria->addSelectColumn($alias . '.OFFLINE');
            $criteria->addSelectColumn($alias . '.OFFLINE_TEXT');
            $criteria->addSelectColumn($alias . '.CART_REDIRECT');
            $criteria->addSelectColumn($alias . '.MINIMUM_ORDER_VALUE');
            $criteria->addSelectColumn($alias . '.SHOW_TAX');
            $criteria->addSelectColumn($alias . '.ENABLE_OPINIONS');
            $criteria->addSelectColumn($alias . '.ENABLE_TAGS');
            $criteria->addSelectColumn($alias . '.CATALOG_MODE');
            $criteria->addSelectColumn($alias . '.FORCE_LOGIN');
            $criteria->addSelectColumn($alias . '.ENABLE_RSS');
            $criteria->addSelectColumn($alias . '.INVOICE_NUMERATION_KIND');
            $criteria->addSelectColumn($alias . '.INVOICE_DEFAULT_PAYMENT_DUE');
            $criteria->addSelectColumn($alias . '.CONFIRM_REGISTRATION');
            $criteria->addSelectColumn($alias . '.ENABLE_REGISTRATION');
            $criteria->addSelectColumn($alias . '.CURRENCY_ID');
            $criteria->addSelectColumn($alias . '.CONTACT_ID');
            $criteria->addSelectColumn($alias . '.DEFAULT_VAT_ID');
            $criteria->addSelectColumn($alias . '.ORDER_STATUS_GROUPS_ID');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(ShopTableMap::DATABASE_NAME)->getTable(ShopTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ShopTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ShopTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ShopTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Shop or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Shop object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShopTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ShopTableMap::DATABASE_NAME);
            $criteria->add(ShopTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ShopQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ShopTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ShopTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the shop table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ShopQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Shop or Criteria object.
     *
     * @param mixed               $criteria Criteria or Shop object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ShopTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Shop object
        }

        if ($criteria->containsKey(ShopTableMap::COL_ID) && $criteria->keyContainsValue(ShopTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ShopTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ShopQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // ShopTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ShopTableMap::buildTableMap();
