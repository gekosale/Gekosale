<?php

namespace Gekosale\Plugin\Client\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Client\Model\ORM\Client as ChildClient;
use Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery as ChildClientAddressQuery;
use Gekosale\Plugin\Client\Model\ORM\ClientQuery as ChildClientQuery;
use Gekosale\Plugin\Client\Model\ORM\Map\ClientAddressTableMap;
use Gekosale\Plugin\Country\Model\ORM\Country as ChildCountry;
use Gekosale\Plugin\Country\Model\ORM\CountryQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;

abstract class ClientAddress implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Map\\ClientAddressTableMap';


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
     * The value for the street field.
     * @var        resource
     */
    protected $street;

    /**
     * The value for the street_no field.
     * @var        resource
     */
    protected $street_no;

    /**
     * The value for the place_no field.
     * @var        resource
     */
    protected $place_no;

    /**
     * The value for the post_code field.
     * @var        resource
     */
    protected $post_code;

    /**
     * The value for the company_name field.
     * @var        resource
     */
    protected $company_name;

    /**
     * The value for the firstname field.
     * @var        resource
     */
    protected $firstname;

    /**
     * The value for the surname field.
     * @var        resource
     */
    protected $surname;

    /**
     * The value for the client_id field.
     * @var        int
     */
    protected $client_id;

    /**
     * The value for the regon field.
     * @var        resource
     */
    protected $regon;

    /**
     * The value for the tax_id field.
     * @var        resource
     */
    protected $tax_id;

    /**
     * The value for the city field.
     * @var        resource
     */
    protected $city;

    /**
     * The value for the is_main field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $is_main;

    /**
     * The value for the country_id field.
     * @var        int
     */
    protected $country_id;

    /**
     * The value for the client_type field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $client_type;

    /**
     * @var        Client
     */
    protected $aClient;

    /**
     * @var        Country
     */
    protected $aCountry;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->is_main = 1;
        $this->client_type = 1;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Client\Model\ORM\Base\ClientAddress object.
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
     * Compares this with another <code>ClientAddress</code> instance.  If
     * <code>obj</code> is an instance of <code>ClientAddress</code>, delegates to
     * <code>equals(ClientAddress)</code>.  Otherwise, returns <code>false</code>.
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
     * @return ClientAddress The current object, for fluid interface
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
     * @return ClientAddress The current object, for fluid interface
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
     * Get the [street] column value.
     * 
     * @return   resource
     */
    public function getStreet()
    {

        return $this->street;
    }

    /**
     * Get the [street_no] column value.
     * 
     * @return   resource
     */
    public function getStreetNo()
    {

        return $this->street_no;
    }

    /**
     * Get the [place_no] column value.
     * 
     * @return   resource
     */
    public function getPlaceNo()
    {

        return $this->place_no;
    }

    /**
     * Get the [post_code] column value.
     * 
     * @return   resource
     */
    public function getPostCode()
    {

        return $this->post_code;
    }

    /**
     * Get the [company_name] column value.
     * 
     * @return   resource
     */
    public function getCompanyName()
    {

        return $this->company_name;
    }

    /**
     * Get the [firstname] column value.
     * 
     * @return   resource
     */
    public function getFirstname()
    {

        return $this->firstname;
    }

    /**
     * Get the [surname] column value.
     * 
     * @return   resource
     */
    public function getSurname()
    {

        return $this->surname;
    }

    /**
     * Get the [client_id] column value.
     * 
     * @return   int
     */
    public function getClientId()
    {

        return $this->client_id;
    }

    /**
     * Get the [regon] column value.
     * 
     * @return   resource
     */
    public function getRegon()
    {

        return $this->regon;
    }

    /**
     * Get the [tax_id] column value.
     * 
     * @return   resource
     */
    public function getTaxId()
    {

        return $this->tax_id;
    }

    /**
     * Get the [city] column value.
     * 
     * @return   resource
     */
    public function getCity()
    {

        return $this->city;
    }

    /**
     * Get the [is_main] column value.
     * 
     * @return   int
     */
    public function getIsMain()
    {

        return $this->is_main;
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
     * Get the [client_type] column value.
     * 
     * @return   int
     */
    public function getClientType()
    {

        return $this->client_type;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ClientAddressTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [street] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setStreet($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->street = fopen('php://memory', 'r+');
            fwrite($this->street, $v);
            rewind($this->street);
        } else { // it's already a stream
            $this->street = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_STREET] = true;


        return $this;
    } // setStreet()

    /**
     * Set the value of [street_no] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setStreetNo($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->street_no = fopen('php://memory', 'r+');
            fwrite($this->street_no, $v);
            rewind($this->street_no);
        } else { // it's already a stream
            $this->street_no = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_STREET_NO] = true;


        return $this;
    } // setStreetNo()

    /**
     * Set the value of [place_no] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setPlaceNo($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->place_no = fopen('php://memory', 'r+');
            fwrite($this->place_no, $v);
            rewind($this->place_no);
        } else { // it's already a stream
            $this->place_no = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_PLACE_NO] = true;


        return $this;
    } // setPlaceNo()

    /**
     * Set the value of [post_code] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setPostCode($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->post_code = fopen('php://memory', 'r+');
            fwrite($this->post_code, $v);
            rewind($this->post_code);
        } else { // it's already a stream
            $this->post_code = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_POST_CODE] = true;


        return $this;
    } // setPostCode()

    /**
     * Set the value of [company_name] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setCompanyName($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->company_name = fopen('php://memory', 'r+');
            fwrite($this->company_name, $v);
            rewind($this->company_name);
        } else { // it's already a stream
            $this->company_name = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_COMPANY_NAME] = true;


        return $this;
    } // setCompanyName()

    /**
     * Set the value of [firstname] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setFirstname($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->firstname = fopen('php://memory', 'r+');
            fwrite($this->firstname, $v);
            rewind($this->firstname);
        } else { // it's already a stream
            $this->firstname = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_FIRSTNAME] = true;


        return $this;
    } // setFirstname()

    /**
     * Set the value of [surname] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setSurname($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->surname = fopen('php://memory', 'r+');
            fwrite($this->surname, $v);
            rewind($this->surname);
        } else { // it's already a stream
            $this->surname = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_SURNAME] = true;


        return $this;
    } // setSurname()

    /**
     * Set the value of [client_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setClientId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->client_id !== $v) {
            $this->client_id = $v;
            $this->modifiedColumns[ClientAddressTableMap::COL_CLIENT_ID] = true;
        }

        if ($this->aClient !== null && $this->aClient->getId() !== $v) {
            $this->aClient = null;
        }


        return $this;
    } // setClientId()

    /**
     * Set the value of [regon] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setRegon($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->regon = fopen('php://memory', 'r+');
            fwrite($this->regon, $v);
            rewind($this->regon);
        } else { // it's already a stream
            $this->regon = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_REGON] = true;


        return $this;
    } // setRegon()

    /**
     * Set the value of [tax_id] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setTaxId($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->tax_id = fopen('php://memory', 'r+');
            fwrite($this->tax_id, $v);
            rewind($this->tax_id);
        } else { // it's already a stream
            $this->tax_id = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_TAX_ID] = true;


        return $this;
    } // setTaxId()

    /**
     * Set the value of [city] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setCity($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->city = fopen('php://memory', 'r+');
            fwrite($this->city, $v);
            rewind($this->city);
        } else { // it's already a stream
            $this->city = $v;
        }
        $this->modifiedColumns[ClientAddressTableMap::COL_CITY] = true;


        return $this;
    } // setCity()

    /**
     * Set the value of [is_main] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setIsMain($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->is_main !== $v) {
            $this->is_main = $v;
            $this->modifiedColumns[ClientAddressTableMap::COL_IS_MAIN] = true;
        }


        return $this;
    } // setIsMain()

    /**
     * Set the value of [country_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setCountryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->country_id !== $v) {
            $this->country_id = $v;
            $this->modifiedColumns[ClientAddressTableMap::COL_COUNTRY_ID] = true;
        }

        if ($this->aCountry !== null && $this->aCountry->getId() !== $v) {
            $this->aCountry = null;
        }


        return $this;
    } // setCountryId()

    /**
     * Set the value of [client_type] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     */
    public function setClientType($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->client_type !== $v) {
            $this->client_type = $v;
            $this->modifiedColumns[ClientAddressTableMap::COL_CLIENT_TYPE] = true;
        }


        return $this;
    } // setClientType()

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
            if ($this->is_main !== 1) {
                return false;
            }

            if ($this->client_type !== 1) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ClientAddressTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ClientAddressTableMap::translateFieldName('Street', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->street = fopen('php://memory', 'r+');
                fwrite($this->street, $col);
                rewind($this->street);
            } else {
                $this->street = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ClientAddressTableMap::translateFieldName('StreetNo', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->street_no = fopen('php://memory', 'r+');
                fwrite($this->street_no, $col);
                rewind($this->street_no);
            } else {
                $this->street_no = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ClientAddressTableMap::translateFieldName('PlaceNo', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->place_no = fopen('php://memory', 'r+');
                fwrite($this->place_no, $col);
                rewind($this->place_no);
            } else {
                $this->place_no = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ClientAddressTableMap::translateFieldName('PostCode', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->post_code = fopen('php://memory', 'r+');
                fwrite($this->post_code, $col);
                rewind($this->post_code);
            } else {
                $this->post_code = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ClientAddressTableMap::translateFieldName('CompanyName', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->company_name = fopen('php://memory', 'r+');
                fwrite($this->company_name, $col);
                rewind($this->company_name);
            } else {
                $this->company_name = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ClientAddressTableMap::translateFieldName('Firstname', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->firstname = fopen('php://memory', 'r+');
                fwrite($this->firstname, $col);
                rewind($this->firstname);
            } else {
                $this->firstname = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : ClientAddressTableMap::translateFieldName('Surname', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->surname = fopen('php://memory', 'r+');
                fwrite($this->surname, $col);
                rewind($this->surname);
            } else {
                $this->surname = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : ClientAddressTableMap::translateFieldName('ClientId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->client_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : ClientAddressTableMap::translateFieldName('Regon', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->regon = fopen('php://memory', 'r+');
                fwrite($this->regon, $col);
                rewind($this->regon);
            } else {
                $this->regon = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : ClientAddressTableMap::translateFieldName('TaxId', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->tax_id = fopen('php://memory', 'r+');
                fwrite($this->tax_id, $col);
                rewind($this->tax_id);
            } else {
                $this->tax_id = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : ClientAddressTableMap::translateFieldName('City', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->city = fopen('php://memory', 'r+');
                fwrite($this->city, $col);
                rewind($this->city);
            } else {
                $this->city = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : ClientAddressTableMap::translateFieldName('IsMain', TableMap::TYPE_PHPNAME, $indexType)];
            $this->is_main = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : ClientAddressTableMap::translateFieldName('CountryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : ClientAddressTableMap::translateFieldName('ClientType', TableMap::TYPE_PHPNAME, $indexType)];
            $this->client_type = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = ClientAddressTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Client\Model\ORM\ClientAddress object", 0, $e);
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
        if ($this->aClient !== null && $this->client_id !== $this->aClient->getId()) {
            $this->aClient = null;
        }
        if ($this->aCountry !== null && $this->country_id !== $this->aCountry->getId()) {
            $this->aCountry = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(ClientAddressTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildClientAddressQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aClient = null;
            $this->aCountry = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see ClientAddress::setDeleted()
     * @see ClientAddress::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientAddressTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildClientAddressQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientAddressTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ClientAddressTableMap::addInstanceToPool($this);
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

            if ($this->aClient !== null) {
                if ($this->aClient->isModified() || $this->aClient->isNew()) {
                    $affectedRows += $this->aClient->save($con);
                }
                $this->setClient($this->aClient);
            }

            if ($this->aCountry !== null) {
                if ($this->aCountry->isModified() || $this->aCountry->isNew()) {
                    $affectedRows += $this->aCountry->save($con);
                }
                $this->setCountry($this->aCountry);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                // Rewind the street LOB column, since PDO does not rewind after inserting value.
                if ($this->street !== null && is_resource($this->street)) {
                    rewind($this->street);
                }

                // Rewind the street_no LOB column, since PDO does not rewind after inserting value.
                if ($this->street_no !== null && is_resource($this->street_no)) {
                    rewind($this->street_no);
                }

                // Rewind the place_no LOB column, since PDO does not rewind after inserting value.
                if ($this->place_no !== null && is_resource($this->place_no)) {
                    rewind($this->place_no);
                }

                // Rewind the post_code LOB column, since PDO does not rewind after inserting value.
                if ($this->post_code !== null && is_resource($this->post_code)) {
                    rewind($this->post_code);
                }

                // Rewind the company_name LOB column, since PDO does not rewind after inserting value.
                if ($this->company_name !== null && is_resource($this->company_name)) {
                    rewind($this->company_name);
                }

                // Rewind the firstname LOB column, since PDO does not rewind after inserting value.
                if ($this->firstname !== null && is_resource($this->firstname)) {
                    rewind($this->firstname);
                }

                // Rewind the surname LOB column, since PDO does not rewind after inserting value.
                if ($this->surname !== null && is_resource($this->surname)) {
                    rewind($this->surname);
                }

                // Rewind the regon LOB column, since PDO does not rewind after inserting value.
                if ($this->regon !== null && is_resource($this->regon)) {
                    rewind($this->regon);
                }

                // Rewind the tax_id LOB column, since PDO does not rewind after inserting value.
                if ($this->tax_id !== null && is_resource($this->tax_id)) {
                    rewind($this->tax_id);
                }

                // Rewind the city LOB column, since PDO does not rewind after inserting value.
                if ($this->city !== null && is_resource($this->city)) {
                    rewind($this->city);
                }

                $this->resetModified();
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

        $this->modifiedColumns[ClientAddressTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ClientAddressTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ClientAddressTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_STREET)) {
            $modifiedColumns[':p' . $index++]  = 'STREET';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_STREET_NO)) {
            $modifiedColumns[':p' . $index++]  = 'STREET_NO';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_PLACE_NO)) {
            $modifiedColumns[':p' . $index++]  = 'PLACE_NO';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_POST_CODE)) {
            $modifiedColumns[':p' . $index++]  = 'POST_CODE';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_COMPANY_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'COMPANY_NAME';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_FIRSTNAME)) {
            $modifiedColumns[':p' . $index++]  = 'FIRSTNAME';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_SURNAME)) {
            $modifiedColumns[':p' . $index++]  = 'SURNAME';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_CLIENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CLIENT_ID';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_REGON)) {
            $modifiedColumns[':p' . $index++]  = 'REGON';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_TAX_ID)) {
            $modifiedColumns[':p' . $index++]  = 'TAX_ID';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_CITY)) {
            $modifiedColumns[':p' . $index++]  = 'CITY';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_IS_MAIN)) {
            $modifiedColumns[':p' . $index++]  = 'IS_MAIN';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_COUNTRY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'COUNTRY_ID';
        }
        if ($this->isColumnModified(ClientAddressTableMap::COL_CLIENT_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'CLIENT_TYPE';
        }

        $sql = sprintf(
            'INSERT INTO client_address (%s) VALUES (%s)',
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
                    case 'STREET':                        
                        if (is_resource($this->street)) {
                            rewind($this->street);
                        }
                        $stmt->bindValue($identifier, $this->street, PDO::PARAM_LOB);
                        break;
                    case 'STREET_NO':                        
                        if (is_resource($this->street_no)) {
                            rewind($this->street_no);
                        }
                        $stmt->bindValue($identifier, $this->street_no, PDO::PARAM_LOB);
                        break;
                    case 'PLACE_NO':                        
                        if (is_resource($this->place_no)) {
                            rewind($this->place_no);
                        }
                        $stmt->bindValue($identifier, $this->place_no, PDO::PARAM_LOB);
                        break;
                    case 'POST_CODE':                        
                        if (is_resource($this->post_code)) {
                            rewind($this->post_code);
                        }
                        $stmt->bindValue($identifier, $this->post_code, PDO::PARAM_LOB);
                        break;
                    case 'COMPANY_NAME':                        
                        if (is_resource($this->company_name)) {
                            rewind($this->company_name);
                        }
                        $stmt->bindValue($identifier, $this->company_name, PDO::PARAM_LOB);
                        break;
                    case 'FIRSTNAME':                        
                        if (is_resource($this->firstname)) {
                            rewind($this->firstname);
                        }
                        $stmt->bindValue($identifier, $this->firstname, PDO::PARAM_LOB);
                        break;
                    case 'SURNAME':                        
                        if (is_resource($this->surname)) {
                            rewind($this->surname);
                        }
                        $stmt->bindValue($identifier, $this->surname, PDO::PARAM_LOB);
                        break;
                    case 'CLIENT_ID':                        
                        $stmt->bindValue($identifier, $this->client_id, PDO::PARAM_INT);
                        break;
                    case 'REGON':                        
                        if (is_resource($this->regon)) {
                            rewind($this->regon);
                        }
                        $stmt->bindValue($identifier, $this->regon, PDO::PARAM_LOB);
                        break;
                    case 'TAX_ID':                        
                        if (is_resource($this->tax_id)) {
                            rewind($this->tax_id);
                        }
                        $stmt->bindValue($identifier, $this->tax_id, PDO::PARAM_LOB);
                        break;
                    case 'CITY':                        
                        if (is_resource($this->city)) {
                            rewind($this->city);
                        }
                        $stmt->bindValue($identifier, $this->city, PDO::PARAM_LOB);
                        break;
                    case 'IS_MAIN':                        
                        $stmt->bindValue($identifier, $this->is_main, PDO::PARAM_INT);
                        break;
                    case 'COUNTRY_ID':                        
                        $stmt->bindValue($identifier, $this->country_id, PDO::PARAM_INT);
                        break;
                    case 'CLIENT_TYPE':                        
                        $stmt->bindValue($identifier, $this->client_type, PDO::PARAM_INT);
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
        $pos = ClientAddressTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getStreet();
                break;
            case 2:
                return $this->getStreetNo();
                break;
            case 3:
                return $this->getPlaceNo();
                break;
            case 4:
                return $this->getPostCode();
                break;
            case 5:
                return $this->getCompanyName();
                break;
            case 6:
                return $this->getFirstname();
                break;
            case 7:
                return $this->getSurname();
                break;
            case 8:
                return $this->getClientId();
                break;
            case 9:
                return $this->getRegon();
                break;
            case 10:
                return $this->getTaxId();
                break;
            case 11:
                return $this->getCity();
                break;
            case 12:
                return $this->getIsMain();
                break;
            case 13:
                return $this->getCountryId();
                break;
            case 14:
                return $this->getClientType();
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
        if (isset($alreadyDumpedObjects['ClientAddress'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ClientAddress'][$this->getPrimaryKey()] = true;
        $keys = ClientAddressTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getStreet(),
            $keys[2] => $this->getStreetNo(),
            $keys[3] => $this->getPlaceNo(),
            $keys[4] => $this->getPostCode(),
            $keys[5] => $this->getCompanyName(),
            $keys[6] => $this->getFirstname(),
            $keys[7] => $this->getSurname(),
            $keys[8] => $this->getClientId(),
            $keys[9] => $this->getRegon(),
            $keys[10] => $this->getTaxId(),
            $keys[11] => $this->getCity(),
            $keys[12] => $this->getIsMain(),
            $keys[13] => $this->getCountryId(),
            $keys[14] => $this->getClientType(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aClient) {
                $result['Client'] = $this->aClient->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCountry) {
                $result['Country'] = $this->aCountry->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = ClientAddressTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setStreet($value);
                break;
            case 2:
                $this->setStreetNo($value);
                break;
            case 3:
                $this->setPlaceNo($value);
                break;
            case 4:
                $this->setPostCode($value);
                break;
            case 5:
                $this->setCompanyName($value);
                break;
            case 6:
                $this->setFirstname($value);
                break;
            case 7:
                $this->setSurname($value);
                break;
            case 8:
                $this->setClientId($value);
                break;
            case 9:
                $this->setRegon($value);
                break;
            case 10:
                $this->setTaxId($value);
                break;
            case 11:
                $this->setCity($value);
                break;
            case 12:
                $this->setIsMain($value);
                break;
            case 13:
                $this->setCountryId($value);
                break;
            case 14:
                $this->setClientType($value);
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
        $keys = ClientAddressTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setStreet($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setStreetNo($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPlaceNo($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPostCode($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCompanyName($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setFirstname($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setSurname($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setClientId($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setRegon($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setTaxId($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setCity($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setIsMain($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setCountryId($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setClientType($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ClientAddressTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ClientAddressTableMap::COL_ID)) $criteria->add(ClientAddressTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(ClientAddressTableMap::COL_STREET)) $criteria->add(ClientAddressTableMap::COL_STREET, $this->street);
        if ($this->isColumnModified(ClientAddressTableMap::COL_STREET_NO)) $criteria->add(ClientAddressTableMap::COL_STREET_NO, $this->street_no);
        if ($this->isColumnModified(ClientAddressTableMap::COL_PLACE_NO)) $criteria->add(ClientAddressTableMap::COL_PLACE_NO, $this->place_no);
        if ($this->isColumnModified(ClientAddressTableMap::COL_POST_CODE)) $criteria->add(ClientAddressTableMap::COL_POST_CODE, $this->post_code);
        if ($this->isColumnModified(ClientAddressTableMap::COL_COMPANY_NAME)) $criteria->add(ClientAddressTableMap::COL_COMPANY_NAME, $this->company_name);
        if ($this->isColumnModified(ClientAddressTableMap::COL_FIRSTNAME)) $criteria->add(ClientAddressTableMap::COL_FIRSTNAME, $this->firstname);
        if ($this->isColumnModified(ClientAddressTableMap::COL_SURNAME)) $criteria->add(ClientAddressTableMap::COL_SURNAME, $this->surname);
        if ($this->isColumnModified(ClientAddressTableMap::COL_CLIENT_ID)) $criteria->add(ClientAddressTableMap::COL_CLIENT_ID, $this->client_id);
        if ($this->isColumnModified(ClientAddressTableMap::COL_REGON)) $criteria->add(ClientAddressTableMap::COL_REGON, $this->regon);
        if ($this->isColumnModified(ClientAddressTableMap::COL_TAX_ID)) $criteria->add(ClientAddressTableMap::COL_TAX_ID, $this->tax_id);
        if ($this->isColumnModified(ClientAddressTableMap::COL_CITY)) $criteria->add(ClientAddressTableMap::COL_CITY, $this->city);
        if ($this->isColumnModified(ClientAddressTableMap::COL_IS_MAIN)) $criteria->add(ClientAddressTableMap::COL_IS_MAIN, $this->is_main);
        if ($this->isColumnModified(ClientAddressTableMap::COL_COUNTRY_ID)) $criteria->add(ClientAddressTableMap::COL_COUNTRY_ID, $this->country_id);
        if ($this->isColumnModified(ClientAddressTableMap::COL_CLIENT_TYPE)) $criteria->add(ClientAddressTableMap::COL_CLIENT_TYPE, $this->client_type);

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
        $criteria = new Criteria(ClientAddressTableMap::DATABASE_NAME);
        $criteria->add(ClientAddressTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Client\Model\ORM\ClientAddress (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setStreet($this->getStreet());
        $copyObj->setStreetNo($this->getStreetNo());
        $copyObj->setPlaceNo($this->getPlaceNo());
        $copyObj->setPostCode($this->getPostCode());
        $copyObj->setCompanyName($this->getCompanyName());
        $copyObj->setFirstname($this->getFirstname());
        $copyObj->setSurname($this->getSurname());
        $copyObj->setClientId($this->getClientId());
        $copyObj->setRegon($this->getRegon());
        $copyObj->setTaxId($this->getTaxId());
        $copyObj->setCity($this->getCity());
        $copyObj->setIsMain($this->getIsMain());
        $copyObj->setCountryId($this->getCountryId());
        $copyObj->setClientType($this->getClientType());
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
     * @return                 \Gekosale\Plugin\Client\Model\ORM\ClientAddress Clone of current object.
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
     * Declares an association between this object and a ChildClient object.
     *
     * @param                  ChildClient $v
     * @return                 \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
     * @throws PropelException
     */
    public function setClient(ChildClient $v = null)
    {
        if ($v === null) {
            $this->setClientId(NULL);
        } else {
            $this->setClientId($v->getId());
        }

        $this->aClient = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildClient object, it will not be re-added.
        if ($v !== null) {
            $v->addClientAddress($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildClient object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildClient The associated ChildClient object.
     * @throws PropelException
     */
    public function getClient(ConnectionInterface $con = null)
    {
        if ($this->aClient === null && ($this->client_id !== null)) {
            $this->aClient = ChildClientQuery::create()->findPk($this->client_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aClient->addClientAddresses($this);
             */
        }

        return $this->aClient;
    }

    /**
     * Declares an association between this object and a ChildCountry object.
     *
     * @param                  ChildCountry $v
     * @return                 \Gekosale\Plugin\Client\Model\ORM\ClientAddress The current object (for fluent API support)
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
            $v->addClientAddress($this);
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
                $this->aCountry->addClientAddresses($this);
             */
        }

        return $this->aCountry;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->street = null;
        $this->street_no = null;
        $this->place_no = null;
        $this->post_code = null;
        $this->company_name = null;
        $this->firstname = null;
        $this->surname = null;
        $this->client_id = null;
        $this->regon = null;
        $this->tax_id = null;
        $this->city = null;
        $this->is_main = null;
        $this->country_id = null;
        $this->client_type = null;
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
        } // if ($deep)

        $this->aClient = null;
        $this->aCountry = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ClientAddressTableMap::DEFAULT_STRING_FORMAT);
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
