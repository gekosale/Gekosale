<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;
use FormEngine;
use xajaxResponse;
use stdClass;
use DateTime;

class AllegroController extends Component\Controller\Admin
{

    protected $error;

    public function __construct ($registry)
    {
        parent::__construct($registry);
        
        try{
            $this->allegro = new AllegroApi($registry);
        }
        catch (\Exception $e){
            $this->error = $e->getMessage();
        }
    }

    protected function sync ()
    {
        @set_time_limit(0);
        
        App::getModel('allegro')->updateAuctionStatus();
        
        $count = $this->allegro->doMyAccountItemsCount('sold');
        
        $auctions = array();
        $offset = 0;
        while ($count > 0){
            $auctions = array_merge($auctions, $this->allegro->doMyAccount2('sold', $offset));
            $count -= 25;
            $offset += 25;
        }
        
        $ids = array();
        foreach ($auctions as $auction){
            $id = $auction['my-account-array'][0];
            
            $isShopAuction = $this->model->checkIsShopAuction((float) $id);
            if ($isShopAuction > 0){
                $ids[] = (float) $id;
            }
        }
        
        $items = array_chunk($ids, 25);
        $transactions = Array();
        $contactData = Array();
        foreach ($items as $portion){
            $transactions = array_merge($this->allegro->doGetTransactionsIDs($portion));
            $contactData = array_merge($this->allegro->doMyContact($portion));
        }
        
        $orderIds = Array();
        foreach ($transactions as $transaction){
            $fod = $this->allegro->doGetPostBuyFormsDataForSellers(Array(
                $transaction
            ));
            
            if (! empty($fod)){
                $orderIds[] = App::getModel('allegro')->addAllegroOrder($fod, $contactData);
            }
        }
        $message = 'Synchronizacja zakończona sukcesem.';
        if (count($orderIds) > 0){
            $message .= '<br />Zaimportowano nowe zamówienia o numerach: ' . implode(',', $orderIds);
        }
        
        return $message;
    }

    public function view ()
    {
        if ($this->error){
            App::redirect(__ADMINPANE__ . '/allegro');
        }
        
        App::getContainer()->get('session')->setVolatileMessage($this->sync());
        App::redirect(__ADMINPANE__ . '/allegro');
    }

    public function index ()
    {
        if ($this->error){
            $this->renderLayout(Array(
                'errormsg' => $this->error
            ));
            return;
        }
        
        App::getModel('contextmenu')->add('Szablony opcji Allegro', $this->getRouter()->url('admin', 'allegrooptionstemplate'));
        App::getModel('contextmenu')->add('Ustawienia kategorii Allegro', $this->getRouter()->url('admin', 'allegrocategories'));
        
        $this->registry->xajax->registerFunction(array(
            'finishAuction',
            $this->model,
            'finishAuction'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'doDeleteAuction',
            $this->model,
            'doAJAXDeleteAuction'
        ));
        
        $this->registry->xajax->registerFunction(array(
            'LoadAllAuction',
            $this->model,
            'getAuctionForAjax'
        ));
        
        App::getModel('allegro/allegrocategories')->addAllegroCategories();
        
        $this->renderLayout(Array(
            'datagrid_filter' => $this->model->getDatagridFilterData()
        ));
    }

