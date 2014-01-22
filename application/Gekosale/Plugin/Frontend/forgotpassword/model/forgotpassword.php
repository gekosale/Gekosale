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
 * $Id: forgotpassword.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class ForgotPasswordModel extends Component\Model
{

	public function authProccess ($login)
	{
		$hash = new \PasswordHash\PasswordHash();		
		$sql = 'SELECT idclient, disable FROM client WHERE login = :login AND viewid = :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($login));
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if ($rs['disable'] == 0){
				return $rs['idclient'];
			}
			else{
				return - 1;
			}
		}
		else{
			return 0;
		}
	}

	public function validateLink ($param)
	{
		$sql = 'SELECT idclient,disable FROM client WHERE SHA1(AES_ENCRYPT(login, :encryptionkey)) = :link';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('link', $param);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if ($rs['disable'] == 0){
				return $rs['idclient'];
			}
			else{
				return - 1;
			}
		}
		else{
			return 0;
		}
	}

	public function generateLink ($email)
	{
		$sql = 'SELECT SHA1(AES_ENCRYPT(SHA1(:email), :encryptionkey)) AS link';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('email', $email);
		$stmt->execute();
		$rs = $stmt->fetch();
		return $rs['link'];
	}

	public function forgotPassword ($idclient, $pass)
	{
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'UPDATE client SET password = :password WHERE idclient = :idclient';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idclient', $idclient);
		$stmt->bindValue('password', $hash->HashPassword($pass));
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return true;
	}
}
