<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2012 WellCommerce sp. z o.o. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 279 $
 * $Author: gekosale $
 * $Date: 2011-07-28 23:13:43 +0200 (Cz, 28 lip 2011) $
 * $Id: app.class.php 279 2011-07-28 21:13:43Z gekosale $ 
 */
namespace Gekosale\Core;

class Server
{

	public function handle ($object)
	{
// 		$layer = $this->registry->loader->getCurrentLayer();
// 		$apiKey = $layer['apikey'];
		$apiKey = 'test';
		
		if (is_null($apiKey) || $apiKey == ''){
			return false;
		}
		
		if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] != 'application/json'){
			return false;
		}
		
		$request = json_decode(file_get_contents('php://input'), true);
		
		if ($request['key'] !== $apiKey){
			$response = array(
				'id' => $request['id'],
				'result' => NULL,
				'error' => 'authentification failed'
			);
			header('content-type: text/javascript');
			echo json_encode($response);
			die();
		}
		try{
			if ($result = @call_user_func_array(array(
				$object,
				$request['method']
			), $request['params'])){
				$response = array(
					'id' => $request['id'],
					'result' => $result,
					'error' => NULL
				);
			}
			else{
				$response = array(
					'id' => $request['id'],
					'result' => NULL,
					'error' => 'unknown method '.$request['method'].' or incorrect parameters',
				);
			}
		}
		catch (Exception $e){
			$response = array(
				'id' => $request['id'],
				'result' => NULL,
				'error' => $e->getMessage()
			);
		}
		
		if (! empty($request['id'])){
			header('content-type: text/javascript');
			echo json_encode($response);
		}
		return true;
	}
}