<?php

/**
*
* WellCommerce
*
* @copyright   Copyright (c) 2012-2014 WellCommerce
* @author      WellCommerce, info@wellcommerce.pl
*/
namespace Gekosale\Migrate;
use Gekosale\Migrate;

class Migrate_1390133491 extends Migrate
{

    public function up ()
    {
        $this->getDb()->getConnection()->query('
        	CREATE TABLE IF NOT EXISTS `migration` 
        	(
			  `idmigration` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
			  `migrationclass` VARCHAR(255) NOT NULL ,
			  `adddate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			  PRIMARY KEY (`idmigration`) 
        	);
        ');
        
        $files = $this->getFinder()->files()->in(ROOTPATH . DS . 'plugin')->name('*.php')->notContains('Gekosale\Super\Coremigration');
        
        foreach ($files as $file){
            $content = $file->getContents();
            $previousContent = $content;
            
            /*
             *	Replace $this->registry->cache with  App::getContainer()->get('cache')
            */
            $content = str_replace('$this->registry->cache', 'App::getContainer()->get(\'cache\')', $content);
            
            /*
             *	Replace Session:: with App::getContainer()->get('session')->
            */
            $content = str_replace('Session::', 'App::getContainer()->get(\'session\')->', $content);
            
            /*
             *	Replace Translation::get with $this->trans
            */
            $content = str_replace('Translation::get', '$this->trans', $content);
            
            /*
             *	Replace _( with $this->trans(
            */
            $content = str_replace('_(', '$this->trans(', $content);
            
            /*
             *	Replace $this->registry->session-> with App::getContainer()->get('session')->
             */
            $content = str_replace('$this->registry->session->', 'App::getContainer()->get(\'session\')->', $content);
            
            /*
             *	Replace Core::arrayAsString($Categories) with implode(',', $Categories)
            */
            $content = str_replace('Core::arrayAsString($Categories)', 'implode(\',\', $Categories)', $content);
            /*
	         *	Write new file
	         */
            if ($previousContent != $content){
                $this->getFilesystem()->dumpFile($file->getRealpath(), $content);
            }
        }
    }

    public function down ()
    {
    }
}