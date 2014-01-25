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
 * $Id: payment.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Component\Payment\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend;

class Payment extends Frontend
{

	public function index ()
	{
		$this->Render('Payment');
	}

	public function accept ()
	{
		$this->Render('Payment', 'accept');
	}

	public function cancel ()
	{
		$this->Render('Payment', 'cancel');
	}

	public function confirm ()
	{
		$this->Render('Payment', 'confirm');
	}

	public function report ()
	{
		$paymentMethodData = App::getModel('payment/' . $this->getParam())->reportPayment();
	}
}