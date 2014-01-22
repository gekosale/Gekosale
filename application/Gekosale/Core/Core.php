<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: core.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Core;

class Core
{

    const PRICE_MAX = 99999;

    protected $container;

    protected $registry;

    protected $viewid;

    public function __construct ($registry, $container)
    {
        $this->container = $container;
        $this->registry = $registry;
        $this->setEnvironmentVariables();
        $this->layerData = $this->registry->loader->getCurrentLayer();
    }

    public function processPrice ($price, $withSymbol = true)
    {
        if (! is_null($price)){
            if ($price < 0){
                return ($this->layerData['negativepreffix'] . number_format(abs($price), $this->layerData['decimalcount'], $this->layerData['decimalseparator'], $this->layerData['thousandseparator']) . $this->layerData['negativesuffix']);
            }
            return ($this->layerData['positivepreffix'] . number_format($price, $this->layerData['decimalcount'], $this->layerData['decimalseparator'], $this->layerData['thousandseparator']) . (($withSymbol == true) ? $this->layerData['positivesuffix'] : ''));
        }
        return NULL;
    }

    public static function arrayAsString ($Data, $glue = ',')
    {
        return implode($glue, $Data);
    }

    public function setLanguage ()
    {
        $Data = Array();
        $browserLanguage = $this->getBrowserFirstLanguage();
        if ($this->container->get('session')->getActiveLanguage() == NULL){
            $sql = 'SELECT 
						L.idlanguage,
						L.name,
						C.idcurrency, 
						C.currencysymbol
					FROM language L
					LEFT JOIN languageview LV ON L.idlanguage = LV.languageid
					LEFT JOIN currency C ON C.idcurrency = L.currencyid
					WHERE LV.viewid = :viewid ORDER BY L.idlanguage';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('viewid', Helper::getViewId());
            $stmt->execute();
            $set = false;
            while ($rs = $stmt->fetch()){
                $Data[substr($rs['name'], 0, 2)] = Array(
                    'id' => $rs['idlanguage'],
                    'name' => $rs['name'],
                    'currencyid' => $rs['idcurrency'],
                    'currencysymbol' => $rs['currencysymbol']
                );
            }
            foreach ($Data as $language => $val){
                if ($language == $browserLanguage){
                    $this->container->get('session')->setActiveLanguage($val['name']);
                    $this->container->get('session')->setActiveLanguageId($val['id']);
                    $this->container->get('session')->setActiveCurrencyId($val['currencyid']);
                    $this->container->get('session')->setActiveCurrencySymbol($val['currencysymbol']);
                    break;
                }
            }
            
            if ($this->container->get('session')->getActiveLanguage() == NULL){
                reset($Data);
                $val = current($Data);
                $this->container->get('session')->setActiveLanguage($val['name']);
                $this->container->get('session')->setActiveLanguageId($val['id']);
                $this->container->get('session')->setActiveCurrencyId($val['currencyid']);
                $this->container->get('session')->setActiveCurrencySymbol($val['currencysymbol']);
            }
        }
    }

    public function getBrowserFirstLanguage ()
    {
        if (! isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            return ('');
        }
        
        $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $browserLanguagesSize = sizeof($browserLanguages);
        for ($i = 0; $i < $browserLanguagesSize; $i ++){
            $browserLanguage = explode(';', $browserLanguages[$i]);
            $browserLanguages[$i] = substr($browserLanguage[0], 0, 2);
        }
        
        if (isset($browserLanguages[0]))
            return ($browserLanguages[0]);
        
        return ('');
    }