    public function add ()
    {
        if ($this->error){
            $this->renderLayout(Array(
                'errormsg' => $this->error
            ));
            return;
        }
        
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'add_allegro',
            'action' => '',
            'method' => 'post'
        ));
        
        $optionstemplatePane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'optionstemplate_data',
            'label' => 'Wybór szablonu opcji'
        )));
        
        $optionstemplatePane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Wybierz szablon opcji jaki został zdefiniowany wcześniej</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $optionstemplatePane->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'optionstemplateid',
            'label' => 'Szablon opcji',
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('allegro/allegrooptionstemplate')->getAllegrooptionstemplateToSelect())
        )));
        
        $optionstemplatePane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Wprowadź nazwę jeżeli chcesz ustawienia zapisać jako nowy szablon. Szablony upraszczają i skracają czas potrzebny na wystawienie nowych aukcji. Możesz nimi zarządzać na stronie <a href="' . $this->registry->router->generate('admin', true, Array(
                'controller' => 'allegrooptionstemplate'
            )) . '" target="_blank">Integracja &raquo; Integracja Allegro &raquo; Szablony Allegro</a>.</p></p>
				 ',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $optionstemplatePane->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'optionstemplate',
            'label' => 'Nazwa szablonu Allegro',
            'rules' => Array(
                new FormEngine\Rules\Unique('Taka nazwa wystepuje już w bazie', 'allegrooptionstemplate', 'name', null, Array(
                    'column' => 'idallegrooptionstemplate',
                    'values' => (int) $this->registry->core->getParam()
                ))
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
            'maxlength' => 50,
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TITLE_FORMA')),
                new FormEngine\Rules\Custom('Maksymalna ilość znaków: 50.', function  ($value)
                {
                    return strlen($value) <= 50;
                })
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
            'minDate' => 0, //date("y-n-j"),
            'default' => date('y-n-j'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE')),
                new FormEngine\Rules\Custom($this->trans('ERR_WRONG_ALLEGRO_START_DATE'), Array(
                    $this,
                    'checkDate'
                ))
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
                '0' => 'Aukcja (z licytacją) lub Kup Teraz!',
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
            // 'rules' => Array(
            // new
            // FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
            // ),
            'suffix' => '%'
        )));
        
        $pricePane->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'sell-form-id-7',
            'label' => 'Cena minimalna',
            'comment' => 'Wpisz % ceny bazowej produktu',
            // 'rules' => Array(
            // new
            // FormEngine\Rules\Required($this->trans('ERR_EMPTY_VALUE'))
            // ),
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
        
        if ($this->registry->core->getParam() > 0){
            
            $rawData = App::getModel('allegro/allegrooptionstemplate')->getAllegrooptionstemplateById((int) $this->registry->core->getParam());
            $populateData = Array(
                'optionstemplate_data' => Array(
                    'optionstemplateid' => $this->registry->core->getParam()
                ),
                'main_data' => Array(
                    'sell-form-id-1' => $rawData['sell-form-id-1'],
                    'sell-form-id-3' => $this->parseDate($rawData['sell-form-id-3']),
                    'sell-form-id-4' => $rawData['sell-form-id-4'],
                    'sell-form-id-29' => $rawData['sell-form-id-29']
                ),
                'seller_data' => Array(
                    'sell-form-id-9' => $rawData['sell-form-id-9'],
                    'sell-form-id-10' => $rawData['sell-form-id-10'],
                    'sell-form-id-32' => $rawData['sell-form-id-32'],
                    'sell-form-id-11' => $rawData['sell-form-id-11']
                ),
                'delivery_data' => Array(
                    'sell-form-id-12' => $rawData['sell-form-id-12'],
                    'sell-form-id-13' => $rawData['sell-form-id-13'],
                    'sell-form-id-35' => $rawData['sell-form-id-35'],
                    'sell-form-id-36' => $rawData['sell-form-id-36'],
                    'sell-form-id-36-cost' => $rawData['sell-form-id-36-cost'],
                    'sell-form-id-37' => $rawData['sell-form-id-37'],
                    'sell-form-id-37-cost' => $rawData['sell-form-id-37-cost'],
                    'sell-form-id-38' => $rawData['sell-form-id-38'],
                    'sell-form-id-38-cost' => $rawData['sell-form-id-38-cost'],
                    'sell-form-id-39' => $rawData['sell-form-id-39'],
                    'sell-form-id-39-cost' => $rawData['sell-form-id-39-cost'],
                    'sell-form-id-40' => $rawData['sell-form-id-40'],
                    'sell-form-id-40-cost' => $rawData['sell-form-id-40-cost'],
                    'sell-form-id-41' => $rawData['sell-form-id-41'],
                    'sell-form-id-41-cost' => $rawData['sell-form-id-41-cost'],
                    'sell-form-id-42' => $rawData['sell-form-id-42'],
                    'sell-form-id-42-cost' => $rawData['sell-form-id-42-cost'],
                    'sell-form-id-43' => $rawData['sell-form-id-43'],
                    'sell-form-id-43-cost' => $rawData['sell-form-id-43-cost'],
                    'sell-form-id-44' => $rawData['sell-form-id-44'],
                    'sell-form-id-44-cost' => $rawData['sell-form-id-44-cost'],
                    'sell-form-id-45' => $rawData['sell-form-id-45'],
                    'sell-form-id-45-cost' => $rawData['sell-form-id-45-cost'],
                    'sell-form-id-46' => $rawData['sell-form-id-46'],
                    'sell-form-id-46-cost' => $rawData['sell-form-id-46-cost'],
                    'sell-form-id-47' => $rawData['sell-form-id-47'],
                    'sell-form-id-47-cost' => $rawData['sell-form-id-47-cost'],
                    'sell-form-id-48' => $rawData['sell-form-id-48'],
                    'sell-form-id-48-cost' => $rawData['sell-form-id-48-cost'],
                    'sell-form-id-49' => $rawData['sell-form-id-49'],
                    'sell-form-id-49-cost' => $rawData['sell-form-id-49-cost'],
                    'sell-form-id-50' => $rawData['sell-form-id-50'],
                    'sell-form-id-50-cost' => $rawData['sell-form-id-50-cost'],
                    'sell-form-id-51' => $rawData['sell-form-id-51'],
                    'sell-form-id-51-cost' => $rawData['sell-form-id-51-cost'],
                    'sell-form-id-52' => $rawData['sell-form-id-52'],
                    'sell-form-id-52-cost' => $rawData['sell-form-id-52-cost']
                ),
                'payment_data' => Array(
                    'sell-form-id-14' => $rawData['sell-form-id-14'],
                    'sell-form-id-27' => $rawData['sell-form-id-27']
                ),
                'additional_data' => Array(
                    'sell-form-id-15' => $rawData['sell-form-id-15']
                ),
                'price_data' => Array(
                    'sell-form-id-6' => $rawData['sell-form-id-6'],
                    'sell-form-id-7' => $rawData['sell-form-id-7'],
                    'sell-form-id-8' => $rawData['sell-form-id-8']
                ),
                'template_data' => Array(
                    'content' => $rawData['content']
                )
            );
            
            $form->Populate($populateData);
        }
        else{
            $rawData = App::getContainer()->get('session')->getActiveNewAuctionData();
            
            if ($rawData != NULL){
                
                $populateData = Array(
                    'optionstemplate_data' => Array(
                        'optionstemplateid' => $rawData['optionstemplateid']
                    ),
                    'main_data' => Array(
                        'sell-form-id-1' => $rawData['sell-form-id-1'],
                        'sell-form-id-3' => $this->parseDate($rawData['sell-form-id-3']),
                        'sell-form-id-4' => $rawData['sell-form-id-4'],
                        'sell-form-id-29' => $rawData['sell-form-id-29']
                    ),
                    'seller_data' => Array(
                        'sell-form-id-9' => $rawData['sell-form-id-9'],
                        'sell-form-id-10' => $rawData['sell-form-id-10'],
                        'sell-form-id-32' => $rawData['sell-form-id-32'],
                        'sell-form-id-11' => $rawData['sell-form-id-11']
                    ),
                    'delivery_data' => Array(
                        'sell-form-id-12' => $rawData['sell-form-id-12'],
                        'sell-form-id-13' => $rawData['sell-form-id-13'],
                        'sell-form-id-35' => $rawData['sell-form-id-35'],
                        'sell-form-id-36' => $rawData['sell-form-id-36'],
                        'sell-form-id-36-cost' => $rawData['sell-form-id-36-cost'],
                        'sell-form-id-37' => $rawData['sell-form-id-37'],
                        'sell-form-id-37-cost' => $rawData['sell-form-id-37-cost'],
                        'sell-form-id-38' => $rawData['sell-form-id-38'],
                        'sell-form-id-38-cost' => $rawData['sell-form-id-38-cost'],
                        'sell-form-id-39' => $rawData['sell-form-id-39'],
                        'sell-form-id-39-cost' => $rawData['sell-form-id-39-cost'],
                        'sell-form-id-40' => $rawData['sell-form-id-40'],
                        'sell-form-id-40-cost' => $rawData['sell-form-id-40-cost'],
                        'sell-form-id-41' => $rawData['sell-form-id-41'],
                        'sell-form-id-41-cost' => $rawData['sell-form-id-41-cost'],
                        'sell-form-id-42' => $rawData['sell-form-id-42'],
                        'sell-form-id-42-cost' => $rawData['sell-form-id-42-cost'],
                        'sell-form-id-43' => $rawData['sell-form-id-43'],
                        'sell-form-id-43-cost' => $rawData['sell-form-id-43-cost'],
                        'sell-form-id-44' => $rawData['sell-form-id-44'],
                        'sell-form-id-44-cost' => $rawData['sell-form-id-44-cost'],
                        'sell-form-id-45' => $rawData['sell-form-id-45'],
                        'sell-form-id-45-cost' => $rawData['sell-form-id-45-cost'],
                        'sell-form-id-46' => $rawData['sell-form-id-46'],
                        'sell-form-id-46-cost' => $rawData['sell-form-id-46-cost'],
                        'sell-form-id-47' => $rawData['sell-form-id-47'],
                        'sell-form-id-47-cost' => $rawData['sell-form-id-47-cost'],
                        'sell-form-id-48' => $rawData['sell-form-id-48'],
                        'sell-form-id-48-cost' => $rawData['sell-form-id-48-cost'],
                        'sell-form-id-49' => $rawData['sell-form-id-49'],
                        'sell-form-id-49-cost' => $rawData['sell-form-id-49-cost'],
                        'sell-form-id-50' => $rawData['sell-form-id-50'],
                        'sell-form-id-50-cost' => $rawData['sell-form-id-50-cost'],
                        'sell-form-id-51' => $rawData['sell-form-id-51'],
                        'sell-form-id-51-cost' => $rawData['sell-form-id-51-cost'],
                        'sell-form-id-52' => $rawData['sell-form-id-52'],
                        'sell-form-id-52-cost' => $rawData['sell-form-id-52-cost']
                    ),
                    'payment_data' => Array(
                        'sell-form-id-14' => $rawData['sell-form-id-14'],
                        'sell-form-id-27' => $rawData['sell-form-id-27']
                    ),
                    'additional_data' => Array(
                        'sell-form-id-15' => $rawData['sell-form-id-15']
                    ),
                    'price_data' => Array(
                        'sell-form-id-6' => $rawData['sell-form-id-6'],
                        'sell-form-id-7' => $rawData['sell-form-id-7'],
                        'sell-form-id-8' => $rawData['sell-form-id-8']
                    ),
                    'template_data' => Array(
                        'content' => $rawData['content']
                    )
                );
                
                $form->Populate($populateData);
            }
        }
        
        if ($form->Validate(FormEngine\FE::SubmittedData())){
            try{
                $Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
                if ($Data['optionstemplate'] != ''){
                    App::getModel('allegro/allegrooptionstemplate')->addAllegroOptionsTemplate($Data['optionstemplate'], $Data);
                }
                App::getContainer()->get('session')->setActiveNewAuctionData($Data);
                App::redirect(__ADMINPANE__ . '/allegro/confirm');
            }
            catch (Exception $e){
                $this->registry->template->assign('error', $e->getMessage());
            }
        }
        else{
            /* $errors = $form->GetErrors();
              foreach ($errors['main_data'] as $_ => $error) {
              if (!empty($error)) {
              App::getContainer()->get('session')->setVolatileMessage($error);
              App::redirect(App::getUri());
              }
              } */
        }
        
        $this->renderLayout(array(
            'form' => $form->Render()
        ));
    }

    protected function _defaultAllegroValueSelect() {

    }

    public function jsAllegroParams ($categoryId = 0)
    {
        $response = new xajaxResponse();
        
        if ($categoryId < 1){
            $response->append('allegro-params-selector', 'innerHTML', '<div class="alert alert-warning">Brak parametrów.</div>');
            $response->script('$("#allegroParamsLoader").remove();');
            return $response;
        }
        
        $params = array();
        $fields = $this->allegro->doGetSellFormFieldsForCategory($categoryId);
        $fields = $fields->{'sell-form-fields-list'};
        if (is_array($fields)){
            foreach ($fields as $field){
                if ($field->{'sell-form-cat'} > 0){
                    $params[] = $field;
                }
            }
        }
        
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'add_allegro_params',
            'action' => '',
            'method' => 'post'
        ));
        
        foreach ($params as $key => $value){
            $rules = array();
            $required = false;
            if ($value->{'sell-form-opt'} == 1){
                $required = true;
                $rules = array(
                    new FormEngine\Rules\Required('Wymagane')
                );
            }
            switch ($value->{'sell-form-type'}) {
                case 4:
                    $labels = explode('|', $value->{'sell-form-desc'});
                    $values = explode('|', $value->{'sell-form-opts-values'});
                    $options = array();
                    foreach ($labels as $key => $label){
                        $options[$values[$key]] = $label;
                    }
                    //default
                    $default = $value->{'sell-form-def-value'};
                    //required
                    if ($required) {
                        unset($options[0]);
                        ($default == 0) &&
                            $default = current($options);
                    }
                    $form->AddChild(new FormEngine\Elements\Select(Array(
                        'name' => 'allegro-params-' . $value->{'sell-form-id'} . '-' . $value->{'sell-form-res-type'},
                        'label' => $value->{'sell-form-title'},
                        'options' => FormEngine\Option::Make($options),
                        'default' => $default,
                        'rules' => $rules,
                        'suffix' => $value->{'sell-form-unit'}
                    )));
                    break;
                
                case 8:
                    $form->AddChild(new FormEngine\Elements\Textarea(Array(
                        'allegro-params-' . $value->{'sell-form-id'} . '-' . $value->{'sell-form-res-type'},
                        'label' => $value->{'sell-form-title'},
                        'rows' => 5,
                        'rules' => $rules,
                        'max_length' => $value->{'sell-form-length'},
                        'suffix' => $value->{'sell-form-unit'}
                    )));
                    break;
                
                case $value->{'sell-form-type'} <= 3:
                    $form->AddChild(new FormEngine\Elements\TextField(Array(
                        'name' => 'allegro-params-' . $value->{'sell-form-id'} . '-' . $value->{'sell-form-res-type'},
                        'label' => $value->{'sell-form-title'},
                        'rules' => $rules,
                        'max_length' => $value->{'sell-form-length'},
                        'suffix' => $value->{'sell-form-unit'}
                    )));
                    break;
                
                default:
                    break;
            }
        }
        
        try{
            $response->append('allegro-params-selector', 'innerHTML', $v = $form->Render('JSAllegroParams'));
            $response->script('eval(document.getElementById("JSAllegroParams").innerHTML);JSAllegroParams();');
            $response->script('$("#allegroParamsLoader").remove();');
        }
        catch (Exception $e){
            throw new FrontendException('Wystąpił nieoczekiwany błąd Allegro.');
        }
        
        return $response;
    }

    public function confirm ()
    {
        if ($this->error){
            $this->renderLayout(Array(
                'errormsg' => $this->error
            ));
            return;
        }
        
        $this->registry->xajax->registerFunction(array(
            'allegroParams',
            $this,
            'jsAllegroParams'
        ));
        
        try{
            $newAuctionData = App::getContainer()->get('session')->getActiveNewAuctionData();
            if ($newAuctionData == NULL){
                App::redirect(__ADMINPANE__ . '/allegro/add');
            }
            
            $form = new FormEngine\Elements\Form(Array(
                'name' => 'confirm_allegro',
                'action' => '',
                'method' => 'post',
                'tabs' => 1
            ));
            
            $allegroProductsPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                'name' => 'allegro_products_pane',
                'label' => $this->trans('TXT_NEW_AUCTION')
            )));
            
            $products = $allegroProductsPane->AddChild(new FormEngine\Elements\AllegroProductSelect(Array(
                'name' => 'products',
                'label' => $this->trans('TXT_SELECT_PRODUCTS'),
                'main_allegro_categories' => App::getModel('allegro/allegrocategories')->getLocalChildAllegroCategories(),
                'load_allegro_category_children' => Array(
                    App::getModel('allegro/allegrocategories'),
                    'getLocalChildAllegroCategories'
                ),
                // 'min_price_factor' => new
            // FormEngine\PriceFactor(FormEngine\PriceFactor::TYPE_PERCENTAGE,
            // $newAuctionData['sell-form-id-7']),
            // 'start_price_factor' => new
            // FormEngine\PriceFactor(FormEngine\PriceFactor::TYPE_PERCENTAGE,
            // $newAuctionData['sell-form-id-6']),
                'min_price_factor' => new FormEngine\PriceFactor(FormEngine\PriceFactor::TYPE_PERCENTAGE, 100),
                'start_price_factor' => new FormEngine\PriceFactor(FormEngine\PriceFactor::TYPE_PERCENTAGE, 100),
                'buy_price_factor' => new FormEngine\PriceFactor(FormEngine\PriceFactor::TYPE_PERCENTAGE, str_replace('%', '', $newAuctionData['sell-form-id-8'])),
                'default_title_format' => $newAuctionData['sell-form-id-1'],
                'default_description_format' => '',
                'tags_translation_table' => Array(
                    'Produkt.Nazwa' => '$name',
                    'Produkt.CenaWywolawcza' => '$allegro_start_price',
                    'Produkt.CenaMinimalna' => '$allegro_min_price',
                    'Produkt.CenaKupTeraz' => '$allegro_buy_price',
                    'Sklep.Nazwa' => $this->registry->loader->getParam('shopname')
                )
            )));
            
            $allegroProductsPane->AddChild(new FormEngine\Elements\Tip(Array(
                'tip' => '
						<div class="column-5">
							<p><strong>Tytuł aukcji</strong> generuje się automatycznie, możesz
							go zmienić wpisując swój tekst lub korzystając z tagów.</p>
							<p>Najpopularniejsze tagi to:</p>
							<ul>
							<li>{Produkt.Nazwa}</li>
							<li>{Produkt.CenaWywolawcza}</li>
							<li>{Produkt.CenaKupTeraz}</li>
							<li>{Produkt.CenaMinimalna}</li>
							</ul>
						</div>
						<div class="column-5">
							<p><strong>Opis</strong>, podawałeś w opcjach aukcji. System
							zastosował ten opis do wszystkich wystawianych niżej przedmiotów.
							Zmień go klikając w ikonkę dokumentu.</p>
							<p>Pamiętaj że do szablonu aukcji wystawiane są również dodatkowo
							opisy produktu z pól “krótki opis” i “Opis”</p>
						</div>
						<div class="column-5">
							<p><strong>Kategorie</strong>, w których chcesz wystawić produkt
							podstawią się automatycznie jeśli sa powiązane z kategoriami w
							sklepie. W innym przypadku możesz wybrać je ręcznie korzystając z
							kategorii ulubionych, które wcześniej zdefiniowałeś lub kategorii
							Allegro, klikajć w ikonkę obok pola.</p>
						</div>
						<div class="column-5">
							<p><strong>Parametry</strong>, to dodatkowe pola dla produktów wymagane w kategoriach. Przed wystawieniem aukcji, sprawdź zakładkę Parametry Allegro i uzupełnij brakujące informacje.</p>
						</div>
						<div class="column-5">
							<p><strong>Ceny wywoławcze, Kup Teraz oraz cena minimalna</strong>
							pobierane są na podstawie danych, które zdefiniowałeś w szablonie
							opcji aukcji. Możesz je dowolnie zmieniać korzystającc z przycisków i
							opcji lub wpisać ręcznie.</p>
							<p><strong>Ilość</strong> wystawianych przedmiotów działa identycznie
							jak cena.</p>
						</div>
			',
                'direction' => FormEngine\Elements\Tip::UP,
                'short_tip' => '<p>Wskazówki dotyczące wystawiania przedmiotów</p>',
                'default_state' => FormEngine\Elements\Tip::EXPANDED
            )));
            if ($form->Validate(FormEngine\FE::SubmittedData())){
                try{
                    $formData = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
                    if (empty($formData['products'])){
                        App::getContainer()->get('session')->setVolatileMessage('Musisz wybrać produkty do wystawienia na aukcjach Allegro.');
                        App::redirect(__ADMINPANE__ . '/allegro/confirm');
                    }
                    //allegro params
                    $i = 0;
                    $j = null;
                    foreach ($_POST as $key => $value){
                        $key = explode('-', $key);
                        if (null === $j) {
                            if (isset($key[4])) {
                                $j = $key[4]; //init
                            }
                            else {
                                continue;
                            }
                        }
                        elseif ($j != $key[4]) {
                            $i++;
                            $j = $key[4];
                        }
                        if (($key[0] == 'allegro') && ($key[1] == 'params')){
                            $newAuctionData['allegro-params'][$i][$key[2]] = array(
                                'sell-form-id' => $key[2],
                                'sell-form-res-type' => $key[3],
                                'value' => $value
                            );
                        }
                    }
                    App::getModel('allegro')->doNewAuction($formData, $newAuctionData);
                    App::getContainer()->get('session')->setVolatileMessage('Nowe aukcje zostały wystawione na Allegro. ' . $this->sync());
                    App::redirect(__ADMINPANE__ . '/allegro');
                }
                catch (Exception $e){
                    $this->registry->template->assign('error', $e->getMessage());
                }
            }
        }
        catch (\Exception $e){
            $this->registry->template->assign('errormessage', $e->getMessage());
        }
        
        $this->renderLayout(array(
            'form' => $form->Render()
        ));
    }

    public function doGetStatesForSelect ($country)
    {
        $states = $this->allegro->doGetStatesInfo($country);
        foreach ($states as $state){
            $tmp[$state['state-id']] = $state['state-name'];
        }
        return FormEngine\Option::Make($tmp);
    }

    public function checkDate ($date)
    {
        return strtotime($date) >= strtotime(date("Y-m-d"));
    }

    protected function parseDate ($date)
    {
        if (! $this->checkDate($date)){
            return date("Y-m-d");
        }
        else{
            return $date;
        }
    }
}
