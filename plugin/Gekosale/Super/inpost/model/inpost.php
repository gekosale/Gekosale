<?php

namespace Gekosale;
use FormEngine;

class InpostModel extends Component\Model\Datagrid
{

    protected $inpost_data_dir;

    protected $inpost_api_url = 'http://api.paczkomaty.pl';

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
        $this->inpost_data_dir = dirname(__FILE__) . '/../data';
    }

    public function checkOptions ()
    {
        $settings = array_filter($this->registry->core->loadModuleSettings('inpost', Helper::getViewId()));
        $dispatchMethodChecked = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
        if (isset($settings['inpostlogin']) && isset($settings['inpostdispatchmethod']) && (int) $settings['inpostdispatchmethod'] > 0 && isset($dispatchMethodChecked['dispatchmethodid']) && ($dispatchMethodChecked['dispatchmethodid'] == $settings['inpostdispatchmethod'])){
            $selectedOption = App::getContainer()->get('session')->getActiveDispatchmethodOption();
            if (! is_array($selectedOption) || (is_array($selectedOption) && isset($selectedOption['option']) && is_numeric($selectedOption['option']) && ($selectedOption['option'] == 0))){
                App::getContainer()->get('session')->setVolatileMessage('Musisz wybrać paczkomat do odbioru przesyłki');
                return App::redirectSeo($this->registry->router->generate('frontend.cart', true));
            }
        }
    }

    protected function initDatagrid ($datagrid)
    {
        $datagrid->setTableData('order', Array(
            'idorder' => Array(
                'source' => 'O.idorder'
            ),
            'client' => Array(
                'source' => 'CONCAT(\'<strong>\',AES_DECRYPT(OC.surname,:encryptionkey),\' \',AES_DECRYPT(OC.firstname,:encryptionkey),\'</strong><br />\',AES_DECRYPT(OC.email,:encryptionkey))'
            ),
            'price' => Array(
                'source' => 'O.price'
            ),
            'globalprice' => Array(
                'source' => 'CONCAT(O.globalprice,\' \',O.currencysymbol)'
            ),
            'dispatchmethodprice' => Array(
                'source' => 'O.dispatchmethodprice'
            ),
            'orderstatusname' => Array(
                'source' => 'OST.name'
            ),
            'adddate' => Array(
                'source' => 'O.adddate'
            ),
            'paczkomat' => Array(
                'source' => 'O.paczkomat'
            ),
            'inpostpackage' => Array(
                'source' => 'O.inpostpackage'
            ),
            'packagestatus' => Array(
                'source' => 'O.packagestatus',
                'processFunction' => Array(
                    $this,
                    'getPackageStatusName'
                )
            ),
            'checkpackagestatus' => Array(
                'source' => 'O.inpostpackage',
                'processFunction' => Array(
                    $this,
                    'getPackageStatus'
                )
            ),
            'clientid' => Array(
                'source' => 'O.clientid'
            ),
            'view' => Array(
                'source' => 'V.name',
                'prepareForSelect' => true
            )
        ));
        
        $datagrid->setFrom('
		`order` O
		LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
		LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = :languageid
		LEFT JOIN orderclientdata OC ON OC.orderid=O.idorder
		LEFT JOIN view V ON V.idview = O.viewid
		');
        
        $datagrid->setAdditionalWhere('
			O.viewid IN (' . Helper::getViewIdsAsString() . ') AND O.paczkomat IS NOT NULL
		');
    }

    public function getPackageStatusName ($status)
    {
        $statuses = Array(
            'Pending' => 'Oczekuje na przygotowanie',
            'Created' => 'Oczekuje na wysyłkę',
            'Prepared' => 'Gotowa do wysyłki',
            'Sent' => 'Przesyłka Nadana',
            'InTransit' => 'W drodze',
            'Stored' => 'Oczekuje na odbiór',
            'Avizo' => 'Ponowne Avizo',
            'Expired' => 'Nie odebrana',
            'Delivered' => 'Dostarczona',
            'RetunedToAgency' => 'Przekazana do Oddziału',
            'Cancelled' => 'Anulowana',
            'Claimed' => 'Przyjęto zgłoszenie reklamacyjne',
            'ClaimProcessed' => 'Rozpatrzono zgłoszenie reklamacyjne'
        );
        return $statuses[$status];
    }

    public function getPackageStatus ($package)
    {
        if (strlen($package) > 0){
        }
        return '';
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function getOrderForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function renderMenu ($event, $menu)
    {
        $Data = Array(
            'item' => 1,
            'name' => 'Inpost Paczkomaty',
            'link' => 'inpost',
            'sort_order' => 99,
            'controller' => 'action'
        );
        $event->setReturnValues($Data);
    }

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        
        $infakt = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'inpost_data',
            'label' => 'Integracja z InPost Paczkomaty'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostlogin',
            'label' => 'Login do InPost'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'inpostpassword',
            'label' => 'Hasło do InPost'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'inpostdispatchmethod',
            'label' => 'Moduł wysyłki',
            'options' => FormEngine\Option::Make(App::getModel('dispatchmethod')->getDispatchmethodToSelect())
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostname',
            'label' => 'Imię nadawcy'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostsurName',
            'label' => 'Nazwisko nadawcy'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostemail',
            'label' => 'E-mail nadawcy'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostphoneNum',
            'label' => 'Numer telefonu nadawcy'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpoststreet',
            'label' => 'Ulica'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostbuildingNo',
            'label' => 'Numer ulicy'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostflatNo',
            'label' => 'Numer lokalu'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inposttown',
            'label' => 'Miasto'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'inpostzipCode',
            'label' => 'Kod pocztowy'
        )));
        
        $infakt->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'inpostprovince',
            'default' => ''
        )));
        
        $settings = $this->registry->core->loadModuleSettings('inpost', (int) $request['id']);
        
        if (! empty($settings)){
            $populate = Array(
                'inpost_data' => Array(
                    'inpostlogin' => $settings['inpostlogin'],
                    'inpostpassword' => $settings['inpostpassword'],
                    'inpostdispatchmethod' => $settings['inpostdispatchmethod'],
                    'inpostname' => isset($settings['inpostname']) ? $settings['inpostname'] : '',
                    'inpostsurName' => isset($settings['inpostsurName']) ? $settings['inpostsurName'] : '',
                    'inpostemail' => isset($settings['inpostemail']) ? $settings['inpostemail'] : '',
                    'inpostphoneNum' => isset($settings['inpostphoneNum']) ? $settings['inpostphoneNum'] : '',
                    'inpoststreet' => isset($settings['inpoststreet']) ? $settings['inpoststreet'] : '',
                    'inpostbuildingNo' => isset($settings['inpostbuildingNo']) ? $settings['inpostbuildingNo'] : '',
                    'inpostflatNo' => isset($settings['inpostflatNo']) ? $settings['inpostflatNo'] : '',
                    'inposttown' => isset($settings['inposttown']) ? $settings['inposttown'] : '',
                    'inpostzipCode' => isset($settings['inpostzipCode']) ? $settings['inpostzipCode'] : ''
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function getOptions ()
    {
        $paczkomaty = $this->inpost_get_machine_list();
        $tmp = Array();
        foreach ($paczkomaty as $paczkomat){
            $town = $paczkomat['town'];
            $tmp[$town][] = Array(
                'id' => $paczkomat['name'],
                'active' => false,
                'label' => $paczkomat['name'] . ' - ' . $paczkomat['street'] . ' ' . $paczkomat['buildingnumber'] . ' (' . $paczkomat['operatinghours'] . ')'
            );
        }
        uksort($tmp, 'strnatcasecmp');
        return $tmp;
    }

    public function addFieldsCartbox ($request)
    {
        $this->towns = $this->inpost_get_towns();
        $settings = $this->registry->core->loadModuleSettings('inpost', Helper::getViewId());
        $method = App::getContainer()->get('session')->getActiveDispatchmethodChecked();
        if ($method['dispatchmethodid'] == $settings['inpostdispatchmethod']){
            echo "<script>
					$(document).ready(function(){
						setTimeout(function() {
							$('#inpostenabled').val(1).trigger('change');
						}, 150);
					});
			</script>";
        }
        else{
            echo "<script>
					$(document).ready(function(){
						setTimeout(function() {
							$('#inpostenabled').val(0).trigger('change');
						}, 150);
					});
					</script>";
        }
        
        $enabled = &$request['form']->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'inpostenabled'
        )));
        
        $infakt = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'inpost_data',
            'label' => 'Wybierz paczkomat do odioru przesyłki',
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::SHOW, $enabled, new FormEngine\Conditions\Equals(1))
            )
        )));
        
        $town = $infakt->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'inposttown',
            'label' => 'Wybierz miejscowość',
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $this->towns),
            'rules' => Array(
                new FormEngine\Rules\Required('Wybierz miejscowość do odbioru paczki')
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::SHOW, $enabled, new FormEngine\Conditions\Equals(1))
            )
        )));
        
        $town = $infakt->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'paczkomat',
            'label' => 'Wybierz Paczkomat',
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::SHOW, $enabled, new FormEngine\Conditions\Equals(1)),
                new FormEngine\Dependency(FormEngine\Dependency::EXCHANGE_OPTIONS, $town, Array(
                    $this,
                    'getPaczkomat'
                ))
            ),
            'rules' => Array(
                new FormEngine\Rules\Required('Wybierz Paczkomat')
            )
        )));
    }

    public function getPaczkomat ($town)
    {
        if (empty($this->towns))
            $this->towns = $this->inpost_get_towns();
        $paczkomaty = $this->inpost_get_machine_list($this->towns[$town]);
        foreach ($paczkomaty as $paczkomat){
            $tmp[$paczkomat['name']] = $paczkomat['name'] . ' - ' . $paczkomat['street'] . ' ' . $paczkomat['buildingnumber'] . ' (' . $paczkomat['operatinghours'] . ')';
        }
        natsort($tmp);
        return FormEngine\Option::Make($tmp);
    }

    public function getInpostOrders ()
    {
        $sql = "SELECT 
        			idorder, 
        			inpostpackage
				FROM  `order`
				WHERE paczkomat != '' AND inpostpackage != '' ";
        $stmt = Db::getInstance()->prepare($sql);
        $Data = Array();
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[$rs['inpostpackage']] = $rs['idorder'];
        }
        return $Data;
    }

    public function getOrderViewIdByInpostPackage ($code)
    {
        $sql = "SELECT	viewid
				FROM  `order`
				WHERE inpostpackage = :code ";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindParam('code', $code);
        $Data = Array();
        try{
            $rs = $stmt->execute();
            if ($rs){
                return $rs['viewid'];
            }
        }
        catch (Exception $e){
            throw new FrontendException($this->registry->core->getMessage('ERR_RULES_CART_NAME'));
        }
    }

    public function getDataByOrderId ($id)
    {
        $sql = "SELECT paczkomat
				FROM  `order`
				WHERE idorder = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        try{
            $rs = $stmt->fetch();
            if ($rs){
                return $rs['paczkomat'];
            }
        }
        catch (Exception $e){
            throw new FrontendException($this->registry->core->getMessage('ERR_RULES_CART_NAME'));
        }
    }

    public function getInpostPackageByOrderId ($id)
    {
        $sql = "SELECT 
        			inpostpackage,
        			inpostdata
				FROM  `order`
				WHERE idorder = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        return Array(
            'inpostpackage' => $rs['inpostpackage'],
            'inpostdata' => unserialize($rs['inpostdata'])
        );
    }

    public function updatePackCodeNo ($id, $code, $data)
    {
        $sql = 'UPDATE `order` SET inpostpackage = :inpostpackage, inpostdata = :inpostdata WHERE idorder = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindParam('inpostpackage', $code);
        $stmt->bindParam('inpostdata', $data);
        $stmt->bindParam('id', $id);
        $stmt->execute();
    }

    public function updatePackageStatus ($code, $status)
    {
        $sql = 'UPDATE `order` SET packagestatus = :status WHERE inpostpackage = :inpostpackage';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindParam('inpostpackage', $code);
        $stmt->bindParam('status', $status);
        $stmt->execute();
    }

    public function saveOrder ($event, $request)
    {
        $orderid = $request['id'];
        $selectedOption = App::getContainer()->get('session')->getActiveDispatchmethodOption();
        if (! empty($selectedOption)){
            $sql = 'UPDATE `order` SET paczkomat = :paczkomat WHERE idorder = :id AND dispatchmethodid = :dispatchmethodid';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('paczkomat', $selectedOption['option']);
            $stmt->bindValue('id', $orderid);
            $stmt->bindValue('dispatchmethodid', $selectedOption['id']);
            $stmt->execute();
        }
    }

    public function saveSettings ($request)
    {
        $Settings = Array(
            'inpostlogin' => $request['data']['inpostlogin'],
            'inpostpassword' => $request['data']['inpostpassword'],
            'inpostdispatchmethod' => $request['data']['inpostdispatchmethod'],
            'inpostname' => $request['data']['inpostname'],
            'inpostsurName' => $request['data']['inpostsurName'],
            'inpostemail' => $request['data']['inpostemail'],
            'inpostphoneNum' => $request['data']['inpostphoneNum'],
            'inpoststreet' => $request['data']['inpoststreet'],
            'inpostbuildingNo' => $request['data']['inpostbuildingNo'],
            'inpostflatNo' => $request['data']['inpostflatNo'],
            'inposttown' => $request['data']['inposttown'],
            'inpostzipCode' => $request['data']['inpostzipCode']
        );
        
        $this->registry->core->saveModuleSettings('inpost', $Settings, $request['id']);
    }

    public function inpost_check_environment ($verbose = 0)
    {
        $status = 1;
        if (! file_exists($this->inpost_data_dir)){
            if ($verbose)
                echo "Paczkomaty API: path to proper data directory must be set (config.php)!<br/>";
            $status = 0;
        }
        
        if (! is_writable("$this->inpost_data_dir/time1.dat")){
            if ($verbose)
                echo "Paczkomaty API: file data/time1.dat must be writable!<br/>";
            $status = 0;
        }
        
        if (! is_writable("$this->inpost_data_dir/time2.dat")){
            if ($verbose)
                echo "Paczkomaty API: file data/time2.dat must be writable!<br/>";
            $status = 0;
        }
        
        if (! is_writable("$this->inpost_data_dir/cache1.dat")){
            if ($verbose)
                echo "Paczkomaty API: file data/cache1.dat must be writable!<br/>";
            $status = 0;
        }
        
        if (! is_writable("$this->inpost_data_dir/cache2.dat")){
            if ($verbose)
                echo "Paczkomaty API: file data/cache2.dat must be writable!<br/>";
            $status = 0;
        }
        
        if (! function_exists('xml_parser_create')){
            if ($verbose)
                echo "Paczkomaty API: PHP xml_parser_create() function is required!<br/>";
            $status = 0;
        }
        
        if (! ini_get('allow_url_fopen')){
            if ($verbose)
                echo "Paczkomaty API: PHP allow_url_fopen setting is required for server communication!<br/>";
            $status = 0;
        }
        
        return $status;
    }

    public function inpost_get_params ()
    {
        if ($Contents = file_get_contents("$this->inpost_api_url/?do=getparams")){
            $parsedXML = $this->inpost_xml2array($Contents);
            $wynik = array();
            foreach ($parsedXML['paczkomaty'] as $name => $array)
                $wynik[$name] = $array['value'];
            $wynik['current_api_version'] = 2.1;
            return $wynik;
        }
        return 0;
    }

    public function inpost_get_machine_list ($town = '', $paymentavailable = '')
    {
        if ($this->inpost_cache_is_valid(1) == 0){
            $this->inpost_download_machines();
        }
        if ($cache = @file_get_contents("$this->inpost_data_dir/cache1.dat")){
            $machineList = unserialize($cache);
            if (count($machineList)){
                if ($town){
                    foreach ($machineList as $machine){
                        if ($machine[4] == $town)
                            $resultList[] = $machine;
                    }
                    $machineList = $resultList;
                }
                if (count($machineList)){
                    $resultList = array();
                    $i = 0;
                    foreach ($machineList as $machine){
                        if (! $paymentavailable || ($paymentavailable == 't' && $machine[7] == 't') || ($paymentavailable == 'f' && $machine[7] == 'f')){
                            $resultList[$i]['name'] = $machine[0];
                            $resultList[$i]['street'] = $machine[1];
                            $resultList[$i]['buildingnumber'] = $machine[2];
                            $resultList[$i]['postcode'] = $machine[3];
                            $resultList[$i]['town'] = $machine[4];
                            $resultList[$i]['latitude'] = $machine[5];
                            $resultList[$i]['longitude'] = $machine[6];
                            if ($machine[7] == 't')
                                $resultList[$i]['paymentavailable'] = 1;
                            else
                                $resultList[$i]['paymentavailable'] = 0;
                            $resultList[$i]['operatinghours'] = $machine[8];
                            $resultList[$i]['locationdescription'] = $machine[9];
                            $resultList[$i]['paymentpointdescr'] = $machine[10];
                            $resultList[$i]['partnerid'] = $machine[11];
                            $resultList[$i]['paymenttype'] = $machine[12];
                            $i ++;
                        }
                    }
                    usort($resultList, Array(
                        $this,
                        'inpost_machine_sort'
                    ));
                    return $resultList;
                }
            }
        }
        return 0;
    }

    public function inpost_get_pricelist ()
    {
        if ($this->inpost_cache_is_valid(2) == 0){
            $this->inpost_download_pricelist();
        }
        if ($cache = @file_get_contents("$this->inpost_data_dir/cache2.dat")){
            return unserialize($cache);
        }
        return 0;
    }

    public function inpost_get_pack_status ($packcode)
    {
        if ($statusContents = @file_get_contents("$this->inpost_api_url/?do=getpackstatus&packcode=$packcode")){
            $parsedXML = $this->inpost_xml2array($statusContents);
            if (isset($parsedXML['paczkomaty']['error'])){
                return array(
                    'error' => array(
                        'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                        'message' => $parsedXML['paczkomaty']['error']['value']
                    )
                );
            }
            $parsedXML = $parsedXML['paczkomaty'];
            $packStatus = $parsedXML['status']['value'];
            return $packStatus;
        }
        return 0;
    }

    public function inpost_machines_dropdown ($param)
    {
        $machines_all = $this->inpost_get_machine_list();
        $machines_with_names_as_keys = array();
        if (! isset($param['paymentavailable']))
            $param['paymentavailable'] = 0;
        
        if (count($machines_all)){
            foreach ($machines_all as $id => $machine){
                if (($param['paymentavailable'] and $machine['paymentavailable']) or ! $param['paymentavailable']){
                    $machines = $machines_with_names_as_keys[$machine['name']] = $machine;
                }
            }
        }
        
        $result = "";
        $result .= "<select ";
        if (isset($param['class']))
            $result .= " class=\"" . $param['class'] . "\"";
        if (isset($param['name']))
            $result .= " name=\"" . $param['name'] . "\"";
        $result .= ">";
        
        if (isset($param['email'])){
            $client = inpost_find_customer($param['email']);
            // paczkomat domyĹ›lny
            if (isset($client['preferedBoxMachineName']) and array_key_exists($client['preferedBoxMachineName'], $machines_with_names_as_keys)){
                $result .= "<option disabled>Paczkomat domyĹ›lny</option>";
                $result .= "<option value=\"" . $client['preferedBoxMachineName'] . "\"";
                
                if (isset($param['email']) and ! isset($param['selected'])){
                    $result .= " selected=\"selected\"";
                    unset($param['selected']);
                }
                
                $result .= ">" . $machines_with_names_as_keys[$client['preferedBoxMachineName']]['name'] . " " . $machines_with_names_as_keys[$client['preferedBoxMachineName']]['street'] . " " . $machines_with_names_as_keys[$client['preferedBoxMachineName']]['buildingnumber'] . ", " . $machines_with_names_as_keys[$client['preferedBoxMachineName']]['postcode'] . " " . $machines_with_names_as_keys[$client['preferedBoxMachineName']]['town'];
                if (isset($param['paymentavailable_suffix']))
                    $result .= $param['paymentavailable_suffix'];
                $result .= "</option>";
            }
            
            // paczkomat alternatywny
            if (isset($client['alternativeBoxMachineName']) and array_key_exists($client['alternativeBoxMachineName'], $machines_with_names_as_keys)){
                $result .= "<option disabled>Paczkomat alternatywny</option>";
                $result .= "<option value=\"" . $client['alternativeBoxMachineName'] . "\"";
                
                if (isset($param['selected']) and $param['selected'] == $client['alternativeBoxMachineName']){
                    $result .= " selected=\"selected\"";
                    unset($param['selected']);
                }
                
                $result .= ">" . $machines_with_names_as_keys[$client['alternativeBoxMachineName']]['name'] . " " . $machines_with_names_as_keys[$client['alternativeBoxMachineName']]['street'] . " " . $machines_with_names_as_keys[$client['alternativeBoxMachineName']]['buildingnumber'] . ", " . $machines_with_names_as_keys[$client['alternativeBoxMachineName']]['postcode'] . " " . $machines_with_names_as_keys[$client['alternativeBoxMachineName']]['town'] . "</option>";
            }
        }
        
        // paczkomaty w pobliĹĽu kodu
        if (isset($param['postcode'])){
            $machines = inpost_find_nearest_machines($param['postcode'], ($param['paymentavailable']) ? 't' : '');
            if (! empty($machines)){
                $result .= "<option disabled>Paczkomaty najbliĹĽej kodu " . $param['postcode'] . "</option>";
                foreach ($machines as $machine){
                    $result .= "<option value=\"" . $machine['name'] . "\"";
                    if (isset($param['selected'])){
                        if ($param['selected'] == $machine['name']){
                            $result .= " selected=\"selected\"";
                            unset($param['selected']);
                        }
                    }
                    
                    $result .= ">" . $machine['name'] . " " . $machine['street'] . " " . $machine['buildingnumber'] . ", " . $machine['postcode'] . " " . $machine['town'] . "</option>";
                }
            }
        }
        
        // wszystkie paczkomaty
        

        if (! empty($machines_with_names_as_keys)){
            $result .= "<option disabled>Wszytkie paczkomaty</option>";
            foreach ($machines_with_names_as_keys as $machine){
                
                $result .= "<option value=\"" . $machine['name'] . "\"";
                
                if (isset($param['selected'])){
                    if ($param['selected'] == $machine['name']){
                        $result .= " selected=\"selected\"";
                        unset($param['selected']);
                    }
                }
                
                $result .= ">" . $machine['name'] . " " . $machine['street'] . " " . $machine['buildingnumber'] . ", " . $machine['postcode'] . " " . $machine['town'] . "</option>";
            }
        }
        
        $result .= "</select>";
        
        return $result;
    }

    public function inpost_find_customer ($email)
    {
        if ($customerContents = @file_get_contents("$this->inpost_api_url/?do=findcustomer&email=$email")){
            $parsedXML = $this->inpost_xml2array($customerContents);
            
            if (isset($parsedXML['paczkomaty']['error'])){
                return array(
                    'error' => array(
                        'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                        'message' => $parsedXML['paczkomaty']['error']['value']
                    )
                );
            }
            $parsedXML = $parsedXML['paczkomaty']['customer'];
            if (isset($parsedXML['email']['value']) and $parsedXML['email']['value'] == $email){
                $preferedBoxMachineName = $parsedXML['preferedBoxMachineName']['value'];
                if (isset($parsedXML['alternativeBoxMachineName']['value']))
                    $alternativeBoxMachineName = $parsedXML['alternativeBoxMachineName']['value'];
                else
                    $alternativeBoxMachineName = '';
                return array(
                    'preferedBoxMachineName' => $preferedBoxMachineName,
                    'alternativeBoxMachineName' => $alternativeBoxMachineName
                );
            }
        }
        return 0;
    }

    public function inpost_find_nearest_machines ($postcode, $paymentavailable = '')
    {
        if ($machinesContents = @file_get_contents("$this->inpost_api_url/?do=findnearestmachines&postcode=$postcode&paymentavailable=$paymentavailable")){
            $parsedXML = $this->inpost_xml2array($machinesContents);
            if (! isset($parsedXML['paczkomaty']['machine']))
                return 0;
            $machines = $parsedXML['paczkomaty']['machine'];
            if (count($machines)){
                $machineList = array();
                $allMachines = $this->inpost_get_machine_list();
                $i = 0;
                if (count($allMachines)){
                    foreach ($allMachines as $machineDetails){
                        foreach ($machines as $machine){
                            if (isset($machine['name']['value']) and $machine['name']['value'] == $machineDetails['name']){
                                $machineList[$i] = $machineDetails;
                                $machineList[$i]['distance'] = $machine['distance']['value'];
                                $i ++;
                            }
                        }
                    }
                }
                usort($machineList, Array(
                    $this,
                    'inpost_machine_distance_sort'
                ));
                return $machineList;
            }
        }
        return 0;
    }

    public function inpost_get_towns ()
    {
        $machines = $this->inpost_get_machine_list();
        
        if (isset($machines) and count($machines)){
            foreach ($machines as $machine){
                $towns[] = $machine['town'];
            }
            $towns = array_unique($towns);
            sort($towns);
            return ($towns);
        }
        
        return 0;
    }

    public function inpost_create_customer_partner ($email, $password, $customerData)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (count($customerData)){
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            $customerXML = "<paczkomaty>\n";
            $customerXML .= "<customer>\n";
            $customerXML .= "<email>" . $customerData['email'] . "</email>\n";
            $customerXML .= "<mobileNumber>" . $customerData['mobileNumber'] . "</mobileNumber>\n";
            $customerXML .= "<preferedBoxMachineName>" . $customerData['preferedBoxMachineName'] . "</preferedBoxMachineName>\n";
            $customerXML .= "<alternativeBoxMachineName>" . $customerData['alternativeBoxMachineName'] . "</alternativeBoxMachineName>\n";
            $customerXML .= "<phoneNum>" . $customerData['phoneNum'] . "</phoneNum>\n";
            $customerXML .= "<street>" . $customerData['street'] . "</street>\n";
            $customerXML .= "<town>" . $customerData['town'] . "</town>\n";
            $customerXML .= "<postCode>" . $customerData['postCode'] . "</postCode>\n";
            $customerXML .= "<building>" . $customerData['building'] . "</building>\n";
            $customerXML .= "<flat>" . $customerData['flat'] . "</flat>\n";
            $customerXML .= "<firstName>" . $customerData['firstName'] . "</firstName>\n";
            $customerXML .= "<lastName>" . $customerData['lastName'] . "</lastName>\n";
            $customerXML .= "<companyName>" . $customerData['companyName'] . "</companyName>\n";
            $customerXML .= "<regon>" . $customerData['regon'] . "</regon>\n";
            $customerXML .= "<nip>" . $customerData['nip'] . "</nip>\n";
            $customerXML .= "</customer>\n";
            $customerXML .= "</paczkomaty>\n";
            
            $customerEmail = $customerData['email'];
            $customerData = array(
                'email' => $email,
                'digest' => $digest,
                'content' => $customerXML
            );
            $postData = http_build_query($customerData);
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=createcustomerpartner", $postData)){
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
                $parsedXML = $parsedXML['paczkomaty']['customer'];
                if (isset($parsedXML['email']['value']) and $parsedXML['email']['value'] == $customerEmail){
                    return array(
                        'email' => $parsedXML['email']['value']
                    );
                }
            }
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_send_packs ($email, $password, $packsData, $autoLabels = 1, $selfSend = 0)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (count($packsData)){
            
            $packsXML = "<paczkomaty>\n";
            $packsXML .= "<autoLabels>$autoLabels</autoLabels>\n";
            $packsXML .= "<selfSend>$selfSend</selfSend>\n";
            foreach ($packsData as $packId => $packData){
                $packsXML .= "<pack>\n";
                $packsXML .= "<id>" . $packId . "</id>\n";
                $packsXML .= "<adreseeEmail>" . $packData['adreseeEmail'] . "</adreseeEmail>\n";
                $packsXML .= "<senderEmail>" . $packData['senderEmail'] . "</senderEmail>\n";
                $packsXML .= "<phoneNum>" . $packData['phoneNum'] . "</phoneNum>\n";
                $packsXML .= "<boxMachineName>" . $packData['boxMachineName'] . "</boxMachineName>\n";
                if (array_key_exists('alternativeBoxMachineName', $packData))
                    $packsXML .= "<alternativeBoxMachineName>" . $packData['alternativeBoxMachineName'] . "</alternativeBoxMachineName>\n";
                $packsXML .= "<packType>" . $packData['packType'] . "</packType>\n";
                if (array_key_exists('customerDelivering', $packData))
                    $packsXML .= "<customerDelivering>" . $packData['customerDelivering'] . "</customerDelivering>\n";
                else
                    $packsXML .= "<customerDelivering>false</customerDelivering>\n";
                $packsXML .= "<insuranceAmount>" . $packData['insuranceAmount'] . "</insuranceAmount>\n";
                $packsXML .= "<onDeliveryAmount>" . $packData['onDeliveryAmount'] . "</onDeliveryAmount>\n";
                if (array_key_exists('customerRef', $packData))
                    $packsXML .= "<customerRef>" . $packData['customerRef'] . "</customerRef>\n";
                if (array_key_exists('senderBoxMachineName', $packData))
                    $packsXML .= "<senderBoxMachineName>" . $packData['senderBoxMachineName'] . "</senderBoxMachineName>\n";
                if (array_key_exists('senderAddress', $packData) and ! empty($packData['senderAddress'])){
                    $packsXML .= "<senderAddress>\n";
                    $tmpFieldsArray = array(
                        'name',
                        'surName',
                        'email',
                        'phoneNum',
                        'street',
                        'buildingNo',
                        'flatNo',
                        'town',
                        'zipCode',
                        'province'
                    );
                    foreach ($tmpFieldsArray as $tmpField){
                        if (array_key_exists($tmpField, $packData['senderAddress']) && ! empty($packData['senderAddress'][$tmpField])){
                            $packsXML .= "<$tmpField>" . $packData['senderAddress'][$tmpField] . "</$tmpField>\n";
                        }
                    }
                    $packsXML .= "</senderAddress>\n";
                }
                $packsXML .= "</pack>\n";
            }
            $packsXML .= "</paczkomaty>\n";
            
            $packsData = array(
                'email' => $email,
                'digest' => $digest,
                'content' => $packsXML
            );
            
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            $postData = http_build_query($packsData);
            if ($packsResponse = $this->inpost_post_request("$this->inpost_api_url/?do=createdeliverypacks", $postData)){
                
                $parsedXML = $this->inpost_xml2array($packsResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
                
                if (isset($parsedXML['paczkomaty']['pack']))
                    $packsData = $parsedXML['paczkomaty']['pack'];
                if (! isset($packsData[0])){
                    $temp = $packsData;
                    $packsData = array();
                    $packsData[0] = $temp;
                }
                if (count($packsData)){
                    foreach ($packsData as $packData){
                        if (isset($packData['packcode']['value']))
                            $resultData[$packData['id']['value']]['packcode'] = $packData['packcode']['value'];
                        if (isset($packData['customerdeliveringcode']['value']))
                            $resultData[$packData['id']['value']]['customerdeliveringcode'] = $packData['customerdeliveringcode']['value'];
                        if (isset($packData['error']['attr']['key']))
                            $resultData[$packData['id']['value']]['error_key'] = $packData['error']['attr']['key'];
                        if (isset($packData['error']['value']))
                            $resultData[$packData['id']['value']]['error_message'] = $packData['error']['value'];
                    }
                    if (isset($resultData))
                        return $resultData;
                    else
                        return array();
                }
            }
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_get_sticker ($email, $password, $packCode, $labelType = '')
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (isset($packCode)){
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            
            $customerData = array(
                'email' => $email,
                'digest' => $digest,
                'packcode' => $packCode,
                'labeltype' => $labelType
            );
            $postData = http_build_query($customerData);
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=getsticker", $postData)){
                if (strpos($customerResponse, 'PDF'))
                    return $customerResponse;
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
            }
            
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_cancel_pack ($email, $password, $packCode)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (isset($packCode)){
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            
            $customerData = array(
                'email' => $email,
                'digest' => $digest,
                'packcode' => $packCode
            );
            $postData = http_build_query($customerData);
            
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=cancelpack", $postData)){
                
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
                else
                    return $customerResponse;
            }
            
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_change_packsize ($email, $password, $packCode, $packSize)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (isset($packCode) && isset($packSize)){
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            
            $customerData = array(
                'email' => $email,
                'digest' => $digest,
                'packcode' => $packCode,
                'packsize' => $packSize
            );
            $postData = http_build_query($customerData);
            
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=change_packsize", $postData)){
                
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
                else
                    return $customerResponse;
            }
            
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_get_stickers ($email, $password, $packCodes, $labelType = '')
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (is_array($packCodes)){
            // $customerEmail = $customerData['email'];
            $customerData = array(
                'email' => $email,
                'digest' => $digest,
                'packcodes' => $packCodes,
                'labeltype' => $labelType
            );
            
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            $postData = http_build_query($customerData);
            
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=getstickers", $postData)){
                if (strpos($customerResponse, 'PDF'))
                    return $customerResponse;
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
            }
            
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_set_customer_ref ($email, $password, $packCode, $customerRef)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (isset($packCode)){
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            
            // $customerEmail = $customerData['email'];
            $customerData = array(
                'email' => $email,
                'digest' => $digest,
                'packcode' => $packCode,
                'customerref' => $customerRef
            );
            $postData = http_build_query($customerData);
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=setcustomerref", $postData)){
                if (strpos($customerResponse, 'Set'))
                    return 1;
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
            }
            
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_get_confirm_printout ($email, $password, $packCodes, $testPrintout = 0)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        
        $digest = $this->inpost_digest($password);
        
        if (is_array($packCodes)){
            $_lastArgSeparatorOutput = ini_get('arg_separator.output');
            ini_set('arg_separator.output', '&');
            
            $packsXML = "<paczkomaty>\n";
            $packsXML .= "<testprintout>$testPrintout</testprintout>\n";
            foreach ($packCodes as $packCode){
                $packsXML .= "<pack>\n";
                $packsXML .= "<packcode>" . $packCode . "</packcode>\n";
                $packsXML .= "</pack>\n";
            }
            $packsXML .= "</paczkomaty>\n";
            
            $packsData = array(
                'email' => $email,
                'digest' => $digest,
                'content' => $packsXML
            );
            $postData = http_build_query($packsData);
            
            if ($customerResponse = $this->inpost_post_request("$this->inpost_api_url/?do=getconfirmprintout", $postData)){
                if (strpos($customerResponse, 'PDF'))
                    return $customerResponse;
                $parsedXML = $this->inpost_xml2array($customerResponse);
                if (isset($parsedXML['paczkomaty']['error'])){
                    ini_set('arg_separator.output', $_lastArgSeparatorOutput);
                    return array(
                        'error' => array(
                            'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                            'message' => $parsedXML['paczkomaty']['error']['value']
                        )
                    );
                }
            }
            
            ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        }
        return 0;
    }

    public function inpost_get_packs_by_sender ($email, $password, $parameters = array())
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        $digest = $this->inpost_digest($password);
        $paramData = array(
            'email' => $email,
            'digest' => $digest
        );
        
        if (isset($parameters['status']))
            $paramData['status'] = $parameters['status'];
        if (isset($parameters['startdate']))
            $paramData['startdate'] = $parameters['startdate'];
        if (isset($parameters['enddate']))
            $paramData['enddate'] = $parameters['enddate'];
        if (isset($parameters['is_conf_printed']))
            $paramData['is_conf_printed'] = $parameters['is_conf_printed'];
        
        $_lastArgSeparatorOutput = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');
        
        $postData = http_build_query($paramData);
        if ($packsResponse = $this->inpost_post_request("$this->inpost_api_url/?do=getpacksbysender", $postData)){
            $parsedXML = $this->inpost_xml2array($packsResponse);
            
            $packsData = $parsedXML['paczkomaty']['pack'];
            if (! isset($packsData[0])){
                $temp = $packsData;
                $packsData = array();
                $packsData[0] = $temp;
            }
            if (count($packsData)){
                $i = 0;
                foreach ($packsData as $packData){
                    foreach ($packData as $param => $value){
                        if (isset($value['value']))
                            $resultData[$i][$param] = $value['value'];
                        else
                            $resultData[$i][$param] = '';
                    }
                    $i ++;
                }
                return $resultData;
            }
        }
        
        ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        
        return 0;
    }

    public function inpost_get_cod_report ($email, $password, $parameters = array())
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        $digest = $this->inpost_digest($password);
        $paramData = array(
            'email' => $email,
            'digest' => $digest
        );
        
        if (isset($parameters['startdate']))
            $paramData['startdate'] = $parameters['startdate'];
        if (isset($parameters['enddate']))
            $paramData['enddate'] = $parameters['enddate'];
        
        $_lastArgSeparatorOutput = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');
        
        $postData = http_build_query($paramData);
        if ($packsResponse = $this->inpost_post_request("$this->inpost_api_url/?do=getcodreport", $postData)){
            $parsedXML = $this->inpost_xml2array($packsResponse);
            
            $packsData = $parsedXML['paczkomaty']['payment'];
            if (! isset($packsData[0])){
                $temp = $packsData;
                $packsData = array();
                $packsData[0] = $temp;
            }
            if (count($packsData)){
                $i = 0;
                foreach ($packsData as $packData){
                    foreach ($packData as $param => $value){
                        if (isset($value['value']))
                            $resultData[$i][$param] = $value['value'];
                        else
                            $resultData[$i][$param] = '';
                    }
                    $i ++;
                }
                return $resultData;
            }
        }
        
        ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        
        return 0;
    }

    public function inpost_pay_for_pack ($email, $password, $packcode)
    {
        $this->inpost_api_url = str_replace('http://', 'https://', $this->inpost_api_url);
        $digest = $this->inpost_digest($password);
        $paramData = array(
            'email' => $email,
            'digest' => $digest,
            'packcode' => $packcode
        );
        
        $_lastArgSeparatorOutput = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');
        
        $postData = http_build_query($paramData);
        if ($packsResponse = $this->inpost_post_request("$this->inpost_api_url/?do=payforpack", $postData)){
            
            $parsedXML = $this->inpost_xml2array($packsResponse);
            
            if (isset($parsedXML['paczkomaty']['error'])){
                return array(
                    'error' => array(
                        'key' => $parsedXML['paczkomaty']['error']['attr']['key'],
                        'message' => $parsedXML['paczkomaty']['error']['value']
                    )
                );
            }
            else
                return 1;
        }
        
        ini_set('arg_separator.output', $_lastArgSeparatorOutput);
        
        return 0;
    }

    public function inpost_cache_is_valid ($cache)
    {
        if (isset($cache)){
            $cachedTimestamp = @file_get_contents("$this->inpost_data_dir/time$cache.dat");
            if ($lastModifiedContents = @file_get_contents("$this->inpost_api_url/?do=getparams")){
                $parsedXML = $this->inpost_xml2array($lastModifiedContents);
                $lastModifiedTimestamp = $parsedXML['paczkomaty']['last_update']['value'];
                if ($lastModifiedTimestamp > $cachedTimestamp)
                    return 0;
                return 1;
            }
        }
        return - 1;
    }

    public function inpost_download_machines ()
    {
        if ($machinesContents = @file_get_contents("$this->inpost_api_url/?do=listmachines_csv")){
            $machinesArray = explode("\n", $machinesContents);
            $machinesChecksum = $machinesArray[0];
            $machinesContents = substr($machinesContents, strlen($machinesChecksum) + 1);
            if ($machinesChecksum != $this->inpost_crc16($machinesContents))
                return 0;
            if (count($machinesArray)){
                array_shift($machinesArray);
                foreach ($machinesArray as $machine){
                    $machine = explode(";", $machine);
                    $data[] = $machine;
                }
                
                if (($cacheHandle = @fopen("$this->inpost_data_dir/cache1.dat", "wb")) && ($timeHandle = @fopen("$this->inpost_data_dir/time1.dat", "w"))){
                    fwrite($cacheHandle, serialize($data));
                    fclose($cacheHandle);
                    fwrite($timeHandle, time());
                    fclose($timeHandle);
                    return 1;
                }
            }
        }
        return 0;
    }

    public function inpost_download_pricelist ()
    {
        if ($pricelistContents = @file_get_contents("$this->inpost_api_url/?do=pricelist")){
            $parsedXML = $this->inpost_xml2array($pricelistContents);
            $parsedXML = $parsedXML['paczkomaty'];
            if (isset($parsedXML['on_delivery_payment']))
                $data['on_delivery_payment'] = $parsedXML['on_delivery_payment']['value'];
            if (isset($parsedXML['packtype']) and count($parsedXML['packtype'])){
                foreach ($parsedXML['packtype'] as $packtype){
                    $data[$packtype['type']['value']] = $packtype['price']['value'];
                }
                if (! isset($parsedXML['insurance'][0]['limit'])){
                    $temp = $parsedXML['insurance'];
                    $parsedXML['insurance'] = array();
                    $parsedXML['insurance'][] = $temp;
                }
                
                foreach ($parsedXML['insurance'] as $insurance){
                    $data['insurance'][$insurance['limit']['value']] = $insurance['price']['value'];
                }
                
                if (($cacheHandle = fopen("$this->inpost_data_dir/cache2.dat", "wb")) && ($timeHandle = fopen("$this->inpost_data_dir/time2.dat", "w"))){
                    fwrite($cacheHandle, serialize($data));
                    fclose($cacheHandle);
                    fwrite($timeHandle, time());
                    fclose($timeHandle);
                    return 1;
                }
            }
        }
        return 0;
    }

    public function inpost_machine_sort ($m1, $m2)
    {
        return strcmp($m1["name"], $m2["name"]);
    }

    public function inpost_machine_distance_sort ($m1, $m2)
    {
        if ($m1['distance'] == $m2['distance'])
            return 0;
        return ($m1['distance'] < $m2['distance']) ? - 1 : 1;
    }

    public function inpost_crc16 ($data)
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i ++){
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }

    public function inpost_xml2array ($contents, $get_attributes = 1)
    {
        if (! $contents)
            return array();
        if (! function_exists('xml_parser_create')){
            return array();
        }
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $contents, $xml_values);
        xml_parser_free($parser);
        
        if (! $xml_values)
            return;
        
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = &$xml_array;
        
        foreach ($xml_values as $data){
            unset($attributes, $value);
            extract($data);
            $result = '';
            if ($get_attributes){
                $result = array();
                if (isset($value))
                    $result['value'] = $value;
                if (isset($attributes)){
                    foreach ($attributes as $attr => $val){
                        if ($get_attributes == 1)
                            $result['attr'][$attr] = $val;
                    }
                }
            }
            elseif (isset($value)){
                $result = $value;
            }
            
            if ($type == 'open'){
                $parent[$level - 1] = &$current;
                if (! is_array($current) or (! in_array($tag, array_keys($current)))){
                    $current[$tag] = $result;
                    $current = &$current[$tag];
                }
                else{
                    if (isset($current[$tag][0])){
                        array_push($current[$tag], $result);
                    }
                    else{
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                    }
                    $last = count($current[$tag]) - 1;
                    $current = &$current[$tag][$last];
                }
            }
            elseif ($type == 'complete'){
                if (! isset($current[$tag])){
                    $current[$tag] = $result;
                }
                else{
                    if ((is_array($current[$tag]) and $get_attributes == 0) or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)){
                        array_push($current[$tag], $result);
                    }
                    else{
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                    }
                }
            }
            elseif ($type == 'close'){
                $current = &$parent[$level - 1];
            }
        }
        return ($xml_array);
    }

    public function inpost_post_request ($url, $data)
    {
        $_lastArgSeparatorOutput = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');
        
        $params = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
            )
        );
        
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (! $fp){
            return 0;
        }
        
        $response = '';
        while (! feof($fp)){
            $response .= fread($fp, 8192);
        }
        
        if ($response === false || $response == ''){
            return 0;
        }
        return $response;
        ini_set('arg_separator.output', $_lastArgSeparatorOutput);
    }

    public function inpost_digest ($string)
    {
        return base64_encode(md5($string, true));
    }
}
