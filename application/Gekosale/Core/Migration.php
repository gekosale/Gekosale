<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core;

/**
 * Class Migration
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Migration
{

    /**
     * @var ServiceContainer
     */
    protected $container;

    /**
     * @var string
     */
    protected $migrationClass;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->container = new ServiceContainer();
    }

    /**
     * Shortcut to get Filesystem service
     *
     * @return object
     */
    protected function getFilesystem()
    {
        return $this->container->get('filesystem');
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object Service
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    protected function getDb()
    {
        return $this->container->get('database_manager');
    }

    protected function getFinder()
    {
        return $this->container->get('finder');
    }

    public function check()
    {
        $this->migrationClass = get_class($this);

        $sql  = 'SELECT COUNT(idmigration) AS total FROM migration WHERE migrationclass = :migrationclass';
        $stmt = $this->getDb()->getConnection()->prepare($sql);
        $stmt->bindValue('migrationclass', $this->migrationClass);
        $stmt->execute();
        $rs = $stmt->fetch();

        return $rs['total'];
    }

    public function save()
    {
        $sql  = 'INSERT INTO migration SET migrationclass = :migrationclass';
        $stmt = $this->getDb()->getConnection()->prepare($sql);
        $stmt->bindValue('migrationclass', $this->migrationClass);
        $stmt->execute();
    }

    /**
     * Action needed to update application
     *
     * @return mixed
     */
    abstract function up();

    /**
     * Action needed to downgrade application
     *
     * @return mixed
     */
    abstract function down();
}