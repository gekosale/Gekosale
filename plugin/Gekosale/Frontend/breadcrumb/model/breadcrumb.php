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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: breadcrumb.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale;

class BreadcrumbModel extends Component\Model
{
	public function getPageLinks ()
	{
		$Data = Array();
		$controller = $this->registry->router->getCurrentController();
		if (method_exists($this, $controller) == true){
			$Data = call_user_func(Array(
				$this,
				$controller
			), (int) $this->registry->core->getParam());
		}
		else{
			if ($this->registry->router->getCurrentController() != 'mainside'){
				$Data = call_user_func(Array(
					$this,
					'getDefault'
				), (int) $this->registry->core->getParam());
			}
		}
		
		$Breadcrumb = Array();
		$Breadcrumb[] = Array(
			'link' => '',
			'title' => $this->trans('TXT_MAINSIDE')
		);
		
		foreach ($Data as $key => $link){
			$Breadcrumb[] = array(
				'link' => $link['link'],
				'title' => $link['title']
			);
		}
		
		return $Breadcrumb;
	}

	protected function getDefault ($id)
	{
		$Data = Array();
		$sql = "SELECT
					description
				FROM controller
				WHERE name = :controller AND mode = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('controller', $this->registry->router->getCurrentController());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data[] = Array(
				'link' => Seo::getSeo($this->registry->router->getCurrentController()),
				'title' => $this->trans($rs['description'])
			);
		}
		return $Data;
	}

	protected function categorylist ($id)
	{
		$category = App::getModel('categorylist')->getCurrentCategory();
		if (empty($category)){
			App::redirectUrl($this->registry->router->generate('frontend.sitemap', true));
		}
		$sql = "SELECT
					CONCAT(:seo,'/',IF(CT.seo IS NOT NULL, CT.seo,'')) AS link, 
					CT.name AS title
				FROM categorypath CP
				LEFT JOIN categorytranslation CT ON CP.ancestorcategoryid = CT.categoryid AND CT.languageid = :languageid
				WHERE CP.categoryid = :categoryid
				ORDER BY CP.order DESC";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('seo', Seo::getSeo('categorylist'));
		$stmt->bindValue('categoryid', $category['id']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'link' => $rs['link'],
				'title' => $rs['title']
			);
		}
		return $Data;
	}

	protected function productcart ($id)
	{
		$this->productid = App::getModel('product')->getProductIdBySeo($this->getParam());
		
		$sql = "SELECT 
					PC.categoryid 
				FROM productcategory PC 
				LEFT JOIN category C ON PC.categoryid = C.idcategory
				WHERE PC.productid = :productid AND C.enable = 1
				LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $this->productid);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$sql = "SELECT
						CONCAT(:seo,'/',IF(CT.seo IS NOT NULL, CT.seo,'')) AS link, 
						CT.name AS title
					FROM categorypath CP
					LEFT JOIN categorytranslation CT ON CP.ancestorcategoryid = CT.categoryid AND CT.languageid = :languageid
					WHERE CP.categoryid = :categoryid
					ORDER BY CP.order DESC";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('seo', Seo::getSeo('categorylist'));
			$stmt->bindValue('categoryid', $rs['categoryid']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->execute();
			$Data = Array();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'link' => $rs['link'],
					'title' => $rs['title']
				);
			}
		}
		$sql = "SELECT
					CONCAT(:seo,'/',IF(PT.seo IS NOT NULL, PT.seo,'')) AS link, 
					PT.name AS title
				FROM producttranslation PT 
				WHERE PT.productid = :productid AND PT.languageid = :languageid
				";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('seo', Seo::getSeo('productcart'));
		$stmt->bindValue('productid', $this->productid);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data[] = Array(
				'link' => $rs['link'],
				'title' => $rs['title']
			);
		}
		return $Data;
	}

	protected function producerlist ($id)
	{
		$Data = Array();
		$Data[] = Array(
			'link' => Seo::getSeo($this->registry->router->getCurrentController()),
			'title' => $this->trans('TXT_PRODUCER')
		);
		
		$producer = App::getModel('producerlistbox')->getProducerBySeo($this->getParam());
		if (! empty($producer)){
			$Data[] = Array(
				'link' => Seo::getSeo($this->registry->router->getCurrentController()) . '/' . $producer['seo'],
				'title' => $producer['name']
			);
		}
		return $Data;
	}

	public function news ($id)
	{
		$Data = Array();
		$Data[] = Array(
			'link' => Seo::getSeo($this->registry->router->getCurrentController()),
			'title' => $this->trans('TXT_NEWS')
		);
		$sql = "SELECT
				CONCAT(:seo,'/',NT.newsid,'/',NT.seo) AS link,
				NT.topic AS title
				FROM newstranslation NT WHERE NT.newsid = :id AND NT.languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('seo', Seo::getSeo('news'));
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'link' => $rs['link'],
				'title' => $rs['title']
			);
		}
		return $Data;
	}

	public function staticcontent ($id)
	{
		$sql = "SELECT
				CONCAT(:seo,'/',CC.idcontentcategory) AS link,
				CCT.name AS title
				FROM contentcategory CC
				LEFT JOIN contentcategorytranslation CCT ON CCT.contentcategoryid = CC.idcontentcategory AND CCT.languageid = :languageid
				WHERE CC.idcontentcategory = :id OR CC.idcontentcategory = (SELECT contentcategoryid FROM contentcategory WHERE idcontentcategory = :id)";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('seo', Seo::getSeo('staticcontent'));
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'link' => $rs['link'],
				'title' => $rs['title']
			);
		}
		return $Data;
	}
}