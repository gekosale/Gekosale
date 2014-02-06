<?php

namespace Gekosale\Plugin\Company\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\Company\Model\ORM\Company as ChildCompany;
use Gekosale\Plugin\Company\Model\ORM\CompanyQuery as ChildCompanyQuery;
use Gekosale\Plugin\Company\Model\ORM\Map\CompanyTableMap;
use Gekosale\Plugin\Controller\Model\ORM\ControllerPermission as ChildControllerPermission;
use Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery;
use Gekosale\Plugin\Controller\Model\ORM\Base\ControllerPermission;
use Gekosale\Plugin\Country\Model\ORM\Country as ChildCountry;
use Gekosale\Plugin\Country\Model\ORM\CountryQuery;
use Gekosale\Plugin\File\Model\ORM\File as ChildFile;
use Gekosale\Plugin\File\Model\ORM\FileQuery;
use Gekosale\Plugin\Shop\Model\ORM\Shop as ChildShop;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery;
use Gekosale\Plugin\Shop\Model\ORM\Base\Shop;
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

abstract class Company implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Company\\Model\\ORM\\Map\\CompanyTableMap';


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
     * The value for the country_id field.
     * @var        int
     */
    protected $country_id;

    /**
     * The value for the photo_id field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $photo_id;

    /**
     * The value for the bank_name field.
     * @var        string
     */
    protected $bank_name;

    /**
     * The value for the bank_account_no field.
     * @var        string
     */
    protected $bank_account_no;

    /**
     * The value for the tax_id field.
     * @var        string
     */
    protected $tax_id;

    /**
     * The value for the company_name field.
     * @var        string
     */
    protected $company_name;

    /**
     * The value for the short_company_name field.
     * @var        string
     */
    protected $short_company_name;

    /**
     * The value for the post_code field.
     * @var        string
     */
    protected $post_code;

    /**
     * The value for the city field.
     * @var        string
     */
    protected $city;

    /**
     * The value for the street field.
     * @var        string
     */
    protected $street;

    /**
     * The value for the street_no field.
     * @var        string
     */
    protected $street_no;

    /**
     * The value for the place_no field.
     * @var        string
     */
    protected $place_no;

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
     * @var        Country
     */
    protected $aCountry;

    /**
     * @var        File
     */
    protected $aFile;

    /**
     * @var        ObjectCollection|ChildControllerPermission[] Collection to store aggregation of ChildControllerPermission objects.
     */
    protected $collControllerPermissions;
    protected $collControllerPermissionsPartial;

    /**
     * @var        ObjectCollection|ChildShop[] Collection to store aggregation of ChildShop objects.
     */
    protected $collShops;
    protected $collShopsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $controllerPermissionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $shopsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->photo_id = 1;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Company\Model\ORM\Base\Company object.
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
     * Compares this with another <code>Company</code> instance.  If
     * <code>obj</code> is an instance of <code>Company</code>, delegates to
     * <code>equals(Company)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Company The current object, for fluid interface
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
     * @return Company The current object, for fluid interface
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
     * Get the [country_id] column value.
     * 
     * @return   int
     */
    public function getCountryId()
    {

        return $this->country_id;
    }

    /**
     * Get the [photo_id] column value.
     * 
     * @return   int
     */
    public function getPhotoId()
    {

        return $this->photo_id;
    }

    /**
     * Get the [bank_name] column value.
     * 
     * @return   string
     */
    public function getBankName()
    {

        return $this->bank_name;
    }

    /**
     * Get the [bank_account_no] column value.
     * 
     * @return   string
     */
    public function getBankAccountNo()
    {

        return $this->bank_account_no;
    }

    /**
     * Get the [tax_id] column value.
     * 
     * @return   string
     */
    public function getTaxId()
    {

        return $this->tax_id;
    }

    /**
     * Get the [company_name] column value.
     * 
     * @return   string
     */
    public function getCompanyName()
    {

        return $this->company_name;
    }

    /**
     * Get the [short_company_name] column value.
     * 
     * @return   string
     */
    public function getShortCompanyName()
    {

        return $this->short_company_name;
    }

    /**
     * Get the [post_code] column value.
     * 
     * @return   string
     */
    public function getPostCode()
    {

        return $this->post_code;
    }

    /**
     * Get the [city] column value.
     * 
     * @return   string
     */
    public function getCity()
    {

        return $this->city;
    }

    /**
     * Get the [street] column value.
     * 
     * @return   string
     */
    public function getStreet()
    {

        return $this->street;
    }

    /**
     * Get the [street_no] column value.
     * 
     * @return   string
     */
    public function getStreetNo()
    {

        return $this->street_no;
    }

    /**
     * Get the [place_no] column value.
     * 
     * @return   string
     */
    public function getPlaceNo()
    {

        return $this->place_no;
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
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CompanyTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [country_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setCountryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->country_id !== $v) {
            $this->country_id = $v;
            $this->modifiedColumns[CompanyTableMap::COL_COUNTRY_ID] = true;
        }

        if ($this->aCountry !== null && $this->aCountry->getId() !== $v) {
            $this->aCountry = null;
        }


        return $this;
    } // setCountryId()

    /**
     * Set the value of [photo_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setPhotoId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->photo_id !== $v) {
            $this->photo_id = $v;
            $this->modifiedColumns[CompanyTableMap::COL_PHOTO_ID] = true;
        }

        if ($this->aFile !== null && $this->aFile->getId() !== $v) {
            $this->aFile = null;
        }


        return $this;
    } // setPhotoId()

    /**
     * Set the value of [bank_name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setBankName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->bank_name !== $v) {
            $this->bank_name = $v;
            $this->modifiedColumns[CompanyTableMap::COL_BANK_NAME] = true;
        }


        return $this;
    } // setBankName()

    /**
     * Set the value of [bank_account_no] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setBankAccountNo($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->bank_account_no !== $v) {
            $this->bank_account_no = $v;
            $this->modifiedColumns[CompanyTableMap::COL_BANK_ACCOUNT_NO] = true;
        }


        return $this;
    } // setBankAccountNo()

    /**
     * Set the value of [tax_id] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setTaxId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->tax_id !== $v) {
            $this->tax_id = $v;
            $this->modifiedColumns[CompanyTableMap::COL_TAX_ID] = true;
        }


        return $this;
    } // setTaxId()

    /**
     * Set the value of [company_name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setCompanyName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->company_name !== $v) {
            $this->company_name = $v;
            $this->modifiedColumns[CompanyTableMap::COL_COMPANY_NAME] = true;
        }


        return $this;
    } // setCompanyName()

    /**
     * Set the value of [short_company_name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setShortCompanyName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->short_company_name !== $v) {
            $this->short_company_name = $v;
            $this->modifiedColumns[CompanyTableMap::COL_SHORT_COMPANY_NAME] = true;
        }


        return $this;
    } // setShortCompanyName()

    /**
     * Set the value of [post_code] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setPostCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->post_code !== $v) {
            $this->post_code = $v;
            $this->modifiedColumns[CompanyTableMap::COL_POST_CODE] = true;
        }


        return $this;
    } // setPostCode()

    /**
     * Set the value of [city] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setCity($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->city !== $v) {
            $this->city = $v;
            $this->modifiedColumns[CompanyTableMap::COL_CITY] = true;
        }


        return $this;
    } // setCity()

    /**
     * Set the value of [street] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setStreet($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->street !== $v) {
            $this->street = $v;
            $this->modifiedColumns[CompanyTableMap::COL_STREET] = true;
        }


        return $this;
    } // setStreet()

    /**
     * Set the value of [street_no] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setStreetNo($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->street_no !== $v) {
            $this->street_no = $v;
            $this->modifiedColumns[CompanyTableMap::COL_STREET_NO] = true;
        }


        return $this;
    } // setStreetNo()

    /**
     * Set the value of [place_no] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setPlaceNo($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->place_no !== $v) {
            $this->place_no = $v;
            $this->modifiedColumns[CompanyTableMap::COL_PLACE_NO] = true;
        }


        return $this;
    } // setPlaceNo()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[CompanyTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[CompanyTableMap::COL_UPDATED_AT] = true;
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
            if ($this->photo_id !== 1) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CompanyTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CompanyTableMap::translateFieldName('CountryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CompanyTableMap::translateFieldName('PhotoId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->photo_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CompanyTableMap::translateFieldName('BankName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->bank_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CompanyTableMap::translateFieldName('BankAccountNo', TableMap::TYPE_PHPNAME, $indexType)];
            $this->bank_account_no = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CompanyTableMap::translateFieldName('TaxId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tax_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CompanyTableMap::translateFieldName('CompanyName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->company_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CompanyTableMap::translateFieldName('ShortCompanyName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->short_company_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : CompanyTableMap::translateFieldName('PostCode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->post_code = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : CompanyTableMap::translateFieldName('City', TableMap::TYPE_PHPNAME, $indexType)];
            $this->city = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : CompanyTableMap::translateFieldName('Street', TableMap::TYPE_PHPNAME, $indexType)];
            $this->street = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : CompanyTableMap::translateFieldName('StreetNo', TableMap::TYPE_PHPNAME, $indexType)];
            $this->street_no = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : CompanyTableMap::translateFieldName('PlaceNo', TableMap::TYPE_PHPNAME, $indexType)];
            $this->place_no = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : CompanyTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : CompanyTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = CompanyTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Company\Model\ORM\Company object", 0, $e);
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
        if ($this->aCountry !== null && $this->country_id !== $this->aCountry->getId()) {
            $this->aCountry = null;
        }
        if ($this->aFile !== null && $this->photo_id !== $this->aFile->getId()) {
            $this->aFile = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(CompanyTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCompanyQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCountry = null;
            $this->aFile = null;
            $this->collControllerPermissions = null;

            $this->collShops = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Company::setDeleted()
     * @see Company::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CompanyTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCompanyQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(CompanyTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CompanyTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CompanyTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CompanyTableMap::COL_UPDATED_AT)) {
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
                CompanyTableMap::addInstanceToPool($this);
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

            if ($this->aCountry !== null) {
                if ($this->aCountry->isModified() || $this->aCountry->isNew()) {
                    $affectedRows += $this->aCountry->save($con);
                }
                $this->setCountry($this->aCountry);
            }

            if ($this->aFile !== null) {
                if ($this->aFile->isModified() || $this->aFile->isNew()) {
                    $affectedRows += $this->aFile->save($con);
                }
                $this->setFile($this->aFile);
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

            if ($this->controllerPermissionsScheduledForDeletion !== null) {
                if (!$this->controllerPermissionsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery::create()
                        ->filterByPrimaryKeys($this->controllerPermissionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->controllerPermissionsScheduledForDeletion = null;
                }
            }

                if ($this->collControllerPermissions !== null) {
            foreach ($this->collControllerPermissions as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->shopsScheduledForDeletion !== null) {
                if (!$this->shopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Shop\Model\ORM\ShopQuery::create()
                        ->filterByPrimaryKeys($this->shopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->shopsScheduledForDeletion = null;
                }
            }

                if ($this->collShops !== null) {
            foreach ($this->collShops as $referrerFK) {
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

        $this->modifiedColumns[CompanyTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CompanyTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CompanyTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_COUNTRY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'COUNTRY_ID';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_PHOTO_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PHOTO_ID';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_BANK_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'BANK_NAME';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_BANK_ACCOUNT_NO)) {
            $modifiedColumns[':p' . $index++]  = 'BANK_ACCOUNT_NO';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_TAX_ID)) {
            $modifiedColumns[':p' . $index++]  = 'TAX_ID';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_COMPANY_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'COMPANY_NAME';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_SHORT_COMPANY_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'SHORT_COMPANY_NAME';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_POST_CODE)) {
            $modifiedColumns[':p' . $index++]  = 'POST_CODE';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_CITY)) {
            $modifiedColumns[':p' . $index++]  = 'CITY';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_STREET)) {
            $modifiedColumns[':p' . $index++]  = 'STREET';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_STREET_NO)) {
            $modifiedColumns[':p' . $index++]  = 'STREET_NO';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_PLACE_NO)) {
            $modifiedColumns[':p' . $index++]  = 'PLACE_NO';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(CompanyTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO company (%s) VALUES (%s)',
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
                    case 'COUNTRY_ID':                        
                        $stmt->bindValue($identifier, $this->country_id, PDO::PARAM_INT);
                        break;
                    case 'PHOTO_ID':                        
                        $stmt->bindValue($identifier, $this->photo_id, PDO::PARAM_INT);
                        break;
                    case 'BANK_NAME':                        
                        $stmt->bindValue($identifier, $this->bank_name, PDO::PARAM_STR);
                        break;
                    case 'BANK_ACCOUNT_NO':                        
                        $stmt->bindValue($identifier, $this->bank_account_no, PDO::PARAM_STR);
                        break;
                    case 'TAX_ID':                        
                        $stmt->bindValue($identifier, $this->tax_id, PDO::PARAM_STR);
                        break;
                    case 'COMPANY_NAME':                        
                        $stmt->bindValue($identifier, $this->company_name, PDO::PARAM_STR);
                        break;
                    case 'SHORT_COMPANY_NAME':                        
                        $stmt->bindValue($identifier, $this->short_company_name, PDO::PARAM_STR);
                        break;
                    case 'POST_CODE':                        
                        $stmt->bindValue($identifier, $this->post_code, PDO::PARAM_STR);
                        break;
                    case 'CITY':                        
                        $stmt->bindValue($identifier, $this->city, PDO::PARAM_STR);
                        break;
                    case 'STREET':                        
                        $stmt->bindValue($identifier, $this->street, PDO::PARAM_STR);
                        break;
                    case 'STREET_NO':                        
                        $stmt->bindValue($identifier, $this->street_no, PDO::PARAM_STR);
                        break;
                    case 'PLACE_NO':                        
                        $stmt->bindValue($identifier, $this->place_no, PDO::PARAM_STR);
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
        $pos = CompanyTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getCountryId();
                break;
            case 2:
                return $this->getPhotoId();
                break;
            case 3:
                return $this->getBankName();
                break;
            case 4:
                return $this->getBankAccountNo();
                break;
            case 5:
                return $this->getTaxId();
                break;
            case 6:
                return $this->getCompanyName();
                break;
            case 7:
                return $this->getShortCompanyName();
                break;
            case 8:
                return $this->getPostCode();
                break;
            case 9:
                return $this->getCity();
                break;
            case 10:
                return $this->getStreet();
                break;
            case 11:
                return $this->getStreetNo();
                break;
            case 12:
                return $this->getPlaceNo();
                break;
            case 13:
                return $this->getCreatedAt();
                break;
            case 14:
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
        if (isset($alreadyDumpedObjects['Company'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Company'][$this->getPrimaryKey()] = true;
        $keys = CompanyTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCountryId(),
            $keys[2] => $this->getPhotoId(),
            $keys[3] => $this->getBankName(),
            $keys[4] => $this->getBankAccountNo(),
            $keys[5] => $this->getTaxId(),
            $keys[6] => $this->getCompanyName(),
            $keys[7] => $this->getShortCompanyName(),
            $keys[8] => $this->getPostCode(),
            $keys[9] => $this->getCity(),
            $keys[10] => $this->getStreet(),
            $keys[11] => $this->getStreetNo(),
            $keys[12] => $this->getPlaceNo(),
            $keys[13] => $this->getCreatedAt(),
            $keys[14] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aCountry) {
                $result['Country'] = $this->aCountry->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aFile) {
                $result['File'] = $this->aFile->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collControllerPermissions) {
                $result['ControllerPermissions'] = $this->collControllerPermissions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collShops) {
                $result['Shops'] = $this->collShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CompanyTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setCountryId($value);
                break;
            case 2:
                $this->setPhotoId($value);
                break;
            case 3:
                $this->setBankName($value);
                break;
            case 4:
                $this->setBankAccountNo($value);
                break;
            case 5:
                $this->setTaxId($value);
                break;
            case 6:
                $this->setCompanyName($value);
                break;
            case 7:
                $this->setShortCompanyName($value);
                break;
            case 8:
                $this->setPostCode($value);
                break;
            case 9:
                $this->setCity($value);
                break;
            case 10:
                $this->setStreet($value);
                break;
            case 11:
                $this->setStreetNo($value);
                break;
            case 12:
                $this->setPlaceNo($value);
                break;
            case 13:
                $this->setCreatedAt($value);
                break;
            case 14:
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
        $keys = CompanyTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCountryId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPhotoId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setBankName($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setBankAccountNo($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setTaxId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCompanyName($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setShortCompanyName($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setPostCode($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCity($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setStreet($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setStreetNo($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setPlaceNo($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setCreatedAt($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setUpdatedAt($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CompanyTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CompanyTableMap::COL_ID)) $criteria->add(CompanyTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(CompanyTableMap::COL_COUNTRY_ID)) $criteria->add(CompanyTableMap::COL_COUNTRY_ID, $this->country_id);
        if ($this->isColumnModified(CompanyTableMap::COL_PHOTO_ID)) $criteria->add(CompanyTableMap::COL_PHOTO_ID, $this->photo_id);
        if ($this->isColumnModified(CompanyTableMap::COL_BANK_NAME)) $criteria->add(CompanyTableMap::COL_BANK_NAME, $this->bank_name);
        if ($this->isColumnModified(CompanyTableMap::COL_BANK_ACCOUNT_NO)) $criteria->add(CompanyTableMap::COL_BANK_ACCOUNT_NO, $this->bank_account_no);
        if ($this->isColumnModified(CompanyTableMap::COL_TAX_ID)) $criteria->add(CompanyTableMap::COL_TAX_ID, $this->tax_id);
        if ($this->isColumnModified(CompanyTableMap::COL_COMPANY_NAME)) $criteria->add(CompanyTableMap::COL_COMPANY_NAME, $this->company_name);
        if ($this->isColumnModified(CompanyTableMap::COL_SHORT_COMPANY_NAME)) $criteria->add(CompanyTableMap::COL_SHORT_COMPANY_NAME, $this->short_company_name);
        if ($this->isColumnModified(CompanyTableMap::COL_POST_CODE)) $criteria->add(CompanyTableMap::COL_POST_CODE, $this->post_code);
        if ($this->isColumnModified(CompanyTableMap::COL_CITY)) $criteria->add(CompanyTableMap::COL_CITY, $this->city);
        if ($this->isColumnModified(CompanyTableMap::COL_STREET)) $criteria->add(CompanyTableMap::COL_STREET, $this->street);
        if ($this->isColumnModified(CompanyTableMap::COL_STREET_NO)) $criteria->add(CompanyTableMap::COL_STREET_NO, $this->street_no);
        if ($this->isColumnModified(CompanyTableMap::COL_PLACE_NO)) $criteria->add(CompanyTableMap::COL_PLACE_NO, $this->place_no);
        if ($this->isColumnModified(CompanyTableMap::COL_CREATED_AT)) $criteria->add(CompanyTableMap::COL_CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CompanyTableMap::COL_UPDATED_AT)) $criteria->add(CompanyTableMap::COL_UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CompanyTableMap::DATABASE_NAME);
        $criteria->add(CompanyTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Company\Model\ORM\Company (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCountryId($this->getCountryId());
        $copyObj->setPhotoId($this->getPhotoId());
        $copyObj->setBankName($this->getBankName());
        $copyObj->setBankAccountNo($this->getBankAccountNo());
        $copyObj->setTaxId($this->getTaxId());
        $copyObj->setCompanyName($this->getCompanyName());
        $copyObj->setShortCompanyName($this->getShortCompanyName());
        $copyObj->setPostCode($this->getPostCode());
        $copyObj->setCity($this->getCity());
        $copyObj->setStreet($this->getStreet());
        $copyObj->setStreetNo($this->getStreetNo());
        $copyObj->setPlaceNo($this->getPlaceNo());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getControllerPermissions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addControllerPermission($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addShop($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Company\Model\ORM\Company Clone of current object.
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
     * Declares an association between this object and a ChildCountry object.
     *
     * @param                  ChildCountry $v
     * @return                 \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCountry(ChildCountry $v = null)
    {
        if ($v === null) {
            $this->setCountryId(NULL);
        } else {
            $this->setCountryId($v->getId());
        }

        $this->aCountry = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCountry object, it will not be re-added.
        if ($v !== null) {
            $v->addCompany($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCountry object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCountry The associated ChildCountry object.
     * @throws PropelException
     */
    public function getCountry(ConnectionInterface $con = null)
    {
        if ($this->aCountry === null && ($this->country_id !== null)) {
            $this->aCountry = CountryQuery::create()->findPk($this->country_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCountry->addCompanies($this);
             */
        }

        return $this->aCountry;
    }

    /**
     * Declares an association between this object and a ChildFile object.
     *
     * @param                  ChildFile $v
     * @return                 \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     * @throws PropelException
     */
    public function setFile(ChildFile $v = null)
    {
        if ($v === null) {
            $this->setPhotoId(1);
        } else {
            $this->setPhotoId($v->getId());
        }

        $this->aFile = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildFile object, it will not be re-added.
        if ($v !== null) {
            $v->addCompany($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildFile object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildFile The associated ChildFile object.
     * @throws PropelException
     */
    public function getFile(ConnectionInterface $con = null)
    {
        if ($this->aFile === null && ($this->photo_id !== null)) {
            $this->aFile = FileQuery::create()->findPk($this->photo_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aFile->addCompanies($this);
             */
        }

        return $this->aFile;
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
        if ('ControllerPermission' == $relationName) {
            return $this->initControllerPermissions();
        }
        if ('Shop' == $relationName) {
            return $this->initShops();
        }
    }

    /**
     * Clears out the collControllerPermissions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addControllerPermissions()
     */
    public function clearControllerPermissions()
    {
        $this->collControllerPermissions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collControllerPermissions collection loaded partially.
     */
    public function resetPartialControllerPermissions($v = true)
    {
        $this->collControllerPermissionsPartial = $v;
    }

    /**
     * Initializes the collControllerPermissions collection.
     *
     * By default this just sets the collControllerPermissions collection to an empty array (like clearcollControllerPermissions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initControllerPermissions($overrideExisting = true)
    {
        if (null !== $this->collControllerPermissions && !$overrideExisting) {
            return;
        }
        $this->collControllerPermissions = new ObjectCollection();
        $this->collControllerPermissions->setModel('\Gekosale\Plugin\Controller\Model\ORM\ControllerPermission');
    }

    /**
     * Gets an array of ChildControllerPermission objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCompany is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildControllerPermission[] List of ChildControllerPermission objects
     * @throws PropelException
     */
    public function getControllerPermissions($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collControllerPermissionsPartial && !$this->isNew();
        if (null === $this->collControllerPermissions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collControllerPermissions) {
                // return empty collection
                $this->initControllerPermissions();
            } else {
                $collControllerPermissions = ControllerPermissionQuery::create(null, $criteria)
                    ->filterByCompany($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collControllerPermissionsPartial && count($collControllerPermissions)) {
                        $this->initControllerPermissions(false);

                        foreach ($collControllerPermissions as $obj) {
                            if (false == $this->collControllerPermissions->contains($obj)) {
                                $this->collControllerPermissions->append($obj);
                            }
                        }

                        $this->collControllerPermissionsPartial = true;
                    }

                    reset($collControllerPermissions);

                    return $collControllerPermissions;
                }

                if ($partial && $this->collControllerPermissions) {
                    foreach ($this->collControllerPermissions as $obj) {
                        if ($obj->isNew()) {
                            $collControllerPermissions[] = $obj;
                        }
                    }
                }

                $this->collControllerPermissions = $collControllerPermissions;
                $this->collControllerPermissionsPartial = false;
            }
        }

        return $this->collControllerPermissions;
    }

    /**
     * Sets a collection of ControllerPermission objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $controllerPermissions A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCompany The current object (for fluent API support)
     */
    public function setControllerPermissions(Collection $controllerPermissions, ConnectionInterface $con = null)
    {
        $controllerPermissionsToDelete = $this->getControllerPermissions(new Criteria(), $con)->diff($controllerPermissions);

        
        $this->controllerPermissionsScheduledForDeletion = $controllerPermissionsToDelete;

        foreach ($controllerPermissionsToDelete as $controllerPermissionRemoved) {
            $controllerPermissionRemoved->setCompany(null);
        }

        $this->collControllerPermissions = null;
        foreach ($controllerPermissions as $controllerPermission) {
            $this->addControllerPermission($controllerPermission);
        }

        $this->collControllerPermissions = $controllerPermissions;
        $this->collControllerPermissionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ControllerPermission objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ControllerPermission objects.
     * @throws PropelException
     */
    public function countControllerPermissions(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collControllerPermissionsPartial && !$this->isNew();
        if (null === $this->collControllerPermissions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collControllerPermissions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getControllerPermissions());
            }

            $query = ControllerPermissionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCompany($this)
                ->count($con);
        }

        return count($this->collControllerPermissions);
    }

    /**
     * Method called to associate a ChildControllerPermission object to this object
     * through the ChildControllerPermission foreign key attribute.
     *
     * @param    ChildControllerPermission $l ChildControllerPermission
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function addControllerPermission(ChildControllerPermission $l)
    {
        if ($this->collControllerPermissions === null) {
            $this->initControllerPermissions();
            $this->collControllerPermissionsPartial = true;
        }

        if (!in_array($l, $this->collControllerPermissions->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddControllerPermission($l);
        }

        return $this;
    }

    /**
     * @param ControllerPermission $controllerPermission The controllerPermission object to add.
     */
    protected function doAddControllerPermission($controllerPermission)
    {
        $this->collControllerPermissions[]= $controllerPermission;
        $controllerPermission->setCompany($this);
    }

    /**
     * @param  ControllerPermission $controllerPermission The controllerPermission object to remove.
     * @return ChildCompany The current object (for fluent API support)
     */
    public function removeControllerPermission($controllerPermission)
    {
        if ($this->getControllerPermissions()->contains($controllerPermission)) {
            $this->collControllerPermissions->remove($this->collControllerPermissions->search($controllerPermission));
            if (null === $this->controllerPermissionsScheduledForDeletion) {
                $this->controllerPermissionsScheduledForDeletion = clone $this->collControllerPermissions;
                $this->controllerPermissionsScheduledForDeletion->clear();
            }
            $this->controllerPermissionsScheduledForDeletion[]= $controllerPermission;
            $controllerPermission->setCompany(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Company is new, it will return
     * an empty collection; or if this Company has previously
     * been saved, it will retrieve related ControllerPermissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Company.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildControllerPermission[] List of ChildControllerPermission objects
     */
    public function getControllerPermissionsJoinController($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ControllerPermissionQuery::create(null, $criteria);
        $query->joinWith('Controller', $joinBehavior);

        return $this->getControllerPermissions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Company is new, it will return
     * an empty collection; or if this Company has previously
     * been saved, it will retrieve related ControllerPermissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Company.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildControllerPermission[] List of ChildControllerPermission objects
     */
    public function getControllerPermissionsJoinUserGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ControllerPermissionQuery::create(null, $criteria);
        $query->joinWith('UserGroup', $joinBehavior);

        return $this->getControllerPermissions($query, $con);
    }

    /**
     * Clears out the collShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addShops()
     */
    public function clearShops()
    {
        $this->collShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collShops collection loaded partially.
     */
    public function resetPartialShops($v = true)
    {
        $this->collShopsPartial = $v;
    }

    /**
     * Initializes the collShops collection.
     *
     * By default this just sets the collShops collection to an empty array (like clearcollShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initShops($overrideExisting = true)
    {
        if (null !== $this->collShops && !$overrideExisting) {
            return;
        }
        $this->collShops = new ObjectCollection();
        $this->collShops->setModel('\Gekosale\Plugin\Shop\Model\ORM\Shop');
    }

    /**
     * Gets an array of ChildShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCompany is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildShop[] List of ChildShop objects
     * @throws PropelException
     */
    public function getShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collShopsPartial && !$this->isNew();
        if (null === $this->collShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collShops) {
                // return empty collection
                $this->initShops();
            } else {
                $collShops = ShopQuery::create(null, $criteria)
                    ->filterByCompany($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collShopsPartial && count($collShops)) {
                        $this->initShops(false);

                        foreach ($collShops as $obj) {
                            if (false == $this->collShops->contains($obj)) {
                                $this->collShops->append($obj);
                            }
                        }

                        $this->collShopsPartial = true;
                    }

                    reset($collShops);

                    return $collShops;
                }

                if ($partial && $this->collShops) {
                    foreach ($this->collShops as $obj) {
                        if ($obj->isNew()) {
                            $collShops[] = $obj;
                        }
                    }
                }

                $this->collShops = $collShops;
                $this->collShopsPartial = false;
            }
        }

        return $this->collShops;
    }

    /**
     * Sets a collection of Shop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $shops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCompany The current object (for fluent API support)
     */
    public function setShops(Collection $shops, ConnectionInterface $con = null)
    {
        $shopsToDelete = $this->getShops(new Criteria(), $con)->diff($shops);

        
        $this->shopsScheduledForDeletion = $shopsToDelete;

        foreach ($shopsToDelete as $shopRemoved) {
            $shopRemoved->setCompany(null);
        }

        $this->collShops = null;
        foreach ($shops as $shop) {
            $this->addShop($shop);
        }

        $this->collShops = $shops;
        $this->collShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Shop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Shop objects.
     * @throws PropelException
     */
    public function countShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collShopsPartial && !$this->isNew();
        if (null === $this->collShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getShops());
            }

            $query = ShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCompany($this)
                ->count($con);
        }

        return count($this->collShops);
    }

    /**
     * Method called to associate a ChildShop object to this object
     * through the ChildShop foreign key attribute.
     *
     * @param    ChildShop $l ChildShop
     * @return   \Gekosale\Plugin\Company\Model\ORM\Company The current object (for fluent API support)
     */
    public function addShop(ChildShop $l)
    {
        if ($this->collShops === null) {
            $this->initShops();
            $this->collShopsPartial = true;
        }

        if (!in_array($l, $this->collShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddShop($l);
        }

        return $this;
    }

    /**
     * @param Shop $shop The shop object to add.
     */
    protected function doAddShop($shop)
    {
        $this->collShops[]= $shop;
        $shop->setCompany($this);
    }

    /**
     * @param  Shop $shop The shop object to remove.
     * @return ChildCompany The current object (for fluent API support)
     */
    public function removeShop($shop)
    {
        if ($this->getShops()->contains($shop)) {
            $this->collShops->remove($this->collShops->search($shop));
            if (null === $this->shopsScheduledForDeletion) {
                $this->shopsScheduledForDeletion = clone $this->collShops;
                $this->shopsScheduledForDeletion->clear();
            }
            $this->shopsScheduledForDeletion[]= clone $shop;
            $shop->setCompany(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Company is new, it will return
     * an empty collection; or if this Company has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Company.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinContact($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Contact', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Company is new, it will return
     * an empty collection; or if this Company has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Company.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinCurrency($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Currency', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Company is new, it will return
     * an empty collection; or if this Company has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Company.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinVat($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Vat', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Company is new, it will return
     * an empty collection; or if this Company has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Company.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinOrderStatusGroups($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('OrderStatusGroups', $joinBehavior);

        return $this->getShops($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->country_id = null;
        $this->photo_id = null;
        $this->bank_name = null;
        $this->bank_account_no = null;
        $this->tax_id = null;
        $this->company_name = null;
        $this->short_company_name = null;
        $this->post_code = null;
        $this->city = null;
        $this->street = null;
        $this->street_no = null;
        $this->place_no = null;
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
            if ($this->collControllerPermissions) {
                foreach ($this->collControllerPermissions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collShops) {
                foreach ($this->collShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collControllerPermissions = null;
        $this->collShops = null;
        $this->aCountry = null;
        $this->aFile = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CompanyTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior
    
    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildCompany The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[CompanyTableMap::COL_UPDATED_AT] = true;
    
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
