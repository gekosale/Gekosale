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
 * $Id: confirmation.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class confirmationModel extends Component\Model
{

	public function getURLParamToValidOrderLink ($orderLink)
	{
		$sql = "SELECT 
					idorder, 
					orderstatusid, 
					paymentmethodid, 
					activelink
				FROM `order` 
				WHERE activelink= :orderlink";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('orderlink', $orderLink);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		try{
			if ($rs){
				$Data = Array(
					'idorder' => $rs['idorder'],
					'paymentmethodid' => $rs['paymentmethodid'],
					'orderstatusid' => $rs['orderstatusid'],
					'activelink' => $rs['activelink']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_NO_EXIST'));
		}
		return $Data;
	}

	public function changeOrderStatus ($idorder, $paymentmethodid)
	{
		$statusorder = $this->getCurrentOrderStatus($paymentmethodid);
		$upateOrder = 0;
		if (is_array($statusorder) && $statusorder != NULL){
			$sql = "UPDATE `order` SET 
						activelink = 1, 
						orderstatusid = :orderstatusid
					WHERE idorder = :idorder";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('idorder', $idorder);
			$stmt->bindValue('orderstatusid', $statusorder['orderstatusid']);
			try{
				$stmt->execute();
				$rs = $stmt->fetch();
				$upateOrder = 1;
			}
			catch (Exception $e){
				throw new FrontendException('Error while executing query (update order)- confirmation model');
			}
		}
		return $upateOrder;
	}

	public function getCurrentOrderStatus ($paymentMethodId)
	{
		$sql = "SELECT 
					orderstatusid, 
					paymentmethodid
				FROM paymentmethodorderstatus
				WHERE paymentmethodid = :paymentmethodid AND hierarchy = 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('paymentmethodid', $paymentMethodId);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		try{
			if ($rs){
				$Data = Array(
					'orderstatusid' => $rs['orderstatusid'],
					'paymentmethodid' => $rs['paymentmethodid']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_NO_EXIST'));
		}
		return $Data;
	}
}