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
 * $Id: helper.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Core;

class Helper
{

	public static function getViewId ()
	{
		if(!App::getRegistry()->router) // CLI compatibility
			return 0;
		
		if (App::getRegistry()->router->getMode() == 0){
			return App::getContainer()->get('session')->getActiveMainsideViewId();
		}
		else{
			return App::getContainer()->get('session')->getActiveViewId();
		}
	}

	public static function getViewIds ()
	{
		if (self::getViewId() > 0){
			return Array(
				self::getViewId()
			);
		}
		else{
			return array_merge(Array(
				0
			), App::getModel('stores')->getViewForHelperAll());
		}
	}

	public static function getViewIdsDefault ()
	{
		return App::getModel('stores')->getViewForHelperAll();
	}

	public static function getViewIdsAsString ()
	{
		return implode(',', self::getViewIds());
	}

	public static function getLanguageId ()
	{
		return App::getContainer()->get('session')->getActiveLanguageId();
	}

	public static function getEncryptionKey ()
	{
		return App::getContainer()->get('session')->getActiveEncryptionKeyValue();
	}

	public static function setViewId ($id)
	{
		if (App::getRegistry()->router->getMode() == 0){
			return App::getContainer()->get('session')->setActiveMainsideViewId($id);
		}
		else{
			return App::getContainer()->get('session')->setActiveViewId($id);
		}
	}

	public static function getStoreId ()
	{
		if (App::getRegistry()->router->getMode() == 0){
			return App::getContainer()->get('session')->getActiveMainsideStoreId();
		}
		else{
			return App::getContainer()->get('session')->getActiveStoreId();
		}
	}

	public static function setStoreId ($id)
	{
		if (App::getRegistry()->router->getMode() == 0){
			return App::getContainer()->get('session')->setActiveMainsideStoreId($id);
		}
		else{
			return App::getContainer()->get('session')->setActiveStoreId($id);
		}
	}

}
