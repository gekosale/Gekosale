<?php

class authDataV1
{

    public $login;

    public $masterFid;

    public $password;

    public function __construct ($login, $fid, $pass)
    {
        $this->login = $login;
        $this->masterFid = $fid;
        $this->password = $pass;
    }
}

class DpdApi
{
    
    /*
	 * DPD - class provide access to DPD webservice Darek Gorecki 09.2011 Based
	 * on official documentation (http://dpd.com.pl)
	 */
    private $authData = null;

    private $host = null;

    private $soapClient = null;

    private $baseFolder = null;

    public $current_year = null;

    public $current_month = null;

    private $department = null;

    private $lang = null;

    public $login = null;

    public $password = null;

    public $masterfid = null;

    public $shipFrom = null;

    public $shipTo = null;

    public $packageDetails = null;

    public function __construct ()
    {
        $this->current_year = date("Y");
        $this->current_month = date("m");
    }

    public function setFolder ($string)
    {
        if (! is_writable($string)){
            Throw new Exception("Folder " . $string . " musi miec mozliwosc zapisu.");
        }
        $this->baseFolder = $string;
    }

    public function setShipFrom (Array $array)
    {
        $this->shipFrom = $array;
        foreach ($this->shipFrom as $key => $value){
            $this->shipFrom[$key] = trim(strip_tags(htmlspecialchars_decode($value)));
        }
    }

    public function setDepartment ($string)
    {
        $this->department = $string;
    }

    public function setLang ($string)
    {
        $this->lang = $string;
    }

    public function setHost ($string)
    {
        $this->host = $string;
    }

    public function setLogin ($string)
    {
        $this->login = $string;
    }

    public function setPassword ($string)
    {
        $this->password = $string;
    }

    public function setMasterfid ($string)
    {
        $this->masterfid = $string;
    }

    public function getFunctions ()
    {
        var_dump($this->soapClient->__getFunctions());
    }

    public function setShipTo (Array $array)
    {
        $this->shipTo = $array;
        foreach ($this->shipTo as $key => $value){
            $this->shipTo[$key] = trim(strip_tags(htmlspecialchars_decode($value)));
        }
    }

    public function setPackageDetails (Array $array)
    {
        $this->packageDetails = $array;
    }

    public function setConnection ()
    {
        if (! is_string($this->host)){
            Throw new Exception(DpdMessages::$error[$this->lang][1]);
        }
        
        try{
            $this->authData = new authDataV1($this->login, $this->masterfid, $this->password);
            $options = array(
                'exceptions' => 1
            );
            $this->soapClient = new SoapClient($this->host, $options);
        }
        catch (Exception $e){
            $return_array = array(
                "type" => "error",
                "message" => "Wystąpił problem z komunikacja DPD Webserivce, błąd: " . $e->getMessage()
            );
            return $return_array;
        }
        ;
    }

    private function prepareProtocolString (Array $references)
    {
        $dpdServiceParam10 = "<DPDServicesParamsV1>
                <Policy>STOP_ON_FIRST_ERROR</Policy>
                <PickupAddress>
                    <FID>" . $this->authData->masterFid . "</FID>
                </PickupAddress>
                <Session>
                    <SessionType>DOMESTIC</SessionType>
                    <Packages>";
        foreach ($references as $reference){
            if (is_string($reference)){
                $dpdServiceParam10 .= "
                            <Package>
                                <Reference>" . $reference . "</Reference>
                            </Package>";
            }
        }
        $dpdServiceParam10 .= "</Packages>
                </Session>
            </DPDServicesParamsV1>";
        
        return $dpdServiceParam10;
    }

    private function prepareReferenceString ($string_in)
    {
        if (is_string($string_in)){
            $string_out = "<DPDServicesParamsV1>
                <Policy>STOP_ON_FIRST_ERROR</Policy>
                <Session>
                    <SessionType>DOMESTIC</SessionType>
                        <Packages>
                            <Package>
                                <Reference>" . $string_in . "</Reference>
                            </Package>
                        </Packages>
                </Session>
            </DPDServicesParamsV1>";
            
            return $string_out;
        }
        return false;
    }

