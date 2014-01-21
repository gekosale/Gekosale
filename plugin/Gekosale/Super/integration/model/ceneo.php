<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;
use FormEngine;

class CeneoModel extends Component\Model
{

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
        $this->layer = $this->registry->loader->getCurrentLayer();
    }

    public function addFieldsView ($event, $request)
    {
        $form = &$request['form'];
        
        $ceneoData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'ceneo_data',
            'label' => 'Zaufane opinie Ceneo.pl'
        )));
        
        $ceneoData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Podaj numer GUID sklepu. Znajdziesz go w <a href="https://panel.ceneo.pl" target="_blank">panelu partnera Ceneo.pl</a>. Po wprowadzeniu kodu, widget opinii pojawi się automatycznie. Informacje o składanych zamówieniach będą przekazywane do Ceneo automatycznie.</p>'
        )));
        
        $ceneoData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'ceneoguid',
            'label' => 'Nr GUID Sklepu'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('ceneo', (int) $request['id']);
        
        if (! empty($settings)){
            $populate = Array(
                'ceneo_data' => Array(
                    'ceneoguid' => $settings['ceneoguid']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function integrationUpdateView ($request)
    {
        $Settings = Array(
            'ceneoguid' => $request['data']['ceneoguid']
        );
        
        $this->registry->core->saveModuleSettings('ceneo', $Settings, $request['id']);
    }

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        
        $ceneo = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'ceneo_data',
            'label' => 'Integracja z Ceneo.pl'
        )));
        
        $ceneo->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wybierz dział w portalu Ceneo.pl w którym będą publikowane produkty z tej kategorii.</p>'
        )));
        
        $ceneo->AddChild(new FormEngine\Elements\Tree(Array(
            'name' => 'ceneocategory',
            'label' => $this->trans('TXT_CATEGORY'),
            'choosable' => true,
            'selectable' => false,
            'sortable' => false,
            'clickable' => false,
            'items' => $this->getChildCategories(),
            'load_children' => Array(
                $this,
                'getChildCategories'
            )
        )));
        
        $id = $this->getCeneoIdByCategoryId($request['id']);
        
        if (NULL !== $id){
            $populate = Array(
                'ceneo_data' => Array(
                    'ceneocategory' => $id
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function getChildCategories ($parentCategory = 0)
    {
        $sql = '
				SELECT
					A.idorginal AS id,
					A.name,
					COUNT(B.idceneo) AS has_children
				FROM
					ceneo A
					LEFT JOIN ceneo B ON A.idorginal = B.parentorginalid
				WHERE
					A.parentorginalid = :parent
				GROUP BY
					A.idceneo
			';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('parent', $parentCategory);
        $rs = $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[$rs['id']] = Array(
                'name' => $rs['name'],
                'hasChildren' => ($rs['has_children'] > 0) ? true : false
            );
        }
        return $Data;
    }

    public function integrationUpdate ($request)
    {
        DbTracker::deleteRows('categoryceneo', 'categoryid', $request['id']);
        $sql = 'INSERT INTO categoryceneo (categoryid, ceneoid)
				VALUES (:categoryid, :ceneoid)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('categoryid', $request['id']);
        $stmt->bindValue('ceneoid', (int) $request['data']['ceneocategory']);
        $stmt->execute();
    }

    public function getCeneoIdByCategoryId ($id)
    {
        $sql = 'SELECT ceneoid FROM categoryceneo WHERE categoryid = :categoryid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('categoryid', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['ceneoid'];
        }
        return NULL;
    }

    public function Delete ($id)
    {
        $sql = 'DELETE FROM categoryceneo WHERE categoryid = :categoryid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('categoryid', (int) $id);
        $stmt->execute();
    }

    public function updateCategories ()
    {
        $sql = 'TRUNCATE ceneo';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        
        $this->xmlParser = new XmlParser();
        $categories = $this->xmlParser->parseExternal('http://api.ceneo.pl/Kategorie/dane.xml');
        $this->xmlParser->flush();
        $Data = Array();
        
        Db::getInstance()->beginTransaction();
        
        foreach ($categories->Category as $category){
            
            $sql = 'INSERT INTO ceneo (name, idorginal, parentorginalid, path)
					VALUES (:name, :idorginal, :parentorginalid, :path)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('name', (string) $category->Name);
            $stmt->bindValue('idorginal', (int) $category->Id);
            $stmt->bindValue('parentorginalid', 0);
            $stmt->bindValue('path', (string) $category->Name);
            $stmt->execute();
            
            foreach ($category->Subcategories->Category as $subcategory){
                $sql = 'INSERT INTO ceneo (name, idorginal, parentorginalid, path)
						VALUES (:name, :idorginal, :parentorginalid, :path)';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('name', (string) $subcategory->Name);
                $stmt->bindValue('idorginal', (int) $subcategory->Id);
                $stmt->bindValue('parentorginalid', (int) $category->Id);
                $stmt->bindValue('path', (string) $category->Name . "|" . (string) $subcategory->Name);
                $stmt->execute();
                
                foreach ($subcategory->Subcategories->Category as $thirdcategory){
                    $sql = 'INSERT INTO ceneo (name, idorginal, parentorginalid, path)
							VALUES (:name, :idorginal, :parentorginalid, :path)';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('name', (string) $thirdcategory->Name);
                    $stmt->bindValue('idorginal', (int) $thirdcategory->Id);
                    $stmt->bindValue('parentorginalid', (int) $subcategory->Id);
                    $stmt->bindValue('path', (string) $category->Name . "|" . (string) $subcategory->Name . "|" . (string) $thirdcategory->Name);
                    $stmt->execute();
                    
                    foreach ($thirdcategory->Subcategories->Category as $fourthcategory){
                        $sql = 'INSERT INTO ceneo (name, idorginal, parentorginalid, path)
								VALUES (:name, :idorginal, :parentorginalid, :path)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('name', (string) $fourthcategory->Name);
                        $stmt->bindValue('idorginal', (int) $fourthcategory->Id);
                        $stmt->bindValue('parentorginalid', (int) $thirdcategory->Id);
                        $stmt->bindValue('path', (string) $category->Name . "|" . (string) $subcategory->Name . "|" . (string) $thirdcategory->Name . "|" . (string) $fourthcategory->Name);
                        $stmt->execute();
                    }
                }
            }
        }
        
        Db::getInstance()->commit();
    }

    public function getDescription ()
    {
        return '<p><h3>Ceneo.pl jest największą na polskim rynku porównywarką cen produktów w sklepach internetowych.</h3></p>
<p>Jako jeden z największych serwisów e-commerce w Polsce dostarczamy naszym Użytkownikom narzędzie, które umożliwia <b>szybkie i łatwe wyszukanie 
produktów oraz informacji o produktach</b>, a następnie <b>porównanie ich cen w wielu sklepach</b></p>
<p>Kupującym w sieci chcemy umożliwić <b>szybkie znalezienie atrakcyjnej oferty oraz wiarygodnego sklepu</b>, a współpracującym z nami sklepom oferujemy 
możliwość <b>zwiększenia sprzedaży oraz promocję swojej marki</b> wśród dynamicznie rozwijającej się społeczności internetowej.</p>
<p>Użytkownikom, chcącym skorzystać z profesjonalnego doradztwa w zakresie planowanego zakupu, dostarczamy szereg <b>przewodników zakupowych</b>, które 
oferują kompleksową pomoc w wyborze konkretnego modelu z danej grupy produktów. Posiadamy także obszerną bazę opinii o produktach, tworzoną przez 
ich nabywców i użytkowników.</p>
<p>Jednak Ceneo to nie tylko ceny i produkty. To także <b>opinie o sklepach internetowych</b> - poziomie obsługi klienta, przebiegu realizacji zamówień 
czy terminowości przesyłek, wystawiane przez użytkowników po dokonaniu zakupu. Dzięki programowi <b>„Zaufane opinie”</b>, czyli specjalnemu mechanizmowi 
weryfikacji komentarzy, <b>ograniczamy możliwość wystawiania nieprawdziwych	 opinii o sklepach</b>, zwiększając tym samym 
bezpieczeństwo dokonywania transakcji handlowych w Internecie.</p>
<p><b>Liczba ofert sklepów internetowych, dostępnych na Ceneo.pl, stale rośnie</b>. Każdego dnia dołączają do nas nowe sklepy, współtworząc razem z nami 
największą w polskim Internecie bazę informacji o produktach i ich cenach.</p>
';
    }

    public function getConfigurationFields ()
    {
        return Array();
    }

    public function getProductListIntegration ()
    {
        $sql = "SELECT
				  	PC.categoryid AS id,
				  	P.idproduct,
				  	IF(P.trackstock = 1, P.stock, 1) AS stock,
				  	P.weight,
				  	PT.name,
				  	(P.sellprice * (1 + (V.value / 100)) * CR.exchangerate) AS sellprice,
				  	IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
				 	PT.shortdescription,
				  	Photo.photoid,
				  	NC.name as ceneooriginal,
				  	CN.categoryid,
				  	NC.idceneo,
				  	CN.ceneoid,
				  	PT.seo,
					PRT.name AS producername,
					P.ean,
					NC.path
				FROM product P
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid=:languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				INNER JOIN viewcategory VC ON VC.categoryid = PC.categoryid AND VC.viewid = :viewid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				INNER JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto=1
				INNER JOIN categoryceneo CN ON CN.categoryid = PC.categoryid
				INNER JOIN ceneo NC ON NC.idorginal = CN.ceneoid
				LEFT JOIN producertranslation PRT ON PRT.producerid = P.producerid AND PRT.languageid = :languageid
				WHERE P.enable = 1
	            GROUP BY P.idproduct";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
        $rs = $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'categoryid' => $rs['id'],
                'producername' => $rs['producername'],
                'ean' => $rs['ean'],
                'productid' => $rs['idproduct'],
                'stock' => $rs['stock'],
                'avail' => ($rs['stock'] > 0) ? 1 : 7,
                'weight' => $rs['weight'],
                'seo' => $rs['seo'],
                'name' => $rs['name'],
                'shortdescription' => $rs['shortdescription'],
                'sellprice' => number_format((! is_null($rs['discountprice'])) ? $rs['discountprice'] : $rs['sellprice'], 2, '.', ''),
                'photoid' => $rs['photoid'],
                'idproduct' => $rs['idproduct'],
                'ceneo' => str_replace('|', '\\', $rs['path'])
            );
        }
        foreach ($Data as $key => $Product){
            $Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
            $Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
        }
        return $Data;
    }

    public function addTransJs ($order)
    {
        $ids = implode('#', array_keys($order['orderData']['cart']));
        $settings = $this->registry->core->loadModuleSettings('ceneo', Helper::getViewId());
        if (! empty($settings) && isset($settings['ceneoguid'])){
            $account = $settings['ceneoguid'];
            $amount = round($order['orderData']['globalPrice'], 2);
            $code = '';
            if (strlen($account) > 0){
                $code .= "<script type=\"text/javascript\">";
                $code .= "ceneo_client_email = '{$order['orderData']['contactData']['email']}';";
                $code .= "ceneo_order_id = '{$order['orderId']}';";
                $code .= "ceneo_amount = {$amount};";
                $code .= "ceneo_shop_product_ids = '#{$ids}#';";
                $code .= "</script>";
                $code .= "<script type=\"text/javascript\" src=\"https://ssl.ceneo.pl/transactions/track/v2/script.js?accountGuid={$account}\"></script>";
            }
            return $code;
        }
    }
}