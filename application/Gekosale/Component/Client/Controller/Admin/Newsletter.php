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
 * $Revision: 464 $
 * $Author: gekosale $
 * $Date: 2011-08-31 08:19:48 +0200 (Śr, 31 sie 2011) $
 * $Id: clientnewsletter.php 464 2011-08-31 06:19:48Z gekosale $ 
 */
namespace Gekosale\Component\Clientnewsletter\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;

use FormEngine;
use sfEvent;

class Newsletter extends Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->model = App::getModel('clientnewsletter');
	}

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteClientNewsletter',
			$this->model,
			'doAJAXDeleteClientNewsletter'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllClientNewsletter',
			$this->model,
			'getClientNewsletterForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'disableClientNewsletter',
			$this->model,
			'doAJAXDisableClientNewsletter'
		));
		
		$this->registry->xajax->registerFunction(array(
			'enableClientNewsletter',
			$this->model,
			'doAJAXEnableClientNewsletter'
		));
		
		$this->renderLayout();
	}

	public function view ()
	{
		$Data = App::getModel('clientnewsletter')->getClientsNewsletterAll();
		$filename = 'subscribers_' . date('Y_m_d_H_i_s') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$fp = fopen("php://output", 'w');
		foreach ($Data as $key => $values){
			fputcsv($fp, $values);
		}
		fclose($fp);
		exit();
	}
}