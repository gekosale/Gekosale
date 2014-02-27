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
namespace Gekosale\Core\Console\Command\Migration;

use Gekosale\Core\Console\Command\AbstractCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Add
 *
 * @package Gekosale\Core\Console\Command\Migration
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Add extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('migration:add');

        $this->setDescription('Adds new migration class');

        $this->setHelp(sprintf('%Adds new migration class.%s', PHP_EOL, PHP_EOL));
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
        $class       = 'Migration' . time();
        $fileContent = $this->startClass($class);
        $fileContent .= $this->addClassMethods();
        $fileContent .= $this->endClass();

        $this->getFilesystem()->dumpFile($this->getMigrationClassesPath() . DS . $class . '.php', $fileContent);

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

    private function endClass()
    {
        return <<<EOF

}
EOF;
    }

    public function addClassMethods()
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