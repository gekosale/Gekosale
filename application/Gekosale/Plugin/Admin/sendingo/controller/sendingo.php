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

class SendingoController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->model = App::getModel('sendingo');
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

		$settings = $this->registry->core->loadModuleSettings('sendingo');

		$this->renderLayout(array(
			'inactive' => empty($settings['auth_token'])
		));
	}

	public function sync ()
	{
		$settings = $this->registry->core->loadModuleSettings('sendingo');

		if (empty($settings['auth_token'])) {
			App::getContainer()->get('session')->setVolatileMessage('Moduł Sendingo nie jest aktywny.');
		}
		else {
			$this->model->sendingoSyncEmails();
			App::getContainer()->get('session')->setVolatileMessage('Dane zostały poprawnie zsynchronizowane.');
		}

		App::redirect(__ADMINPANE__ . '/sendingo');
	}
}