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

    protected $errorMsg;
    protected $container;
    protected $options;
    protected $language;
    protected $jsFunction;
    protected static $_nextId = 0;

    public function __construct($errorMsg, $options, ContainerInterface $container)
    {
        parent::__construct($errorMsg);

        $this->errorMsg   = $errorMsg;
        $this->container  = $container;
        $this->options    = $options;
        $this->id         = self::$_nextId++;
        $this->jsFunction = 'CheckUniqueness_' . $this->id;
        $this->pdo        = $this->container->get('database_manager')->getConnection()->getPdo();

        $this->container->get('xajax_manager')->registerFunction([
            $this->jsFunction,
            $this,
            'doAjaxCheck'
        ]);
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
        $this->language = $language;
    }

    public function checkValue($value)
    {
        $sql = "SELECT
				  COUNT(*) AS items_count
			    FROM
				  {$this->options['table']}
			    WHERE
				  {$this->options['column']} = :value
				  AND language_id = :language
		";
        if (isset($this->options['exclude']) && is_array($this->options['exclude'])) {
            if (!is_array($this->options['exclude']['values'])) {
                $this->options['exclude']['values'] = [$this->options['exclude']['values']];
            }

            $values = array_filter($this->options['exclude']['values']);

            if (count($values)) {
                $excludedValues = implode(', ', $this->options['exclude']['values']);
                $sql .= " AND NOT {$this->options['exclude']['column']} IN ({$excludedValues})";
            }
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('value', $value);
        $stmt->bindValue('language', $this->language);
        $stmt->execute();
        $rs = $stmt->fetch();

        return ($rs['items_count'] == 0);
    }

    public function render()
    {
        $errorMsg = addslashes($this->_errorMsg);

        return "{sType: '{$this->getType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: xajax_{$this->jsFunction}}";
    }

}
