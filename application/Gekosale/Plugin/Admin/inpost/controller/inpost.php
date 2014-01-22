<?php

namespace Gekosale\Plugin;
use FormEngine;

class InpostController extends Component\Controller\Admin
{

    public function __construct ($registry)
    {
        parent::__construct($registry);
        $this->model = App::getModel('inpost');
    }

    public function index ()
    {
        $this->registry->xajax->registerFunction(array(
            'LoadAllOrder',
            $this->model,
            'getOrderForAjax'
        ));
        
        $this->registry->template->assign('datagrid_filter', $this->model->getDatagridFilterData());
        
        $this->renderLayout();
    }

    public function view ()
    {
        $param = $this->registry->core->getParam();
        /*
	     * Parametr to ID, pobieramy zwykły sticker
	     */
        if (is_numeric($param)){
            $viewid = App::getModel('inpost')->getOrderViewIdByInpostPackage($this->registry->core->getParam());
            $settings = $this->registry->core->loadModuleSettings('inpost', $viewid);
            $pdf = App::getModel('inpost')->inpost_get_sticker($settings['inpostlogin'], $settings['inpostpassword'], $this->registry->core->getParam());
            $filename = $this->registry->core->getParam() . '.pdf';
        }
        else{
            $orders = json_decode(base64_decode($this->registry->core->getParam()));
            $packCodes = Array();
            foreach ($orders as $order){
                $packageData = $this->model->getInpostPackageByOrderId($order);
                if ($packageData['inpostdata']['inpost_data']['selfsend'] == 0){
                    $packCodes[] = $packageData['inpostpackage'];
                }
            }
            $testPrintout = 0;
            if ($settings['inpostlogin'] == 'test@testowy.pl'){
                $testPrintout = 1;
            }
            $settings = $this->registry->core->loadModuleSettings('inpost', $viewid);
            $pdf = App::getModel('inpost')->inpost_get_confirm_printout($settings['inpostlogin'], $settings['inpostpassword'], $packCodes, $testPrintout);
            if(is_array($pdf) && isset($pdf['error']['message'])){
                App::getContainer()->get('session')->setVolatileMessage('Wystąpił błąd w trakcie generowania potwierdzenia: '.$pdf['error']['message']);
                App::redirect(__ADMINPANE__ . '/inpost');
            }
            $filename = 'potwierdzenia_' . date('YmdHis') . '.pdf';
        }
        
        header("Pragma: public");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=$filename");
        header('Content-Transfer-Encoding: binary');
        echo $pdf;
    }

    public function confirm ()
    {
        App::getContainer()->get('cache')->delete('inpost');
        
        $inpostOrders = App::getModel('inpost')->getInpostOrders();
        if (! empty($inpostOrders)){
            $views = Helper::getViewIds();
            foreach ($views as $view){
                if ($view > 0){
                    $settings = $this->registry->core->loadModuleSettings('inpost', $view);
                    if (! empty($settings)){
                        $packages = App::getModel('inpost')->inpost_get_packs_by_sender($settings['inpostlogin'], $settings['inpostpassword']);
                        foreach ($packages as $package){
                            if (isset($inpostOrders[$package['packcode']])){
                                App::getModel('inpost')->updatePackageStatus($package['packcode'], $package['status']);
                            }
                        }
                    }
                }
            }
        }
        App::redirect(__ADMINPANE__ . '/inpost/');
    }