    private function prepareWaybillString ($string_in)
    {
        if (is_string($string_in)){
            $string_out = "<DPDServicesParamsV1>
            <Policy>STOP_ON_FIRST_ERROR</Policy>
            <Session>
                <SessionType>DOMESTIC</SessionType>
                <Packages>
                    <Package>
                        <Parcels>
                            <Parcel>
                                <Waybill>" . $string_in . "</Waybill>
                            </Parcel>
                        </Parcels>
                    </Package>
                </Packages>
            </Session>
            </DPDServicesParamsV1>";
            
            return $string_out;
        }
        return false;
    }

    private function prepareRegisterString ()
    {
        $customer_data_1 = substr($this->packageDetails['customer_data_1'], 0, 200);
        $package_content = wordwrap($this->packageDetails['package_content'], 14, " ", true);
        $package_amount = (int) $this->packageDetails['package_amount'];
        
        if (is_numeric($package_amount) and $package_amount > 0){
            
            $openUMLFV1 = "<Packages>
                <Package>
                    <PayerType>SENDER</PayerType>
                    <Sender>
                        <FID>" . $this->authData->masterFid . "</FID>
                        <Company>" . $this->shipFrom['Company'] . "</Company>
                        <Name>" . $this->shipFrom['Name'] . "</Name>
                        <Address>" . $this->shipFrom['Street'] . "</Address>
                        <City>" . $this->shipFrom['City'] . "</City>
                        <CountryCode>" . $this->shipFrom['CountryCode'] . "</CountryCode>
                        <PostalCode>" . $this->shipFrom['PostalCode'] . "</PostalCode>
                        <Phone>" . $this->shipFrom['Phone'] . "</Phone>
                        <Email>" . $this->shipFrom['Email'] . "</Email>
                    </Sender>
                    <Receiver>
                        <Company><![CDATA[" . $this->shipTo['Company'] . "]]></Company>
                        <Name><![CDATA[" . substr($this->shipTo['Name'] . " " . $this->shipTo['Surname'], 0, 100) . "]]></Name>
                        <Address>" . $this->shipTo['Street'] . " " . $this->shipTo['Number'] . "</Address>
                        <City>" . $this->shipTo['City'] . "</City>
                        <CountryCode>" . $this->shipTo['CountryCode'] . "</CountryCode>
                        <PostalCode>" . str_replace("-", "", $this->shipTo['PostalCode']) . "</PostalCode>
                        <Phone>" . trim($this->shipTo['Phone'] . ' ' . $this->shipTo['Phone2']) . "</Phone>
                        <Email>" . $this->shipTo['Email'] . "</Email>
                    </Receiver>
                    <Reference><![CDATA[" . mb_strtoupper(substr($this->packageDetails['reference_number'], 0, 100), 'utf-8') . "]]></Reference>
                    <Ref1><![CDATA[" . mb_strtoupper(substr($this->packageDetails['Ref1'], 0, 100), 'utf-8') . "]]></Ref1>
                    <Ref2><![CDATA[" . mb_strtoupper(substr($this->packageDetails['Ref2'], 0, 100), 'utf-8') . "]]></Ref2>
                    <Ref3><![CDATA[" . mb_strtoupper(substr($this->packageDetails['Ref3'], 0, 100), 'utf-8') . "]]></Ref3>
                    <Services>
                    ";
            
            // ustawienie kwoty pobrania (platne przy odbiorze)
            if ($this->packageDetails['COD'] != null and $this->packageDetails['COD'] > 0){
                $openUMLFV1 .= "<COD>
                                        <Amount>" . $this->packageDetails['COD'] . "</Amount>
                                        <Currency>PLN</Currency>
                                    </COD>";
            }
            
            // ustawienie ubezpieczenia dla wartosci powyzej 5000 pln
            if ($this->packageDetails['DeclaredValue'] >= 5000){
                $openUMLFV1 .= "
                        <DeclaredValue>
                            <Amount>" . $this->packageDetails['DeclaredValue'] . "</Amount>
                            <Currency>PLN</Currency>
                        </DeclaredValue>";
            }
            
            // czy DOX (dokumenty w kopercie)
            // if ($this->packageDetails['dox']){
            // $openUMLFV1 .= "<DOX/>";
            // }
            

            $openUMLFV1 .= "</Services>
                    <Parcels>";
            
            // podzial zamowienia na paczki, jezeli wiecej paczek
            for ($i = 1; $i <= $package_amount; $i ++){
                $openUMLFV1 .= "
                        <Parcel>
                            <Weight>" . round($this->packageDetails['Weight'] / $package_amount, 2) . "</Weight>
                            <Content>" . mb_strtoupper(substr($package_content, 0, 299), 'utf-8') . "</Content>
                            <CustomerData1>" . mb_strtoupper($customer_data_1, 'utf-8') . "</CustomerData1>
                        </Parcel>";
            }
            
            $openUMLFV1 .= "</Parcels>
                </Package>
            </Packages>";
            
            return $openUMLFV1;
        }
        else{
            Throw new Exception('Ilość paczek musi zostać poprawnie określona.');
        }
    }

