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
 * $Revision: 222 $
 * $Author: gekosale $
 * $Date: 2011-06-25 15:20:08 +0200 (So, 25 cze 2011) $
 * $Id: categorylist.php 222 2011-06-25 13:20:08Z gekosale $
 */
namespace Gekosale\Plugin;

class ProducerListModel extends Component\Model
{

	public function getProducerAll ()
	{
		$sql = 'SELECT
					P.idproducer AS id,
					PT.name,
					PT.description,
					PT.seo,
					P.photoid,
					COUNT(PROD.idproduct) AS totalproducts
				FROM producer P
				LEFT JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :language
				LEFT JOIN producerview PV ON P.idproducer = PV.producerid
				LEFT JOIN product PROD ON PROD.producerid = P.idproducer AND PROD.enable = 1
				WHERE PV.viewid = :viewid AND PT.seo != \'\'
				GROUP BY P.idproducer
				HAVING totalproducts > 0';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('language', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'description' => $rs['description'],
				'link' => $this->registry->router->generate('frontend.producerlist', true, Array(
					'param' => $rs['seo']
				)),
				'photo' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($rs['photoid'], 0))
			);
		}
		return $Data;
	}
}