<?php
/*
 * Kin Lane - @kinlane PHP Library to support xAuth for Instapapers REST API.
 * This is a strip down and rework of @abraham Twitter OAuth -
 * https://github.com/abraham/twitteroauth His was just so well written, it made
 * sense to reuse. Thanks @abraham!
 */
namespace Gekosale;

use FormEngine;
use SoapClient;

class SmsApiModel extends Component\Model
{
	const SMSAPI_ENDPOINT = 'http://api.smsapi.pl/sms.do';

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	public function addFields ($event, $request)
	{
	    $form = &$request['form'];
	    
		$smsapi = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'smsapi_data',
			'label' => 'Integracja z SMSApi.pl'
		)));

		$smsapi->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Jeżeli nie posiadasz jeszcze konta w serwisie SMSApi.pl, załóż je na stronie <a href="https://ssl.smsapi.pl/rejestracja/wellcommerce" target="_blank">https://ssl.smsapi.pl/rejestracja/wellcommerce</a>.</p>'
		)));

		$smsapi->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'smsapi_username',
			'label' => 'Nazwa użytkownika'
		)));

		$smsapi->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'smsapi_password',
			'label' => 'Hasło użytkownika'
		)));

		$smsapi->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Zaznaczenie spowoduje wysyłanie SMS jako wiadomości ECO. Odznaczenie tej opcji spowoduje wysyłanie wiadomości w trybie PRO i wymaga aby nadawca był zarejestrowany w systemie SMSApi.<br/>Przyjmowane są tylko nazwy zweryfikowane. Pole <b>nadawcy wiadomości</b> należy dodać po zalogowaniu na stronie SMSApi, w zakładce USTAWIENIA &gt; POLA NADAWCY a następnie wprowadzić w poniższym formularzu.</p>'
		)));

		$eco = $smsapi->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'smsapi_eco',
			'label' => 'Wysyłanie wiadomości ECO'
		)));

		$smsapi->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'smsapi_from',
			'label' => 'Nazwa nadawcy wiadomości',
			'dependencies' => array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $eco, new FormEngine\Conditions\Equals(1))
			)
		)));

		$smsapi->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>SMS w trybie testowym nie zostanie wysłany do klienta. Zamiast tego jego treść zostanie wysłana poprzez e-mail na adres sklepu.'
		)));

		$smsapi->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'smsapi_test',
			'label' => 'Tryb testowy'
		)));

		$settings = $this->registry->core->loadModuleSettings('smsapi', (int) $request['id']);

		if (! empty($settings)){
			$populate = Array(
				'smsapi_data' => Array(
					'smsapi_username' => $settings['smsapi_username'],
					'smsapi_password' => $settings['smsapi_password'],
					'smsapi_eco' => $settings['smsapi_eco'],
					'smsapi_test' => $settings['smsapi_test']
				)
			);
		}
		else{
			$populate = Array(
				'smsapi_data' => Array(
					'smsapi_username' => '',
					'smsapi_password' => '',
					'smsapi_eco' => 1,
					'smsapi_test' => 1
				)
			);
		}

		$event->setReturnValues($populate);
	}

	public function saveSettings ($request)
	{
		$Settings = Array(
			'smsapi_username' => $request['data']['smsapi_username'],
			'smsapi_password' => $request['data']['smsapi_password'],
			'smsapi_eco' => $request['data']['smsapi_eco'],
			'smsapi_test' => $request['data']['smsapi_test']
		);

		$this->registry->core->saveModuleSettings('smsapi', $Settings, $request['id']);
	}

	public function clearMessage ($message)
	{
		$a = array(
			'À',
			'Á',
			'Â',
			'Ã',
			'Ä',
			'Å',
			'Æ',
			'Ç',
			'È',
			'É',
			'Ê',
			'Ë',
			'Ì',
			'Í',
			'Î',
			'Ï',
			'Ð',
			'Ñ',
			'Ò',
			'Ó',
			'Ô',
			'Õ',
			'Ö',
			'Ø',
			'Ù',
			'Ú',
			'Û',
			'Ü',
			'Ý',
			'ß',
			'à',
			'á',
			'â',
			'ã',
			'ä',
			'å',
			'æ',
			'ç',
			'è',
			'é',
			'ê',
			'ë',
			'ì',
			'í',
			'î',
			'ï',
			'ñ',
			'ò',
			'ó',
			'ô',
			'õ',
			'ö',
			'ø',
			'ù',
			'ú',
			'û',
			'ü',
			'ý',
			'ÿ',
			'Ā',
			'ā',
			'Ă',
			'ă',
			'Ą',
			'ą',
			'Ć',
			'ć',
			'Ĉ',
			'ĉ',
			'Ċ',
			'ċ',
			'Č',
			'č',
			'Ď',
			'ď',
			'Đ',
			'đ',
			'Ē',
			'ē',
			'Ĕ',
			'ĕ',
			'Ė',
			'ė',
			'Ę',
			'ę',
			'Ě',
			'ě',
			'Ĝ',
			'ĝ',
			'Ğ',
			'ğ',
			'Ġ',
			'ġ',
			'Ģ',
			'ģ',
			'Ĥ',
			'ĥ',
			'Ħ',
			'ħ',
			'Ĩ',
			'ĩ',
			'Ī',
			'ī',
			'Ĭ',
			'ĭ',
			'Į',
			'į',
			'İ',
			'ı',
			'Ĳ',
			'ĳ',
			'Ĵ',
			'ĵ',
			'Ķ',
			'ķ',
			'Ĺ',
			'ĺ',
			'Ļ',
			'ļ',
			'Ľ',
			'ľ',
			'Ŀ',
			'ŀ',
			'Ł',
			'ł',
			'Ń',
			'ń',
			'Ņ',
			'ņ',
			'Ň',
			'ň',
			'ŉ',
			'Ō',
			'ō',
			'Ŏ',
			'ŏ',
			'Ő',
			'ő',
			'Œ',
			'œ',
			'Ŕ',
			'ŕ',
			'Ŗ',
			'ŗ',
			'Ř',
			'ř',
			'Ś',
			'ś',
			'Ŝ',
			'ŝ',
			'Ş',
			'ş',
			'Š',
			'š',
			'Ţ',
			'ţ',
			'Ť',
			'ť',
			'Ŧ',
			'ŧ',
			'Ũ',
			'ũ',
			'Ū',
			'ū',
			'Ŭ',
			'ŭ',
			'Ů',
			'ů',
			'Ű',
			'ű',
			'Ų',
			'ų',
			'Ŵ',
			'ŵ',
			'Ŷ',
			'ŷ',
			'Ÿ',
			'Ź',
			'ź',
			'Ż',
			'ż',
			'Ž',
			'ž',
			'ſ',
			'ƒ',
			'Ơ',
			'ơ',
			'Ư',
			'ư',
			'Ǎ',
			'ǎ',
			'Ǐ',
			'ǐ',
			'Ǒ',
			'ǒ',
			'Ǔ',
			'ǔ',
			'Ǖ',
			'ǖ',
			'Ǘ',
			'ǘ',
			'Ǚ',
			'ǚ',
			'Ǜ',
			'ǜ',
			'Ǻ',
			'ǻ',
			'Ǽ',
			'ǽ',
			'Ǿ',
			'ǿ',
			'Ą',
			'Ć',
			'Ę',
			'Ł',
			'Ń',
			'Ó',
			'Ś',
			'Ź',
			'Ż',
			'ą',
			'ć',
			'ę',
			'ł',
			'ń',
			'ó',
			'ś',
			'ź',
			'ż'
		);
		$b = array(
			'A',
			'A',
			'A',
			'A',
			'A',
			'A',
			'AE',
			'C',
			'E',
			'E',
			'E',
			'E',
			'I',
			'I',
			'I',
			'I',
			'D',
			'N',
			'O',
			'O',
			'O',
			'O',
			'O',
			'O',
			'U',
			'U',
			'U',
			'U',
			'Y',
			's',
			'a',
			'a',
			'a',
			'a',
			'a',
			'a',
			'ae',
			'c',
			'e',
			'e',
			'e',
			'e',
			'i',
			'i',
			'i',
			'i',
			'n',
			'o',
			'o',
			'o',
			'o',
			'o',
			'o',
			'u',
			'u',
			'u',
			'u',
			'y',
			'y',
			'A',
			'a',
			'A',
			'a',
			'A',
			'a',
			'C',
			'c',
			'C',
			'c',
			'C',
			'c',
			'C',
			'c',
			'D',
			'd',
			'D',
			'd',
			'E',
			'e',
			'E',
			'e',
			'E',
			'e',
			'E',
			'e',
			'E',
			'e',
			'G',
			'g',
			'G',
			'g',
			'G',
			'g',
			'G',
			'g',
			'H',
			'h',
			'H',
			'h',
			'I',
			'i',
			'I',
			'i',
			'I',
			'i',
			'I',
			'i',
			'I',
			'i',
			'IJ',
			'ij',
			'J',
			'j',
			'K',
			'k',
			'L',
			'l',
			'L',
			'l',
			'L',
			'l',
			'L',
			'l',
			'l',
			'l',
			'N',
			'n',
			'N',
			'n',
			'N',
			'n',
			'n',
			'O',
			'o',
			'O',
			'o',
			'O',
			'o',
			'OE',
			'oe',
			'R',
			'r',
			'R',
			'r',
			'R',
			'r',
			'S',
			's',
			'S',
			's',
			'S',
			's',
			'S',
			's',
			'T',
			't',
			'T',
			't',
			'T',
			't',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'W',
			'w',
			'Y',
			'y',
			'Y',
			'Z',
			'z',
			'Z',
			'z',
			'Z',
			'z',
			's',
			'f',
			'O',
			'o',
			'U',
			'u',
			'A',
			'a',
			'I',
			'i',
			'O',
			'o',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'U',
			'u',
			'A',
			'a',
			'AE',
			'ae',
			'O',
			'o',
			'A',
			'C',
			'E',
			'L',
			'N',
			'O',
			'S',
			'Z',
			'Z',
			'a',
			'c',
			'e',
			'l',
			'n',
			'o',
			's',
			'z',
			'z'
		);
		return substr(str_replace($a, $b, $message), 0, 180);
	}

	public function parseNumber ($number)
	{
		// dodamy parsowanie i weryfikowanie numeru
		$chars = array(
			'-',
			',',
			' ',
			'+'
		);
		$number = str_replace($chars, '', $number);
		$number = trim($number);
		return $number;
	}

	public function notifyUser ($order, $comment)
	{
		$Data = App::getModel('orderstatus')->getOrderStatusTranslation($comment['status']);

		$statusName = isset($Data[Helper::getLanguageId()]['name']) ? $Data[Helper::getLanguageId()]['name'] : '';

		$client = ($order['delivery_address']['companyname'] != '') ? $order['delivery_address']['companyname'] : $order['delivery_address']['firstname'] . ' ' . $order['delivery_address']['surname'];

		$search = Array(
			'{ORDER_CLIENT}',
			'{ORDER_ID}',
			'{ORDER_DATE}',
			'{ORDER_STATUS}'
		);

		$replace = Array(
			$client,
			$order['order_id'],
			date('d.m.Y', strtotime($order['order_date'])),
			$statusName
		);

		$message = $this->clearMessage(str_replace($search, $replace, $comment['smscomment']));

		$recipient = $this->parseNumber($order['delivery_address']['phone']);

		$result = $this->sendMessage($recipient, $message, $order['viewid']);
	}

	public function sendMessage ($recipient, $message, $viewid)
	{
		$settings = $this->registry->core->loadModuleSettings('smsapi', $viewid);
		$viewData = App::getModel('view')->getView($viewid);

		$username = $settings['smsapi_username'];
		$password = md5($settings['smsapi_password']);
		$to = $recipient;
		$from = $settings['smsapi_eco'] ? @ $settings['smsapi_from'] : '';
		$eco = $settings['smsapi_eco'];
		$test = $settings['smsapi_test'];
		$details = 1;
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, self::SMSAPI_ENDPOINT);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, 'username=' . $username . '&password=' . $password . '&from=' . $from . '&details=' . $details . '&test=' . $test . '&eco=' . $eco . '&to=' . $to . '&message=' . $message);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($c);
		curl_close($c);

		if ($settings['smsapi_test'] == 1){
			@mail($viewData['mailer']['fromemail'], 'SMS testowy dla numeru ' . $recipient, $content);
		}
		return $content;
	}
}