    public function setAdminStoreConfig ()
    {
        $sql = 'SELECT
					(SELECT version FROM updatehistory WHERE packagename = :packagename ORDER BY idupdatehistory DESC LIMIT 1) AS appversion,
					(SELECT url FROM viewurl WHERE viewid = :viewid LIMIT 1) AS shopurl
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('packagename', 'Gekosale');
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $this->container->get('session')->setActiveAppVersion($rs['appversion']);
            $this->container->get('session')->setActiveShopUrl($rs['shopurl']);
        }
        if ($this->container->get('session')->getActiveGlobalSettings() == NULL){
            $settingsData = App::getModel('globalsettings')->getSettings();
            $this->container->get('session')->setActiveGlobalSettings($settingsData);
        }
    }

    public function getParam ($index = 0)
    {
        $clean = explode('?', $this->registry->router->getParams());
        if (count($clean) > 0){
            $url = $clean[0];
        }
        else{
            $url = $this->registry->router->getParams();
        }
        $params = explode(',', str_replace('/', ',', $url));
        if (isset($params[$index])){
            return $params[$index];
        }
        else{
            return false;
        }
    }

    public function getDefaultValueToSelect ()
    {
        return Array(
            _('TXT_CHOOSE_SELECT')
        );
    }

    public function setEnvironmentVariables ()
    {
        $this->setLayerVariables();
        $this->setLanguage();
    }

    public function setLayerVariables ()
    {
        if (App::getRegistry()->router == null) // CLI compatibility
            return;
        
        if (App::getRegistry()->router->getMode() == 0){
            $this->container->get('session')->setActiveMainsideViewId($this->registry->loader->getParam('idview'));
        }
        else{
            if ($this->container->get('session')->getActiveViewId() !== NULL){
                $viewid = $this->container->get('session')->getActiveViewId();
            }
            else{
                $viewid = 0;
            }
            $this->container->get('session')->setActiveViewId($viewid);
        }
    }

    public static function passwordGenerate ()
    {
        $passwdlen = 8;
        $passwd = NULL;
        $length = 74;
        $collection = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^*_-+=?:';
        for ($x = 0; $x < $passwdlen; $x ++){
            $passwd .= $collection{rand(0, $length)};
        }
        return $passwd;
    }

    public static function clearUTF ($string)
    {
        $a = array(
            'À',
            'Á',
            'Â',
            'Ã',
            'Ä',
            'Å',
            'Æ',
            'Ç',
            'È',
            'É',
            'Ê',
            'Ë',
            'Ì',
            'Í',
            'Î',
            'Ï',
            'Ð',
            'Ñ',
            'Ò',
            'Ó',
            'Ô',
            'Õ',
            'Ö',
            'Ø',
            'Ù',
            'Ú',
            'Û',
            'Ü',
            'Ý',
            'ß',
            'à',
            'á',
            'â',
            'ã',
            'ä',
            'å',
            'æ',
            'ç',
            'è',
            'é',
            'ê',
            'ë',
            'ì',
            'í',
            'î',
            'ï',
            'ñ',
            'ò',
            'ó',
            'ô',
            'õ',
            'ö',
            'ø',
            'ù',
            'ú',
            'û',
            'ü',
            'ý',
            'ÿ',
            'Ā',
            'ā',
            'Ă',
            'ă',
            'Ą',
            'ą',
            'Ć',
            'ć',
            'Ĉ',
            'ĉ',
            'Ċ',
            'ċ',
            'Č',
            'č',
            'Ď',
            'ď',
            'Đ',
            'đ',
            'Ē',
            'ē',
            'Ĕ',
            'ĕ',
            'Ė',
            'ė',
            'Ę',
            'ę',
            'Ě',
            'ě',
            'Ĝ',
            'ĝ',
            'Ğ',
            'ğ',
            'Ġ',
            'ġ',
            'Ģ',
            'ģ',
            'Ĥ',
            'ĥ',
            'Ħ',
            'ħ',
            'Ĩ',
            'ĩ',
            'Ī',
            'ī',
            'Ĭ',
            'ĭ',
            'Į',
            'į',
            'İ',
            'ı',
            'Ĳ',
            'ĳ',
            'Ĵ',
            'ĵ',
            'Ķ',
            'ķ',
            'Ĺ',
            'ĺ',
            'Ļ',
            'ļ',
            'Ľ',
            'ľ',
            'Ŀ',
            'ŀ',
            'Ł',
            'ł',
            'Ń',
            'ń',
            'Ņ',
            'ņ',
            'Ň',
            'ň',
            'ŉ',
            'Ō',
            'ō',
            'Ŏ',
            'ŏ',
            'Ő',
            'ő',
            'Œ',
            'œ',
            'Ŕ',
            'ŕ',
            'Ŗ',
            'ŗ',
            'Ř',
            'ř',
            'Ś',
            'ś',
            'Ŝ',
            'ŝ',
            'Ş',
            'ş',
            'Š',
            'š',
            'Ţ',
            'ţ',
            'Ť',
            'ť',
            'Ŧ',
            'ŧ',
            'Ũ',
            'ũ',
            'Ū',
            'ū',
            'Ŭ',
            'ŭ',
            'Ů',
            'ů',
            'Ű',
            'ű',
            'Ų',
            'ų',
            'Ŵ',
            'ŵ',
            'Ŷ',
            'ŷ',
            'Ÿ',
            'Ź',
            'ź',
            'Ż',
            'ż',
            'Ž',
            'ž',
            'ſ',
            'ƒ',
            'Ơ',
            'ơ',
            'Ư',
            'ư',
            'Ǎ',
            'ǎ',
            'Ǐ',
            'ǐ',
            'Ǒ',
            'ǒ',
            'Ǔ',
            'ǔ',
            'Ǖ',
            'ǖ',
            'Ǘ',
            'ǘ',
            'Ǚ',
            'ǚ',
            'Ǜ',
            'ǜ',
            'Ǻ',
            'ǻ',
            'Ǽ',
            'ǽ',
            'Ǿ',
            'ǿ',
            'Ą',
            'Ć',
            'Ę',
            'Ł',
            'Ń',
            'Ó',
            'Ś',
            'Ź',
            'Ż',
            'ą',
            'ć',
            'ę',
            'ł',
            'ń',
            'ó',
            'ś',
            'ź',
            'ż',
            ' ',
            ',',
            '_',
            '.',
            '?'
        );
        $b = array(
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'AE',
            'C',
            'E',
            'E',
            'E',
            'E',
            'I',
            'I',
            'I',
            'I',
            'D',
            'N',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'U',
            'U',
            'U',
            'U',
            'Y',
            's',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'ae',
            'c',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'n',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'A',
            'a',
            'A',
            'a',
            'A',
            'a',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'D',
            'd',
            'D',
            'd',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'H',
            'h',
            'H',
            'h',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'IJ',
            'ij',
            'J',
            'j',
            'K',
            'k',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'l',
            'l',
            'N',
            'n',
            'N',
            'n',
            'N',
            'n',
            'n',
            'O',
            'o',
            'O',
            'o',
            'O',
            'o',
            'OE',
            'oe',
            'R',
            'r',
            'R',
            'r',
            'R',
            'r',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'T',
            't',
            'T',
            't',
            'T',
            't',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'W',
            'w',
            'Y',
            'y',
            'Y',
            'Z',
            'z',
            'Z',
            'z',
            'Z',
            'z',
            's',
            'f',
            'O',
            'o',
            'U',
            'u',
            'A',
            'a',
            'I',
            'i',
            'O',
            'o',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'A',
            'a',
            'AE',
            'ae',
            'O',
            'o',
            'A',
            'C',
            'E',
            'L',
            'N',
            'O',
            'S',
            'Z',
            'Z',
            'a',
            'c',
            'e',
            'l',
            'n',
            'o',
            's',
            'z',
            'z',
            '-',
            '',
            '-',
            '-',
            ''
        );
        return str_replace($a, $b, $string);
    }

    public static function clearSeoUTF ($string)
    {
        $a = array(
            'À',
            'Á',
            'Â',
            'Ã',
            'Ä',
            'Å',
            'Æ',
            'Ç',
            'È',
            'É',
            'Ê',
            'Ë',
            'Ì',
            'Í',
            'Î',
            'Ï',
            'Ð',
            'Ñ',
            'Ò',
            'Ó',
            'Ô',
            'Õ',
            'Ö',
            'Ø',
            'Ù',
            'Ú',
            'Û',
            'Ü',
            'Ý',
            'ß',
            'à',
            'á',
            'â',
            'ã',
            'ä',
            'å',
            'æ',
            'ç',
            'è',
            'é',
            'ê',
            'ë',
            'ì',
            'í',
            'î',
            'ï',
            'ñ',
            'ò',
            'ó',
            'ô',
            'õ',
            'ö',
            'ø',
            'ù',
            'ú',
            'û',
            'ü',
            'ý',
            'ÿ',
            'Ā',
            'ā',
            'Ă',
            'ă',
            'Ą',
            'ą',
            'Ć',
            'ć',
            'Ĉ',
            'ĉ',
            'Ċ',
            'ċ',
            'Č',
            'č',
            'Ď',
            'ď',
            'Đ',
            'đ',
            'Ē',
            'ē',
            'Ĕ',
            'ĕ',
            'Ė',
            'ė',
            'Ę',
            'ę',
            'Ě',
            'ě',
            'Ĝ',
            'ĝ',
            'Ğ',
            'ğ',
            'Ġ',
            'ġ',
            'Ģ',
            'ģ',
            'Ĥ',
            'ĥ',
            'Ħ',
            'ħ',
            'Ĩ',
            'ĩ',
            'Ī',
            'ī',
            'Ĭ',
            'ĭ',
            'Į',
            'į',
            'İ',
            'ı',
            'Ĳ',
            'ĳ',
            'Ĵ',
            'ĵ',
            'Ķ',
            'ķ',
            'Ĺ',
            'ĺ',
            'Ļ',
            'ļ',
            'Ľ',
            'ľ',
            'Ŀ',
            'ŀ',
            'Ł',
            'ł',
            'Ń',
            'ń',
            'Ņ',
            'ņ',
            'Ň',
            'ň',
            'ŉ',
            'Ō',
            'ō',
            'Ŏ',
            'ŏ',
            'Ő',
            'ő',
            'Œ',
            'œ',
            'Ŕ',
            'ŕ',
            'Ŗ',
            'ŗ',
            'Ř',
            'ř',
            'Ś',
            'ś',
            'Ŝ',
            'ŝ',
            'Ş',
            'ş',
            'Š',
            'š',
            'Ţ',
            'ţ',
            'Ť',
            'ť',
            'Ŧ',
            'ŧ',
            'Ũ',
            'ũ',
            'Ū',
            'ū',
            'Ŭ',
            'ŭ',
            'Ů',
            'ů',
            'Ű',
            'ű',
            'Ų',
            'ų',
            'Ŵ',
            'ŵ',
            'Ŷ',
            'ŷ',
            'Ÿ',
            'Ź',
            'ź',
            'Ż',
            'ż',
            'Ž',
            'ž',
            'ſ',
            'ƒ',
            'Ơ',
            'ơ',
            'Ư',
            'ư',
            'Ǎ',
            'ǎ',
            'Ǐ',
            'ǐ',
            'Ǒ',
            'ǒ',
            'Ǔ',
            'ǔ',
            'Ǖ',
            'ǖ',
            'Ǘ',
            'ǘ',
            'Ǚ',
            'ǚ',
            'Ǜ',
            'ǜ',
            'Ǻ',
            'ǻ',
            'Ǽ',
            'ǽ',
            'Ǿ',
            'ǿ',
            'Ą',
            'Ć',
            'Ę',
            'Ł',
            'Ń',
            'Ó',
            'Ś',
            'Ź',
            'Ż',
            'ą',
            'ć',
            'ę',
            'ł',
            'ń',
            'ó',
            'ś',
            'ź',
            'ż',
            ' ',
            ',',
            '_',
            '.',
            '?',
            '',
            '|',
            '(',
            ')',
            '"',
            '*',
            '+',
            '&',
            ':',
            '\''
        );
        $b = array(
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'AE',
            'C',
            'E',
            'E',
            'E',
            'E',
            'I',
            'I',
            'I',
            'I',
            'D',
            'N',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'U',
            'U',
            'U',
            'U',
            'Y',
            's',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'ae',
            'c',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'n',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'A',
            'a',
            'A',
            'a',
            'A',
            'a',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'D',
            'd',
            'D',
            'd',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'H',
            'h',
            'H',
            'h',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'IJ',
            'ij',
            'J',
            'j',
            'K',
            'k',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'l',
            'l',
            'N',
            'n',
            'N',
            'n',
            'N',
            'n',
            'n',
            'O',
            'o',
            'O',
            'o',
            'O',
            'o',
            'OE',
            'oe',
            'R',
            'r',
            'R',
            'r',
            'R',
            'r',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'T',
            't',
            'T',
            't',
            'T',
            't',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'W',
            'w',
            'Y',
            'y',
            'Y',
            'Z',
            'z',
            'Z',
            'z',
            'Z',
            'z',
            's',
            'f',
            'O',
            'o',
            'U',
            'u',
            'A',
            'a',
            'I',
            'i',
            'O',
            'o',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'A',
            'a',
            'AE',
            'ae',
            'O',
            'o',
            'A',
            'C',
            'E',
            'L',
            'N',
            'O',
            'S',
            'Z',
            'Z',
            'a',
            'c',
            'e',
            'l',
            'n',
            'o',
            's',
            'z',
            'z',
            '-',
            '',
            '',
            '-',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );
        $str = str_replace($a, $b, strip_tags(html_entity_decode($string)));
        $str = str_replace('-------------', '-', $str);
        $str = str_replace('-----------', '-', $str);
        $str = str_replace('-------', '-', $str);
        $str = str_replace('-----', '-', $str);
        $str = str_replace('---', '-', $str);
        $str = str_replace('--', '-', $str);
        return $str;
    }

    public static function clearNonAlpha ($string)
    {
        $a = Array(
            "!",
            "@",
            "#",
            "$",
            "%",
            "^",
            "*",
            "(",
            ")",
            ",",
            ".",
            ":",
            "\"",
            "/",
            "\\",
            "<",
            ">",
            "|"
        );
        return str_replace($a, '', strip_tags($string));
    }

    public function getModuleSettingsForView ()
    {
        if (($Data = App::getContainer()->get('cache')->load('modulesettings')) === FALSE){
            $sql = 'SELECT * FROM modulesettings WHERE viewid = :viewid';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('viewid', Helper::getViewId());
            $stmt->execute();
            $Data = Array();
            while ($rs = $stmt->fetch()){
                $Data[$rs['module']][$rs['param']] = $rs['value'];
            }
            App::getContainer()->get('cache')->save('modulesettings', $Data);
        }
        return $Data;
    }

    public function loadModuleSettings ($module, $viewid = 0)
    {
        $sql = 'SELECT * FROM modulesettings WHERE module = :module';
        if ($viewid > 0){
            $sql .= ' AND viewid = :viewid';
        }
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('module', $module);
        if ($viewid > 0){
            $stmt->bindValue('viewid', $viewid);
        }
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[$rs['param']] = $rs['value'];
        }
        return $Data;
    }

    public function saveModuleSettings ($module, $Data, $viewid = 0)
    {
        foreach ($Data as $param => $value){
            $sql = 'INSERT INTO modulesettings SET
						param = :param,
						module = :module,
						viewid = :viewid,
						value = :value
					ON DUPLICATE KEY UPDATE
						value = :value';
            
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('param', $param);
            $stmt->bindValue('module', $module);
            $stmt->bindValue('viewid', $viewid);
            $stmt->bindValue('value', $value);
            $stmt->execute();
        }
        
        App::getContainer()->get('cache')->delete('modulesettings');
    }

    public static function getRealIpAddress ()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}
