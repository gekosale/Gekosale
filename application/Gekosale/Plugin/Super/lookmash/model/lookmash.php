<?php
/*
 * Kin Lane - @kinlane PHP Library to support xAuth for Instapapers REST API.
 * This is a strip down and rework of @abraham Twitter OAuth -
 * https://github.com/abraham/twitteroauth His was just so well written, it made
 * sense to reuse. Thanks @abraham!
 */

namespace Gekosale\Plugin;
use FormEngine;

class LookmashModel extends Component\Model
{
	
	public function addFields ($event, $request)
	{
	    $form = &$request['form'];
		
		$lookmash = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'lookmash_data',
			'label' => 'Integracja z Lookmash.com'
		)));
		
		$lookmash->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'lookmashlogin',
			'label' => 'ID konta Lookmash.com'
		)));
		
		$settings = $this->registry->core->loadModuleSettings('lookmash', (int)$request['id']);
		
		if (! empty($settings)){
			$populate = Array(
				'lookmash_data' => Array(
					'lookmashlogin' => $settings['lookmashlogin'],
				)
			);
			
			$event->setReturnValues($populate);
		}
	}

	public function saveSettings ($request)
	{
		$Settings = Array(
			'lookmashlogin' => $request['data']['lookmashlogin'],
		);
		
		$this->registry->core->saveModuleSettings('lookmash', $Settings, $request['id']);
	}
}