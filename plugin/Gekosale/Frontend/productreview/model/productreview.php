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
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: productreview.php 576 2011-10-22 08:23:55Z gekosale $
 */
namespace Gekosale;

class ProductReviewModel extends Component\Model
{

	public function getProductReviews ($productid)
	{
		$sql = "SELECT
					review,
					nick,
					adddate,
					idproductreview
				FROM productreview
				WHERE productid = :productid AND viewid = :viewid AND enable = 1
				GROUP BY idproductreview
				ORDER BY adddate ASC
		";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productid);
		$stmt->bindValue('viewid', Helper::getViewId());
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$sql2 = "SELECT 
							PR.rangetypeid,
							PR.value,
							RTT.name
						FROM
							productrange PR
							LEFT JOIN rangetypetranslation RTT ON RTT.rangetypeid = PR.rangetypeid AND RTT.languageid = :languageid
						WHERE
							PR.productreviewid = :reviewid";
				$stmt2 = Db::getInstance()->prepare($sql2);
				$stmt2->bindValue('reviewid', $rs['idproductreview']);
				$stmt2->bindValue('languageid', Helper::getLanguageId());
				$rangesRes = $stmt2->execute();
				$ranges = Array();
				while ($rangesRes = $stmt2->fetch()){
					$ranges[] = Array(
						'rangetypeid' => $rangesRes['rangetypeid'],
						'value' => $rangesRes['value'],
						'name' => $rangesRes['name']
					);
				}
				$Data[] = Array(
					'nick' => $rs['nick'],
					'review' => $rs['review'],
					'adddate' => $rs['adddate'],
					'ranges' => $ranges
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException('Error while doing sql query- getProductReviews (productreview)');
		}
		return $Data;
	}

	public function getHumanOpinionsCount ($count)
	{
		if ($count == 0){
			return sprintf($this->trans('TXT_OPINIONS_QTY0'), $count);
		}
		if ($count == 1){
			return sprintf($this->trans('TXT_OPINIONS_QTY1'), $count);
		}
		else{
			$sufix = $count % 100;
			if ($sufix > 11 && $sufix < 15){
				return sprintf($this->trans('TXT_OPINIONS_QTY2'), $count);
			}
			else{
				$modulo = $sufix % 10;
				switch ($modulo) {
					case 0:
					case 1:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
						return sprintf($this->trans('TXT_OPINIONS_QTY2'), $count);
					case 2:
					case 3:
					case 4:
					default:
						return sprintf($this->trans('TXT_OPINIONS_QTY3'), $count);
				}
			}
		}
	}
}