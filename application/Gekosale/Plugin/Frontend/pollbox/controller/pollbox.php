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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: pollbox.php 612 2011-11-28 20:02:10Z gekosale $
 */

namespace Gekosale\Plugin;

class PollBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
	}

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'setAnswersChecked',
			App::getModel('pollbox'),
			'setAJAXAnswersMethodChecked'
		));
		$poll = App::getModel('PollBox')->getPoll();
		$clientId = App::getContainer()->get('session')->getActiveClientid();
		$this->show = false;
		
		if (isset($poll['idpoll'])){
			$this->show = true;
			$answers = App::getModel('PollBox')->checkAnswers($poll['idpoll']);
			$check = 0;
			$maxQty = 0;
			foreach ($answers as $value){
				if (! empty($value['qty']['clientid']) && $value['qty']['clientid'] == $clientId){
					$check = 1;
				}
				$maxQty = max($maxQty, $value['qty']['qty']);
			}
			foreach ($answers as &$value){
				if ($maxQty){
					$value['qty']['percentage'] = ceil($value['qty']['qty'] / $maxQty * 100);
				}
				else{
					$value['qty']['percentage'] = 0;
				}
			}
			
			$this->registry->template->assign('check', $check);
			$this->registry->template->assign('poll', $poll);
			$this->registry->template->assign('answers', $answers);
		}
		$this->registry->template->assign('clientdata', $clientId);
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	public function boxVisible ()
	{
		return true;
	}

}