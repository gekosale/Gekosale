<?php
/*
 * Kin Lane - @kinlane PHP Library to support xAuth for Instapapers REST API.
 * This is a strip down and rework of @abraham Twitter OAuth -
 * https://github.com/abraham/twitteroauth His was just so well written, it made
 * sense to reuse. Thanks @abraham!
 */
namespace Gekosale\Plugin;

use FormEngine;

class KodyrabatoweModel extends Component\Model
{

	public function addFields ($event, $request)
	{
	    $form = &$request['form'];
	    
		$kodyrabatowe = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'kodyrabatowe_data',
			'label' => 'Integracja z Kodyrabatowe.pl'
		)));
		
		$kodyrabatowe->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'organization',
			'label' => 'ID konta'
		)));
		
		$kodyrabatowe->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'checksumCode',
			'label' => 'Kod kontrolny'
		)));
		
		$kodyrabatowe->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'event',
			'label' => 'Numer zdarzenia sprzedaży'
		)));
		
		$settings = $this->registry->core->loadModuleSettings('kodyrabatowe', (int) $request['id']);
		
		if (! empty($settings)){
			$populate = Array(
				'kodyrabatowe_data' => Array(
					'organization' => $settings['organization'],
					'checksumCode' => $settings['checksumCode'],
					'event' => $settings['event']
				)
			);
			
			$event->setReturnValues($populate);
		}
	}

	public function saveSettings ($request)
	{
		$Settings = Array(
			'organization' => $request['data']['organization'],
			'checksumCode' => $request['data']['checksumCode'],
			'event' => $request['data']['event']
		);
		
		$this->registry->core->saveModuleSettings('kodyrabatowe', $Settings, $request['id']);
	}

	protected function formatPrice ($price)
	{
		return number_format($price, 2, '.', '');
	}

	public function getAffirmeoTrackBackUrl ($Data)
	{
		$settings = $this->registry->core->loadModuleSettings('kodyrabatowe', Helper::getViewId());
		if (! empty($_COOKIE["TRADEDOUBLER"]) && ! empty($settings) && strlen($settings['organization']) > 0 && strlen($settings['checksumCode']) > 0 && strlen($settings['event']) > 0){
			
			if (isset($Data['orderData']['globalPricePromo'])){
				$kwota = $this->formatPrice($Data['orderData']['globalPricePromo']);
			}
			else{
				$kwota = $this->formatPrice($Data['orderData']['globalPrice']);
			}
			
			$organization = $settings['organization'];
			$checksumCode = $settings['checksumCode'];
			$orderValue = $kwota;
			$currency = "PLN";
			$event = $settings['event'];
			$orderNumber = $Data['orderId'];
			$isSale = true;
			$isSecure = true;
			
			$aff_uid = "";
			
			$aff_uid = $_COOKIE["TRADEDOUBLER"];
			
			$checksum = "v04" . md5($checksumCode . $orderNumber . $orderValue);
			
			$url = "http://affirmeo.com/trackbacks/pixel/org:{$organization}/checksum:{$checksumCode}/total:{$orderValue}/curr:{$currency}/event:{$event}/order_id:{$orderNumber}/affuid:{$aff_uid}";
			return '<img src="' . $url . '" />';
		}
	}
}