    public function add ()
    {
        $orderData = App::getModel('order')->getOrderById((int) $this->registry->core->getParam());
        
        if (! $orderData){
            App::redirect(__ADMINPANE__ . '/inpost/');
        }
        
        $viewData = App::getModel('view')->getView($orderData['viewid']);
        
        $settings = $this->registry->core->loadModuleSettings('inpost', $orderData['viewid']);
        $packsData = App::getModel('inpost')->getDataByOrderId((int) $this->registry->core->getParam());
        $this->towns = App::getModel('inpost')->inpost_get_towns();
        
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'add_inpost',
            'action' => '',
            'method' => 'post'
        ));
        
        $invoiceData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'inpost_data',
            'label' => 'Dane paczki'
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'senderEmail',
            'default' => $settings['inpostlogin']
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'adreseeEmail',
            'label' => 'Adres e-mail odbiorcy',
            'default' => $orderData['delivery_address']['email']
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'phoneNum',
            'label' => 'Numer telefonu odbiorcy',
            'default' => $this->parseNumber($orderData['delivery_address']['phone']),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PHONE')),
                new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[0-9]{9,}$/')
            )
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'boxMachineName',
            'label' => 'Numer paczkomatu',
            'default' => $packsData
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'packType',
            'label' => 'Typ paczki',
            'options' => Array(
                new FormEngine\Option('A', 'A'),
                new FormEngine\Option('B', 'B'),
                new FormEngine\Option('C', 'C')
            )
        )));
        
        $insurance = $invoiceData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'insurance',
            'label' => 'Ubezpieczenie przesyłki'
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'selfsend',
            'label' => 'Sposób nadania',
            'options' => Array(
                new FormEngine\Option('0', 'w oddziale (lub odbierane przez kuriera dla nadawców z umową'),
                new FormEngine\Option('1', 'bezpośrednio w paczkomacie')
            ),
            'default' => 1
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'insuranceAmount',
            'label' => 'Kwota ubezpieczenia',
            'default' => $orderData['total'],
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $insurance, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $ondelivery = $invoiceData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'ondelivery',
            'label' => 'Przesyłka pobraniowa',
            'default' => ($orderData['pricebeforepromotion'] > 0 && ($orderData['pricebeforepromotion'] < $orderData['total'])) ? 1 : 0
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'onDeliveryAmount',
            'label' => 'Kwota pobrania',
            'default' => $orderData['total'],
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $ondelivery, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $customerDelivering = $invoiceData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'customerDelivering',
            'label' => 'Wysyłka z paczkomatu nadawczego',
            'default' => 0
        )));
        
        $ids = array_flip($this->towns);
        
        $defaultTown = isset($ids[$settings['inposttown']]) ? $ids[$settings['inposttown']] : 0;
        
        $town = $invoiceData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'inposttown',
            'label' => 'Wybierz miejscowość',
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $this->towns),
            'default' => $defaultTown,
            'rules' => Array(
                new FormEngine\Rules\Required('Wybierz miejscowość do odbioru paczki')
            ),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::SHOW, $customerDelivering, new FormEngine\Conditions\Equals(1))
            )
        )));
        
        $defaultOptions = App::getModel('inpost')->getPaczkomat($defaultTown);
        
        $town = $invoiceData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'senderBoxMachineName',
            'label' => 'Wybierz Paczkomat',
            'options' => ! empty($defaultOptions) ? $defaultOptions : Array(),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::SHOW, $customerDelivering, new FormEngine\Conditions\Equals(1)),
                new FormEngine\Dependency(FormEngine\Dependency::EXCHANGE_OPTIONS, $town, Array(
                    App::getModel('inpost'),
                    'getPaczkomat'
                ))
            ),
            'rules' => Array(
                new FormEngine\Rules\Required('Wybierz Paczkomat')
            )
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'onDeliveryAmount',
            'label' => 'Kwota pobrania',
            'default' => $orderData['total'],
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $ondelivery, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $invoiceData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'customerRef',
            'label' => 'Informacja dodatkowa',
            'default' => $this->trans('TXT_ORDER') . ' ' . (int) $this->registry->core->getParam()
        )));
        
        $senderAddress = $invoiceData->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'senderAddress',
            'label' => 'Dane nadawcy'
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'name',
            'label' => 'Imię nadawcy',
            'default' => $settings['inpostname']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'surName',
            'label' => 'Nazwisko nadawcy',
            'default' => $settings['inpostsurName']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'email',
            'label' => 'E-mail nadawcy',
            'default' => $settings['inpostemail']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'phoneNum',
            'label' => 'Numer telefonu nadawcy',
            'default' => $settings['inpostphoneNum'],
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PHONE')),
                new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[0-9]{9,}$/')
            )
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'street',
            'label' => 'Ulica',
            'default' => $settings['inpoststreet']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'buildingNo',
            'label' => 'Numer ulicy',
            'default' => $settings['inpostbuildingNo']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'flatNo',
            'label' => 'Numer lokalu',
            'default' => $settings['inpostflatNo']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'town',
            'label' => 'Miasto',
            'default' => $settings['inposttown']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'zipCode',
            'label' => 'Kod pocztowy',
            'default' => $settings['inpostzipCode']
        )));
        
        $senderAddress->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'province',
            'default' => ''
        )));
        
        if ($form->Validate(FormEngine\FE::SubmittedData())){
            
            $formData = $form->getSubmitValues();
            $formData['inpost_data']['phoneNum'] = $this->parseNumber($formData['inpost_data']['phoneNum']);
            $formData['inpost_data']['senderAddress']['phoneNum'] = $this->parseNumber($formData['inpost_data']['senderAddress']['phoneNum']);
            if ($formData['inpost_data']['insurance'] != 1){
                $formData['inpost_data']['insuranceAmount'] = '';
            }
            if ($formData['inpost_data']['ondelivery'] != 1){
                $formData['inpost_data']['onDeliveryAmount'] = '';
            }
            if ($formData['inpost_data']['customerDelivering'] != 1){
                $formData['inpost_data']['customerDelivering'] = 0;
                $formData['inpost_data']['senderBoxMachineName'] = '';
            }
            $Package[(int) $this->registry->core->getParam()] = $formData['inpost_data'];
            $packcode = App::getModel('inpost')->inpost_send_packs($settings['inpostlogin'], $settings['inpostpassword'], $Package, 0, (int) $formData['inpost_data']['selfsend']);
            App::getContainer()->get('session')->setVolatileMessage("Dodano przesyłkę Inpost do zamówienia {$this->registry->core->getParam()}");
            App::getModel('inpost')->updatePackCodeNo((int) $this->registry->core->getParam(), $packcode[(int) $this->registry->core->getParam()]['packcode'], serialize($formData));
            App::redirect(__ADMINPANE__ . '/inpost');
        }
        
        $this->renderLayout(Array(
            'form' => $form->Render()
        ));
    }

    public function parseNumber ($number)
    {
        $chars = array(
            '-',
            ',',
            ' ',
            '+'
        );
        $number = str_replace($chars, '', $number);
        $number = trim($number);
        if (strlen($number) == 9){
            return $number;
        }
        return substr($number, - 9);
    }
}