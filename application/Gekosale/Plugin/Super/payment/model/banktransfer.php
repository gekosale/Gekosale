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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: banktransfer.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;
use FormEngine;

class BanktransferModel extends Component\Model
{

    protected $_name = 'Przelew bankowy';

    public function getPaymentMethod ($event, $request)
    {
        $Data[$this->getName()] = $this->_name;
        $event->setReturnValues($Data);
    }

    public function getPaymentMethodConfigurationForm ($event, $request)
    {
        if ($request['data']['paymentmethodmodel'] != $this->getName()){
            return false;
        }
        
        $banktransfer = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'banktransfer_data',
            'label' => 'Konfiguracja'
        )));
        
        $banktransfer->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'bankacct',
            'label' => 'Numer rachunku bankowego',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać numer rachunku bankowego.')
            )
        )));
        
        $banktransfer->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'bankname',
            'label' => 'Nazwa banku',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać nazwę banku.')
            )
        )));
        
        $banktransfer->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'iban',
            'label' => 'IBAN'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('banktransfer', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'banktransfer_data' => Array(
                    'bankacct' => $settings['bankacct'],
                    'bankname' => $settings['bankname']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        if ($request['model'] != $this->getName()){
            return false;
        }
        
        $Settings = Array(
            'bankacct' => $request['data']['bankacct'],
            'bankname' => $request['data']['bankname']
        );
        
        $this->registry->core->saveModuleSettings('banktransfer', $Settings, Helper::getViewId());
    }

    public function getPaymentData ($order)
    {
        return $this->registry->core->loadModuleSettings('banktransfer', Helper::getViewId());
    }

    public function validateIbanNumber ($num)
    {
        //(c) Bartłomiej Zastawnik, "Rzast".
        //Użycie funkcji dozwolone przy zachowaniu komentarzy.
        $puste = array(
            ' ',
            '-',
            '_',
            '.',
            ',',
            '/',
            '|'
        ); //znaki do usuniącia
        $temp = strtoupper(str_replace($puste, '', $num)); //Zostają cyferki + duże litery
        if (($temp{0} <= '9') && ($temp{1} <= '9')){ //Jeżeli na początku są cyfry, to dopisujemy PL, inne kraje muszć być jawnie wprowadzone
            $temp = 'PL' . $temp;
        }
        $temp = substr($temp, 4) . substr($temp, 0, 4); //przesuwanie cyfr kontrolnych na koniec
        $znaki = array(
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            'A' => '10',
            'B' => '11',
            'C' => '12',
            'D' => '13',
            'E' => '14',
            'F' => '15',
            'G' => '16',
            'H' => '17',
            'I' => '18',
            'J' => '19',
            'K' => '20',
            'L' => '21',
            'M' => '22',
            'N' => '23',
            'O' => '24',
            'P' => '25',
            'Q' => '26',
            'R' => '27',
            'S' => '28',
            'T' => '29',
            'U' => '30',
            'V' => '31',
            'W' => '32',
            'X' => '33',
            'Y' => '34',
            'Z' => '35'
        ); //Tablica zamienników, potrzebnych do wyliczenia sumy kontrolnej
        $ilosc = strlen($temp); //długość numeru
        $ciag = '';
        for ($i = 0; $i < $ilosc; $i ++){
            $ciag .= $znaki[$temp{$i}];
        }
        $mod = 0;
        $ilosc = strlen($ciag); //nowa długość numeru
        for ($i = 0; $i < $ilosc; $i = $i + 6){
            //oblicznie modulo, $ciag jest zbyt wielkć liczbę na format integer, wiąc dzielć go na kawaśki
            $mod = (int) ($mod . substr($ciag, $i, 6)) % 97;
        }
        $out = ($mod == 1) ? true : false;
        return $out;
    }
}
