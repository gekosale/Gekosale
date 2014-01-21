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
 * $Id: newsletter.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale;
use FormEngine;

class NewsletterModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('newsletter', Array(
			'idnewsletter' => Array(
				'source' => 'idnewsletter'
			),
			'name' => Array(
				'source' => 'name'
			),
			'subject' => Array(
				'source' => 'subject'
			),
			'email' => Array(
				'source' => 'email'
			),
			'adddate' => Array(
				'source' => 'adddate'
			),
		));

		$datagrid->setFrom('
			newsletter
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getNewsletterForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteNewsletter ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteNewsletter'
		), $this->getName());
	}

	public function deleteNewsletter ($id)
	{
		DbTracker::deleteRows('newsletter', 'idnewsletter', $id);
	}

	public function getNewsletterData ($id)
	{
		$sql = "SELECT
					name,
					email,
					subject,
					htmlform,
					textform,
					recipients
				FROM newsletter
				WHERE idnewsletter = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'name' => $rs['name'],
				'email' => $rs['email'],
				'subject' => $rs['subject'],
				'htmlform' => $rs['htmlform'],
				'textform' => $rs['textform'],
				'recipient' => explode(',', $rs['recipients'])
			);

		}
		else{
			throw new CoreException($this->trans('ERR_NEWSLETTER_NO_EXIST'));
		}
		return $Data;
	}

	public function clientnewsletterhistory ($id)
	{
		$sql = 'SELECT clientnewsletterid
					FROM clientnewsletterhistory
					WHERE newsletterid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['clientnewsletterid'][] = $rs['clientnewsletterid'];
		}
		return $Data;
	}

	public function clientgroupnewsletterhistory ($id)
	{
		$sql = 'SELECT clientgroupid
					FROM clientgroupnewsletterhistory
					WHERE newsletterid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['clientgroupid'][] = $rs['clientgroupid'];
		}
		return $Data;
	}

	public function addNewsletter ($Data)
	{
		if (empty($Data['recipient'])) {
			$Data['recipient'] = array();
		}

		$sql = 'INSERT INTO newsletter (name, email, subject, textform, htmlform, recipients)
				VALUES (:name, :email, :subject, :textform, :htmlform, :recipients)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('email', $Data['email']);
		$stmt->bindValue('textform', $Data['textform']);
		$stmt->bindValue('subject', $Data['subject']);
		$stmt->bindValue('htmlform', $Data['htmlform']);
		$stmt->bindValue('recipients', implode(',', $Data['recipient']));
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWSLETTER_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewNewsletterHistory ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newsletterid = $this->addNewsletter($Data);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWSLETTER_ADD'), 112, $e->getMessage());
		}

		Db::getInstance()->commit();
		return $newsletterid;
	}

	protected function updateClientGroupNewsletterHistory ($Data, $id)
	{
		DbTracker::deleteRows('clientgroupnewsletterhistory', 'newsletterid', $id);

		if (is_array($Data['groups'])){
			foreach ($Data['groups'] as $value){
				$sql = 'INSERT INTO clientgroupnewsletterhistory (clientgroupid, newsletterid)
							VALUES (:clientgroupid, :newsletterid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('clientgroupid', $value);
				$stmt->bindValue('newsletterid', $id);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CLIENT_GROUP_NEWSLETTER_ADD'), 112, $e->getMessage());
				}
			}
		}
		else{
			$sql = 'INSERT INTO clientgroupnewsletterhistory (clientgroupid, newsletterid)
						VALUES (:clientgroupid, :newsletterid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', NULL);
			$stmt->bindValue('newsletterid', $id);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_GROUP_NEWSLETTER_ADD'), 11, $e->getMessage());
			}
		}

	}

	protected function updateClientNewsletterHistory ($Data, $id)
	{
		DbTracker::deleteRows('clientgroupnewsletterhistory', 'newsletterid', $id);

		if (is_array($Data['clients'])){
			foreach ($Data['clients'] as $value){
				$sql = 'INSERT INTO clientnewsletterhistory (clientnewsletterid, newsletterid)
							VALUES (:clientnewsletterid, :newsletterid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('clientnewsletterid', $value);
				$stmt->bindValue('newsletterid', $id);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CLIENT_NEWSLETTER_ADD'), 112, $e->getMessage());
				}
			}
		}
		else{
			$sql = 'INSERT INTO clientnewsletterhistory (clientnewsletterid, newsletterid)
						VALUES (:clientnewsletterid, :newsletterid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientnewsletterid', NULL);
			$stmt->bindValue('newsletterid', $id);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_NEWSLETTER_ADD'), 11, $e->getMessage());
			}
		}
	}

	public function updateNewsletter ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->editNewsletter($Data, $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWSLETTER_EDIT'), 3002, $e->getMessage());
		}

		Db::getInstance()->commit();
	}

	public function editNewsletter ($Data, $id)
	{
		if (empty($Data['recipient'])) {
			$Data['recipient'] = array();
		}

		$sql = 'UPDATE newsletter SET
					name=:name,
					email=:email,
					textform=:textform,
					subject=:subject,
					htmlform=:htmlform,
					recipients=:recipients
				WHERE
					idnewsletter =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('email', $Data['email']);
		$stmt->bindValue('htmlform', $Data['htmlform']);
		$stmt->bindValue('subject', $Data['subject']);
		$stmt->bindValue('textform', $Data['textform']);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('recipients', implode(',', $Data['recipient']));
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWSLETTER_EDIT'), 13, $e->getMessage());
			return false;
		}
		return true;
	}

	public function addClientGroupNewsletterHostory ($Data, $newsletterid)
	{
		if (is_array($Data['groups'])){
			foreach ($Data['groups'] as $value){
				$sql = 'INSERT INTO clientgroupnewsletterhistory (clientgroupid, newsletterid)
							VALUES (:clientgroupid, :newsletterid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('clientgroupid', $value);
				$stmt->bindValue('newsletterid', $newsletterid);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CLIENT_GROUP_NEWSLETTER_ADD'), 112, $e->getMessage());
				}
			}
		}
		else{
			$sql = 'INSERT INTO clientgroupnewsletterhistory (clientgroupid, newsletterid)
						VALUES (:clientgroupid, :newsletterid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', NULL);
			$stmt->bindValue('newsletterid', $newsletterid);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_GROUP_NEWSLETTER_ADD'), 11, $e->getMessage());
			}
		}
	}

	public function addClientNewsletterHostory ($Data, $newsletterid)
	{
		if (is_array($Data['clients'])){
			foreach ($Data['clients'] as $value){
				$sql = 'INSERT INTO clientnewsletterhistory (clientnewsletterid, newsletterid)
							VALUES (:clientnewsletterid, :newsletterid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('clientnewsletterid', $value);
				$stmt->bindValue('newsletterid', $newsletterid);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CLIENT_NEWSLETTER_ADD'), 112, $e->getMessage());
				}
			}
		}
		else{
			$sql = 'INSERT INTO clientnewsletterhistory (clientnewsletterid, newsletterid)
						VALUES (:clientnewsletterid, :newsletterid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientnewsletterid', NULL);
			$stmt->bindValue('newsletterid', $newsletterid);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_GROUP_NEWSLETTER_ADD'), 11, $e->getMessage());
			}
		}
	}
}