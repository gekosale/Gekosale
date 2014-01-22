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
 * $Id: feeds.php 484 2011-09-07 11:42:04Z gekosale $
 */
namespace Gekosale\Plugin;

class KodyrabatoweController extends Component\Controller\Frontend
{

	public function index ()
	{
		if (! empty($_GET["tduid"])){
			$cookieDomain = "." . App::getHost();
			setcookie("TRADEDOUBLER", $_GET["tduid"], (time() + 3600 * 24 * 365), "/", $cookieDomain);
		}
		App::redirectUrl($this->registry->router->generate('frontend.home', true));
	}
}