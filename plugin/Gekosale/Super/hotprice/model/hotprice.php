<?php
/*
 * Kin Lane - @kinlane PHP Library to support xAuth for Instapapers REST API.
 * This is a strip down and rework of @abraham Twitter OAuth -
 * https://github.com/abraham/twitteroauth His was just so well written, it made
 * sense to reuse. Thanks @abraham!
 */
namespace Gekosale;

use FormEngine;
use Exception;

class HotpriceModel extends Component\Model
{
	const HOTPRICE_API_URL = 'http://api2.hotprice.pl/';

	protected $populateData;

	public function addFields ($event, $request)
	{
	    $form = &$request['form'];
	    
		$hotprice = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'hotprice_data',
			'label' => 'Integracja z Hotprice.pl'
		)));

		$hotprice->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Jeżeli nie posiadasz jeszcze konta w serwisie Hotprice.pl, załóż je na stronie <a href="http://hotprice.pl/partner" target="_blank">http://hotprice.pl/partner</a>.</p>'
		)));

		$hotprice->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'hotprice_email',
			'label' => 'Adres e-mail użytkownika'
		)));

		$hotprice->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'hotprice_password',
			'label' => 'Hasło użytkownika'
		)));

		$hotprice->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'hotprice_partner',
			'label' => 'Unikalny kod partnera'
		)));

		$settings = $this->registry->core->loadModuleSettings('hotprice', (int) $request['id']);

		if (! empty($settings['hotprice_email'])){
			$populate = Array(
				'hotprice_data' => Array(
					'hotprice_email' => $settings['hotprice_email'],
					'hotprice_password' => $settings['hotprice_password'],
					'hotprice_partner' => $settings['hotprice_partner'],
				)
			);
		}
		else{
			$populate = Array(
				'hotprice_data' => Array(
					'hotprice_email' => '',
					'hotprice_password' => '',
					'hotprice_partner' => '',
				)
			);
		}

		$event->setReturnValues($populate);
	}

	public function saveSettings ($request)
	{
		$Settings = Array(
			'hotprice_email' => $request['data']['hotprice_email'],
			'hotprice_password' => $request['data']['hotprice_password'],
			'hotprice_partner' => $request['data']['hotprice_partner'],
		);

		$this->registry->core->saveModuleSettings('hotprice', $Settings, $request['id']);
	}

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'coupons',
			'action' => '',
			'method' => 'post'
		));

		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));

		$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Generowania kuponów spowoduje dodanie wybranej ilości kuponów, o ustalonych parametrach z losowymi kodami tak aby mogły być dystrybuowane wśród użytkowników.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));

		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_TOPIC')
		)));

		$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Jeśli ustawisz przedrostek <b>KOD-</b> zostaną wygenerowane kupony typu <b>KOD-12345</b>, <b>KOD-AbCdE</b> itp.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'prefix',
			'label' => 'Przedrostek dla kuponów',
		)));

		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'qty',
			'label' => 'Ilość wygenerowanych kuponów',
			'comment' => 'Od 1 do 9999',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_CART_QUANTITY_IS_NOT_NUMERIC')),
				new FormEngine\Rules\Format($this->trans('ERR_CART_QUANTITY_IS_NOT_NUMERIC'), '/^\d{1,4}$/')
			)
		)));

		/*
		$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Jeśli chcesz, by kupon obowiązywał zawsze, zostaw puste pola z datą</strong></p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$requiredData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'datefrom',
			'label' => $this->trans('TXT_START_DATE')
		)));

		$requiredData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'dateto',
			'label' => $this->trans('TXT_END_DATE')
		)));

		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'globalqty',
			'label' => 'Globalna ilość kuponów',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			),
			'default' => 1000
		)));
		*/

		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => $this->trans('TXT_PROMOTIONRULE_DISCOUNT_DATA')
		)));

		$additionalData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Możesz podać wysokość zniżki kwotowo (modyfikator "-") lub procentową wartość zamówienia po wykorzystaniu kuponu. Wprowadzając wartość 10:</p>
			<ul>
				<li><strong>dla modyfikatora "-"</strong> obniżysz kwotę zamówienia o 10 w danej walucie</li>
				<li><strong>dla modyfikatora "%"</strong> kwota zamówienia wyniesie 10% pierwotnej wartości</li>
			</ul>
			<p><strong>Przykład:</strong> Chcąc udzielić klientowi 10% rabatu na zamówienie, wprowadź wartość 90 i wybierz modyfikator "%".</p>
		'
		)));

		$additionalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'suffixtypeid',
			'label' => $this->trans('TXT_SUFFIXTYPE'),
			'options' => FormEngine\Option::Make(App::getModel('coupons')->getCouponSuffixTypesForSelect())
		)));

		$additionalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'discount',
			'label' => $this->trans('TXT_VALUE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			)
		)));

		$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'freeshipping',
			'label' => 'Darmowa wysyłka',
			'comment' => 'Zwolnienie z kosztów wysyłki przy wykorzystaniu kuponu'
		)));

		$additionalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'minimumordervalue',
			'label' => $this->trans('TXT_MINIMUM_ORDER_VALUE'),
			'comment' => $this->trans('TXT_MINIMUM_ORDER_VALUE_HELP'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
			),
			'default' => 0
		)));

		$additionalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'currencyid',
			'label' => $this->trans('TXT_KIND_OF_CURRENCY'),
			'options' => FormEngine\Option::Make(App::getModel('currencieslist')->getCurrencyForSelect()),
			'default' => 0,
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CURRENCY'))
			),
			'default' => App::getContainer()->get('session')->getActiveShopCurrencyId()
		)));

		$excludeData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'exclude_data',
			'label' => 'Wykluczenie kategorii'
		)));

		$excludeData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'category',
			'label' => $this->trans('TXT_CATEGORY'),
			'choosable' => false,
			'selectable' => true,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getChildCategories(),
			'load_children' => Array(
				App::getModel('category'),
				'getChildCategories'
			)
		)));

		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));

		$layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW'),
			'default' => Helper::getViewIdsDefault()
		)));

		$Data = Event::dispatch($this, 'admin.coupons.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));

		if (! empty($Data)){
			$form->Populate($Data);
		}

		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());

		return $form;
	}

	public function initExportForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'coupons',
			'action' => '',
			'method' => 'post'
		));

		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));

		$coupons = $requiredData->addChild(new FormEngine\Elements\Select(array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $this->getCouponsNames()),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		return $form;
	}

	public function getCouponsNames ()
	{
		$sql = 'SELECT
				CT.name, COUNT(*) AS total
			FROM
				coupons C
			INNER JOIN
				couponstranslation CT ON C.idcoupons = CT.couponsid AND CT.languageid = :languageid
			GROUP BY CT.name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$data = Array();
		while ($rs = $stmt->fetch()){
			$data[$rs['name']] = $rs['name'] . ' (' . $rs['total'] . ')';
		}
		return $data;
	}

	public function exportCoupons ($name)
	{
		$sql = "SELECT
				C.code
			FROM
				coupons C
			INNER JOIN
				couponstranslation CT ON C.idcoupons = CT.couponsid AND CT.languageid = :languageid
			WHERE CT.name = :name
		";

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();

		$data = Array();
		while ($rs = $stmt->fetch()){
			$data[] = Array(
				'code' => $rs['code'],
			);
		}

		$filename = 'coupons_' . date('Y_m_d_H_i_s') . '.csv';
		if (isset($data[0])){
			$header = array_keys($data[0]);
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$fp = fopen("php://output", 'w');
			fputcsv($fp, $header);
			foreach ($data as $key => $values){
				fputcsv($fp, $values);
			}
			fclose($fp);
			exit();
		}
		else{
			return 0;
		}
	}

	public function doRequest ($method, $params = array())
	{
		$query = array(
			'jsonrpc' => '2.0',
			'id' => time(),
			'method' => $method,
			'params' => $params
		);

		$ci = curl_init();

		curl_setopt($ci, CURLOPT_USERAGENT, 'WellCommerce API');
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ci, CURLOPT_POST, TRUE);
		curl_setopt($ci, CURLOPT_POSTFIELDS, json_encode($query));

		curl_setopt($ci, CURLOPT_URL, self::HOTPRICE_API_URL);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ci);
		curl_close($ci);

		if( $response === FALSE) {
			throw new Exception('Nie można połączyć się z serwerem API');
		}

		$result = json_decode($response, TRUE);

		if ( !isset($result['result'])) {
			if( isset($result['error'])) {
				throw new Exception('Wystąpił błąd: ' . $result['error']['message'] . ' Kod: ' . $result['error']['code']);
			}
			throw new Exception('Błędna odpowiedź z serwera API');
		}

		return $result['result'];
	}

	public function couponUse($coupon)
	{
		$settings = $this->registry->core->loadModuleSettings('hotprice', Helper::getViewId());

		if ( empty($settings['hotprice_partner'])) {
			return ;
		}

		try {
			$this->doRequest('Coupons.use', array(
				'code' => $coupon,
				'partner' => $settings['hotprice_partner']
			));

			return TRUE;
		}
		catch(Exception $e) {

			return FALSE;
		}
	}
}