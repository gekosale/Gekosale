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
namespace Gekosale\Frontend\Productcompare\Storage;

use Gekosale\App;
use Gekosale\Db;
use Gekosale\Helper;
use Gekosale\Session;

class DbModel extends \Gekosale\Component\Model
{
	private $clientId;
	private $viewId;

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->clientId = App::getContainer()->get('session')->getActiveClientid();
		$this->viewId = (Helper::getViewId() > 0) ? Helper::getViewId() : App::getRegistry()->loader->getLayerViewId();
	}

	public function getProductIds ()
	{
		$sql = "SELECT `productid` FROM `productcompare`
                WHERE `clientid` = :clientid AND `viewid` = :viewid";
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $this->clientId);
		$stmt->bindValue('viewid', $this->viewId);
		
		try{
			$stmt->execute();
			$ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage(''));
		}
		
		return $ids;
	}

	public function addProduct ($productId)
	{
		$sql = "INSERT IGNORE INTO `productcompare`
                VALUES (NULL, :productid, :clientid, :viewid)";
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productId);
		$stmt->bindValue('clientid', $this->clientId);
		$stmt->bindValue('viewid', $this->viewId);
		
		try{
			return $stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage(''));
		}
	}

	public function deleteProduct ($productId)
	{
		$sql = 'DELETE FROM `productcompare`
                WHERE clientid = :clientid
                AND viewid = :viewid
                AND productid = :productid';
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $this->clientId);
		$stmt->bindValue('viewid', $this->viewId);
		$stmt->bindValue('productid', $productId);
		
		try{
			return $stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage(''));
		}
	}

	public function deleteAllProducts ()
	{
		$sql = 'DELETE FROM `productcompare`
                WHERE clientid = :clientid
                AND viewid = :viewid';
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $this->clientId);
		$stmt->bindValue('viewid', $this->viewId);
		
		try{
			return $stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage(''));
		}
	}
}