    private function saveFile ($folder, $content, $filename)
    {
        if (! is_string($folder)){
            Throw new Exception("Folder musi zostac podany");
        }
        
        $protocols_dir = $this->baseFolder . "/" . $folder . "/" . $this->current_year . "/" . $this->current_month . "/";
        $file = $protocols_dir . $filename . ".pdf";
        if (! is_dir($protocols_dir)){
            mkdir($protocols_dir, 0755, true);
        }
        if (file_put_contents($file, base64_decode($content)) != false){
            return "/" . $folder . "/" . $this->current_year . "/" . $this->current_month . "/" . $filename . '.pdf';
        }
        else{
            return false;
        }
    }

    public function getProtocol (Array $references)
    {
        // zapisuje protokol dla przekazanych numerow referencyjnych
        try{
            $params11->dpdServicesParamsCV1 = $this->prepareProtocolString($references);
            $params11->outputDocFormatV1 = "PDF";
            $params11->outputDocPageFormatV1 = "A4";
            $params11->authDataV1 = $this->authData;
            $result = $this->soapClient->generateProtocolXV1($params11);
            $xml = simplexml_load_string($result->return);
            
            $founded_all = true;
            
            foreach ($xml->Session->Packages->Package as $pack){
                if ($pack->StatusInfo->Status == "NOT_FOUND"){
                    $error .= "<br/>Przesyłka o numerze: " . $pack->Reference . ", nie została odnaleziona.<br/>";
                    $founded_all = false;
                }
            }
            
            if ($founded_all and $xml->DocumentId != ''){
                return $xml->DocumentData;
                
                if ($filename){
                    $message = "Protokół został wygenerowany prawidłowo. ";
                }
                else{
                    $error .= "Wystąpił problem z zapisem pliku protkołu.";
                }
            }
            
            if ($error){
                $return_array = array(
                    "type" => "error",
                    "message" => $error . '<br/><br/>Generowanie raportu zostało wstrzymane.'
                );
                return $return_array;
            }
            else{
                $return_array = array(
                    "type" => "ok",
                    "message" => $message,
                    "filename" => $filename
                );
                return $return_array;
            }
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function registerNewPackage ()
    {
        /*
		 * zapisuje paczke w DPD
		 */
        if (! is_array($this->shipTo) or ! is_array($this->shipFrom) or ! is_array($this->packageDetails)){
            Throw new Exception("Parametry paczki muszą zostać uzupełnione poprawnie");
        }
        
        $openUMLFV1 = $this->prepareRegisterString();
        
        try{
            $params1 = new stdClass();
            $params1->pkgNumsGenerationPolicyV1 = "STOP_ON_FIRST_ERROR";
            $params1->openUMLXV1 = $openUMLFV1;
            $params1->authDataV1 = $this->authData;
            $result = $this->soapClient->generatePackagesNumbersXV1($params1);
            
            $xml = simplexml_load_string($result->return);
            
            // sprawdzenie statusu
            switch ($xml->Status) {
                case "DUPLICATED_PACKAGE_SEARCH_KEY":
                    
                    $error = "Identyfikator " . $this->packageDetails['reference_number'] . ", jest juz w systemie. Paczka nie może zostać ponownie zapisana pod tym samym identyfikatorem. Błąd: " . $xml->Packages->Package->InvalidFields->InvalidField->Info;
                    $return_array = array(
                        "type" => "error",
                        "message" => $error
                    );
                    break;
                
                case "DISALLOWED_FID":
                    $error = "Błąd konfiguracji, niepoprawny numer FID. Kod błędu: " . $xml->Status;
                    $return_array = array(
                        "type" => "error",
                        "message" => $error
                    );
                    break;
                
                case "INCORRECT_DATA":
                    
                    $error = '';
                    // wyswietlenie bledu dla kazdej dostepnej parcel
                    foreach ($xml->Packages->Package->InvalidFields->InvalidField as $invalid_field){
                        if (in_array($invalid_field->Status,Array("VALUE_INCORRECT","DONT_MATCH_PATTERN"))){
                        	$error .= $invalid_field->Info . ', w polu: ' . $invalid_field->FieldName . '<br/>';
                        }
                    }
                    
                    $return_array = array(
                        "type" => "error",
                        "message" => $error
                    );
                    break;
                
                case "OK":
                    $return_array = array(
                        "type" => "ok",
                        "array" => array(
                            "package_id" => $xml->Packages->Package->PackageId,
                            "reference" => $xml->Packages->Package->Reference,
                            "parcels" => $xml->Packages->Package->Parcels,
                            "first_waybill" => $xml->Packages->Package->Parcels->Parcel->Waybill
                        )
                    );
                    break;
            }
        }
        catch (Exception $e){
            $error = $e->getMessage();
            
            $return_array = array(
                "type" => "error",
                "message" => $error
            );
        }
        ;
        return $return_array;
    }

    public function getLabelPDF ($findBy, $string)
    {
        /*
		 * zapisz etykiete w PDF, do podanego katalogu findBy: 1 - po reference,
		 * 2 - po waybill
		 */
        if (is_string($string) and is_integer($findBy)){
            
            switch ($findBy) {
                case 1:
                    $dpdServiceParam3 = $this->prepareReferenceString($string);
                    break;
                
                case 2:
                    $dpdServiceParam3 = $this->prepareWaybillString($string);
                    break;
            }
            
            $params4->dpdServicesParamsXV1 = $dpdServiceParam3;
            $params4->outputDocFormatV1 = "PDF";
            $params4->outputDocPageFormatV1 = "LBL_PRINTER";
            $params4->authDataV1 = $this->authData;
            
            try{
                $result = $this->soapClient->generateSpedLabelsXV1($params4);
                $xml = simplexml_load_string($result->return);
                
                switch ($xml->Session->StatusInfo->Status) {
                    case 'NOT_FOUND':
                        $return_array = array(
                            "type" => "error",
                            "message" => "Paczka o numerze: " . $string . ", nie została odnaleziona w systemie DPDservice."
                        );
                        break;
                    
                    case 'OK':
                        return $xml->DocumentData;
                        break;
                }
            }
            catch (Exception $e){
                $return_array = array(
                    "type" => "error",
                    "message" => $e->getMessage()
                );
            }
            
            return $return_array;
        }
        else{
            $return_array = array(
                "type" => "error",
                "message" => "Brak wymaganych parametrów (rodzaj dokumentu lub numer dokument)"
            );
            return $return_array;
        }
    }
}
