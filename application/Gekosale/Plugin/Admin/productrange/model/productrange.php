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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: productrange.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class ProductRangeModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('productrange', Array(
			'idproductreview' => Array(
				'source' => 'PR.idproductreview'
			),
			'productname' => Array(
				'source' => 'PT.name'
			),
			'nick' => Array(
				'source' => 'PR.nick',
				'processFunction' => Array(
					$this,
					'parseContent'
				)
			),
			'client' => Array(
				'source' => 'CONCAT(\'<strong>\',CONVERT(LOWER(AES_DECRYPT(CD.surname, :encryptionkey)) USING utf8),\' \',CONVERT(LOWER(AES_DECRYPT(CD.firstname, :encryptionkey)) USING utf8),\'</strong><br />\',CONVERT(LOWER(AES_DECRYPT(CD.email, :encryptionkey)) USING utf8))'
			),
			'rating' => Array(
				'source' => 'IF(AVG(PRR.value) IS NULL, 0, AVG(PRR.value))',
				'filter' => 'having'
			),
			'review' => Array(
				'source' => 'PR.review',
				'processFunction' => Array(
					$this,
					'parseContent'
				)
			),
			'enable' => Array(
				'source' => 'PR.enable'
			)
		));

		$datagrid->setFrom('
			productreview PR
			LEFT JOIN productrange PRR ON PRR.productreviewid = PR.idproductreview
			LEFT JOIN clientdata CD ON CD.clientid = PR.clientid
			LEFT JOIN producttranslation PT ON PR.productid = PT.productid AND PT.languageid = :languageid
		');

		$datagrid->setGroupBy('
			PR.idproductreview
		');
	}

	public function parseContent ($content)
	{
		return strtr($content, array(
			"\r\n" => ' ',
			"\n" => ' ',
			"\r" => ' ',
			"\t" => ' ',
			"\x00" => ' '
		));
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getProductRangeForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteProductRange ($datagrid, $id)
	{
		$this->deleteProductRange($id);
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function disableOpinion ($datagrid, $id)
	{
		$sql = 'UPDATE productreview SET enable = 0	WHERE idproductreview = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function enableOpinion ($datagrid, $id)
	{
		$sql = 'UPDATE productreview SET enable = 1	WHERE idproductreview = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function deleteProductRange ($id)
	{
		DbTracker::deleteRows('productreview', 'idproductreview', $id);
	}

	public function getOpinion ($id)
	{
		$sql = "SELECT nick, review, enable FROM productreview WHERE idproductreview = :idproductreview";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idproductreview', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if(!$rs) {
			throw new CoreException('Opinion ID:' . $id . ' not exists');
		}

		return $rs;
	}

	public function editOpinion ($data, $id)
	{
		$sql = "UPDATE productreview SET review = :review, enable = :enable WHERE idproductreview = :idproductreview";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('review', $data['data']['review']);
		$stmt->bindValue('enable', (int) $data['data']['enable']);
		$stmt->bindValue('idproductreview', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
	}
}