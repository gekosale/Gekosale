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

namespace Gekosale\Core\Form\Rules;

use Gekosale\Core\Rules\RuleInterface;
use Gekosale\Core\Form\Rule;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LanguageUnique
 *
 * @package Gekosale\Core\Form\Rules
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageUnique extends Rule implements RuleInterface
{

    protected $_table;
    protected $_column;
    protected $_id;
    protected $_exclude;
    protected $_jsFunction;
    protected $_valueProcessFunction;
    protected $_language;

    protected static $_nextId = 0;

    public function __construct(ContainerInterface $container, $options)
    {
        parent::__construct($errorMsg);
        $this->_table                = $table;
        $this->_column               = $column;
        $this->_exclude              = $exclude;
        $this->_id                   = self::$_nextId++;
        $this->_valueProcessFunction = $valueProcessFunction;
        $this->_jsFunction           = 'CheckUniqueness_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunction,
            $this,
            'doAjaxCheck'
        ));
        $this->_language = '0';
    }

    public function doAjaxCheck($request)
    {
        $this->setLanguage($request['language']);

        return Array(
            'unique' => $this->checkValue($request['value'])
        );
    }

    public function setLanguage($language)
    {
        $this->_language = $language;
    }

    protected function checkValue($value)
    {
        if ($this->_valueProcessFunction) {
            $f     = $this->_valueProcessFunction;
            $value = $f($value);
        }
        $sql = "
			SELECT
				COUNT(*) AS items_count
			FROM
				{$this->_table}
			WHERE
				{$this->_column} = :value
				AND languageid = :language
		";
        if ($this->_exclude && is_array($this->_exclude)) {
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
        $stmt->bindValue('language', $this->_language);
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

    public function render()
    {
        $errorMsg = addslashes($this->_errorMsg);

        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: xajax_{$this->_jsFunction}}";
    }

}
