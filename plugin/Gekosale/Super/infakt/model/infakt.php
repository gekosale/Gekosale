<?php
/*
 * Kin Lane - @kinlane PHP Library to support xAuth for Instapapers REST API.
 * This is a strip down and rework of @abraham Twitter OAuth -
 * https://github.com/abraham/twitteroauth His was just so well written, it made
 * sense to reuse. Thanks @abraham!
 */
namespace Gekosale;

use FormEngine;

require_once ROOTPATH . 'lib' . DS . 'OAuth' . DS . 'OAuth.php';
class InfaktModel extends Component\Model
{
	public $http_code;
	public $url;
	public $host = "https://www.infakt.pl/api/v2";
	public $timeout = 30;
	public $connecttimeout = 30;
	public $ssl_verifypeer = FALSE;
	public $format = 'json';
	public $decode_json = TRUE;
	public $http_info;
	public $useragent = 'Gekosale InFakt 1.0.0';
	public $accessTokenURL = 'https://www.infakt.pl/oauth/access_token';

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	public function setUp ($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL)
	{
		$this->sha1_method = new \OAuthSignatureMethod_HMAC_SHA1();
		$this->consumer = new \OAuthConsumer($consumer_key, $consumer_secret);
		if (! empty($oauth_token) && ! empty($oauth_token_secret)){
			$this->token = new \OAuthConsumer($oauth_token, $oauth_token_secret);
		}
		else{
			$this->token = NULL;
		}
	}

	public function getUserData ()
	{
		return $this->post($this->host . '/settings/user_data.json');
	}

	public function getClients ($nip = '', $page = 1, $perpage = 100)
	{
		return $this->get($this->host . '/clients.json', Array(
			'page' => $page,
			'per_page' => $perpage,
			'nip' => $nip
		));
	}

	public function getClient ($id)
	{
		return $this->get($this->host . '/clients/show.json', Array(
			'id' => $id
		));
	}

	public function addClient ($Data)
	{
		return $this->post($this->host . '/clients/create.json', Array(
			'client' => Array(
				'nazwa_firmy' => (strlen($Data['companyname']) > 0 ? $Data['companyname'] : $Data['firstname'].' '.$Data['surname']),
				'ulica' => $Data['street'] . ' ' . $Data['streetno'] . (($Data['placeno'] != '') ? '/' . $Data['placeno'] : ''),
				'miejscowosc' => $Data['city'],
				'kod_pocztowy' => $Data['postcode'],
				'email' => $Data['email'],
				'numer_telefonu' => $Data['phone'],
				'nip' => $Data['nip']
			)
		));
	}

	public function deleteClient ($id)
	{
		return $this->post($this->host . '/clients/delete/' . $id . '.json', Array(
			'client' => Array(
				'client_id' => $id
			)
		));
	}

	public function createInvoice ($Data)
	{
		return $this->post($this->host . '/invoices/create.json', $Data);
	}

	public function updateInvoice ($Data, $id)
	{
		return $this->post($this->host . '/invoices/update/' . $id . '.json', $Data);
	}

	public function updateClient ($Data, $id)
	{
		return $this->post($this->host . '/clients/update/' . $id . '.json', $Data);
	}

	public function getInvoices ($nip = '', $page = 1, $perpage = 100)
	{
		return $this->get($this->host . '/invoices.json', Array(
			'page' => $page,
			'per_page' => $perpage
		));
	}

	public function deleteInvoice ($id)
	{
		return $this->post($this->host . '/invoices/delete/' . $id . '.json', Array(
			'invoice' => Array(
				'invoice_id' => $id
			)
		));
	}

	public function sendInvoice ($id)
	{
		return $this->get($this->host . '/invoices/deliver.json', Array(
			'type' => 'mail',
			'doc_type' => 'org',
			'id' => $id
		));
	}

	public function downloadInvoice ($id, $type)
	{
		return $this->get($this->host . '/invoices/pdf.json', Array(
			'doc_type' => $type,
			'id' => $id
		));
	}

	function getXAuthToken ($username, $password)
	{
		$parameters = array();
		$parameters['x_auth_username'] = $username;
		$parameters['x_auth_password'] = $password;
		$parameters['x_auth_mode'] = 'client_auth';
		$request = $this->oAuthRequest($this->accessTokenURL, 'POST', $parameters);
		$token = \OAuthUtil::parse_parameters($request);
		$this->token = new \OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}

