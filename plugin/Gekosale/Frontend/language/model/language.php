<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 484 $
 * $Author: gekosale $
 * $Date: 2011-09-07 13:42:04 +0200 (Śr, 07 wrz 2011) $
 * $Id: language.php 484 2011-09-07 11:42:04Z gekosale $ 
 */
namespace Gekosale;
use xajaxResponse;

class LanguageModel extends Component\Model
{

    public function getLanguages ()
    {
        if (($Data = App::getContainer()->get('cache')->load('languages')) === FALSE){
            $sql = 'SELECT 
					idlanguage AS id, 
					flag, 
					translation,
					viewid
				FROM language L
				INNER JOIN languageview LV ON LV.languageid = L.idlanguage AND LV.viewid = :viewid';
            $Data = Array();
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('viewid', Helper::getViewId());
            $stmt->execute();
            while ($rs = $stmt->fetch()){
                $Data[$rs['id']] = Array(
                    'id' => $rs['id'],
                    'flag' => $rs['flag'],
                    'weight' => $rs['id'],
                    'icon' => $rs['flag'],
                    'name' => $this->trans($rs['translation']),
                    'active' => 0
                );
            }
            App::getContainer()->get('cache')->save('languages', $Data);
        }
        
        $Data[Helper::getLanguageId()]['active'] = 1;
        return $Data;
    }

    public function changeAJAXLanguageAboutView ($id)
    {
        $objResponseChangeLanguage = new xajaxResponse();
        try{
            $checkId = $this->checkLanguageId($id);
            App::getContainer()->get('session')->setActiveLanguageId($id);
            App::getContainer()->get('session')->setActiveLanguage($checkId['name']);
            $shopCurrencyId = App::getContainer()->get('session')->getActiveShopCurrencyId();
            if (App::getContainer()->get('session')->getActiveShopCurrencyId() != $checkId['currencyid']){
                $this->changeAJAXCurrencyView($checkId['currencyid']);
            }
        }
        catch (Exception $fe){
            echo 'error';
        }
        $objResponseChangeLanguage->script('window.location.href = "'.$this->registry->router->generate('frontend.home').'"');
        return $objResponseChangeLanguage;
    }

    public function checkLanguageId ($id)
    {
        $sql = 'SELECT 
					languageid, 
					name,
					currencyid
				FROM languageview 
				LEFT JOIN language L ON L.idlanguage = languageid
				WHERE viewid = :viewid AND languageid=:id';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $Data = Array(
                'languageid' => $rs['languageid'],
                'name' => $rs['name'],
                'currencyid' => $rs['currencyid']
            );
        }
        return $Data;
    }

    public function changeAJAXCurrencyView ($id)
    {
        $objResponse = new xajaxResponse();
        try{
            $shopCurrencyId = App::getContainer()->get('session')->getActiveShopCurrencyId();
            $currencyData = $this->getCurrencySelectedData($id);
            $currencyRate = $this->getCurrencyRate($shopCurrencyId, $id);
            if (is_array($currencyData) && ! empty($currencyData)){
                App::getContainer()->get('session')->setActiveCurrencyId($currencyData['id']);
                App::getContainer()->get('session')->setActiveCurrencySymbol($currencyData['symbol']);
                App::getContainer()->get('session')->setActiveCurrencyRate($currencyRate);
                App::getModel('cart')->setTempCartAfterCurrencyChange();
            }
            $objResponse->script('window.location.reload( false )');
        }
        catch (Exception $fe){
            throw new FrontendException($e->getMessage());
        }
        return $objResponse;
    }

    public function getCurrencySelectedData ($currencyId)
    {
        $Data = Array();
        $sql = "SELECT C.idcurrency, C.currencysymbol
					FROM currency C
					WHERE C.idcurrency= :idcurrency";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('idcurrency', $currencyId);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $Data = Array(
                'id' => $rs['idcurrency'],
                'symbol' => $rs['currencysymbol']
            );
        }
        return $Data;
    }

    public function getCurrencyRate ($currencyFrom, $currencyTo)
    {
        $sql = "SELECT 
        			CR.exchangerate
				FROM currencyrates CR 
				WHERE CR.currencyfrom = :currencyfrom AND CR.currencyto = :currencyto";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('currencyfrom', $currencyFrom);
        $stmt->bindValue('currencyto', $currencyTo);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['exchangerate'];
        }
        else{
            return 1;
        }
    }

    public function getAllCurrenciesForView ()
    {
        $shopCurrencyId = App::getContainer()->get('session')->getActiveCurrencyId();
        if (($Data = App::getContainer()->get('cache')->load('currencies')) === FALSE){
            $sql = "SELECT 
						C.idcurrency, 
						C.currencysymbol, 
						C.currencyname
					FROM currency C
					LEFT JOIN currencyview CV ON CV.currencyid = C.idcurrency
					WHERE CV.viewid= :viewid";
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('viewid', Helper::getViewId());
            $stmt->execute();
            while ($rs = $stmt->fetch()){
                $id = $rs['idcurrency'];
                $Data[$id] = Array(
                    'id' => $id,
                    'name' => $rs['currencysymbol'],
                    'selected' => 0
                );
            }
            App::getContainer()->get('cache')->save('currencies', $Data);
        }
        $Data[$shopCurrencyId]['selected'] = 1;
        return $Data;
    }

    public function getLanguageALL ()
    {
        $sql = 'SELECT
					idlanguage AS id,
					translation,
					name,
					flag
				FROM language';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['id'],
                'translation' => $this->trans($rs['translation']),
                'flag' => $rs['flag'],
                'name' => $rs['name']
            );
        }
        return $Data;
    }

    public function updateTranslation ($languageid, $name, $value, $updateOnExists = false)
    {
        $sql = 'SELECT idtranslation as id FROM translation WHERE name = :name';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('name', $name);
        $stmt->execute();
        $rs = $stmt->fetch();
        
        if ($rs && $updateOnExists == false)
            return false;
        
        if ($rs){
            $id = $rs['id'];
            $sql = 'INSERT INTO translationdata SET
						translationid = :translationid,
						languageid = :languageid,
						translation = :translation
					ON DUPLICATE KEY UPDATE
						translation = :translation';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('translation', $value);
            $stmt->bindValue('translationid', $id);
            $stmt->bindValue('languageid', $languageid);
            $stmt->execute();
        }
        else{
            $sql = 'INSERT INTO translation SET name = :name';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('name', $name);
            
            $stmt->execute();
            $id = Db::getInstance()->lastInsertId();
            $sql = 'INSERT INTO translationdata SET
						translationid = :translationid,
						languageid = :languageid,
						translation = :translation
					ON DUPLICATE KEY UPDATE
						translation = :translation';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('translation', $value);
            $stmt->bindValue('translationid', $id);
            $stmt->bindValue('languageid', $languageid);
            $stmt->execute();
        }
        
        return true;
    }
}