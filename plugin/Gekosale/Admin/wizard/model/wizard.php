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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: updater.php 619 2011-12-19 21:09:00Z gekosale $ 
 */

namespace Gekosale;
use sfEvent;

class WizardModel extends Component\Model
{
	
	const WIZARD_STEPS = 6;
	
	/**
	* Process wizard which helps user to open shop. Wizard after closing or passing thru is never shown again
	*/
	
	public function process($event, $request){
		
		
		$settings = $this->getCore()->loadModuleSettings('wizard');
		$currentStep = @$settings['step'];
		$wizardDisabled = @$settings['disabled'];
		
		if(!$wizardDisabled) {
			
			$request = App::getRequest();
			if(strpos($request->getPathInfo(), 'admin/product') !== FALSE && $currentStep  <= 1){
				$currentStep = 2;
			}

			if(strpos($request->getPathInfo(), 'pagescheme/edit') !== FALSE && $currentStep == 2){
				$currentStep = 3;
			}

			if(strpos($request->getPathInfo(), 'contentcategory') !== FALSE && $currentStep == 3){
				$currentStep = 4;
			}

			if(strpos($request->getPathInfo(), 'admin/paymentmethod') !== FALSE && $currentStep == 4){
				$currentStep = 5;
			}

			if(strpos($request->getPathInfo(), 'admin/dispatchmethod') !== FALSE && $currentStep == 5){
				$currentStep = 6;
			}

			if(strpos($request->getPathInfo(), 'admin/view') !== FALSE && $currentStep == 6){
				$currentStep = 7;
			}

			
		}
		
		if($currentStep > self::WIZARD_STEPS) {
			$wizardDisabled = true;
		}
		
		
		$this->getCore()->saveModuleSettings('wizard', array('step' => $currentStep, 'disabled' => $wizardDisabled ));
		
		
		$event->setReturnValues(array(
			'wizard_step' => intval($currentStep),
			'wizard_disabled' => intval($wizardDisabled)
		));
				
		
	}
}