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
 * $Revision: 484 $
 * $Author: gekosale $
 * $Date: 2011-09-07 13:42:04 +0200 (Śr, 07 wrz 2011) $
 * $Id: newsletter.php 484 2011-09-07 11:42:04Z gekosale $
 */
namespace Gekosale;

use xajaxResponse;

class NewsletterModel extends Component\Model
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	public function addAJAXClientAboutNewsletter ($email, $privacy)
	{
		$objResponse = new xajaxResponse();
		
		$privacy = ($privacy == 'true') ? 1 : 0;
		
		if (! preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/i', $email)){
			return $objResponse->script('GError("' . $this->trans('TXT_WRONG_EMAIL') . '")');
		}
		
		if ($privacy == 0){
			return $objResponse->script('GError("' . $this->trans('ERR_NEWSLETTER_PRIVACY') . '")');
		}
		
		$checkEmailExists = $this->checkEmailIfExists($email);
		if ($checkEmailExists > 0){
			return $objResponse->assign("error", "innerHTML", $this->trans('ERR_EMAIL_NOT_EXISTS'));
		}
		else{
			$newId = $this->addClientAboutNewsletter($email);
			if ($newId > 0){
				$this->updateNewsletterActiveLink($newId, $email);
			}

			setcookie('newsletter', 1, time() + 24 * 60 * 60 * 30, '/', null, false, true);
			return $objResponse->assign("success", "innerHTML", $this->trans('TXT_RECEIVE_EMAIL_WITH_ACTIVE_LINK'));
		}
	}

	public function checkEmailIfExists ($email)
	{
		$idclientnewsletter = 0;
		$sql = "SELECT idclientnewsletter
					FROM clientnewsletter
					WHERE email= :email";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$idclientnewsletter = $rs['idclientnewsletter'];
			}
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return $idclientnewsletter;
	}

	public function addClientAboutNewsletter ($email, $viewId = 0)
	{
		$sql = 'INSERT INTO clientnewsletter (email, viewid)
					VALUES (:email, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId !== 0 ? $viewId : Helper::getViewId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		
		return Db::getInstance()->lastInsertId();
	}

	public function updateNewsletterActiveLink ($idclientnewsletter, $email)
	{
		$date = date("Ymd");
		$activelink = sha1($date . $idclientnewsletter);
		$inactivelink = sha1('unwanted' . $date . $idclientnewsletter);
		$sql = "UPDATE clientnewsletter
					SET 
						activelink= :activelink,
						inactivelink= :inactivelink
					WHERE idclientnewsletter= :idclientnewsletter";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('activelink', $activelink);
		$stmt->bindValue('inactivelink', $inactivelink);
		$stmt->bindValue('idclientnewsletter', $idclientnewsletter);
		try{
			$rs = $stmt->execute();
			$this->registry->template->assign('newsletterlink', $activelink);
			$this->registry->template->assign('unwantednewsletterlink', $inactivelink);
			
			App::getModel('mailer')->sendEmail(Array(
				'template' => 'addClientNewsletter',
				'email' => Array(
					$email
				),
				'bcc' => false,
				'subject' => $this->trans('TXT_REGISTRATION_NEWSLETTER'),
				'viewid' => Helper::getViewId()
			));
			
			return true;
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
	}

	public function deleteAJAXClientAboutNewsletter ($email)
	{
		$objResponse = new xajaxResponse();
		try{
			if ($email == NULL){
				$objResponse->assign("info", "innerHTML", '<strong><font color = "red">' . $this->trans('ERR_DELETE_CLIENT_FROM_NEWSLETTER') . '<br> ' . $this->trans('ERR_EMPTY_EMAIL_FORM_LOGIN') . '</font></strong>');
			}
			else{
				$checkmail = $this->checkMailAddress($email);
				if ($checkmail == true){
					$checkEmailExists = $this->checkEmailIfExists($email);
					if ($checkEmailExists > 0){
						$this->unsetClientAboutNewsletter($checkEmailExists, $email);
						$objResponse->assign("info", "innerHTML", '<strong><font color = "green">' . $this->trans('TXT_RECEIVE_EMAIL_WITH_DEACTIVE_LINK') . '</font></strong>');
						$objResponse->assign('newsletterformphrase', 'value', '');
					}
					else{
						$objResponse->assign("info", "innerHTML", '<strong><font color = "red">' . $this->trans('ERR_EMAIL_NO_EXISTS') . '</font></strong>');
						$objResponse->assign('newsletterformphrase', 'value', '');
					}
				}
				else{
					$objResponse->assign('newsletterformphrase', 'value', '');
					$objResponse->assign("info", "innerHTML", '<strong><font color = "red">' . $this->trans('ERR_WRONG_FORMAT') . '</font></strong>');
				}
			}
		}
		catch (Exception $fe){
			$objResponse->assign("info", "innerHTML", '<strong><font color = "red">' . $this->trans('ERR_DELETE_CLIENT_FROM_NEWSLETTER') . '</font></strong>');
		}
		return $objResponse;
	}

	public function unsetClientAboutNewsletter ($idclientnewsletter, $email)
	{
		$sql = "SELECT inactivelink
					FROM clientnewsletter
					WHERE idclientnewsletter= :idclientnewsletter";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idclientnewsletter', $idclientnewsletter);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$inactivelink = $rs['inactivelink'];
				$this->registry->template->assign('unwantednewsletterlink', $inactivelink);
				
				App::getModel('mailer')->sendEmail(Array(
					'template' => 'addClientNewsletter',
					'email' => Array(
						$email
					),
					'bcc' => false,
					'subject' => $this->trans('TXT_REGISTRATION_NEWSLETTER'),
					'viewid' => Helper::getViewId()
				));
				
				return true;
			}
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
	}

	public function deleteClientAboutNewsletter ($email, $viewId = 0)
	{
		$sql = "SELECT sendingoid FROM clientnewsletter WHERE email = :email AND viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$rs = $stmt->fetch();
		
		
		$viewId = $viewId!==0 ? $viewId : Helper::getViewId();
		$sql = 'DELETE FROM clientnewsletter WHERE email = :email AND viewid=:viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		try{
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
		
		if ($rs['sendingoid'] != NULL) {
			App::getModel('sendingo')->sendingoDeleteEmail($email, $viewId, $rs['sendingoid']);
		}
	}

	public function checkLinkToActivate ($activeLink)
	{
		$idclientnewsletter = 0;
		$sql = 'SELECT idclientnewsletter
					FROM clientnewsletter 
					WHERE activelink LIKE :activelink
					AND active=0';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('activelink', $activeLink);
		// $stmt->bindValue('viewid', Helper::getViewId());
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$idclientnewsletter = $rs['idclientnewsletter'];
			}
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
		return $idclientnewsletter;
	}

	public function checkInactiveNewsletter ($inactivelink)
	{
		$idclientnewsletter = 0;
		$sql = 'SELECT idclientnewsletter
					FROM clientnewsletter 
					WHERE inactivelink LIKE :inactivelink';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('inactivelink', $inactivelink);
		// $stmt->bindValue('viewid', Helper::getViewId());
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$idclientnewsletter = $rs['idclientnewsletter'];
			}
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
		return $idclientnewsletter;
	}

	public function changeNewsletterStatus ($id)
	{
		$sql = "SELECT email, viewid FROM clientnewsletter WHERE idclientnewsletter = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$rs = $stmt->fetch();
	
		$sql = "UPDATE clientnewsletter	SET 
					activelink= :activelink,
					active = 1
				WHERE idclientnewsletter = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('activelink', NULL);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		
		$sendingoModel = App::getModel('sendingo');

		$sendingoId = $sendingoModel->sendingoAddEmail($rs['email'], $rs['viewid']);
		$sendingoModel->updateSendingoId($rs['email'], $rs['viewid'], $sendingoId);
	}

	public function deleteClientNewsletter ($id)
	{
		DbTracker::deleteRows('clientnewsletter', 'idclientnewsletter', $id);
	}

	public function isNewsletterButton ()
	{
		if (isset($_COOKIE['newsletter'])) {
			return FALSE;
		}

		if (App::getContainer()->get('session')->getActiveClientid() !== 0 && $this->checkEmailIfExists(App::getContainer()->get('session')->getActiveClientEmail()) !== 0) {
			if( !isset($_COOKIE['newsletter'])) {
				setcookie('newsletter', 1, time() + 24 * 60 * 60 * 30, '/', null, false, true);
			}

			return FALSE;
		}

		return TRUE;
	}
}