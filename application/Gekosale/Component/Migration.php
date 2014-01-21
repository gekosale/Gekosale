<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: model.class.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Component;

abstract class Migration extends \Gekosale\Component
{

    /**
	 * Apply changes
	 */
    public abstract function up ();

    /**
	 * Revert changes
	 */
    public abstract function down ();

    public function getName ()
    {
        $classPath = explode('\\', get_class($this));
        return strtolower(end($classPath));
    }

    protected function getDb ()
    {
        return \Gekosale\Db::getInstance();
    }

    protected function getRegistry ()
    {
        return \Gekosale\App::getRegistry();
    }

    protected function getCore ()
    {
        return $this->getRegistry()->core;
    }

    /**
	 * Bind to specified event
	 *
	 * @param string $eventName
	 *        	event name
	 * @param string $model        	
	 * @param string $method        	
	 * @param string $module        	
	 * @param string $hierarchy        	
	 */
    protected function bindEvent ($eventName, $model, $method, $module, $hierarchy = 0)
    {
        $sql = 'INSERT INTO event (name, model, method, module, hierarchy)
				VALUES (:name, :model, :method, :module, :hierarchy)';
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue('name', $eventName);
        $stmt->bindValue('model', $model);
        $stmt->bindValue('method', $method);
        $stmt->bindValue('module', $module);
        $stmt->bindValue('hierarchy', $hierarchy);
        
        try{
            $stmt->execute();
        }
        catch (\Exception $e){
            throw $e;
        }
    }

    /**
	 * Revert event binding
	 *
	 * @param string $eventName        	
	 * @param string $model        	
	 * @param string $method        	
	 * @param string $module        	
	 * @throws Exception
	 */
    protected function unbindEvent ($eventName, $model, $method, $module)
    {
        $sql = 'DELETE FROM event WHERE name = :name AND model = :model AND method = :method AND module = :module';
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue('name', $eventName);
        $stmt->bindValue('model', $model);
        $stmt->bindValue('method', $method);
        $stmt->bindValue('module', $module);
        
        try{
            $stmt->execute();
        }
        catch (\Exception $e){
            throw $e;
        }
    }

    /**
	 * Execute SQL query and return results
	 *
	 * @param string $query        	
	 * @param array $params        	
	 * @throws Exception
	 * @return Statement
	 */
    protected function execSql ($query, array $params)
    {
        $stmt = $this->getDb()->prepare($query);
        
        foreach ($params as $key => $value){
            $stmt->bindValue($key, $value);
        }
        
        try{
            $stmt->execute();
            
            return $stmt;
        }
        catch (\Exception $e){
            throw $e;
        }
    }

    /**
	 * Remove controller descriptor from DB
	 *
	 * @param string $name        	
	 * @param int $mode        	
	 * @throws Exception
	 */
    protected function uninstallController ($name, $mode = 1)
    {
        $sql = 'DELETE FROM controller WHERE name = :name AND mode = :mode';
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue('name', $name);
        $stmt->bindValue('mode', $mode);
        
        try{
            $stmt->execute();
        }
        catch (\Exception $e){
            throw $e;
        }
    }

    /**
	 *
	 * @param string $moduleName        	
	 * @return array
	 */
    protected function getModuleSettings ($moduleName)
    {
        return $this->getCore()->loadModuleSettings('wizard');
    }

    /**
	 *
	 * @param string $moduleName        	
	 * @param array $settings        	
	 */
    protected function saveModuleSettings ($moduleName, array $settings)
    {
        $this->getCore()->saveModuleSettings($moduleName, $settings);
    }

    /**
	 * In Geko controller have to be setted up in database
	 */
    protected function installController ($name, $description, $mode = 1, $enable = 1, $version = 1)
    {
        $sql = 'INSERT INTO controller (name, version, description, enable, adddate, mode)
				VALUES (:name, :version, :description, :enable, :adddate, :mode)
				ON DUPLICATE KEY UPDATE version = :version';
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue('name', $name);
        $stmt->bindValue('version', $version);
        $stmt->bindValue('description', $description);
        $stmt->bindValue('enable', $enable);
        $stmt->bindValue('mode', $mode);
        $stmt->bindValue('adddate', date('Y-m-d H:i:s'));
        
        try{
            $stmt->execute();
        }
        catch (\Exception $e){
            throw $e;
        }
        
        if ($mode == 1){
            $sql = 'INSERT INTO `right` (controllerid, groupid, permission, adddate) values ((SELECT idcontroller FROM controller WHERE name = :name AND mode = :mode), 1, 127, NOW())';
            $stmt = $this->getDb()->prepare($sql);
            $stmt->bindValue('name', $name);
            $stmt->bindValue('mode', $mode);
            try{
                $stmt->execute();
            }
            catch (\Exception $e){
                throw $e;
            }
        }
    }

    public function getVersion ()
    {
        $classPath = explode('\\', get_class($this));
        return str_replace('migrate_', '', strtolower(end($classPath)));
    }
}