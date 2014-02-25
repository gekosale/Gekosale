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
            'availability.subscriber' => 'getAvailability_SubscriberService',
            'cache' => 'getCacheService',
            'cache_storage' => 'getCacheStorageService',
            'config_locator' => 'getConfigLocatorService',
            'controller_resolver' => 'getControllerResolverService',
            'currency.datagrid' => 'getCurrency_DatagridService',
            'currency.form' => 'getCurrency_FormService',
            'currency.repository' => 'getCurrency_RepositoryService',
            'currency.subscriber' => 'getCurrency_SubscriberService',
            'database_manager' => 'getDatabaseManagerService',
            'event_dispatcher' => 'getEventDispatcherService',
            'filesystem' => 'getFilesystemService',
            'finder' => 'getFinderService',
            'helper' => 'getHelperService',
            'kernel' => 'getKernelService',
            'request' => 'getRequestService',
            'router' => 'getRouterService',
            'router.loader' => 'getRouter_LoaderService',
            'router.subscriber' => 'getRouter_SubscriberService',
            'session' => 'getSessionService',
            'session.handler' => 'getSession_HandlerService',
            'session.storage' => 'getSession_StorageService',
            'template.subscriber' => 'getTemplate_SubscriberService',
            'translation' => 'getTranslationService',
            'twig' => 'getTwigService',
            'twig.extension.asset' => 'getTwig_Extension_AssetService',
            'twig.extension.box' => 'getTwig_Extension_BoxService',
            'twig.extension.datagrid' => 'getTwig_Extension_DatagridService',
            'twig.extension.debug' => 'getTwig_Extension_DebugService',
            'twig.extension.form' => 'getTwig_Extension_FormService',
            'twig.extension.intl' => 'getTwig_Extension_IntlService',
            'twig.extension.routing' => 'getTwig_Extension_RoutingService',
            'twig.extension.translation' => 'getTwig_Extension_TranslationService',
            'twig.loader.admin' => 'getTwig_Loader_AdminService',
            'twig.loader.front' => 'getTwig_Loader_FrontService',
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
     * Gets the 'availability.subscriber' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Plugin\Availability\Event\AvailabilitySubscriber A Gekosale\Plugin\Availability\Event\AvailabilitySubscriber instance.
     */
    protected function getAvailability_SubscriberService()
    {
        return $this->services['availability.subscriber'] = new \Gekosale\Plugin\Availability\Event\AvailabilitySubscriber();
    }

    /**
     * Gets the 'cache' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Resolver\Controller A Gekosale\Core\Resolver\Controller instance.
     */
    protected function getCacheService()
    {
        return $this->services['cache'] = new \Gekosale\Core\Resolver\Controller($this->get('cache_storage'));
    }

    /**
     * Gets the 'cache_storage' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Cache\Storage\File A Gekosale\Core\Cache\Storage\File instance.
     */
    protected function getCacheStorageService()
    {
        return $this->services['cache_storage'] = new \Gekosale\Core\Cache\Storage\File($this, 'D:\\Git\\Gekosale3\\var/cache', 'reg');
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
     * Gets the 'controller_resolver' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Resolver\Controller A Gekosale\Core\Resolver\Controller instance.
     */
    protected function getControllerResolverService()
    {
        return $this->services['controller_resolver'] = new \Gekosale\Core\Resolver\Controller($this);
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
        $instance->addSubscriberService('template.subscriber', 'Gekosale\\Core\\Template\\Subscriber\\Template');
        $instance->addSubscriberService('router.subscriber', 'Symfony\\Component\\HttpKernel\\EventListener\\RouterListener');
        $instance->addSubscriberService('template.subscriber', 'Gekosale\\Core\\Template\\Subscriber\\Template');
        $instance->addSubscriberService('admin_menu.subscriber', 'Gekosale\\Plugin\\AdminMenu\\Event\\AdminMenuEventSubscriber');
        $instance->addSubscriberService('availability.subscriber', 'Gekosale\\Plugin\\Availability\\Event\\AvailabilitySubscriber');
        $instance->addSubscriberService('currency.subscriber', 'Gekosale\\Plugin\\Currency\\Event\\CurrencyEventSubscriber');

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
     * Gets the 'helper' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Helper A Gekosale\Core\Helper instance.
     */
    protected function getHelperService()
    {
        return $this->services['helper'] = new \Gekosale\Core\Helper($this);
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
        return $this->services['twig.extension.routing'] = new \Gekosale\Core\Template\Extension\Routing($this->get('router'));
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
            'cache.class' => 'Gekosale\\Core\\Resolver\\Controller',
            'cache_storage.class' => 'Gekosale\\Core\\Cache\\Storage\\File',
            'config_locator.class' => 'Symfony\\Component\\Config\\FileLocator',
            'controller_resolver.class' => 'Gekosale\\Core\\Resolver\\Controller',
            'event_dispatcher.class' => 'Symfony\\Component\\EventDispatcher\\ContainerAwareEventDispatcher',
            'finder.class' => 'Symfony\\Component\\Finder\\Finder',
            'filesystem.class' => 'Symfony\\Component\\Filesystem\\Filesystem',
            'helper.class' => 'Gekosale\\Core\\Helper',
            'kernel.class' => 'Symfony\\Component\\HttpKernel\\DependencyInjection\\ContainerAwareHttpKernel',
            'translation.class' => 'Gekosale\\Core\\Translation',
            'xajax_manager.class' => 'Gekosale\\Core\\XajaxManager',
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
