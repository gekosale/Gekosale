<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace FormEngine\Rules;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Unique extends \FormEngine\Rule
{
    protected $container;
    protected $_table;
    protected $_column;
    protected $_id;
    protected $_exclude;
    protected $_jsFunction;
    protected static $_nextId = 0;

    public function __construct($container, $options)
    {

        parent::__construct($errorMsg);

        $this->_table      = $table;
        $this->_column     = $column;
        $this->_exclude    = $exclude;
        $this->_id         = self::$_nextId++;
        $this->_jsFunction = 'CheckUniqueness_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunction,
            $this,
            'doAjaxCheck'
        ));
    }

    public function doAjaxCheck($request)
    {
        return Array(
            'unique' => $this->_Check($request['value'])
        );
    }

    protected function _Check($value)
    {
        $sql = "
			SELECT
				COUNT(*) AS items_count
			FROM
				{$this->_table}
			WHERE
				{$this->_column} = :value
		";
        if ($this->_exclude and is_array($this->_exclude)) {
            if (!is_array($this->_exclude['values'])) {
                $this->_exclude['values'] = Array(
                    $this->_exclude['values']
                );
            }
            $excludedValues = implode(', ', $this->_exclude['values']);
            $sql .= "AND NOT {$this->_exclude['column']} IN ({$excludedValues})";
        }
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('value', $value);
        try {
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs['items_count'] == 0) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception('Error while executing sql query: ' . $e->getMessage());
        }

        return false;
    }

    public function Render()
    {
        $errorMsg = addslashes($this->_errorMsg);

        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: xajax_{$this->_jsFunction}}";
    }
}
