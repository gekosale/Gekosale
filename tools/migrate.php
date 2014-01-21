<?php

namespace Gekosale\Tools;
use \Gekosale\App as App;

require_once (ROOTPATH . 'tools' . DS . 'bootstrap.php');

class Migrate extends \Gekosale\Tools\Tool
{

    public function add ()
    {
        $pluginName = $this->getParam('plugin', true);
        $pluginNs = $this->getParam('ns', false, 'Gekosale');
        $pluginMode = $this->getParam('mode', false, 'Admin');
        
        $basePath = ROOTPATH . DS . 'plugin' . DS . $pluginNs . DS . $pluginMode . DS . $pluginName;
        
        if (! is_dir($basePath))
            throw new \Exception(sprintf("Wrong plugin name or namespace or mode - folder %s doesn't exists!", $basePath));
        
        $basePath .= DS . 'migration';
        if (! is_dir($basePath))
            mkdir($basePath);
        
        $maxVer = 0;
        $handle = opendir($basePath);
        while ($migration = readdir($handle)){
            $migr_data = explode('_', str_replace('.php', '', $migration));
            if (count($migr_data) == 2){
                if (($intVer = intval($migr_data[1])) > $maxVer)
                    $maxVer = $intVer;
            }
        }
        
        $this->rcopy(ROOTPATH . DS . 'tools' . DS . 'data' . DS . 'migration_template' . DS . 'migrate_{VER}.php', $basePath . DS . sprintf('migrate_%d.php', $intVer + 1), array(
            'PLUGIN_NAME' => ucfirst(strtolower($pluginName)),
            'PLUGIN_NS' => ucfirst(strtolower($pluginNs)),
            'PLUGIN_MODE' => ucfirst(strtolower($pluginMode)),
            'VER' => $intVer + 1
        ));
    }

    /**
	 * Application logic goes here
	 */
    public function run ()
    {
        $updater = App::getModel('admin/updater/updater');
        $basePath = ROOTPATH . 'plugin' . DS;
        $oDir = new \RecursiveDirectoryIterator($basePath);
        
        foreach ($oDir as $namespaceDir){
            
            $modes = new \RecursiveDirectoryIterator($namespaceDir);
            
            foreach ($modes as $modeDir){
                try{
                    $plugins = new \RecursiveDirectoryIterator($modeDir);
                    
                    foreach ($plugins as $pluginDir){
                        
                        $migrationsDir = $pluginDir . DS . 'migration' . DS;
                        if (is_dir($migrationsDir)){
                            
                            $namespaceName = trim(str_replace($basePath, '', $namespaceDir), DS);
                            $modeName = trim(str_replace($namespaceDir, '', $modeDir), DS);
                            $pluginName = trim(str_replace($modeDir, '', $pluginDir), DS);
                            $pkgName = ucfirst(strtolower($namespaceName)) . '_' . ucfirst(strtolower($pluginName));
                            
                            $currentVersion = intval(max($updater->getLastUpdateHistoryByPackage($pkgName), 0));
                            
                            $source = glob($migrationsDir . 'migrate_*.php');
                            $migrations = Array();
                            foreach ($source as $sourceFile){
                                preg_match('(migrate_(?<version>[\d+]{1,9}?).php)', $sourceFile, $matches);
                                $migrations[$matches['version']] = $sourceFile;
                            }
                            ksort($migrations);
                            
                            $updCount = 0;
                            foreach ($migrations as $migrationFile){
                                $mgrParts = preg_split('/\_/', str_replace('.php', '', $migrationFile));
                                $mgrVersion = end($mgrParts);
                                
                                if ($currentVersion < $mgrVersion){
                                    $this->log('Updating: %s  %d -> %d', array(
                                        $pkgName,
                                        $currentVersion,
                                        $mgrVersion
                                    ));
                                    
                                    try{
                                        $className = sprintf('%s\%s\%s\Migrate_%d', ucfirst($namespaceName), ucfirst($modeName), ucfirst($pluginName), $mgrVersion);
                                        require_once ($migrationFile);
                                        $migrationObj = new $className();
                                        
                                        if ($migrationObj instanceof \Gekosale\Component\Migration){
                                            $migrationObj->up();
                                            
                                            $updater->addPackageHistory(array(
                                                'version' => $mgrVersion,
                                                'packagename' => $pkgName
                                            ));
                                            
                                            $updCount ++;
                                        }
                                        else
                                            throw new \Exception('Invalid Migration Type. Migrations should implement \Gekosale\Admin\Migration');
                                    }
                                    catch (\Exception $err){
                                        
                                        $migrationObj->down();
                                        $this->log('Breaking! Error updating  %s  %d -> %d: %s', array(
                                            $pkgName,
                                            $currentVersion,
                                            $mgrVersion,
                                            $err->getMessage()
                                        ));
                                        break;
                                    }
                                    
                                    $currentVersion = $mgrVersion;
                                }
                            }
                            
                            $this->log('Update done. Applied: %d migrations.', array(
                                $updCount
                            ));
                        }
                    }
                }
                catch (\UnexpectedValueException $err){
                    continue;
                }
            }
        }
        
        // Remove classmap
        @unlink(ROOTPATH . 'serialization' . DS . 'classesmap.reg');
        foreach (glob(ROOTPATH . 'serialization' . DS . '*') as $key => $fn){
            @unlink($fn);
        }
        // Remove cache/*.*
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'cache/'), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file){
            if (in_array($file->getBasename(), array(
                '.',
                '..'
            ))){
                continue;
            }
            
            if ($file->isDir()){
                @rmdir($file->getPathname());
            }
            
            @unlink($file->getPathname());
        }
        
        exit(0);
    }
}