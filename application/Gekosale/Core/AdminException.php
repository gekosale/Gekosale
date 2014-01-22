<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: coreexception.class.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Core;

class AdminException extends BaseException
{

	public function __construct ($message, $code = 0, $messageToLogFile = NULL)
	{
		parent::__construct($message, $code, $messageToLogFile);
		if (__ENABLE_DEBUG__ == 0){
			App::redirect('admin/mainside');
		}
		$this->errorDesignPath = 'error/index/index.tpl';
		echo "<pre>";
		echo $this->errorText;
		die();
	}
}