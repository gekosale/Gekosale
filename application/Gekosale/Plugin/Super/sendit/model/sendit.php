<?php

namespace Gekosale\Plugin;

use FormEngine;
use Gekosale\App;
use Gekosale\Db;
use Gekosale\Helper;

class SenditModel extends Component\Model
{
	private $country_iso = array(
		'AT' => array('Austria','Austria'),
		'BE' => array('Belgia', 'Belgium'),
		'BG' => array('Bułgaria', 'Bulgaria'),
		'CZ' => array('Czechy', 'Czech Republic'),
		'DK' => array('Dania', 'Denmark'),
		'EE' => array('Estonia', 'Estonia'),
		'FI' => array('Finlandia', 'Finland'),
		'FR' => array('Francja', 'France'),
		'GR' => array('Grecja', 'Greece'),
		'ES' => array('Hiszpania', 'Spain'),
		'NL' => array('Holandia', 'Netherlands'),
		'LT' => array('Litwa', 'Lithuania'),
		'LU' => array('Luksemburg', 'Luxembourg'),
		'LV' => array('Łotwa', 'Latvia'),
		'DE' => array('Niemcy', 'Germany'),
		'PL' => array('Polska', 'Poland'),
		'PT' => array('Portugalia', 'Portugal'),
		'RO' => array('Rumunia', 'Romania'),
		'SK' => array('Słowacja', 'Slovakia'),
		'SI' => array('Słowenia', 'Slovenia'),
		'SE' => array('Szwecja', 'Sweden'),
		'HU' => array('Węgry', 'Hungary'),
		'GB' => array('Wielka Brytania', 'United Kingdom'),
		'IT' => array('Włochy', 'Italy'),
	);

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);

		require_once(ROOTPATH . 'lib' . DS . 'nusoap' . DS . 'nusoap.php');
	}

	public function addFields ($event, $request)
	{
		$form = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'sendit_data',
			'label' => 'Integracja z Sendit.pl'
		)));

		$loginData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'login_data',
			'label' => 'Logowanie'
		)));
		$loginData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_API_KEY',
			'label' => 'Klucz API (apiKey)',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Klucz API" nie może być puste'),
			)
		)));
		$loginData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_WSDL',
			'label' => 'Adres WSDL SenditApi',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Adres WSDL SenditApi" nie może być puste'),
			)
		)));
		$loginData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_LOGIN',
			'label' => 'Login do Sendit.pl',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Login do Sendit.pl" nie może być puste'),
			)
		)));
		$loginData->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'SENDIT_PASS',
			'label' => 'Hasło do Sendit.pl',
			'comment' => 'Pozostaw puste pole jeśli nie chcesz zmieniać hasła'
		)));

		////zakładka doane do wysyłki
		$ShipData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'ship_data',
			'label' => 'Dane do wysyłki'
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_NAME',
			'label' => 'Nazwa',
			'comment' => 'Imię i nazwisko lub nazwa firmy',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Nazwa" nie może być puste'),
			)
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_STREET',
			'label' => 'Ulica i nr domu',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Ulica i nr domu" nie może być puste'),
			)
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_ZIP',
			'label' => 'Kod pocztowy',
			'comment' => 'Format kodu XX-XXX',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Kod pocztowy" nie może być puste'),
			)
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_CITY',
			'label' => 'Miasto',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Miasto" nie może być puste'),
			)
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_PHONE',
			'label' => 'Telefon',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Telefon" nie może być puste'),
			)
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_EMAIL',
			'label' => 'Email',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Email" nie może być puste'),
			)
		)));
		$ShipData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'SENDIT_PERSON',
			'label' => 'Osoba kontaktowa',
			'rules' => Array(
				new FormEngine\Rules\Required('Pole "Osoba kontaktowa" nie może być puste'),
			)
		)));

		$settings = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());

		if (! empty($settings)){
			$populate = Array(
				'sendit_data' => array(
					'login_data' => Array(
						'SENDIT_API_KEY' => $settings['SENDIT_API_KEY'],
						'SENDIT_WSDL' => ( (strlen($settings['SENDIT_WSDL']) > 11)? $settings['SENDIT_WSDL']: 'https://api.sendit.pl/webservice.php?wsdl'),
						'SENDIT_LOGIN' => $settings['SENDIT_LOGIN'],
						'SENDIT_PASS' => $settings['SENDIT_PASS']
					),
					'ship_data' => Array(
						'SENDIT_NAME' => $settings['SENDIT_NAME'],
						'SENDIT_STREET' => $settings['SENDIT_STREET'],
						'SENDIT_ZIP' => $settings['SENDIT_ZIP'],
						'SENDIT_CITY' => $settings['SENDIT_CITY'],
						'SENDIT_PHONE' => $settings['SENDIT_PHONE'],
						'SENDIT_EMAIL' =>$settings['SENDIT_PERSON'],
						'SENDIT_PERSON' => $settings['SENDIT_PERSON'],
					)
				)
			);
		}
		else {
			$populate = Array(
				'sendit_data' => array(
					'login_data' => Array(
						'SENDIT_API_KEY' => '',
						'SENDIT_WSDL' => 'https://api.sendit.pl/webservice.php?wsdl',
						'SENDIT_LOGIN' => '',
						'SENDIT_PASS' => ''
					),
					'ship_data' => Array(
						'SENDIT_NAME' => '',
						'SENDIT_STREET' => '',
						'SENDIT_ZIP' => '',
						'SENDIT_CITY' => '',
						'SENDIT_PHONE' => '',
						'SENDIT_EMAIL' => '',
						'SENDIT_PERSON' => '',
					)
				)
			);
		}

		$event->setReturnValues($populate);
	}

	public function saveSettings ($request)
	{
		$settings = Array(
			'SENDIT_API_KEY' => $request['data']['SENDIT_API_KEY'],
			'SENDIT_WSDL' => ( (strlen($request['data']['SENDIT_WSDL']) > 0)? $request['data']['SENDIT_WSDL']: 'https://api.sendit.pl/webservice.php?wsdl'),
			'SENDIT_LOGIN' => $request['data']['SENDIT_LOGIN'],
			'SENDIT_PASS' => $request['data']['SENDIT_PASS'],
			'SENDIT_NAME' => $request['data']['SENDIT_NAME'],
			'SENDIT_STREET' => $request['data']['SENDIT_STREET'],
			'SENDIT_ZIP' => $request['data']['SENDIT_ZIP'],
			'SENDIT_CITY' => $request['data']['SENDIT_CITY'],
			'SENDIT_PHONE' => $request['data']['SENDIT_PHONE'],
			'SENDIT_EMAIL' =>$request['data']['SENDIT_PERSON'],
			'SENDIT_PERSON' => $request['data']['SENDIT_PERSON'],
		);

		$this->registry->core->saveModuleSettings('sendit', $settings, Helper::getViewId());
	}



	private function getPassHash($password_plain)
	{
		if (function_exists('hash') && in_array('sha256', hash_algos())) {
			$password_encrypted = hash('sha256', $password_plain, true);
		}
		elseif (function_exists('mhash') && is_int(MHASH_SHA256))    {
			$password_encrypted = mhash(MHASH_SHA256, $password_plain);
		}
		return base64_encode( $password_encrypted );
	}
	public function cleanXSS($data)
	{
		$ret = '';

			if($ret = @mysql_real_escape_string(strip_tags($data ) ) )
				return htmlspecialchars($ret);
			else
				$ret = addslashes(strip_tags($data ) );


		return htmlspecialchars($ret);
	}
	public function getCountryIso($id)
	{
		$country_name = '';
		$countrylist = App::getModel('countrieslist')->getCountryForSelect();
		@$country_name = $countrylist[$id];

		$iso_country = 'PL';
		if($country_name != '')
		{
			foreach( $this->country_iso as $iso => $data)
			{
				if($data[0] == $country_name || $data[1] == $country_name)
					$iso_country = $iso;
			}
		}
		return $iso_country;
	}
	public function getCountryList()
	{
		$list = array();
		foreach ($this->country_iso as $iso => $data)
		{
			$list[$iso] = $data[0];
		}
		return $list;
	}
	public function getProductsWeight($order_id)
	{
		$sql = "SELECT
					OP.qty as quantity,
					P.weight
				FROM orderproduct OP
				LEFT JOIN product P ON P.idproduct = OP.productid
				WHERE OP.orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $order_id);
		$stmt->execute();
		$weight = 0;
		while ($rs = $stmt->fetch())
		{
			$weight += $rs['quantity'] * $rs['weight'];
		}
		return $weight;
	}
	public  function loginSendit()
	{
		$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());

		$client = new \nusoap_client($config['SENDIT_WSDL']);
		$client->decode_utf8 = false;
		$param = array(
			'apiKey' => $config['SENDIT_API_KEY'],
			'login' => $config['SENDIT_LOGIN'],
			'password' => $config['SENDIT_PASS']
		);
		$result = $client->call('SIUserLogin', $param);

		if( isset($result['status']) && $result['status'] == 'success' && isset($result['userHash']) )
		{
			return $result['userHash'];
		}
		else
		{
			return $result;
		}
	}
	public function checkService($request)
	{
		$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());
		$userHash = $this->loginSendit();
		if( is_array($userHash) )
		{
			return $userHash;
		}
		$client = new \nusoap_client($config['SENDIT_WSDL']);
		$client->decode_utf8 = false;
		$param = array(
			'apiKey' => $config['SENDIT_API_KEY'],
			'userHash' => $userHash,
			'senderZipCode' => $this->cleanXSS($request['sender_zip']),
			'senderCountryCode' => 'PL',
			'receiverZipCode' => $this->cleanXSS($request['receiver_zip']),
			'receiverCountryCode' => $this->cleanXSS($request['receiver_country']),
			'pallet' => $this->cleanXSS($request['pallet']),
		);

		$result = $client->call('SIServicesCheck', $param);

		if(isset($result['status']) && $result['status'] == 'success')
		{
			$services = array(
				't_normal'		=> array('dpd'=> 0,'ups' => 0),
				't_morning'		=> array('dpd'=> 0,'ups' => 0),
				't_tillnoon'	=> array('dpd'=> 0,'ups' => 0),
				't_saturday'	=> array('dpd'=> 0,'ups' => 0),
				'ROD' 			=> array('dpd'=> 0,'ups' => 0),
				'SRE'			=> array('dpd'=> 0,'ups' => 0),
				'SSE'			=> array('dpd'=> 0,'ups' => 0),
				'BYH'			=> array('dpd'=> 0,'ups' => 0),
				'H24'			=> array('dpd'=> 0,'ups' => 0),
				'NST'			=> array('dpd'=> 0,'ups' => 0),
				'PRV'			=> array('dpd'=> 0,'ups' => 0),
				'COD'			=> array('dpd'=> 0,'ups' => 0),
				'INS'			=> array('dpd'=> 0,'ups' => 0),
			);
			foreach($result['services'] as $row)
			{
				if( ($row['operator'] == 'ups' || $row['operator'] == 'dpd') && !isset($row['error']) )
				{
					foreach($row['terms'] as $term)
					{
						if( array_key_exists($term,$services) )
							$services[$term][$row['operator']] = 1;
					}
					foreach($row['services'] as $service)
					{
						if( array_key_exists($service,$services) )
							$services[$service][$row['operator']] = 1;
					}
				}
			}
			$result = '
		<h2>Usługi dodatkowe</h2>
		 <div class="product">
            <h3>Termin dostawy</h3>

		<ul class="sendit_service">
					<li>
						<input ' . $this->checkActiveService('t_normal',$services) . ' name="terms" id="t_normal" class="terms" type="radio" value="t_normal" checked="checked"> <label for="t_normal">'.'Standardowo '.$this->getOperatorForService('t_normal',$services) . '</label>
					</li>
					<li>
						<input ' . $this->checkActiveService('t_morning',$services) . ' name="terms" id="t_morning" class="terms" type="radio" value="t_morning"> <label for="t_morning">Na rano '.$this->getOperatorForService('t_morning',$services) . '</label>
					</li>
					<li>
						<input ' . $this->checkActiveService('t_tillnoon',$services) . ' name="terms" id="t_tillnoon" class="terms" type="radio" value="t_tillnoon"> <label for="t_tillnoon">Do południa '.$this->getOperatorForService('t_tillnoon',$services) . ' </label>
					</li>
					<li>
						<input ' . $this->checkActiveService('t_saturday',$services) . ' name="terms" id="t_saturday" class="terms" type="radio" value="t_saturday"> <label for="t_saturday">W sobotę '.$this->getOperatorForService('t_saturday',$services) . ' </label>
					</li>
				</ul>
			</div>
		</div>
		 <div class="product">
            <h3>Dodatkowe usługi</h3>
			<ul class="sendit_service">
					<li>
						<input ' . $this->checkActiveService('ROD',$services) . ' name="ROD" id="ROD" class="services" type="checkbox" value="1"> <label for="ROD">Zwrot dokumentów' . $this->getOperatorForService('ROD',$services) . ' </label>
					</li>
					<li>
						<input ' . $this->checkActiveService('SRE',$services) . ' name="SRE" id="SRE" class="services" type="checkbox" value="1"> <label for="SRE">Odbiór własny' . $this->getOperatorForService('SRE',$services) . ' </label>
					</li>
					<li>
						<input ' . $this->checkActiveService('SSE',$services) . ' name="SSE" id="SSE" class="services" type="checkbox" value="1"> <label for="SSE">Nadanie własne'. $this->getOperatorForService('SSE',$services) . ' </label>
					</li>
					<li>
						<input ' . $this->checkActiveService('BYH',$services) . ' name="BYH" id="BYH" class="services" type="checkbox" value="1"> <label for="BYH">Doręczenie do rąk własnych' . $this->getOperatorForService('BYH',$services) . ' </label>
					</li>
					<li>
						<input ' . $this->checkActiveService('H24',$services) . ' name="H24" id="H24" class="services" type="checkbox" value="1"> <label for="H24">Dostawa w 24 godziny' . $this->getOperatorForService('H24',$services) . ' </label>
					</li>
					<li>
						<input ' . $this->checkActiveService('COD',$services) . ' name="COD" id="COD" class="services" type="checkbox" value="1"> <label for="COD">Pobranie '. $this->getOperatorForService('COD',$services) . ' </label>
						<span id="cod_value" style="display: none;"><br/>kwota: <input name="cod_value" class="services" type="text"   maxlength="5"></span>
					</li>
					<li>
						<input ' . $this->checkActiveService('INS',$services) . ' name="INS" id="INS" class="services" type="checkbox" value="1"> <label for="INS">'.'Dodatkowe ubezpieczenie' . $this->getOperatorForService('INS',$services) . ' </label>
						<span id="ins_value" style="display: none;"><br/>kwota: <input name="ins_value"  class="services" type="text"   maxlength="5"></span>
					</li>
				</ul>
			</fieldset>
			';
			return array('content'=>$result);
		}
		else
		{
			$result['faultstring'] = $this->getError($result['faultcode'],$result['faultstring']);
			return $result;


		}

	}
	public function getError($code = '',$faultstring = '')
	{
		$errors = array(
			'ERR_ORDER_DATA_S_NAME' 		=> 'Nieprawidłowa wartość pola "nazwa" w danych nadawcy.',
			'ERR_ORDER_DATA_S_STREET' 		=> 'Nieprawidłowa wartość pola "ulica i nr domu" w danych nadawcy.',
			'ERR_ORDER_DATA_S_CITY' 		=> 'Nieprawidłowa wartość pola "miasto" w danych nadawcy.',
			'ERR_ORDER_DATA_S_ZIP' 			=> 'Nieprawidłowa wartość pola "kod pocztowy" w danych nadawcy.',
			'ERR_ORDER_DATA_S_PHONE' 		=> 'Nieprawidłowa wartość pola "telefon" w danych nadawcy.',
			'ERR_ORDER_DATA_S_PERSON'		=> 'Nieprawidłowa wartość pola "osoba kontaktowa" w danych nadawcy.',
			'ERR_ORDER_DATA_S_COUNTRY' 		=> 'Nieprawidłowa wartość pola "kraj" w danych nadawcy',
			'ERR_ORDER_DATA_R_NAME' 		=> 'Nieprawidłowa wartość pola "nazwa" w danych odbiorcy.',
			'ERR_ORDER_DATA_R_STREET' 		=> 'Nieprawidłowa wartość pola "ulica i nr domu" w danych odbiorcy.',
			'ERR_ORDER_DATA_R_CITY' 		=> 'Nieprawidłowa wartość pola "miasto" w danych odbiorcy.',
			'ERR_ORDER_DATA_R_ZIP' 			=> 'Nieprawidłowa wartość pola "kod pocztowy" w danych odbiorcy.',
			'ERR_ORDER_DATA_R_PHONE' 		=> 'Nieprawidłowa wartość pola "telefon" w danych odbiorcy.',
			'ERR_ORDER_DATA_R_PERSON' 		=> 'Nieprawidłowa wartość pola "osoba kontaktowa" w danych odbiorcy.',
			'ERR_ORDER_DATA_R_COUNTRY' 		=> 'Nieprawidłowa wartość pola "kraj" w danych odbiorcy',
			'ERR_ORDER_DATA_NO_PACKS' 		=> 'Nie wybrano żadnej paczki do wysłania.',
			'ERR_ORDER_DATA_NO_COURIER' 	=> 'Nie wybrano operatora.',
			'ERR_ORDER_DATA_TOO_MANY_PACKS' => 'Za dużo paczek do wysłania (max 30).',
			'ERR_ORDER_DATA_S_EMAIL' 		=> 'Nieprawidłowa wartość pola "email" w danych nadawcy.',
			'ERR_ORDER_DATA_R_EMAIL' 		=> 'Nieprawidłowa wartość pola "email" w danych odbiorcy.',
			'ERR_ZIP_1' 					=> 'Nieprawidłowa wartość pola "kod pocztowy" w danych nadawcy.',
			'ERR_ZIP_2' 					=> 'Nieprawidłowa wartość pola "kod pocztowy" w danych odbiorcy.',
		);

		if( $code != '' && array_key_exists($code,$errors))
			return $errors[$code];
		else if ($faultstring != '')
			return $faultstring;
		else
			return 'Błąd połączenia, spróbuj jeszcze raz';
	}
	private function getOperatorForService($service,$services)
	{
		if( array_key_exists($service,$services))
		{
			if($services[$service]['dpd'] == 1 && $services[$service]['ups'] == 1)
				return '( DPD, UPS )';
			elseif($services[$service]['dpd'] == 1)
				return '( DPD )';
			elseif($services[$service]['ups'] == 1)
				return '( UPS )';
		}
		return '( <span class="red"> Usługa niedostępna</span> )';
	}
	private function checkActiveService($service,$services)
	{
		if( array_key_exists($service,$services))
		{
			if($services[$service]['dpd'] == 1 || $services[$service]['ups'] == 1)
				return '';
		}
		return 'disabled="disabled" ';
	}
	private function parsePhoneNr( $phone )
	{
		return preg_replace( '/[^0-9+]/', '', $phone );
	}
	public function rate($request)
	{

		$orderData = array(
			'senderCountryCode'	=> 'PL',
			'senderEmail'		=> $this->cleanXSS($request['senderEmail']),
			'senderName'		=> $this->cleanXSS($request['senderName']),
			'senderStreet'		=> $this->cleanXSS($request['senderStreet']),
			'senderCity'		=> $this->cleanXSS($request['senderCity']),
			'senderPhoneNumber'	=> $this->parsePhoneNr($this->cleanXSS($request['senderPhoneNumber'])),
			'senderZipCode'		=> $this->cleanXSS($request['sender_postcode']),
			'senderContactPerson'	=> $this->cleanXSS($request['senderContactPerson']),
			'receiverCountryCode'	=> $this->cleanXSS($request['receiver_country']),
			'receiverEmail'		=> $this->cleanXSS($request['receiverEmail']),
			'receiverName'		=> $this->cleanXSS($request['receiverName']),
			'receiverStreet'		=> $this->cleanXSS($request['receiverStreet']),
			'receiverCity'		=> $this->cleanXSS($request['receiverCity']),
			'receiverPhoneNumber'	=> $this->parsePhoneNr($this->cleanXSS($request['receiverPhoneNumber'])),
			'receiverZipCode'		=> $this->cleanXSS($request['receiver_postcode']),
			'receiverContactPerson'	=> $this->cleanXSS($request['receiverContactPerson']),
			'kPK'				=> ((int)$this->cleanXSS($request['kPK']) + (int)$this->cleanXSS($request['nstd_kPK'])),
			'kP5'				=> ((int)$this->cleanXSS($request['kP5']) + (int)$this->cleanXSS($request['nstd_kP5'])),
			'kP10'				=> ((int)$this->cleanXSS($request['kP10']) + (int)$this->cleanXSS($request['nstd_kP10'])),
			'kP20'				=> ((int)$this->cleanXSS($request['kP20']) + (int)$this->cleanXSS($request['nstd_kP20'])),
			'kP30'				=> ((int)$this->cleanXSS($request['kP30']) + (int)$this->cleanXSS($request['nstd_kP30'])),
			'kP50'				=> ((int)$this->cleanXSS($request['kP50']) + (int)$this->cleanXSS($request['nstd_kP50'])),
			'kP70'				=> ((int)$this->cleanXSS($request['kP70']) + (int)$this->cleanXSS($request['nstd_kP70'])),
			'kPal'				=> ( ((int)$this->cleanXSS($request['palletWeight']) > 0 || (int)$this->cleanXSS($request['palletHeight']) > 0 )?1:0 ),
			'palletHeight'		=> (int)$this->cleanXSS($request['palletHeight']),
			'palletWeight'		=> (int)$this->cleanXSS($request['palletWeight']),
			'COD'				=> (float)str_replace(' ','',str_replace(',','.',$this->cleanXSS($request['cod_value']))),
			'INS'				=> (float)str_replace(' ','',str_replace(',','.',$this->cleanXSS($request['ins_value']))),
			'ROD'				=> (($this->cleanXSS($request['ROD']) == 'true')?'1':'0'),
			'SRE'				=> (($this->cleanXSS($request['SRE']) == 'true')?'1':'0'),
			'SSE'				=> (($this->cleanXSS($request['SSE']) == 'true')?'1':'0'),
			'BYH'				=> (($this->cleanXSS($request['BYH']) == 'true')?'1':'0'),
			'H24'				=> (($this->cleanXSS($request['H24']) == 'true')?'1':'0'),
			'deliveryTime'		=> $this->cleanXSS($request['term']),
			'alerts'			=> array(
				'receive' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['ReceiveSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['ReceiveSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['ReceiveReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['ReceiveReceiverEmail']) == 'true')?'1':'0')),
				),
				'courier' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['CourierSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['CourierSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['CourierReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['CourierReceiverEmail']) == 'true')?'1':'0')),
				),
				'advice' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['AwizoSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['AwizoSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['AwizoReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['AwizoReceiverEmail']) == 'true')?'1':'0')),
				),
				'deliver' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['DeliverSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['DeliverSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['DeliverReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['DeliverReceiverEmail']) == 'true')?'1':'0')),
				),
				'refuse' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['RefuseSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['RefuseSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['RefuseReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['RefuseReceiverEmail']) == 'true')?'1':'0')),
				),
			),
			'NSTData'			=> array(
				'kPK'				=> (int)$this->cleanXSS($request['nstd_kPK']),
				'kP5'				=> (int)$this->cleanXSS($request['nstd_kP5']),
				'kP10'				=> (int)$this->cleanXSS($request['nstd_kP10']),
				'kP20'				=> (int)$this->cleanXSS($request['nstd_kP20']),
				'kP30'				=> (int)$this->cleanXSS($request['nstd_kP30']),
				'kP50'				=> (int)$this->cleanXSS($request['nstd_kP50']),
				'kP70'				=> (int)$this->cleanXSS($request['nstd_kP70']),
			),
			'comment'		=> $this->cleanXSS($request['saleDocId']),
			'content'		=> $this->cleanXSS($request['packageContent']),
			'invoiceFlag'		=> 0,
			'protocolFlag'		=> 0,


		);

		$userHash = $this->loginSendit();
		if( is_array($userHash) )
		{
			return $userHash;
		}
		$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());
		$client = new \nusoap_client($config['SENDIT_WSDL']);
		$client->decode_utf8 = false;
		$param = array(
			'apiKey' => $config['SENDIT_API_KEY'],
			'userHash' => $userHash,
			'orderData' => $orderData,
		);

		$result = $client->call('SIOrderRate', $param);

		if(isset($result['status']) && $result['status'] == 'success')
		{
			$content = '<div>
            	<h2>Podsumowanie przesyłki</h2>';
			foreach( $result['pricing'] as $courier)
			{
				if( ($courier['operator'] == 'UPS' || $courier['operator'] == 'DPD') && (int)$courier['result']['status'] == 1 )
				{
					$content .= '
				<div class="col2">

					<table class="table" width="100%" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th class="title center" colspan="4" >' . $courier['operator'] . '</th>
					</tr>
					<tr>
						<th>Usługa</th>
						<th>Netto</th>
						<th>VAT</th>
						<th>Brutto</th>
					</tr>
					</thead>
					<tbody>';
					foreach($courier['products'] as $key => $product)
					{
						$content .= '<tr class="' .( ($key % 2 == 0)?'even':'odd') .'" >
					<td class="col1">' . $product['description'].(((isset($product['quantity']))?' x '.$product['quantity']:'')). '</td>
					<td class="col2" >' . $product['nett']. ' zł</td>
					<td class="col2" >' . $product['VAT']. ' zł</td>
					<td class="col2" >' . $product['gross']. ' zł</td>

					</tr>';
					}
					$content .= '
					<tr class="sum">
					<td class="col1" >Razem netto</td>
					<td class="col2" colspan="3">' . $courier['total']['nett']. ' zł</td>
					</tr>
					<tr class="sum">
						<td class="col1" >Razem VAT</td>
						<td class="col2" colspan="3">' . $courier['total']['VAT']. ' zł</td>
					</tr>
					<tr class="sumBrutto">
						<td class="col1" >Razem brutto</td>
						<td class="col2" colspan="3">' . $courier['total']['gross']. ' zł</td>
					</tr>
					<tr class="sumConfirm center">
						<td colspan="4"><button type="button" class="button" value="' . $courier['operator']. '" >Zamów kuriera ' . $courier['operator']. '</button> </td>
					</tr>
					</tbody>

					</table> </div>';

				}
				elseif($courier['operator'] == 'UPS' || $courier['operator'] == 'DPD')
					$content .= '<div class="col2"><div class="error">' . ($courier['operator'].': '.$courier['result']['desc']).'</div></div>';
			}
			$content .= '</div>';

			return array('content'=>$content);
		}
		else
		{

			$result['faultstring'] = $this->getError($result['faultcode'],$result['faultstring']);
			return $result;
		}
	}
	public function confirmOrder($request)
	{
		$orderData = array(
			'senderCountryCode'	=> 'PL',
			'senderEmail'		=> $this->cleanXSS($request['senderEmail']),
			'senderName'		=> $this->cleanXSS($request['senderName']),
			'senderStreet'		=> $this->cleanXSS($request['senderStreet']),
			'senderCity'		=> $this->cleanXSS($request['senderCity']),
			'senderPhoneNumber'	=> $this->parsePhoneNr($this->cleanXSS($request['senderPhoneNumber'])),
			'senderZipCode'		=> $this->cleanXSS($request['sender_postcode']),
			'senderContactPerson'	=> $this->cleanXSS($request['senderContactPerson']),
			'receiverCountryCode'	=> $this->cleanXSS($request['receiver_country']),
			'receiverEmail'		=> $this->cleanXSS($request['receiverEmail']),
			'receiverName'		=> $this->cleanXSS($request['receiverName']),
			'receiverStreet'		=> $this->cleanXSS($request['receiverStreet']),
			'receiverCity'		=> $this->cleanXSS($request['receiverCity']),
			'receiverPhoneNumber'	=> $this->parsePhoneNr($this->cleanXSS($request['receiverPhoneNumber'])),
			'receiverZipCode'		=> $this->cleanXSS($request['receiver_postcode']),
			'receiverContactPerson'	=> $this->cleanXSS($request['receiverContactPerson']),
			'kPK'				=> ((int)$this->cleanXSS($request['kPK']) + (int)$this->cleanXSS($request['nstd_kPK'])),
			'kP5'				=> ((int)$this->cleanXSS($request['kP5']) + (int)$this->cleanXSS($request['nstd_kP5'])),
			'kP10'				=> ((int)$this->cleanXSS($request['kP10']) + (int)$this->cleanXSS($request['nstd_kP10'])),
			'kP20'				=> ((int)$this->cleanXSS($request['kP20']) + (int)$this->cleanXSS($request['nstd_kP20'])),
			'kP30'				=> ((int)$this->cleanXSS($request['kP30']) + (int)$this->cleanXSS($request['nstd_kP30'])),
			'kP50'				=> ((int)$this->cleanXSS($request['kP50']) + (int)$this->cleanXSS($request['nstd_kP50'])),
			'kP70'				=> ((int)$this->cleanXSS($request['kP70']) + (int)$this->cleanXSS($request['nstd_kP70'])),
			'kPal'				=> ( ((int)$this->cleanXSS($request['palletWeight']) > 0 || (int)$this->cleanXSS($request['palletHeight']) > 0 )?1:0 ),
			'palletHeight'		=> (int)$this->cleanXSS($request['palletHeight']),
			'palletWeight'		=> (int)$this->cleanXSS($request['palletWeight']),
			'COD'				=> (float)str_replace(' ','',str_replace(',','.',$this->cleanXSS($request['cod_value']))),
			'INS'				=> (float)str_replace(' ','',str_replace(',','.',$this->cleanXSS($request['ins_value']))),
			'ROD'				=> (($this->cleanXSS($request['ROD']) == 'true')?'1':'0'),
			'SRE'				=> (($this->cleanXSS($request['SRE']) == 'true')?'1':'0'),
			'SSE'				=> (($this->cleanXSS($request['SSE']) == 'true')?'1':'0'),
			'BYH'				=> (($this->cleanXSS($request['BYH']) == 'true')?'1':'0'),
			'H24'				=> (($this->cleanXSS($request['H24']) == 'true')?'1':'0'),
			'deliveryTime'		=> $this->cleanXSS($request['term']),
			'alerts'			=> array(
				'receive' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['ReceiveSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['ReceiveSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['ReceiveReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['ReceiveReceiverEmail']) == 'true')?'1':'0')),
				),
				'courier' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['CourierSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['CourierSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['CourierReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['CourierReceiverEmail']) == 'true')?'1':'0')),
				),
				'advice' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['AwizoSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['AwizoSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['AwizoReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['AwizoReceiverEmail']) == 'true')?'1':'0')),
				),
				'deliver' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['DeliverSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['DeliverSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['DeliverReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['DeliverReceiverEmail']) == 'true')?'1':'0')),
				),
				'refuse' => array(
					'sender' => array( 'sms' => (($this->cleanXSS($request['RefuseSenderSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['RefuseSenderEmail']) == 'true')?'1':'0')),
					'receiver' => array( 'sms' => (($this->cleanXSS($request['RefuseReceiverSMS']) == 'true')?'1':'0'), 'email' => (($this->cleanXSS($request['RefuseReceiverEmail']) == 'true')?'1':'0')),
				),
			),
			'NSTData'			=> array(
				'kPK'				=> (int)$this->cleanXSS($request['nstd_kPK']),
				'kP5'				=> (int)$this->cleanXSS($request['nstd_kP5']),
				'kP10'				=> (int)$this->cleanXSS($request['nstd_kP10']),
				'kP20'				=> (int)$this->cleanXSS($request['nstd_kP20']),
				'kP30'				=> (int)$this->cleanXSS($request['nstd_kP30']),
				'kP50'				=> (int)$this->cleanXSS($request['nstd_kP50']),
				'kP70'				=> (int)$this->cleanXSS($request['nstd_kP70']),
			),
			'comment'		=> $this->cleanXSS($request['saleDocId']),
			'content'		=> $this->cleanXSS($request['packageContent']),
			'invoiceFlag'		=> 1,
			'protocolFlag'		=> (($this->cleanXSS($request['protocol']) == 'true')?'1':'0'),


		);

		$userHash = $this->loginSendit();
		if( is_array($userHash) )
		{
			return $userHash;
		}
		$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());
		$client = new \nusoap_client($config['SENDIT_WSDL']);
		$client->decode_utf8 = false;
		$param = array(
			'apiKey' => $config['SENDIT_API_KEY'],
			'userHash' => $userHash,
			'orderData' => $orderData,
			'courier' => $this->cleanXSS($request['courier']),
		);

		$result = $client->call('SIOrderConfirm', $param);
		if(isset($result['status']) && $result['status'] == 'success')
		{
			$courier  = $param['courier'];
			$brutto = '';
			$status = 'Zlecenie wysłane do Sendit.pl';

			$param = array(
				'apiKey' =>  $config['SENDIT_API_KEY'],
				'userHash' => $userHash,
				'orderNumber' => $result['orderNumbers'][0],
			);
			$result2 = $client->call('SIGetOrder', $param);

			if(isset($result2['status']) && $result2['status'] == 'success' )
			{
				$brutto = $result2['order']['finalGross'];
				$courier = strtoupper( $result2['order']['courierName']);
				$status = end($result2['history']);
			}

			$content = 'Zamówiono kuriera '.$courier.' - nr zlecenia: '.$result['orderNumbers'][0];


			$sql = 'INSERT INTO `sendit_orders` (`order_id`, `order_nr`, `cod`, `courier`,`brutto`,`status`) VALUES (:id, :nr, :cod, :courier, :brutto, :status)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $request['id_order']);
			$stmt->bindValue('nr', $result['orderNumbers'][0]);
			$stmt->bindValue('cod', $orderData['COD']);
			$stmt->bindValue('courier', $courier);
			$stmt->bindValue('brutto', $brutto);
			$stmt->bindValue('status', $status['statusInfo']);
			$stmt->execute();

			return array('content'=>$content);
		}
		else
		{
			$result['faultstring'] = $this->getError($result['faultcode'],$result['faultstring']);

			return $result;
		}

	}
	private function getOrder($order_nr)
	{
		try
		{
			$sql = 'SELECT tracking_code FROM `sendit_orders` WHERE order_nr = :nr';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('nr', $order_nr);
			$stmt->execute();
			$orders =  $stmt->fetchAll();
			if( count($orders) == 1)
			{
				if( isset($orders[0]['tracking_code']) && strlen($orders[0]['tracking_code']) >0 )
				{
					$orders[0]['tracking_code'] = unserialize($orders[0]['tracking_code']);
				}
				return $orders[0];
			}
			else
				return false;

		}
		catch (\Exception $e)
		{
			return false;
		}
	}
	public function updateStatus($request)
	{
		$sql = 'SELECT order_nr FROM `sendit_orders` WHERE order_id = :nr';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('nr', $request['id_order']);
		$stmt->execute();
		$ordersArray = $stmt->fetchAll();

		$content = '<tr><td colspan="6">Brak zleceń</td></tr>';
		$error = '';
		if( count($ordersArray) > 0 )
		{
			$orders = array();
			foreach($ordersArray as $order)
			{
				$orders[] = $this->cleanXSS($order['order_nr']);
			}
			$userHash = $this->loginSendit();
			$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());
			$client = new \nusoap_client($config['SENDIT_WSDL']);
			$client->decode_utf8 = false;
			$param = array(
				'apiKey' =>  $config['SENDIT_API_KEY'],
				'userHash' => $userHash,
				'orders' => $orders,
			);
			$result = $client->call('SIOrderStatus',$param);


			if( isset($result['status']) && $result['status'] == 'success' )
			{
				if(isset($result['orders']) && is_array($result['orders']))
				{
					foreach( $result['orders'] as $order)
					{
						$sql = 'UPDATE `sendit_orders` SET `status`=:status, status_nr=:status_nr WHERE `order_nr`=:nr';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('status', $order['statusInfo']);
						$stmt->bindValue('status_nr', (int) $order['statusNumber']);
						$stmt->bindValue('nr', $order['orderNumber']);
						$stmt->execute();
					}
				}
			}
			else
			{

				$error = $this->getError($result['faultcode'],$result['faultstring']);
			}

			$sql = 'SELECT * FROM `sendit_orders` WHERE order_id = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $request['id_order']);
			$stmt->execute();
			$ordersArray = $stmt->fetchAll();
			$content = '';
			foreach($ordersArray as $order)
			{
				$content .= '<tr>';
				$content .= '<td>'.$this->cleanXSS($order['order_nr']).'</td>';
				$content .= '<td>'.$this->cleanXSS($order['brutto']).' zł</td>';
				$content .= '<td>'.$this->cleanXSS($order['courier']).'</td>';
				$tracking_code = '';
				$protcol = '';
				if( isset($order['tracking_code']) && is_array(unserialize($order['tracking_code'])))
				{
					foreach(unserialize($order['tracking_code']) as $track)
					{
						$tracking_code .= $track.'<br/>';
					}
					$tracking_code .= '<br/>';
				}
				if( isset($order['protocol_number']))
				{
					$protcol = $this->cleanXSS($order['protocol_number']).'<br/><br/>';
				}

				if($order['status_nr'] == 10)
				{
					$url = $this->registry->template->_tpl_vars['URL'];
					$content .= '<td>'.$tracking_code.'<a class="button" href ="'.$url.'sendit/view/'.$this->cleanXSS($request['id_order']).'/print/lp/'.$this->cleanXSS($order['order_nr']).'" target="_blank">Drukuj</a> </td>';
					$content .= '<td>'.$protcol.'<a class="button" href ="'.$url.'sendit/view/'.$this->cleanXSS($request['id_order']).'/print/protocol/'.$this->cleanXSS($order['order_nr']).'" target="_blank">Drukuj</a> </td>';
				}
				else
				{
					$content .= '<td>'.$tracking_code.'</td>';
					$content .= '<td>'.$protcol.'</td>';
				}
				$content .= '<td>'.$order['status'].'</td>';
				$content .= '</tr>';
			}

		}

		return array(
			'content' => $content,
			'count' => count($ordersArray),
			'error' => $error
		);
	}
	public function printLp($order_nr)
	{

		$userHash = $this->loginSendit();
		$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());
		$client = new \nusoap_client($config['SENDIT_WSDL']);
		$client->decode_utf8 = false;
		$order = $this->getOrder($order_nr);
		if($order === false)
			return 'Drukowanie listu przewozowego - bark podanego zlecenia w bazie.';
		$param = array(
			'apiKey' =>  $config['SENDIT_API_KEY'],
			'userHash' => $userHash,
			'orders' => array($order_nr),
		);

		$result = $client->call('SIOrderPDF', $param);
		if(isset($result['status']) && $result['status'] == 'success')
		{
			if( !isset($order['tracking_code']) )
			{
				$param = array(
					'apiKey' =>  $config['SENDIT_API_KEY'],
					'userHash' => $userHash,
					'orderNumber' => $order_nr,
				);
				$result2 = $client->call('SIGetOrder', $param);
				if(isset($result2['status']) && $result2['status']== 'success')
				{
					$status = end($result2['history']);
					$tracking_code = array();
					foreach( $result2['order']['trackingCodes'] as $track)
					{
						$tracking_code[] = $this->cleanXSS($track);
					}
					$tracking_code = addslashes(serialize($tracking_code));
					$sql = 'UPDATE `sendit_orders` SET `status`=:status, `status_nr` = :status_nr, `tracking_code` = :tracking WHERE `order_nr`= :nr';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('status', $status['statusInfo']);
					$stmt->bindValue('status_nr', $status['statusNumber']);
					$stmt->bindValue('tracking_code', $tracking_code);
					$stmt->bindValue('tracking_code', $tracking_code);
					$stmt->bindValue('nr', $order_nr);
					$stmt->execute();
				}
			}
			header( 'Content-type: application/pdf' );
			header( 'Content-disposition: attachment; filename=Sendit.pl-lp-'.date('d-m-Y').'.pdf' );
			if ( strstr( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE' ) !== false )
			{
				header( 'Cache-Control: maxage=1' );
				header( 'Pragma: public' );
			}
			else
			{
				header( 'Pragma: no-cache' );
			}
			ob_clean();
			echo base64_decode( $result[ 'pdf' ] );
			die();
		}
		else
		{
			$error = $this->getError($result['faultcode'],$result['faultstring']);
			return 'Drukowanie listu przewozowego: '.$error;
		}
	}
	public function printProtocol($order_nr)
	{
		$protocol_nr = '';
		$userHash = $this->loginSendit();
		$config = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());
		$order = $this->getOrder($order_nr);
		if($order === false)
			return 'Drukowanie protokołu odbioru - brak podanego zlecenia w bazie.';
		$client = new \nusoap_client($config['SENDIT_WSDL']);
		$client->decode_utf8 = false;
		if( !isset($order['protocol_number']))
		{

			$param = array(
				'apiKey' =>  $config['SENDIT_API_KEY'],
				'userHash' => $userHash,
				'orderNumber' => $order_nr,
			);
			$result2 = $client->call('SIGetOrder', $param);
			if($result2['status'] == 'success')
			{
				$status = end($result2['history']);
				if( isset($result2['order']['protocolNumber']) ) //jest protokół
				{
					$protocol_nr = $result2['order']['protocolNumber'];
					$sql = 'UPDATE `sendit_orders` SET `status`=:status, `status_nr` = :status_nr, `protocol_number` = :protocol_number WHERE `order_nr`= :nr';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('status', $status['statusInfo']);
					$stmt->bindValue('status_nr', $status['statusNumber']);
					$stmt->bindValue('protocol_number', $protocol_nr);
					$stmt->bindValue('nr', $order_nr);
					$stmt->execute();
				}
				else //generowanie nowego
				{
					$param = array(
						'apiKey' =>  $config['SENDIT_API_KEY'],
						'userHash' => $userHash,
						'orders' => array($order_nr),
					);
					$result = $client->call('SIProtocolGenerate', $param);
					if($result['status'] == 'success')
					{
						$protocol_nr = $result['protocols'][0]['protocolNumber'];
						$sql = 'UPDATE `sendit_orders` SET `status`=:status, `status_nr` = :status_nr, `protocol_number` = :protocol_number WHERE `order_nr`= :nr';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('status', $status['statusInfo']);
						$stmt->bindValue('status_nr', $status['statusNumber']);
						$stmt->bindValue('protocol_number', $protocol_nr);
						$stmt->bindValue('nr', $order_nr);
						$stmt->execute();
					}
				}
			}
		}
		else
		{
			$protocol_nr = $order['protocol_number'];
		}
		$param = array(
			'apiKey' =>  $config['SENDIT_API_KEY'],
			'userHash' => $userHash,
			'protocols' => array($protocol_nr),
		);
		$result3 = $client->call('SIProtocolPDF', $param);
		if($result3['status'] == 'success')
		{
			header( 'Content-type: application/pdf' );
			header( 'Content-disposition: attachment; filename=Sendit.pl-protocol-'. $order_nr .'.pdf' );
			if ( strstr( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE' ) !== false )
			{
				header( 'Cache-Control: maxage=1' );
				header( 'Pragma: public' );
			}
			else
			{
				header( 'Pragma: no-cache' );
			}
			ob_clean();
			echo base64_decode( $result3[ 'pdf' ] );
			die();
		}
		else
		{
			$error = $this->getError($result['faultcode'],$result['faultstring']);
			return 'Drukowanie protokołu odbioru: '.$error;

		}
	}
}