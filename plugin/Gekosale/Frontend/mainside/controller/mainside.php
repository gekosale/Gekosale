<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 535 $
 * $Author: gekosale $
 * $Date: 2011-09-12 20:12:30 +0200 (Pn, 12 wrz 2011) $
 * $Id: mainside.php 535 2011-09-12 18:12:30Z gekosale $
 */

namespace Gekosale;

class MainsideController extends Component\Controller\Frontend
{

	public function index ()
	{
		$this->Render('Mainside');
	}
	
}