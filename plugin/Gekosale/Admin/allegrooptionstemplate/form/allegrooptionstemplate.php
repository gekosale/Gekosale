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
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale;
use FormEngine;

class AllegroOptionsTemplateForm extends Component\Form
{

    protected $populateData;

    public function setPopulateData ($Data)
    {
        $this->populateData = $Data;
    }

    public function doGetStatesForSelect ($country)
    {
        $states = $this->allegro->doGetStatesInfo($country);
        foreach ($states as $state){
            $tmp[$state['state-id']] = $state['state-name'];
        }
        return FormEngine\Option::Make($tmp);
    }

    public function initForm ()
    {
        $this->allegro = new AllegroApi($this->registry);
        
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'allegrooptionstemplate',
            'action' => '',
            'method' => 'post'
        ));
        
        $optionstemplatePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'optionstemplate_data',
            'label' => 'Nazwa szablonu opcji'
        )));
        
        $optionstemplatePane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'optionstemplate',
            'label' => 'Nazwa',
            'rules' => Array(
                new FormEngine\Rules\Unique('Taka nazwa wystepuje już w bazie', 'allegrooptionstemplate', 'name', null, Array(
                    'column' => 'idallegrooptionstemplate',
                    'values' => (int) $this->registry->core->getParam()
                )),
                new FormEngine\Rules\Required('Musisz podać nazwę szablonu')
            )
        )));
        
        $optionsTemplateDescEditPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'main_data',
            'label' => 'Tytuł aukcji'
        )));
        
        $optionsTemplateDescEditPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p><strong>Tytuł aukcji</strong> generuje się automatycznie, możesz go zmienić wpisując swój tekst lub korzystając z tagów.</p>
				  <p>Najpopularniejsze tagi to:</p>
				  <ul>
					  <li>{Produkt.Nazwa}</li>
					  <li>{Produkt.CenaWywolawcza}</li>
					  <li>{Produkt.CenaKupTeraz}</li>
					  <li>{Produkt.CenaMinimalna}</li>
				  </ul>
				  <p><strong>Pamiętaj że Allegro dopuszcza tytuły nie dłuższe niż 50 znaków. Podczas wystawiania aukcji zostaną one skrócone do tej wartości. Na kolejnym ekranie zobaczysz nazwy jakie finalnie pojawią się w Allegro.</strong></p>',
            'direction' => FormEngine\Elements\Tip::DOWN,
            'short_tip' => '<p>Wskazówki dotyczące tytułu aukcji</p>',
        )));
        
        $optionsTemplateDescEditPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-1',
            'label' => $this->trans('TXT_TITLE_FORMAT'),
            'comment' => 'Tutaj mozesz wpisac tytuł',
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TITLE_FORMAT'))
            )
        )));
        
        $optionsTemplateDescEditPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wybierz datę rozpoczęcia aukcji. Podanie daty dzisiejszej spowoduje natychmiastowe wystawienie aukcji. Podanie daty w przyszłości spowoduje utworzenie aukcji zaplanowanej do wystawienia.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN,
            'short_tip' => '<p>Wskazówki dotyczące daty rozpoczęcia</p>',
            'default_state' => FormEngine\Elements\Tip::RETRACTED
        )));
        
        $optionsTemplateDescEditPane->AddChild(new FormEngine\Elements\Date(Array(
            'name' => 'sell-form-id-3',
            'label' => 'Data rozpoczęcia',
            'comment' => 'Tutaj możesz wybrać datę rozpoczęcia',
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
            )
        )));
        
        $optionsTemplateDescEditPane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sell-form-id-4',
            'label' => 'Czas trwania aukcji',
            'options' => FormEngine\Option::Make(Array(
                '0' => '3',
                '1' => '5',
                '2' => '7',
                '3' => '10',
                '4' => '14',
                '5' => '30'
            )),
            'suffix' => 'dni'
        )));
        
        $optionsTemplateDescEditPane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sell-form-id-29',
            'label' => 'Format sprzedaży',
            'options' => FormEngine\Option::Make(Array(
                '0' => 'Aukcja (z licytacją) lub Kup Teraz!',
                '1' => 'Sklep (bez licytacji)'
            ))
        )));
        
        $sellerPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'seller_data',
            'label' => 'Dane sprzedawcy'
        )));
        
        $countries = $this->allegro->doGetCountries();
        foreach ($countries as $country){
            $countrySelect[$country['country-id']] = $country['country-name'];
        }
        
        $country = $sellerPane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sell-form-id-9',
            'label' => 'Kraj',
            'comment' => 'Wybierz kraj sprzedawcy',
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $countrySelect),
            'default' => $this->allegro->getCountryCode()
        )));
        
        $sellerPane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sell-form-id-10',
            'label' => 'Województwo',
            'comment' => 'Wybierz województwo sprzedawcy',
            'options' => $this->doGetStatesForSelect($this->allegro->getCountryCode()),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::EXCHANGE_OPTIONS, $country, Array(
                    $this,
                    'doGetStatesForSelect'
                ))
            )
        )));
        
        $sellerPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-32',
            'label' => 'Kod pocztowy',
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_POSTCODE'))
            )
        )));
        
        $sellerPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-11',
            'label' => 'Miasto',
            'comment' => 'Podaj miasto',
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PLACE'))
            )
        )));
        
        $deliveryPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'delivery_data',
            'label' => 'Ustawienia dostawy'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wybierz stronę która pokrywa koszty dostawy</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sell-form-id-12',
            'label' => 'Transport',
            'options' => Array(
                new FormEngine\Option(0, 'Sprzedający pokrywa koszty transportu'),
                new FormEngine\Option(1, 'Kupujący pokrywa koszty transportu')
            ),
            'default' => 1
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sell-form-id-13',
            'label' => 'Opcje dot. transportu',
            'options' => Array(
                new FormEngine\Option(16, 'Szczegóły w opisie'),
                new FormEngine\Option(32, 'Zgadzam się na wysłanie przedmiotu za granicę')
            )
        )));
        
        // OdbiĂłr osobisty|PrzesyĹka elektroniczna (e-mail)|OdbiĂłr osobisty
        // po przedpĹacie
        

        $deliveryPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Zaznacz opcje przesyłki jakie pozwalają na bezpłatną dostawę zakupionych na aukcji produktów.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
            'name' => 'sell-form-id-35',
            'label' => 'Darmowe opcje przesyłki',
            'options' => Array(
                new FormEngine\Option(1, 'Odbiór osobisty'),
                new FormEngine\Option(2, 'Przesyłka elektroniczna (e-mail)'),
                new FormEngine\Option(4, 'Odbiór osobisty po przedpłacie')
            )
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Włącz formy dostawy i określ im koszty brutto w walucie serwisu Allegro.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $sellformid36 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-36',
            'label' => 'Paczka pocztowa ekonomiczna',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-36-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid36, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid37 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-37',
            'label' => 'List ekonomiczny',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-37-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid37, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid38 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-38',
            'label' => 'Paczka pocztowa priorytetowa',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-38-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid38, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid39 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-39',
            'label' => 'List priorytetowy',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-39-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid39, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid40 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-40',
            'label' => 'Przesyłka pobraniowa',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-40-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid40, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid41 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-41',
            'label' => 'List polecony ekonomiczny',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-41-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid41, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid42 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-42',
            'label' => 'Przesyłka pobraniowa priorytetowa',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-42-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid42, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid43 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-43',
            'label' => 'List polecony priorytetowy',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-43-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid43, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid44 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-44',
            'label' => 'Przesyłka kurierska',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-44-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid44, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid45 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-45',
            'label' => 'Przesyłka kurierska pobraniowa',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-45-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid45, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid46 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-46',
            'label' => 'Odbiór w punkcie po przedpłacie - DHL SERVICE POINT',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-46-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid46, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid47 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-47',
            'label' => 'Odbiór w punkcie po przedpłacie - E-PRZESYŁKA',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-47-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid47, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid48 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-48',
            'label' => 'Odbiór w punkcie po przedpłacie - PACZKA W RUCHu',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-48-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid48, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid49 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-49',
            'label' => 'Odbiór w punkcie po przedpłacie - Paczkomaty 24/7',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-49-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid49, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid50 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-50',
            'label' => 'Odbiór w punkcie - E-PRZESYŁKA',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-50-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid50, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid51 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-51',
            'label' => 'Odbiór w punkcie - PACZKA W RUCHu',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-51-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid51, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $sellformid52 = $deliveryPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'sell-form-id-52',
            'label' => 'Odbiór w punkcie - Paczkomaty 24/7',
            'comment' => 'pierwsza sztuka'
        )));
        
        $deliveryPane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-52-cost',
            'label' => $this->trans('TXT_COST'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRICE'))
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $sellformid52, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            ),
            'suffix' => 'PLN brutto'
        )));
        
        $paymentPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'payment_data',
            'label' => 'Ustawienia płatności'
        )));
        
        $paymentPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Zaznacz dodatkowe opcje dotyczące płatności.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        if ($this->allegro->getCountryCode() == 228){
            $paymentDetails = $paymentPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
                'name' => 'sell-form-id-14',
                'label' => 'Formy płatności',
                'options' => Array(
                    new FormEngine\Option(1, 'Zwykły przelew'),
                    new FormEngine\Option(4, 'Inne rodzaje płatności'),
                    new FormEngine\Option(8, 'Wystawiam faktury VAT')
                )
            )));
        }
        else{
            $paymentDetails = $paymentPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
                'name' => 'sell-form-id-14',
                'label' => 'Formy płatności',
                'options' => Array(
                    new FormEngine\Option(1, 'Zwykły przelew'),
                    new FormEngine\Option(16, 'Inne rodzaje płatności'),
                    new FormEngine\Option(32, 'Wystawiam faktury VAT')
                )
            )));
        }
        
        $paymentPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wprowadź dodatkowe informacje dotyczące dostawy i płatności np. czas dostawy, warunki płatności, zwrotów.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $paymentPane->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'sell-form-id-27',
            'label' => 'Dodatkowe informacje o przesyłce i płatności',
            'comment' => $this->trans('TXT_MAX_LENGTH') . ' 500',
            'max_length' => 500,
            'rows' => 20
        )));
        
        $additionalPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'additional_data',
            'label' => 'Opcje dodatkowe'
        )));
        
        // Pogrubienie|Miniaturka|Podswietlenie|Wyroznienie|Strona
        // kategorii|Strona glowna Allegro|Znak wodny
        // 1|2|4|8|16|32|64
        $additionalPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
            'name' => 'sell-form-id-15',
            'label' => 'Wybierz opcje dodatkowe',
            'options' => Array(
                new FormEngine\Option(1, 'Pogrubienie'),
                new FormEngine\Option(2, 'Miniaturka'),
                new FormEngine\Option(4, 'Podświetlenie'),
                new FormEngine\Option(8, 'Wyróżnienie'),
                new FormEngine\Option(16, 'Strona kategorii'),
                new FormEngine\Option(32, 'Strona główna Allegro'),
                new FormEngine\Option(64, 'Znak wodny')
            )
        )));
        
        $pricePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'price_data',
            'label' => 'Ustawienia obliczania cen'
        )));
        
        $pricePane->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'sell-form-id-6',
            'label' => 'Cena wywoławcza',
            'comment' => 'Wpisz % ceny bazowej produktu',
            'suffix' => '%'
        )));
        
        $pricePane->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'sell-form-id-7',
            'label' => 'Cena minimalna',
            'comment' => 'Wpisz % ceny bazowej produktu',
            'suffix' => '%'
        )));
        
        $pricePane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Podaj modyfikator % służący do obliczenia ceny wystawianego produktu na Allegro. </p>
				  <p><strong>100%</strong> oznacza, że cena na aukcji będzie taka sama jak w sklepie</p>
				  <p><strong>50%</strong> oznacza, że cena na aukcji będzie stanowiła 50% ceny w sklepie</p>
				  <p><strong>200%</strong> oznacza, że cena na aukcji będzie stanowiła 200% ceny w sklepie</p>
				  <p>Oczywiście możesz podać dowolny modyfikator większy od 0</p>
				 ',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $pricePane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'sell-form-id-8',
            'label' => 'Cena "Kup Teraz"',
            'comment' => 'Wpisz % ceny bazowej produktu',
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
            ),
            'suffix' => '%'
        )));
        
        $templatePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'template_data',
            'label' => 'Szablon HTML'
        )));
        
        $templatePane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p><strong>Szablon aukcji</strong> generuje się automatycznie, możesz go zmienić wpisując swój tekst lub korzystając z tagów.</p>
				  <p>Dostępne tagi to:</p>
				  <ul>
					  <li><strong>{{ product.name }}</strong> - nazwa produktu w sklepie</li>
					  <li><strong>{{ product.shortdescription }}</strong> - krótki opis produktu w sklepie</li>
					  <li><strong>{{ product.description }}</strong> - długi opis produktu w sklepie</li>
					  <li><strong>{{ product.longdescription }}</strong> - dodatkowy opis produktu w sklepie</li>
					  <li><strong>{{ product.ean }}</strong> - kod EAN produktu w sklepie</li>
					  <li><strong>{{ product.photos }}</strong> - pętla zawierająca ścieżki do zdjęć produktu w sklepie</li>
				  </ul>
				  <p>W przypadku zdjęć produktu należy zastosować pętlę w szablonie. Przykład użycia:</p>
				  <p>{% for photo in product.photos %}</p>
				  <p>Adres URL dla małego zdjęcia produktu: <strong>{{ photo.small }}</strong></p>
				  <p>Adres URL dla normalnego zdjęcia produktu: <strong>{{ photo.normal }}</strong></p>
				  <p>Adres URL dla dużego zdjęcia produktu: <strong>{{ photo.orginal }}</strong></p>
				  <p>{% endfor %}</p>
				 ',
            'direction' => FormEngine\Elements\Tip::DOWN,
            'short_tip' => '<p>Wskazówki dotyczące szablonu aukcji</p>',
            'default_state' => FormEngine\Elements\Tip::EXPANDED
        )));
        
        $templatePane->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'content',
            'label' => 'Wprowadź szablon HTML dla aukcji',
            'rows' => 50
        )));
        
        $Data = Event::dispatch($this, 'admin.allegrooptionsremplate.initForm', Array(
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
}