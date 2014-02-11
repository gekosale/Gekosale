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
            'availability.subscriber' => 'getAvailability_SubscriberService',
            'cache' => 'getCacheService',
            'cache.storage' => 'getCache_StorageService',
            'config.locator' => 'getConfig_LocatorService',
            'controller_resolver' => 'getControllerResolverService',
            'currency.form' => 'getCurrency_FormService',
            'currency.repository' => 'getCurrency_RepositoryService',
            'currency.subscriber' => 'getCurrency_SubscriberService',
            'database.manager' => 'getDatabase_ManagerService',
            'event_dispatcher' => 'getEventDispatcherService',
            'filesystem' => 'getFilesystemService',
            'finder' => 'getFinderService',
            'helper' => 'getHelperService',
            'kernel' => 'getKernelService',
            'request' => 'getRequestService',
            'router' => 'getRouterService',
            'router.loader' => 'getRouter_LoaderService',
            'session' => 'getSessionService',
            'session.handler' => 'getSession_HandlerService',
            'session.storage' => 'getSession_StorageService',
            'template.subscriber' => 'getTemplate_SubscriberService',
            'translation' => 'getTranslationService',
            'twig' => 'getTwigService',
            'twig.extension.asset' => 'getTwig_Extension_AssetService',
            'twig.extension.box' => 'getTwig_Extension_BoxService',
            'twig.extension.form' => 'getTwig_Extension_FormService',
            'twig.extension.intl' => 'getTwig_Extension_IntlService',
            'twig.extension.routing' => 'getTwig_Extension_RoutingService',
            'twig.extension.translation' => 'getTwig_Extension_TranslationService',
            'twig.loader.admin' => 'getTwig_Loader_AdminService',
            'twig.loader.front' => 'getTwig_Loader_FrontService',
        );

        $this->aliases = array();
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
     * @return Gekosale\Core\Cache A Gekosale\Core\Cache instance.
     */
    protected function getCacheService()
    {
        return $this->services['cache'] = new \Gekosale\Core\Cache($this->get('cache.storage'));
    }

    /**
     * Gets the 'cache.storage' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Gekosale\Core\Cache\Storage\File A Gekosale\Core\Cache\Storage\File instance.
     */
    protected function getCache_StorageService()
    {
        return $this->services['cache.storage'] = new \Gekosale\Core\Cache\Storage\File($this, 'D:\\Git\\Gekosale3\\var/cache', 'reg');
    }

    /**
     * Gets the 'config.locator' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Config\FileLocator A Symfony\Component\Config\FileLocator instance.
     */
    protected function getConfig_LocatorService()
    {
        return $this->services['config.locator'] = new \Symfony\Component\Config\FileLocator('D:\\Git\\Gekosale3\\config');
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
        $instance->init();

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
     * Gets the 'database.manager' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Illuminate\Database\Capsule\Manager A Illuminate\Database\Capsule\Manager instance.
     */
    protected function getDatabase_ManagerService()
    {
        $this->services['database.manager'] = $instance = new \Illuminate\Database\Capsule\Manager();

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
     * @return Gekosale\Core\Event A Gekosale\Core\Event instance.
     */
    protected function getEventDispatcherService()
    {
        $this->services['event_dispatcher'] = $instance = new \Gekosale\Core\Event($this);

        $instance->addSubscribers();
        $instance->addSubscriberService('template.subscriber', 'Gekosale\\Core\\Template\\Subscriber\\Template');

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
        return $this->services['router.loader'] = new \Symfony\Component\Routing\Loader\PhpFileLoader($this->get('config.locator'));
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
        return $this->services['session.handler'] = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler($this->get("database.manager")->getConnection()->getPdo(), array('db_table' => 'session'));
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
        $this->services['twig'] = $instance = new \Twig_Environment($this->get('twig.loader.front'), array('cache' => 'D:\\Git\\Gekosale3\\var/cache', 'auto_reload' => true, 'autoescape' => true));

        $instance->addExtension($this->get('twig.extension.translation'));
        $instance->addExtension($this->get('twig.extension.routing'));
        $instance->addExtension($this->get('twig.extension.intl'));
        $instance->addExtension($this->get('twig.extension.box'));
        $instance->addExtension($this->get('twig.extension.form'));
        $instance->addExtension($this->get('twig.extension.asset'));

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
        );
    }
}
