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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: seo.php 612 2011-11-28 20:02:10Z gekosale $ 
 */

namespace Gekosale;

class SeoModel extends Component\Model
{

	public function getMetadataForPage ()
	{
		$controller = $this->registry->router->getCurrentController();
		$Data = Array();
		$sql = "SELECT
					VT.keyword_title,
					C.description,
					VT.keyword,
					VT.keyword_description,
					VT.additionalmeta
				FROM controller C
				LEFT JOIN viewtranslation VT ON VT.viewid = :viewid AND VT.languageid = :languageid
				WHERE C.name = :controller AND C.mode = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('controller', $controller); 
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if ($rs['keyword_title'] == NULL || $rs['keyword_title'] == ''){
				$keyword_title = ($controller == 'mainside') ? App::getContainer()->get('session')->getActiveShopName() : $this->trans($rs['description']);
			}
			else{
				$keyword_title = $rs['keyword_title'];
			}
			$title = ($controller == 'mainside') ? $keyword_title : $this->trans($rs['description']);
			$Data = Array(
				'keyword_title' => $title,
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description'],
				'additionalmeta' => $rs['additionalmeta'],
			);
		}
		return $Data;
	}
	
	public function clearSeoUTF ($name)
	{
		$seo = Core::clearUTF(trim($name));
		$seo = preg_replace('/[^A-Za-z0-9\-\s\s+]/', '', $seo);
		$seo = Core::clearSeoUTF($seo);
		return str_replace('/', '', strtolower($seo));
	}
	
	public function doRefreshSeoCategory ()
	{
		Db::getInstance()->beginTransaction();
		$sql = 'SELECT idcategory FROM category';
		$stmt = $stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'SELECT
						CT.languageid,
						GROUP_CONCAT(SUBSTRING(IF(CT.categoryid = :id, CT.name, LOWER(CT.name)), 1) ORDER BY C.order DESC SEPARATOR \'/\') AS seo
					FROM categorytranslation CT
					LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
					WHERE C.categoryid = :id 
					GROUP BY C.categoryid, CT.languageid
					';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('id', $rs['idcategory']);
			$stmt2->execute();
			$rs2 = $stmt2->fetch();
			if ($rs2){
				
				$seo = Core::clearSeoUTF($rs2['seo']);
				
				$sql3 = 'UPDATE categorytranslation SET
							seo = :seo
						WHERE
							categoryid = :categoryid AND languageid = :languageid
						';
				
				$stmt3 = Db::getInstance()->prepare($sql3);
				$stmt3->bindValue('categoryid', $rs['idcategory']);
				$stmt3->bindValue('languageid', $rs2['languageid']);
				$stmt3->bindValue('seo', strtolower($seo));
				$stmt3->execute();
			}
		}
		
		Db::getInstance()->commit();
		App::getModel('category')->flushCache();
	}	
}