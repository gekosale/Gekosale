<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core\Console\Command\Plugin;

use Gekosale\Core\Console\Command\AbstractCommand;

use Symfony\Component\Console,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Add
 *
 * @package Gekosale\Core\Console\Command\Migration
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Add extends AbstractCommand
{
    protected $namespace;
    protected $plugin;
    protected $actions;
    protected $path;

    protected function configure()
    {
        $this->setName('plugin:add');

        $this->setDescription('Creates plugin skeleton');

        $this->setDefinition(array(
                new InputArgument('namespace', InputArgument::REQUIRED, 'Plugin namespace'),
                new InputArgument('plugin', InputArgument::REQUIRED, 'Plugin name'),
                new InputArgument('actions', InputArgument::REQUIRED, 'Controller actions'),
            )
        );

        $this->setHelp(sprintf('%Creates plugin skeleton.%s', PHP_EOL, PHP_EOL));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');

        $namespace = $dialog->ask(
            $output,
            'Please enter the namespace of the plugin [Gekosale]: ',
            'Gekosale'
        );

        $input->setArgument('namespace', $namespace);

        $plugin = $dialog->ask(
            $output,
            'Please enter the name of the plugin: ',
            ''
        );

        $input->setArgument('plugin', $plugin);

        $actions = $dialog->ask(
            $output,
            'Please enter the actions available in controller, separated by comma [indexAction,addAction,editAction]: ',
            'indexAction,addAction,editAction'
        );

        $input->setArgument('actions', $actions);
    }

    /**
     * Executes migration:add command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $args            = $input->getArguments();
        $this->plugin    = ucfirst($args['plugin']);
        $this->namespace = ucfirst($args['namespace']);
        $this->actions   = explode(',', $args['actions']);
        $this->path      = $this->getPluginDirectory($this->namespace, $this->plugin);

        $this->createPluginDirectories();

        $this->writeServicesFile();

        $this->writeRoutingFile();

//        $fileContent = $this->startClass($class);
//        $fileContent .= $this->addClassMethods();
//        $fileContent .= $this->endClass();
//
//        $this->getFilesystem()->dumpFile($this->getMigrationClassesPath() . DS . $class . '.php', $fileContent);

    }

    private function writeServicesFile()
    {
        $serviceBaseName = strtolower($this->plugin);
        $namespace       = $this->namespace;
        $plugin          = $this->plugin;

        $content
            = <<<EOF
<?xml version="1.0" encoding="UTF-8" ?>
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service id="{$serviceBaseName}.repository'" class="{$this->namespace}\\Plugin\\{$this->plugin}\\Repository\\{$this->plugin}Repository">
            <call method="setContainer">
                <argument type="service" id="service_container"/>
            </call>
        </service>

        <service id="{$serviceBaseName}.datagrid" class="{$this->namespace}\\Plugin\\{$this->plugin}\\DataGrid\\{$this->plugin}DataGrid">
            <call method="setRepository">
                <argument type="service" id="{$serviceBaseName}.repository"/>
            </call>
            <call method="setContainer">
                <argument type="service" id="service_container"/>
            </call>
        </service>

        <service id="{$serviceBaseName}.form" class="{$this->namespace}\\Plugin\\{$this->plugin}\\Form\\{$this->plugin}Form">
            <call method="setContainer">
                <argument type="service" id="service_container"/>
            </call>
        </service>

        <service id="{$serviceBaseName}.subscriber" class="{$this->namespace}\\Plugin\\{$this->plugin}\\Event\\{$this->plugin}EventSubscriber">
            <tag name="kernel.event_subscriber"/>
        </service>
    </services>
</container>
EOF;
        $this->getFilesystem()->dumpFile($this->path . DS . 'config' . DS . 'services.xml', $content);
    }

    private function writeRoutingFile()
    {
        $serviceBaseName = strtolower($this->plugin);
        $namespace       = $this->namespace;
        $plugin          = $this->plugin;
        $controller      = 'Gekosale\\Plugin\\' . $plugin . '\\Controller\\Admin\\' . $plugin . 'Controller';

        $content
            = <<<EOF
<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

\$collection = new RouteCollection();

\$controller = '$controller';

\$collection->add('admin.availability.index', new Route('/index', array(
    'controller' => \$controller,
    'mode'       => 'admin',
    'action'     => 'indexAction'
)));

\$collection->add('admin.availability.add', new Route('/add', array(
    'controller' => \$controller,
    'mode'       => 'admin',
    'action'     => 'addAction'
)));

\$collection->add('admin.availability.edit', new Route('/edit/{id}', array(
    'controller' => \$controller,
    'mode'       => 'admin',
    'action'     => 'editAction',
    'id'         => null
)));

\$collection->addPrefix('/admin/availability');

return \$collection;

EOF;
        $this->getFilesystem()->dumpFile($this->path . DS . 'config' . DS . 'routing.php', $content);
    }

    /**
     * Creates all required plugin directories
     */
    protected function createPluginDirectories()
    {
        $directories = [
            'config',
            'Controller' . DS . 'Admin',
            'Controller' . DS . 'Frontend',
            'DataGrid',
            'Event',
            'Extension',
            'Form',
            'Repository'
        ];

        foreach ($directories as $directory) {
            $this->createDirectory($this->path . $directory);
        }
    }

    private function startClass($class)
    {
        $baseClass     = 'Migration';
        $namespaceLine = "namespace Gekosale\\Core\\Migration;\n";


        return <<<EOF
<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
$namespaceLine
use Gekosale\Core\Migration;

/**
 * $class
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class $class extends $baseClass
{
EOF;
    }

    private
    function endClass()
    {
        return <<<EOF

}
EOF;
    }

    public
    function addClassMethods()
    {
        return <<<EOF

    public function up()
    {

    }

    public function down()
    {

    }
EOF;
    }
}