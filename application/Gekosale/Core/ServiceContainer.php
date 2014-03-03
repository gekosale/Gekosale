<?php
namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

/**
 * ServiceContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class ServiceContainer extends Container
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();

        $this->services =
        $this->scopedServices =
        $this->scopeStacks = array();

        $this->set('service_container', $this);

        $this->scopes = array();
        $this->scopeChildren = array();
        $this->methodMap = array(
            'admin_menu.subscriber' => 'getAdminMenu_SubscriberService',
            'availability.datagrid' => 'getAvailability_DatagridService',
            'availability.form' => 'getAvailability_FormService',
            'availability.repository' => 'getAvailability_RepositoryService',
            'availability.subscriber' => 'getAvailability_SubscriberService',
            'category.form' => 'getCategory_FormService',
            'category.repository' => 'getCategory_RepositoryService',
            'category.subscriber' => 'getCategory_SubscriberService',
            'category.tree' => 'getCategory_TreeService',
            'company.datagrid' => 'getCompany_DatagridService',
            'company.form' => 'getCompany_FormService',
            'company.repository' => 'getCompany_RepositoryService',
            'company.subscriber' => 'getCompany_SubscriberService',
            'config_locator' => 'getConfigLocatorService',
            'contact.datagrid' => 'getContact_DatagridService',
            'contact.form' => 'getContact_FormService',
            'contact.repository' => 'getContact_RepositoryService',
            'contact.subscriber' => 'getContact_SubscriberService',
            'controller_resolver' => 'getControllerResolverService',
            'country.repository' => 'getCountry_RepositoryService',
            'currency.datagrid' => 'getCurrency_DatagridService',
            'currency.form' => 'getCurrency_FormService',
            'currency.repository' => 'getCurrency_RepositoryService',
            'currency.subscriber' => 'getCurrency_SubscriberService',
            'database_manager' => 'getDatabaseManagerService',
            'deliverer.datagrid' => 'getDeliverer_DatagridService',
            'deliverer.form' => 'getDeliverer_FormService',
            'deliverer.repository' => 'getDeliverer_RepositoryService',
            'deliverer.subscriber' => 'getDeliverer_SubscriberService',
            'event_dispatcher' => 'getEventDispatcherService',
            'file.repository' => 'getFile_RepositoryService',
            'filesystem' => 'getFilesystemService',
            'finder' => 'getFinderService',
            'form_helper' => 'getFormHelperService',
            'helper' => 'getHelperService',
            'kernel' => 'getKernelService',
            'language.datagrid' => 'getLanguage_DatagridService',
            'language.form' => 'getLanguage_FormService',
            'language.repository' => 'getLanguage_RepositoryService',
            'language.subscriber' => 'getLanguage_SubscriberService',
            'locale.listener' => 'getLocale_ListenerService',
            'plugin_manager.datagrid' => 'getPluginManager_DatagridService',
            'plugin_manager.form' => 'getPluginManager_FormService',
            'plugin_manager.repository' => 'getPluginManager_RepositoryService',
            'plugin_manager.subscriber' => 'getPluginManager_SubscriberService',
            'producer.datagrid' => 'getProducer_DatagridService',
            'producer.form' => 'getProducer_FormService',
            'producer.repository' => 'getProducer_RepositoryService',
            'producer.subscriber' => 'getProducer_SubscriberService',
            'request' => 'getRequestService',
            'router' => 'getRouterService',
            'router.loader' => 'getRouter_LoaderService',
            'router.subscriber' => 'getRouter_SubscriberService',
            'session' => 'getSessionService',
            'session.handler' => 'getSession_HandlerService',
            'session.storage' => 'getSession_StorageService',
            'shop.datagrid' => 'getShop_DatagridService',
            'shop.form' => 'getShop_FormService',
            'shop.repository' => 'getShop_RepositoryService',
            'shop.subscriber' => 'getShop_SubscriberService',
            'template.subscriber' => 'getTemplate_SubscriberService',
            'translation' => 'getTranslationService',
            'twig' => 'getTwigService',
            'twig.extension.asset' => 'getTwig_Extension_AssetService',
            'twig.extension.box' => 'getTwig_Extension_BoxService',
            'twig.extension.contact' => 'getTwig_Extension_ContactService',
            'twig.extension.datagrid' => 'getTwig_Extension_DatagridService',
            'twig.extension.debug' => 'getTwig_Extension_DebugService',
            'twig.extension.form' => 'getTwig_Extension_FormService',
            'twig.extension.intl' => 'getTwig_Extension_IntlService',
            'twig.extension.routing' => 'getTwig_Extension_RoutingService',
            'twig.extension.translation' => 'getTwig_Extension_TranslationService',
            'twig.loader.admin' => 'getTwig_Loader_AdminService',
            'twig.loader.front' => 'getTwig_Loader_FrontService',
            'unit.datagrid' => 'getUnit_DatagridService',
            'unit.form' => 'getUnit_FormService',
            'unit.repository' => 'getUnit_RepositoryService',
            'unit.subscriber' => 'getUnit_SubscriberService',
            'vat.datagrid' => 'getVat_DatagridService',
            'vat.form' => 'getVat_FormService',
            'vat.repository' => 'getVat_RepositoryService',
            'vat.subscriber' => 'getVat_SubscriberService',
            'xajax' => 'getXajaxService',
            'xajax_manager' => 'getXajaxManagerService',
        );

        $this->aliases = array();
    }

    /**
     * Gets the 'admin_menu.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\AdminMenu\Event\AdminMenuEventSubscriber A Gekosale\Plugin\AdminMenu\Event\AdminMenuEventSubscriber instance.
     */
    protected function getAdminMenu_SubscriberService()
    {
        return $this->services['admin_menu.subscriber'] = new \Gekosale\Plugin\AdminMenu\Event\AdminMenuEventSubscriber();
    }

    /**
     * Gets the 'availability.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Availability\DataGrid\AvailabilityDataGrid A Gekosale\Plugin\Availability\DataGrid\AvailabilityDataGrid instance.
     */
    protected function getAvailability_DatagridService()
    {
        $this->services['availability.datagrid'] = $instance = new \Gekosale\Plugin\Availability\DataGrid\AvailabilityDataGrid();

        $instance->setRepository($this->get('availability.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'availability.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Availability\Form\AvailabilityForm A Gekosale\Plugin\Availability\Form\AvailabilityForm instance.
     */
    protected function getAvailability_FormService()
    {
        $this->services['availability.form'] = $instance = new \Gekosale\Plugin\Availability\Form\AvailabilityForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'availability.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Availability\Repository\AvailabilityRepository A Gekosale\Plugin\Availability\Repository\AvailabilityRepository instance.
     */
    protected function getAvailability_RepositoryService()
    {
        $this->services['availability.repository'] = $instance = new \Gekosale\Plugin\Availability\Repository\AvailabilityRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'availability.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Availability\Event\AvailabilityEventSubscriber A Gekosale\Plugin\Availability\Event\AvailabilityEventSubscriber instance.
     */
    protected function getAvailability_SubscriberService()
    {
        return $this->services['availability.subscriber'] = new \Gekosale\Plugin\Availability\Event\AvailabilityEventSubscriber();
    }

    /**
     * Gets the 'category.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Category\Form\CategoryForm A Gekosale\Plugin\Category\Form\CategoryForm instance.
     */
    protected function getCategory_FormService()
    {
        $this->services['category.form'] = $instance = new \Gekosale\Plugin\Category\Form\CategoryForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'category.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Category\Repository\CategoryRepository A Gekosale\Plugin\Category\Repository\CategoryRepository instance.
     */
    protected function getCategory_RepositoryService()
    {
        $this->services['category.repository'] = $instance = new \Gekosale\Plugin\Category\Repository\CategoryRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'category.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Category\Event\CategoryEventSubscriber A Gekosale\Plugin\Category\Event\CategoryEventSubscriber instance.
     */
    protected function getCategory_SubscriberService()
    {
        return $this->services['category.subscriber'] = new \Gekosale\Plugin\Category\Event\CategoryEventSubscriber();
    }

    /**
     * Gets the 'category.tree' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Category\Form\CategoryTree A Gekosale\Plugin\Category\Form\CategoryTree instance.
     */
    protected function getCategory_TreeService()
    {
        $this->services['category.tree'] = $instance = new \Gekosale\Plugin\Category\Form\CategoryTree();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'company.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Company\DataGrid\CompanyDataGrid A Gekosale\Plugin\Company\DataGrid\CompanyDataGrid instance.
     */
    protected function getCompany_DatagridService()
    {
        $this->services['company.datagrid'] = $instance = new \Gekosale\Plugin\Company\DataGrid\CompanyDataGrid();

        $instance->setRepository($this->get('company.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'company.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Company\Form\CompanyForm A Gekosale\Plugin\Company\Form\CompanyForm instance.
     */
    protected function getCompany_FormService()
    {
        $this->services['company.form'] = $instance = new \Gekosale\Plugin\Company\Form\CompanyForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'company.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Company\Repository\CompanyRepository A Gekosale\Plugin\Company\Repository\CompanyRepository instance.
     */
    protected function getCompany_RepositoryService()
    {
        $this->services['company.repository'] = $instance = new \Gekosale\Plugin\Company\Repository\CompanyRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'company.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Company\Event\CompanyEventSubscriber A Gekosale\Plugin\Company\Event\CompanyEventSubscriber instance.
     */
    protected function getCompany_SubscriberService()
    {
        return $this->services['company.subscriber'] = new \Gekosale\Plugin\Company\Event\CompanyEventSubscriber();
    }

    /**
     * Gets the 'config_locator' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Config\FileLocator A Symfony\Component\Config\FileLocator instance.
     */
    protected function getConfigLocatorService()
    {
        return $this->services['config_locator'] = new \Symfony\Component\Config\FileLocator('D:\\Git\\Gekosale3\\config');
    }

    /**
     * Gets the 'contact.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Contact\DataGrid\ContactDataGrid A Gekosale\Plugin\Contact\DataGrid\ContactDataGrid instance.
     */
    protected function getContact_DatagridService()
    {
        $this->services['contact.datagrid'] = $instance = new \Gekosale\Plugin\Contact\DataGrid\ContactDataGrid();

        $instance->setRepository($this->get('contact.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'contact.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Contact\Form\ContactForm A Gekosale\Plugin\Contact\Form\ContactForm instance.
     */
    protected function getContact_FormService()
    {
        $this->services['contact.form'] = $instance = new \Gekosale\Plugin\Contact\Form\ContactForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'contact.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Contact\Repository\ContactRepository A Gekosale\Plugin\Contact\Repository\ContactRepository instance.
     */
    protected function getContact_RepositoryService()
    {
        $this->services['contact.repository'] = $instance = new \Gekosale\Plugin\Contact\Repository\ContactRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'contact.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Contact\Event\ContactEventSubscriber A Gekosale\Plugin\Contact\Event\ContactEventSubscriber instance.
     */
    protected function getContact_SubscriberService()
    {
        return $this->services['contact.subscriber'] = new \Gekosale\Plugin\Contact\Event\ContactEventSubscriber();
    }

    /**
     * Gets the 'controller_resolver' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\ControllerResolver A Gekosale\Core\ControllerResolver instance.
     */
    protected function getControllerResolverService()
    {
        return $this->services['controller_resolver'] = new \Gekosale\Core\ControllerResolver($this);
    }

    /**
     * Gets the 'country.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Country\Repository\CountryRepository A Gekosale\Plugin\Country\Repository\CountryRepository instance.
     */
    protected function getCountry_RepositoryService()
    {
        $this->services['country.repository'] = $instance = new \Gekosale\Plugin\Country\Repository\CountryRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'currency.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Currency\DataGrid\CurrencyDataGrid A Gekosale\Plugin\Currency\DataGrid\CurrencyDataGrid instance.
     */
    protected function getCurrency_DatagridService()
    {
        $this->services['currency.datagrid'] = $instance = new \Gekosale\Plugin\Currency\DataGrid\CurrencyDataGrid();

        $instance->setRepository($this->get('currency.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'currency.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Currency\Form\CurrencyForm A Gekosale\Plugin\Currency\Form\CurrencyForm instance.
     */
    protected function getCurrency_FormService()
    {
        $this->services['currency.form'] = $instance = new \Gekosale\Plugin\Currency\Form\CurrencyForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'currency.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Currency\Repository\CurrencyRepository A Gekosale\Plugin\Currency\Repository\CurrencyRepository instance.
     */
    protected function getCurrency_RepositoryService()
    {
        $this->services['currency.repository'] = $instance = new \Gekosale\Plugin\Currency\Repository\CurrencyRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'currency.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Currency\Event\CurrencyEventSubscriber A Gekosale\Plugin\Currency\Event\CurrencyEventSubscriber instance.
     */
    protected function getCurrency_SubscriberService()
    {
        return $this->services['currency.subscriber'] = new \Gekosale\Plugin\Currency\Event\CurrencyEventSubscriber();
    }

    /**
     * Gets the 'database_manager' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Illuminate\Database\Capsule\Manager A Illuminate\Database\Capsule\Manager instance.
     */
    protected function getDatabaseManagerService()
    {
        $this->services['database_manager'] = $instance = new \Illuminate\Database\Capsule\Manager();

        $instance->addConnection(array('driver' => 'mysql', 'host' => 'localhost', 'database' => 'gekosale3', 'username' => 'root', 'password' => '', 'charset' => 'utf8', 'collation' => 'utf8_unicode_ci', 'prefix' => ''));
        $instance->setAsGlobal();
        $instance->bootEloquent();

        return $instance;
    }

    /**
     * Gets the 'deliverer.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Deliverer\DataGrid\DelivererDataGrid A Gekosale\Plugin\Deliverer\DataGrid\DelivererDataGrid instance.
     */
    protected function getDeliverer_DatagridService()
    {
        $this->services['deliverer.datagrid'] = $instance = new \Gekosale\Plugin\Deliverer\DataGrid\DelivererDataGrid();

        $instance->setRepository($this->get('deliverer.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'deliverer.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Deliverer\Form\DelivererForm A Gekosale\Plugin\Deliverer\Form\DelivererForm instance.
     */
    protected function getDeliverer_FormService()
    {
        $this->services['deliverer.form'] = $instance = new \Gekosale\Plugin\Deliverer\Form\DelivererForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'deliverer.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Deliverer\Repository\DelivererRepository A Gekosale\Plugin\Deliverer\Repository\DelivererRepository instance.
     */
    protected function getDeliverer_RepositoryService()
    {
        $this->services['deliverer.repository'] = $instance = new \Gekosale\Plugin\Deliverer\Repository\DelivererRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'deliverer.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Deliverer\Event\DelivererEventSubscriber A Gekosale\Plugin\Deliverer\Event\DelivererEventSubscriber instance.
     */
    protected function getDeliverer_SubscriberService()
    {
        return $this->services['deliverer.subscriber'] = new \Gekosale\Plugin\Deliverer\Event\DelivererEventSubscriber();
    }

    /**
     * Gets the 'event_dispatcher' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher A Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher instance.
     */
    protected function getEventDispatcherService()
    {
        $this->services['event_dispatcher'] = $instance = new \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher($this);

        $instance->addSubscriberService('router.subscriber', 'Symfony\\Component\\HttpKernel\\EventListener\\RouterListener');
        $instance->addSubscriberService('locale.listener', 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener');
        $instance->addSubscriberService('template.subscriber', 'Gekosale\\Core\\Template\\Subscriber\\Template');
        $instance->addSubscriberService('router.subscriber', 'Symfony\\Component\\HttpKernel\\EventListener\\RouterListener');
        $instance->addSubscriberService('locale.listener', 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener');
        $instance->addSubscriberService('template.subscriber', 'Gekosale\\Core\\Template\\Subscriber\\Template');
        $instance->addSubscriberService('admin_menu.subscriber', 'Gekosale\\Plugin\\AdminMenu\\Event\\AdminMenuEventSubscriber');
        $instance->addSubscriberService('availability.subscriber', 'Gekosale\\Plugin\\Availability\\Event\\AvailabilityEventSubscriber');
        $instance->addSubscriberService('category.subscriber', 'Gekosale\\Plugin\\Category\\Event\\CategoryEventSubscriber');
        $instance->addSubscriberService('company.subscriber', 'Gekosale\\Plugin\\Company\\Event\\CompanyEventSubscriber');
        $instance->addSubscriberService('contact.subscriber', 'Gekosale\\Plugin\\Contact\\Event\\ContactEventSubscriber');
        $instance->addSubscriberService('currency.subscriber', 'Gekosale\\Plugin\\Currency\\Event\\CurrencyEventSubscriber');
        $instance->addSubscriberService('deliverer.subscriber', 'Gekosale\\Plugin\\Deliverer\\Event\\DelivererEventSubscriber');
        $instance->addSubscriberService('language.subscriber', 'Gekosale\\Plugin\\Language\\Event\\LanguageEventSubscriber');
        $instance->addSubscriberService('plugin_manager.subscriber', 'Gekosale\\Plugin\\PluginManager\\Event\\PluginManagerEventSubscriber');
        $instance->addSubscriberService('producer.subscriber', 'Gekosale\\Plugin\\Producer\\Event\\ProducerEventSubscriber');
        $instance->addSubscriberService('shop.subscriber', 'Gekosale\\Plugin\\Shop\\Event\\ShopEventSubscriber');
        $instance->addSubscriberService('unit.subscriber', 'Gekosale\\Plugin\\Unit\\Event\\UnitEventSubscriber');
        $instance->addSubscriberService('vat.subscriber', 'Gekosale\\Plugin\\Vat\\Event\\VatEventSubscriber');

        return $instance;
    }

    /**
     * Gets the 'file.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\File\Repository\FileRepository A Gekosale\Plugin\File\Repository\FileRepository instance.
     */
    protected function getFile_RepositoryService()
    {
        $this->services['file.repository'] = $instance = new \Gekosale\Plugin\File\Repository\FileRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'filesystem' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Filesystem\Filesystem A Symfony\Component\Filesystem\Filesystem instance.
     */
    protected function getFilesystemService()
    {
        return $this->services['filesystem'] = new \Symfony\Component\Filesystem\Filesystem();
    }

    /**
     * Gets the 'finder' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Finder\Finder A Symfony\Component\Finder\Finder instance.
     */
    protected function getFinderService()
    {
        return $this->services['finder'] = new \Symfony\Component\Finder\Finder();
    }

    /**
     * Gets the 'form_helper' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Form A Gekosale\Core\Form instance.
     */
    protected function getFormHelperService()
    {
        $this->services['form_helper'] = $instance = new \Gekosale\Core\Form();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'helper' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Helper A Gekosale\Core\Helper instance.
     */
    protected function getHelperService()
    {
        $this->services['helper'] = $instance = new \Gekosale\Core\Helper();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'kernel' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel A Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel instance.
     */
    protected function getKernelService()
    {
        return $this->services['kernel'] = new \Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel($this->get('event_dispatcher'), $this, $this->get('controller_resolver'));
    }

    /**
     * Gets the 'language.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Language\DataGrid\LanguageDataGrid A Gekosale\Plugin\Language\DataGrid\LanguageDataGrid instance.
     */
    protected function getLanguage_DatagridService()
    {
        $this->services['language.datagrid'] = $instance = new \Gekosale\Plugin\Language\DataGrid\LanguageDataGrid();

        $instance->setRepository($this->get('language.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'language.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Language\Form\LanguageForm A Gekosale\Plugin\Language\Form\LanguageForm instance.
     */
    protected function getLanguage_FormService()
    {
        $this->services['language.form'] = $instance = new \Gekosale\Plugin\Language\Form\LanguageForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'language.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Language\Repository\LanguageRepository A Gekosale\Plugin\Language\Repository\LanguageRepository instance.
     */
    protected function getLanguage_RepositoryService()
    {
        $this->services['language.repository'] = $instance = new \Gekosale\Plugin\Language\Repository\LanguageRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'language.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Language\Event\LanguageEventSubscriber A Gekosale\Plugin\Language\Event\LanguageEventSubscriber instance.
     */
    protected function getLanguage_SubscriberService()
    {
        return $this->services['language.subscriber'] = new \Gekosale\Plugin\Language\Event\LanguageEventSubscriber();
    }

    /**
     * Gets the 'locale.listener' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\EventListener\LocaleListener A Symfony\Component\HttpKernel\EventListener\LocaleListener instance.
     */
    protected function getLocale_ListenerService()
    {
        return $this->services['locale.listener'] = new \Symfony\Component\HttpKernel\EventListener\LocaleListener();
    }

    /**
     * Gets the 'plugin_manager.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\PluginManager\DataGrid\PluginManagerDataGrid A Gekosale\Plugin\PluginManager\DataGrid\PluginManagerDataGrid instance.
     */
    protected function getPluginManager_DatagridService()
    {
        $this->services['plugin_manager.datagrid'] = $instance = new \Gekosale\Plugin\PluginManager\DataGrid\PluginManagerDataGrid();

        $instance->setRepository($this->get('plugin_manager.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'plugin_manager.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\PluginManager\Form\PluginManagerForm A Gekosale\Plugin\PluginManager\Form\PluginManagerForm instance.
     */
    protected function getPluginManager_FormService()
    {
        $this->services['plugin_manager.form'] = $instance = new \Gekosale\Plugin\PluginManager\Form\PluginManagerForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'plugin_manager.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\PluginManager\Repository\PluginManagerRepository A Gekosale\Plugin\PluginManager\Repository\PluginManagerRepository instance.
     */
    protected function getPluginManager_RepositoryService()
    {
        $this->services['plugin_manager.repository'] = $instance = new \Gekosale\Plugin\PluginManager\Repository\PluginManagerRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'plugin_manager.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\PluginManager\Event\PluginManagerEventSubscriber A Gekosale\Plugin\PluginManager\Event\PluginManagerEventSubscriber instance.
     */
    protected function getPluginManager_SubscriberService()
    {
        return $this->services['plugin_manager.subscriber'] = new \Gekosale\Plugin\PluginManager\Event\PluginManagerEventSubscriber();
    }

    /**
     * Gets the 'producer.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Producer\DataGrid\ProducerDataGrid A Gekosale\Plugin\Producer\DataGrid\ProducerDataGrid instance.
     */
    protected function getProducer_DatagridService()
    {
        $this->services['producer.datagrid'] = $instance = new \Gekosale\Plugin\Producer\DataGrid\ProducerDataGrid();

        $instance->setRepository($this->get('producer.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'producer.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Producer\Form\ProducerForm A Gekosale\Plugin\Producer\Form\ProducerForm instance.
     */
    protected function getProducer_FormService()
    {
        $this->services['producer.form'] = $instance = new \Gekosale\Plugin\Producer\Form\ProducerForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'producer.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Producer\Repository\ProducerRepository A Gekosale\Plugin\Producer\Repository\ProducerRepository instance.
     */
    protected function getProducer_RepositoryService()
    {
        $this->services['producer.repository'] = $instance = new \Gekosale\Plugin\Producer\Repository\ProducerRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'producer.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Producer\Event\ProducerEventSubscriber A Gekosale\Plugin\Producer\Event\ProducerEventSubscriber instance.
     */
    protected function getProducer_SubscriberService()
    {
        return $this->services['producer.subscriber'] = new \Gekosale\Plugin\Producer\Event\ProducerEventSubscriber();
    }

    /**
     * Gets the 'request' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getRequestService()
    {
        throw new RuntimeException('You have requested a synthetic service ("request"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'router' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Routing\Router A Symfony\Component\Routing\Router instance.
     */
    protected function getRouterService()
    {
        return $this->services['router'] = new \Symfony\Component\Routing\Router($this->get('router.loader'), '', array('cache_dir' => 'D:\\Git\\Gekosale3\\var', 'generator_cache_class' => 'GekosaleUrlGenerator', 'matcher_cache_class' => 'GekosaleUrlMatcher'));
    }

    /**
     * Gets the 'router.loader' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Routing\Loader\PhpFileLoader A Symfony\Component\Routing\Loader\PhpFileLoader instance.
     */
    protected function getRouter_LoaderService()
    {
        return $this->services['router.loader'] = new \Symfony\Component\Routing\Loader\PhpFileLoader($this->get('config_locator'));
    }

    /**
     * Gets the 'router.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\EventListener\RouterListener A Symfony\Component\HttpKernel\EventListener\RouterListener instance.
     */
    protected function getRouter_SubscriberService()
    {
        return $this->services['router.subscriber'] = new \Symfony\Component\HttpKernel\EventListener\RouterListener($this->get("router")->getMatcher());
    }

    /**
     * Gets the 'session' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpFoundation\Session\Session A Symfony\Component\HttpFoundation\Session\Session instance.
     */
    protected function getSessionService()
    {
        $this->services['session'] = $instance = new \Symfony\Component\HttpFoundation\Session\Session($this->get('session.storage'));

        $instance->start();

        return $instance;
    }

    /**
     * Gets the 'session.handler' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler A Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler instance.
     */
    protected function getSession_HandlerService()
    {
        return $this->services['session.handler'] = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler($this->get("database_manager")->getConnection()->getPdo(), array('db_table' => 'session'));
    }

    /**
     * Gets the 'session.storage' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage A Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage instance.
     */
    protected function getSession_StorageService()
    {
        return $this->services['session.storage'] = new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage(array(), $this->get('session.handler'));
    }

    /**
     * Gets the 'shop.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Shop\DataGrid\ShopDataGrid A Gekosale\Plugin\Shop\DataGrid\ShopDataGrid instance.
     */
    protected function getShop_DatagridService()
    {
        $this->services['shop.datagrid'] = $instance = new \Gekosale\Plugin\Shop\DataGrid\ShopDataGrid();

        $instance->setRepository($this->get('shop.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'shop.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Shop\Form\ShopForm A Gekosale\Plugin\Shop\Form\ShopForm instance.
     */
    protected function getShop_FormService()
    {
        $this->services['shop.form'] = $instance = new \Gekosale\Plugin\Shop\Form\ShopForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'shop.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Shop\Repository\ShopRepository A Gekosale\Plugin\Shop\Repository\ShopRepository instance.
     */
    protected function getShop_RepositoryService()
    {
        $this->services['shop.repository'] = $instance = new \Gekosale\Plugin\Shop\Repository\ShopRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'shop.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Shop\Event\ShopEventSubscriber A Gekosale\Plugin\Shop\Event\ShopEventSubscriber instance.
     */
    protected function getShop_SubscriberService()
    {
        return $this->services['shop.subscriber'] = new \Gekosale\Plugin\Shop\Event\ShopEventSubscriber();
    }

    /**
     * Gets the 'template.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Template\Subscriber\Template A Gekosale\Core\Template\Subscriber\Template instance.
     */
    protected function getTemplate_SubscriberService()
    {
        return $this->services['template.subscriber'] = new \Gekosale\Core\Template\Subscriber\Template();
    }

    /**
     * Gets the 'translation' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Translation A Gekosale\Core\Translation instance.
     */
    protected function getTranslationService()
    {
        return $this->services['translation'] = new \Gekosale\Core\Translation($this, 'pl_PL');
    }

    /**
     * Gets the 'twig' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Twig_Environment A Twig_Environment instance.
     */
    protected function getTwigService()
    {
        $a = $this->get('twig.extension.translation');
        $b = $this->get('twig.extension.routing');
        $c = $this->get('twig.extension.intl');
        $d = $this->get('twig.extension.debug');
        $e = $this->get('twig.extension.box');
        $f = $this->get('twig.extension.form');
        $g = $this->get('twig.extension.asset');
        $h = $this->get('twig.extension.datagrid');

        $this->services['twig'] = $instance = new \Twig_Environment($this->get('twig.loader.front'), array('cache' => 'D:\\Git\\Gekosale3\\var/cache', 'auto_reload' => true, 'autoescape' => true, 'debug' => true));

        $instance->addExtension($a);
        $instance->addExtension($b);
        $instance->addExtension($c);
        $instance->addExtension($d);
        $instance->addExtension($e);
        $instance->addExtension($f);
        $instance->addExtension($g);
        $instance->addExtension($h);
        $instance->addExtension($a);
        $instance->addExtension($b);
        $instance->addExtension($c);
        $instance->addExtension($d);
        $instance->addExtension($e);
        $instance->addExtension($f);
        $instance->addExtension($g);
        $instance->addExtension($h);
        $instance->addExtension($this->get('twig.extension.contact'));

        return $instance;
    }

    /**
     * Gets the 'twig.extension.asset' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Template\Extension\Asset A Gekosale\Core\Template\Extension\Asset instance.
     */
    protected function getTwig_Extension_AssetService()
    {
        return $this->services['twig.extension.asset'] = new \Gekosale\Core\Template\Extension\Asset($this);
    }

    /**
     * Gets the 'twig.extension.box' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Template\Extension\Box A Gekosale\Core\Template\Extension\Box instance.
     */
    protected function getTwig_Extension_BoxService()
    {
        return $this->services['twig.extension.box'] = new \Gekosale\Core\Template\Extension\Box($this);
    }

    /**
     * Gets the 'twig.extension.contact' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Contact\Twig\Contact A Gekosale\Plugin\Contact\Twig\Contact instance.
     */
    protected function getTwig_Extension_ContactService()
    {
        return $this->services['twig.extension.contact'] = new \Gekosale\Plugin\Contact\Twig\Contact($this);
    }

    /**
     * Gets the 'twig.extension.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Template\Extension\DataGrid A Gekosale\Core\Template\Extension\DataGrid instance.
     */
    protected function getTwig_Extension_DatagridService()
    {
        return $this->services['twig.extension.datagrid'] = new \Gekosale\Core\Template\Extension\DataGrid($this);
    }

    /**
     * Gets the 'twig.extension.debug' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Twig_Extension_Debug A Twig_Extension_Debug instance.
     */
    protected function getTwig_Extension_DebugService()
    {
        return $this->services['twig.extension.debug'] = new \Twig_Extension_Debug();
    }

    /**
     * Gets the 'twig.extension.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Template\Extension\Form A Gekosale\Core\Template\Extension\Form instance.
     */
    protected function getTwig_Extension_FormService()
    {
        return $this->services['twig.extension.form'] = new \Gekosale\Core\Template\Extension\Form($this);
    }

    /**
     * Gets the 'twig.extension.intl' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Twig_Extensions_Extension_Intl A Twig_Extensions_Extension_Intl instance.
     */
    protected function getTwig_Extension_IntlService()
    {
        return $this->services['twig.extension.intl'] = new \Twig_Extensions_Extension_Intl();
    }

    /**
     * Gets the 'twig.extension.routing' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Template\Extension\Routing A Gekosale\Core\Template\Extension\Routing instance.
     */
    protected function getTwig_Extension_RoutingService()
    {
        return $this->services['twig.extension.routing'] = new \Gekosale\Core\Template\Extension\Routing($this->get('router'), $this->get('request'));
    }

    /**
     * Gets the 'twig.extension.translation' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bridge\Twig\Extension\TranslationExtension A Symfony\Bridge\Twig\Extension\TranslationExtension instance.
     */
    protected function getTwig_Extension_TranslationService()
    {
        return $this->services['twig.extension.translation'] = new \Symfony\Bridge\Twig\Extension\TranslationExtension($this->get('translation'));
    }

    /**
     * Gets the 'twig.loader.admin' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Twig_Loader_Filesystem A Twig_Loader_Filesystem instance.
     */
    protected function getTwig_Loader_AdminService()
    {
        return $this->services['twig.loader.admin'] = new \Twig_Loader_Filesystem(array(0 => 'D:\\Git\\Gekosale3\\design/templates'));
    }

    /**
     * Gets the 'twig.loader.front' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Twig_Loader_Filesystem A Twig_Loader_Filesystem instance.
     */
    protected function getTwig_Loader_FrontService()
    {
        return $this->services['twig.loader.front'] = new \Twig_Loader_Filesystem(array(0 => 'D:\\Git\\Gekosale3\\design/frontend/Gekosale/templates'));
    }

    /**
     * Gets the 'unit.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Unit\DataGrid\UnitDataGrid A Gekosale\Plugin\Unit\DataGrid\UnitDataGrid instance.
     */
    protected function getUnit_DatagridService()
    {
        $this->services['unit.datagrid'] = $instance = new \Gekosale\Plugin\Unit\DataGrid\UnitDataGrid();

        $instance->setRepository($this->get('unit.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'unit.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Unit\Form\UnitForm A Gekosale\Plugin\Unit\Form\UnitForm instance.
     */
    protected function getUnit_FormService()
    {
        $this->services['unit.form'] = $instance = new \Gekosale\Plugin\Unit\Form\UnitForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'unit.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Unit\Repository\UnitRepository A Gekosale\Plugin\Unit\Repository\UnitRepository instance.
     */
    protected function getUnit_RepositoryService()
    {
        $this->services['unit.repository'] = $instance = new \Gekosale\Plugin\Unit\Repository\UnitRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'unit.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Unit\Event\UnitEventSubscriber A Gekosale\Plugin\Unit\Event\UnitEventSubscriber instance.
     */
    protected function getUnit_SubscriberService()
    {
        return $this->services['unit.subscriber'] = new \Gekosale\Plugin\Unit\Event\UnitEventSubscriber();
    }

    /**
     * Gets the 'vat.datagrid' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Vat\DataGrid\VatDataGrid A Gekosale\Plugin\Vat\DataGrid\VatDataGrid instance.
     */
    protected function getVat_DatagridService()
    {
        $this->services['vat.datagrid'] = $instance = new \Gekosale\Plugin\Vat\DataGrid\VatDataGrid();

        $instance->setRepository($this->get('vat.repository'));
        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'vat.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Vat\Form\VatForm A Gekosale\Plugin\Vat\Form\VatForm instance.
     */
    protected function getVat_FormService()
    {
        $this->services['vat.form'] = $instance = new \Gekosale\Plugin\Vat\Form\VatForm();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'vat.repository' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Vat\Repository\VatRepository A Gekosale\Plugin\Vat\Repository\VatRepository instance.
     */
    protected function getVat_RepositoryService()
    {
        $this->services['vat.repository'] = $instance = new \Gekosale\Plugin\Vat\Repository\VatRepository();

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Gets the 'vat.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Vat\Event\VatEventSubscriber A Gekosale\Plugin\Vat\Event\VatEventSubscriber instance.
     */
    protected function getVat_SubscriberService()
    {
        return $this->services['vat.subscriber'] = new \Gekosale\Plugin\Vat\Event\VatEventSubscriber();
    }

    /**
     * Gets the 'xajax' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return xajax A xajax instance.
     */
    protected function getXajaxService()
    {
        return $this->services['xajax'] = new \xajax();
    }

    /**
     * Gets the 'xajax_manager' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\XajaxManager A Gekosale\Core\XajaxManager instance.
     */
    protected function getXajaxManagerService()
    {
        return $this->services['xajax_manager'] = new \Gekosale\Core\XajaxManager($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        $name = strtolower($name);

        if (!(isset($this->parameters[$name]) || array_key_exists($name, $this->parameters))) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        $name = strtolower($name);

        return isset($this->parameters[$name]) || array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    /**
     * {@inheritDoc}
     */
    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $this->parameterBag = new FrozenParameterBag($this->parameters);
        }

        return $this->parameterBag;
    }
    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return array(
            'application.root_path' => 'D:\\Git\\Gekosale3\\',
            'application.debug_mode' => true,
            'locale' => 'pl_PL',
            'timezone' => 'Europe/Warsaw',
            'application.namespaces' => array(
                0 => 'Gekosale',
            ),
            'application.design_path' => 'D:\\Git\\Gekosale3\\design',
            'admin.themes' => array(
                0 => 'D:\\Git\\Gekosale3\\design/templates',
            ),
            'front.themes' => array(
                0 => 'D:\\Git\\Gekosale3\\design/frontend/Gekosale/templates',
            ),
            'propel.config' => array(
                'dsn' => 'mysql:host=localhost;dbname=gekosale3',
                'user' => 'root',
                'password' => '',
            ),
            'router.options' => array(
                'cache_dir' => 'D:\\Git\\Gekosale3\\var',
                'generator_cache_class' => 'GekosaleUrlGenerator',
                'matcher_cache_class' => 'GekosaleUrlMatcher',
            ),
            'locales' => array(
                'pl_PL' => 'Polski',
                'en_EN' => 'English',
            ),
            'session.config' => array(
                'db_table' => 'session',
            ),
            'db.config' => array(
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'gekosale3',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ),
            'config_locator.class' => 'Symfony\\Component\\Config\\FileLocator',
            'controller_resolver.class' => 'Gekosale\\Core\\ControllerResolver',
            'event_dispatcher.class' => 'Symfony\\Component\\EventDispatcher\\ContainerAwareEventDispatcher',
            'finder.class' => 'Symfony\\Component\\Finder\\Finder',
            'filesystem.class' => 'Symfony\\Component\\Filesystem\\Filesystem',
            'form_helper.class' => 'Gekosale\\Core\\Form',
            'helper.class' => 'Gekosale\\Core\\Helper',
            'kernel.class' => 'Symfony\\Component\\HttpKernel\\DependencyInjection\\ContainerAwareHttpKernel',
            'translation.class' => 'Gekosale\\Core\\Translation',
            'xajax_manager.class' => 'Gekosale\\Core\\XajaxManager',
            'xajax.class' => 'xajax',
            'database_manager.class' => 'Illuminate\\Database\\Capsule\\Manager',
            'router.class' => 'Symfony\\Component\\Routing\\Router',
            'router.loader.class' => 'Symfony\\Component\\Routing\\Loader\\PhpFileLoader',
            'router.subscriber.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\RouterListener',
            'session.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Session',
            'session.handler.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\Handler\\PdoSessionHandler',
            'session.storage.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\NativeSessionStorage',
        );
    }
}
