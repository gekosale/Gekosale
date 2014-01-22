<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 263 $
 * $Author: gekosale $
 * $Date: 2011-07-24 16:23:40 +0200 (N, 24 lip 2011) $
 * $Id: cart.php 263 2011-07-24 14:23:40Z gekosale $
 */
namespace Gekosale\Plugin;

class FirmesController extends Component\Controller\Frontend
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->key = 'test';
		$this->module = $this->getParam();
		$this->model = App::getModel('firmes');
	}

	public function index ()
	{
		$this->handle() or print 'no request';
	}

	public function handle ()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] != 'application/json'){
			return false;
		}
		
		$request = json_decode(file_get_contents('php://input'), true);
		if ($request['key'] !== $this->key){
			$response = array(
				'result' => 'authentification',
				'error' => 'authentification failed'
			);
			
			header('content-type: text/javascript');
			echo json_encode($response);
			die();
		}
		
		try{
			if ($result = call_user_func_array(array(
				App::getModel('firmes/' . $this->module),
				$request['method']
			), $request['params'])){
				$response = array(
					'result' => $result,
					'error' => NULL
				);
			}
			else{
				$response = array(
					'result' => NULL,
					'error' => 'unknown method ' . $request['method'] . ' or incorrect parameters ' . json_encode($request['params']) . ' RESULT: ' . json_encode($result)
				);
			}
		}
		catch (Exception $e){
			$response = array(
				'result' => NULL,
				'error' => $e->getMessage()
			);
		}
		header('content-type: text/javascript');
		echo json_encode($response);
		return true;
	}
}