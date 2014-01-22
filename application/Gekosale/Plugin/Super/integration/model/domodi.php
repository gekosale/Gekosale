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
namespace Gekosale\Plugin;

use FormEngine;

class DomodiModel extends Component\Model
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->layer = $this->registry->loader->getCurrentLayer();
	}
	
	public function addFields ($event, $request)
	{
	    $form = &$request['form'];
	    
		$domodi = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'domodi_data',
			'label' => 'Integracja z Domodi.pl'
		)));
		
		$domodi->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Wybierz dział w portalu Domodi.pl w którym będą publikowane produkty z tej kategorii.</p>'
		)));
		
		$domodi->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'domodicategory',
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
		
		$id = $this->getDomodiIdByCategoryId($request['id']);
		
		if (NULL !== $id){
			$populate = Array(
				'domodi_data' => Array(
					'domodicategory' => $id
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
					COUNT(B.iddomodi) AS has_children
				FROM
					domodi A
					LEFT JOIN domodi B ON A.idorginal = B.parentorginalid
				WHERE
					A.parentorginalid = :parent
				GROUP BY
					A.iddomodi
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('parent', $parentCategory);
		$rs = $stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'name' => $rs['name'],
				'hasChildren' => (int)$rs['has_children']
			);
		}
		return $Data;
	}

	public function integrationUpdate ($request)
	{
		DbTracker::deleteRows('categorydomodi', 'categoryid', $request['id']);
		$sql = 'INSERT INTO categorydomodi (categoryid, domodiid)
				VALUES (:categoryid, :domodiid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $request['id']);
		$stmt->bindValue('domodiid', (int) $request['data']['domodicategory']);
		$stmt->execute();
	}

	public function getDomodiIdByCategoryId ($id)
	{
		$sql = 'SELECT domodiid FROM categorydomodi WHERE categoryid = :categoryid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['domodiid'];
		}
		return NULL;
	}

	public function Delete ($id)
	{
		$sql = 'DELETE FROM categorydomodi WHERE categoryid = :categoryid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', (int) $id);
		$stmt->execute();
	}

	public function updateCategories ()
	{
		$sql = 'TRUNCATE domodi';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		$this->xmlParser = new XmlParser();
		$categories = $this->xmlParser->parseExternal('http://panel.domodi.pl/pliki/kategorie.xml');
		$this->xmlParser->flush();
		$Data = Array();
		
		Db::getInstance()->beginTransaction();
		
		foreach ($categories->Category as $category){
			
			$sql = 'INSERT INTO domodi (name, idorginal, parentorginalid)
					VALUES (:name, :idorginal, :parentorginalid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', (string) $category->Name);
			$stmt->bindValue('idorginal', (int) $category->Id);
			$stmt->bindValue('parentorginalid', 0);
			$stmt->execute();
			
			foreach ($category->Subcategories->Category as $subcategory){
				$sql = 'INSERT INTO domodi (name, idorginal, parentorginalid)
						VALUES (:name, :idorginal, :parentorginalid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', (string) $subcategory->Name);
				$stmt->bindValue('idorginal', (int) $subcategory->Id);
				$stmt->bindValue('parentorginalid', (int) $category->Id);
				$stmt->execute();
			}
		}
		
		Db::getInstance()->commit();
	}

	public function getDescription ()
	{
		return '<p><strong>Domodi.pl</strong> to serwis skupiający wokół siebie pasjonatów mody, kreatorów stylu i miłośników trendów. Jest to również idealne miejsce dla osób, które poszukują interesujących produktów do kupienia w Internecie. Udostępniamy użytkownikom narzędzia, dzięki którym tworzą oni unikatowe zestawy, dzielą się swoją wiedzą na temat mody, kreują własny styl i inspirują innych. Każdy członek naszej internetowej społeczności ma możliwość stworzenia niepowtarzalnego zestawu korzystając z tysięcy produktów dostępnych w Domodi.pl lub dodając własne elementy graficzne. Dzięki temu dajemy użytkownikom możliwość stworzenia swojego stylowego portfolio – szybko, łatwo i bez konieczności używania skomplikowanych programów graficznych.</p>
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
				  	P.stock,
				  	P.weight,
				  	PT.name,
				  	(P.sellprice * (1 + (V.value / 100)) * CR.exchangerate) AS sellprice,
				  	IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
				 	PT.shortdescription,
				  	Photo.photoid,
				  	NC.name as domodioriginal,
				  	CN.categoryid,
				  	NC.iddomodi,
				  	CN.domodiid,
				  	PT.seo,
					PRT.name AS producername,
					P.ean
				FROM product P
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid=:languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				INNER JOIN viewcategory VC ON VC.categoryid = PC.categoryid AND VC.viewid = :viewid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				INNER JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto=1
				INNER JOIN categorydomodi CN ON CN.categoryid = PC.categoryid
				INNER JOIN domodi NC ON NC.idorginal = CN.domodiid
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
				'domodi' => $this->generateDomodiTreeByCategoryId($rs['categoryid'])
			);
		}
		foreach ($Data as $key => $Product){
			$Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
			$Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
		}
		return $Data;
	}

	public function generateDomodiTreeByCategoryId ($id)
	{
		$sql = "SELECT C.name FROM domodi C LEFT JOIN categorydomodi CN ON C.idorginal = CN.domodiid WHERE CN.categoryid = :catid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('catid', $id);
		$rs = $stmt->execute();
		while ($rs = $stmt->fetch()){
			return $rs['name'];
		}
	}
}