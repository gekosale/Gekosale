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
 */
namespace Gekosale\Plugin;

class ExchangeController extends Component\Controller\Frontend
{
	public function index ()
	{
		@set_time_limit(7200);
		$this->model = App::getModel('exchangexml');

		$operation = $this->model->getOperationBySha1($this->registry->core->getParam());

		if ($operation) {
			if ($operation['periodically'] == 1 && strtotime($operation['lastdate']) + $operation['interval'] > time())
			{
				header('Content-type: text/plain');
				echo 'Can\'t execute operation - ' . (strtotime($operation['lastdate']) + $operation['interval'] - time()) . ' seconds left';
				return;
			}
			
			$this->model->runOperation($operation['idexchange']);
			echo 'Done';
			
			return;
		}

		echo 'Error';
		App::redirect();
	}

}