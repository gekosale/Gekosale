<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Translation
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Translation extends Translator
{

    /**
     * @var null|\Symfony\Component\Translation\MessageSelector
     */
    protected $locale;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface                                  $container
     * @param null|\Symfony\Component\Translation\MessageSelector $locale
     */
    public function __construct (ContainerInterface $container, $locale)
    {
        $this->container = $container;
        $this->locale = $locale;
        
        parent::__construct($this->locale);
        parent::addLoader('array', new ArrayLoader());
        parent::addResource('array', $this->getResource(), $this->locale);
    }

    /**
     * @return array
     */
    protected function getResource ()
    {
        $Data = Array();
        //         if (($Data = $this->container->get('cache')->load('translations')) === false) {
        //             //            $sql = 'SELECT
        //             //                    	T.name,
        //             //                      	TD.translation
        //             //                    FROM translation T
        //             //                    LEFT JOIN translationdata TD ON T.idtranslation = TD.translationid
        //             //                    WHERE TD.languageid = :languageid';
        //             //            $stmt = Db::getInstance()->prepare($sql);
        //             //            $stmt->bindValue('languageid', $this->container->get('helper')->getLanguageId());
        //             //            $stmt->execute();
        //             //            while ($rs = $stmt->fetch()){
        //             //                $Data[$rs['name']] = $rs['translation'];
        //             //            }
        //             $this->container->get('cache')->save('translations', $Data);
        //         }
        return $Data;
    }
}