	function get ($url, $parameters = array())
	{
		$request = \OAuthRequest::from_consumer_and_token($this->consumer, $this->token, 'GET', $url, $parameters);
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json'
		));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array(
			$this,
			'getHeader'
		));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		curl_setopt($ci, CURLOPT_URL, $request->to_url());
		$response = curl_exec($ci);
		curl_close($ci);
		return json_decode($response, true);
	}

	function post ($url, $parameters = array())
	{
		$request = \OAuthRequest::from_consumer_and_token($this->consumer, $this->token, 'POST', $url);
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
		
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json'
		));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array(
			$this,
			'getHeader'
		));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		curl_setopt($ci, CURLOPT_POST, TRUE);
		curl_setopt($ci, CURLOPT_POSTFIELDS, json_encode($parameters));
		curl_setopt($ci, CURLOPT_URL, $request->to_url());
		$response = curl_exec($ci);
		curl_close($ci);
		return json_decode($response, true);
	}

	function oAuthRequest ($url, $method, $parameters)
	{
		$request = \OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
		switch ($method) {
			case 'GET':
				return $this->http($request->to_url(), 'GET');
			default:
				return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
		}
	}

	function http ($url, $method, $postfields = NULL)
	{
		$this->http_info = array();
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array(
			'Expect:'
		));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array(
			$this,
			'getHeader'
		));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		
		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (! empty($postfields)){
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (! empty($postfields)){
					$url = "{$url}?{$postfields}";
				}
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;
		curl_close($ci);
		return $response;
	}

	function getHeader ($ch, $header)
	{
		$i = strpos($header, ':');
		if (! empty($i)){
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	public function addFields ($event, $request)
	{
	    $form = &$request['form'];
	    
		$infakt = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'infakt_data',
			'label' => 'Integracja z inFakt'
		)));
		
		$infakt->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'infaktlogin',
			'label' => 'Login do inFakt'
		)));
		
		$infakt->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'infaktpassword',
			'label' => 'Hasło do inFakt'
		)));
		
		$infakt->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'infaktconsumerkey',
			'label' => 'API Consumer Key'
		)));
		
		$infakt->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'infaktconsumersecret',
			'label' => 'API Consumer Secret'
		)));
		
		$settings = $this->registry->core->loadModuleSettings('infakt', (int) $request['id']);
		
		if (! empty($settings)){
			$populate = Array(
				'infakt_data' => Array(
					'infaktlogin' => $settings['infaktlogin'],
					'infaktpassword' => $settings['infaktpassword'],
					'infaktconsumerkey' => $settings['infaktconsumerkey'],
					'infaktconsumersecret' => $settings['infaktconsumersecret']
				)
			);
			
			$event->setReturnValues($populate);
		}
	}

	public function saveSettings ($request)
	{
		$Settings = Array(
			'infaktlogin' => $request['data']['infaktlogin'],
			'infaktpassword' => $request['data']['infaktpassword'],
			'infaktconsumerkey' => $request['data']['infaktconsumerkey'],
			'infaktconsumersecret' => $request['data']['infaktconsumersecret']
		);
		
		$this->registry->core->saveModuleSettings('infakt', $Settings, $request['id']);
	}

	public function addInvoice ($Data, $orderId, $invoiceTypeId, $orderData)
	{
		$settings = $this->registry->core->loadModuleSettings('infakt', $orderData['viewid']);
		
		$this->setUp($settings['infaktconsumerkey'], $settings['infaktconsumersecret']);
		$this->getXAuthToken($settings['infaktlogin'], $settings['infaktpassword']);
		
		switch ($invoiceTypeId) {
			case 1:
				$rodzaj_faktury = 'Faktura Proforma';
				break;
			case 2:
				$rodzaj_faktury = 'Faktura VAT';
				break;
		}
		
		$nip = str_replace(Array(
			'-',
			' '
		), '', $orderData['billing_address']['nip']);
		
		$infaktClient = $this->getClients($nip);
		
		if (isset($infaktClient['clients'][0]) && ! empty($infaktClient['clients'][0])){
			$this->updateClient($orderData['billing_address'], $infaktClient['clients'][0]['client_id']);
			$infaktClientId = $infaktClient['clients'][0]['client_id'];
		}
		else{
			$infaktClientNew = $this->addClient($orderData['billing_address']);
			$infaktClientId = $infaktClientNew['client']['client_id'];
		}
		
		switch ($orderData['payment_method']['paymentmethodcontroller']) {
			case 'ondelivery':
			case 'pickup':
				$sposob_platnosci = 'Za pobraniem';
				break;
			case 'banktransfer':
				$sposob_platnosci = 'Przelew';
				break;
			default:
				$sposob_platnosci = 'Inny';
		}
		
		if ($orderData['pricebeforepromotion'] > 0 && ($orderData['pricebeforepromotion'] < $orderData['total'])){
			$rulesCostGross = $orderData['total'] - $orderData['pricebeforepromotion'];
			$rulesCostNet = ($orderData['total'] - $orderData['pricebeforepromotion']) / (1 + ($orderData['delivery_method']['deliverervat'] / 100));
			$rulesVat = $rulesCostGross - $rulesCostNet;
			$orderData['products'][] = Array(
				'name' => $orderData['delivery_method']['deliverername'],
				'quantity' => 1,
				'net_price' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto'] + $rulesCostNet),
				'vat' => sprintf('%01.2f', $orderData['delivery_method']['deliverervat'])
			);
		}
		else{
			$orderData['products'][] = Array(
				'name' => $orderData['delivery_method']['deliverername'],
				'quantity' => 1,
				'net_price' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto']),
				'vat' => sprintf('%01.2f', $orderData['delivery_method']['deliverervat'])
			);
		}
		
		$invoiceContents = Array();
		foreach ($orderData['products'] as $key => $val){
			if ($val['net_price'] > 0){
				$invoiceContents[] = Array(
					'nazwa' => $val['name'],
					'wartosc_netto' => $val['net_price'],
					'ilosc' => $val['quantity'],
					'stawka_vat' => round($val['vat'], 0)
				);
			}
		}
		
		$Invoice = Array(
			'invoice' => Array(
				'status' => 'wysłana',
				'uwagi' => $Data['comment'],
				'rodzaj_faktury' => $rodzaj_faktury,
				'sposob_platnosci' => $sposob_platnosci,
				'client_id' => $infaktClientId,
				'data_wystawienia' => $Data['invoicedate'],
				'data_sprzedazy' => date('Y-m-d', strtotime($orderData['order_date'])),
				'termin_zaplaty' => $Data['duedate'],
				'podpis_sprzedawcy' => $Data['salesperson'],
				'zaplacono' => $Data['totalpayed'],
				'services' => $invoiceContents
			)
		);
		
		$createdInvoice = $this->createInvoice($Invoice);
		if(!isset($createdInvoice['invoice_id']['invoice_id'])){
		    App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem z API Infakt. Skontaktuj się z obsługą WellCommerce');
		    App::redirect(__ADMINPANE__ . '/order/edit/' . $orderId);
		}
		$externalid = $createdInvoice['invoice_id']['invoice_id'];
		$symbol = $createdInvoice['invoice_id']['numer'];
		$this->sendInvoice($externalid);
		
		$invoiceCopyUrl = $this->downloadInvoice($externalid, 'KOPIA');
		$ch = curl_init($invoiceCopyUrl['pdf']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 0);
		$contentCopyHtml = curl_exec($ch);
		curl_close($ch);
		
		$invoiceOriginalUrl = $this->downloadInvoice($externalid, 'org');
		$ch = curl_init($invoiceCopyUrl['pdf']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 0);
		$contentOriginalHtml = curl_exec($ch);
		curl_close($ch);
		
		$sql = "INSERT INTO invoice SET
					symbol = :symbol,
					invoicedate = :invoicedate,
					salesdate = :salesdate,
					paymentduedate = :paymentduedate,
					salesperson = :salesperson,
					invoicetype = :invoicetype,
					comment = :comment,
					contentoriginal = :contentoriginal,
					contentcopy = :contentcopy,
					orderid = :orderid,
					totalpayed = :totalpayed,
					externalid = :externalid,
					contenttype = :contenttype,
					viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('symbol', $symbol);
		$stmt->bindValue('invoicedate', $Data['invoicedate']);
		$stmt->bindValue('salesdate', date('Y-m-d', strtotime($orderData['order_date'])));
		$stmt->bindValue('paymentduedate', $Data['duedate']);
		$stmt->bindValue('salesperson', $Data['salesperson']);
		$stmt->bindValue('invoicetype', $invoiceTypeId);
		$stmt->bindValue('comment', $Data['comment']);
		$stmt->bindValue('contentoriginal', base64_encode($contentOriginalHtml));
		$stmt->bindValue('contentcopy', base64_encode($contentCopyHtml));
		$stmt->bindValue('orderid', $orderId);
		$stmt->bindValue('totalpayed', $Data['totalpayed']);
		$stmt->bindValue('viewid', $orderData['viewid']);
		$stmt->bindValue('externalid', $invoiceId);
		$stmt->bindValue('contenttype', 'pdf');
		$stmt->execute();
	}
}