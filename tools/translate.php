<?php

namespace Gekosale\Tools;
use \Gekosale\App as App;
use Gekosale\Db;

require_once (ROOTPATH . 'tools' . DS . 'bootstrap.php');
define('GEKOSALE_PATH', ROOTPATH);

class Translate extends \Gekosale\Tools\Tool
{

    public function add ()
    {
        $culture = $this->getParam('culture', true);
        $pluginName = $this->getParam('plugin', true);
        $pluginNs = $this->getParam('ns', false, 'Gekosale');
        $pluginMode = $this->getParam('mode', false, 'Admin');
        
        $pluginDir = ROOTPATH . 'plugin' . DS . $pluginNs . DS . $pluginMode . DS . $pluginName;
        
        if (! is_dir($pluginDir))
            throw new \Exception(sprintf("Bad plugin name, namespace or mode. Folder: %s doesn't exists!", $pluginDir));
        
        if (! is_dir($pluginDir . DS . 'i18n'))
            mkdir($pluginDir . DS . 'i18n');
        
        $destFile = $pluginDir . DS . 'i18n' . DS . $culture . '.po';
        
        if (! file_exists($destFile)){
            file_put_contents($destFile, '#Gettext format file. To install it use: php tool.php translate install');
            
            $this->log('File %s created!', array(
                $destFile
            ));
        }
        else
            $this->log('File %s already exists!', array(
                $destFile
            ));
    }

    /**
	 * Install new translations from *.po files into database
	 */
    public function install ()
    {
        $basePath = ROOTPATH . 'plugin' . DS;
        $oDir = new \RecursiveDirectoryIterator($basePath);
        $culture = $this->getParam('culture', false, 'pl_PL');
        $forceUpdate = $this->getParam('force', false, false);
        
        $langModel = App::getModel('language');
        $languages = $langModel->getLanguageALL();
        $langId = 0;
        
        foreach ($languages as $lang)
            if ($lang['name'] == $culture){
                $langId = $lang['id'];
                break;
            }
        if (! $langId)
            throw new Exception('Wrong culture name - no lang in database: %s', array(
                $langId
            ));
        else
            $this->log('Lang ID: %d', array(
                $langId
            ));
        
        foreach ($oDir as $namespaceDir){
            
            $modes = new \RecursiveDirectoryIterator($namespaceDir);
            
            foreach ($modes as $modeDir){
                try{
                    $plugins = new \RecursiveDirectoryIterator($modeDir);
                    
                    foreach ($plugins as $pluginDir){
                        
                        $i18ndir = $pluginDir . DS . 'i18n';
                        if (is_dir($i18ndir)){
                            $namespaceName = trim(str_replace($basePath, '', $namespaceDir), '/');
                            $modeName = trim(str_replace($namespaceDir, '', $modeDir), '/');
                            $pluginName = trim(str_replace($modeDir, '', $pluginDir), '/');
                            $pkgName = strtolower($namespaceName . '/' . $modeName . '/' . $pluginName);
                            
                            $poFile = $pluginDir . DS . 'i18n' . DS . $culture . '.po';
                            if (is_file($poFile)){
                                
                                $this->log('Using existing PO file: %s', array(
                                    $poFile
                                ));
                                
                                $translations = $this->readPoFile($poFile);
                                
                                foreach ($translations as $key => $v){
                                    
                                    if (! empty($v)){
                                        $this->log('Importing: "%s" => "%s"', array(
                                            $key,
                                            $v
                                        ));
                                        $langModel->updateTranslation($langId, $key, $v, $forceUpdate);
                                    }
                                }
                            }
                        }
                        App::getRegistry()->cache->delete('translations');
                    }
                }
                catch (\Exception $err){
                    $this->log($err->getMessage(), array());
                }
            }
        }
    }

    /**
	 * Application logic goes here
	 */
    public function run ()
    {
        $viewId = $this->getParam('viewId', false, 3);
        $langId = $this->getParam('langId', false, 1);
        $cultureToUpdate = $this->getParam('culture', false, 'pl_PL');
        
        App::getRegistry()->cache->delete('translations');
        \Gekosale\Translation::loadTranslations();
        $trans = App::getRegistry()->cache->load('translations');
        
        $memcache = App::getConfig('memcache');
        if ($memcache['active'] == 1){
            file_put_contents(GEKOSALE_PATH . 'serialization' . DS . 'translations_' . $viewId . '_' . $langId . '.reg', serialize($trans));
        }
        
        if (! is_file($sTranslationsFile = GEKOSALE_PATH . 'serialization' . DS . 'translations_' . $viewId . '_' . $langId . '.reg')){
            $this->log("File: %s doesn't exist!", array(
                $sTranslationsFile
            ));
            exit();
        }
        
        $aLang = array();
        $aFiles = array();
        
        $aXLang = unserialize(file_get_contents($sTranslationsFile));
        
        $this->log('Total translations from %s file: %d', array(
            $sTranslationsFile,
            count($aXLang)
        ));
        
        $oDir = new \RegExIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(GEKOSALE_PATH)), '~.+\.po\z~');
        
        $this->log("Updating translations");
        
        $i = 0;
        foreach ($oDir as $oFile){
            $lang = $this->readPoFile($oFile->getPathName());
            
            foreach ($lang as $key => $val){
                if (! isset($aXLang[$key]) || (empty($aXLang[$key]) && ! empty($val))){
                    ++ $i;
                }
                
                $aXLang[$key] = $val;
            }
        }
        
        if ($i !== 0){
            $this->log('New translations from PO files: %d', array(
                $i
            ));
            file_put_contents($sTranslationsFile, serialize($aXLang));
        }
        
        $pureLangFile = $aXLang;
        
        $this->log("Searching in PHP files");
        
        $oDir = new \RegExIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(GEKOSALE_PATH)), '~.+\.php\z~');
        
        foreach ($oDir as $oFile){
            $sPathName = $oFile->getPathName();
            if (preg_match_all('~\'((TXT|ERR)_[^\'\\\\]+)~', file_get_contents($sPathName), $aData)){
                foreach ($aData[1] as $sLang){
                    $aLang[] = $sLang;
                    $aFiles[$sLang][] = $sPathName;
                }
            }
        }
        
        $this->log("Searching in admin TPL files");
        
        $oDir = new \RegExIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(GEKOSALE_PATH . 'design' . DS)), '~.+\.tpl\z~');
        
        foreach ($oDir as $oFile){
            $sPathName = $oFile->getPathName();
            if (preg_match_all('~\{% trans %\}((TXT|ERR)_.+?)\{% endtrans %\}~', file_get_contents($sPathName), $aData)){
                foreach ($aData[1] as $sLang){
                    $aLang[] = $sLang;
                    $aFiles[$sLang][] = $sPathName;
                }
            }
        }
        
        $oDir = new \RegExIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(GEKOSALE_PATH . 'themes' . DS)), '~.+\.tpl\z~');
        
        $this->log("Searching in TPL files");
        
        foreach ($oDir as $oFile){
            $sPathName = $oFile->getPathName();
            if (preg_match_all('~\{% trans %\}((TXT|ERR)_.+?)\{% endtrans %\}~', file_get_contents($sPathName), $aData)){
                foreach ($aData[1] as $sLang){
                    $aLang[] = $sLang;
                    $aFiles[$sLang][] = $sPathName;
                }
            }
        }
        
        $oDir = new \RegExIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(GEKOSALE_PATH . 'plugin' . DS)), '~.+\.php\z~');
        
        $this->log("Searching in plugin files");
        
        foreach ($oDir as $oFile){
            $sPathName = $oFile->getPathName();
            if (preg_match_all('~\'((TXT|ERR)_[^\'\\\\]+)~', file_get_contents($sPathName), $aData)){
                foreach ($aData[1] as $sLang){
                    $aLang[] = $sLang;
                    $aFiles[$sLang][] = $sPathName;
                }
            }
        }
        $aXLang = array_keys($aXLang);
        sort($aXLang);
        
        $aLang = array_unique($aLang);
        sort($aLang);
        
        $aDiff = array_diff($aLang, $aXLang);
        $perPlugin = array();
        
        foreach ($aDiff as $sLang){
            if (count($aFiles[$sLang])){
                $this->log("$sLang");
                $aFiles[$sLang] = array_unique($aFiles[$sLang]);
                
                $stringUsed = false;
                foreach ($aFiles[$sLang] as $sFile){
                    $this->log("\t%s", array(
                        $sFile
                    ));
                    
                    if (false == $stringUsed){
                        $pluginName = '';
                        $pluginMode = '';
                        $pluginNs = '';
                        
                        $pluginData = null;
                        
                        // admin tpl
                        if (($pos = strpos($sFile, DS . 'design' . DS . 'admin' . DS)) !== FALSE){
                            // throw new \Exception('Please implement me: Add
                            // recogintion of /design/admin templates ');
                            $pos += strlen('/design/');
                            $temp = trim(substr($sFile, $pos), DS);
                            $parts = explode(DS, $temp);
                            
                            while (count($parts) > 3)
                                array_pop($parts);
                            
                            if (count($parts) < 3)
                                throw new \Exception(sprintf('Unsupported file name: %s/ Could not extratct plugin name, mode and namesapce', $sFile));
                            
                            $temp = $parts[1];
                            $parts[1] = ucfirst(strtolower($parts[0]));
                            $parts[0] = ucfirst(strtolower($temp));
                            
                            $pluginData = implode(DS, $parts);
                        }
                        
                        // tpl
                        if (($pos = strpos($sFile, DS . 'themes' . DS)) !== FALSE){
                            //throw new \Exception('Please implement me: Add recogintion of /design/admin templates ');
                            $pos += strlen('/themes/');
                            $temp = trim(substr($sFile, $pos), DS);
                            $parts = explode(DS, $temp);
                            
                            while (count($parts) > 3)
                                array_pop($parts);
                            
                            if (count($parts) < 3)
                                throw new \Exception(sprintf('Unsupported file name: %s/ Could not extratct plugin name, mode and namesapce', $sFile));
                            
                            $temp = $parts[1];
                            $parts[1] = 'Frontend';
                            $parts[0] = 'Gekosale';
                            
                            $pluginData = implode(DS, $parts);
                        }
                        
                        if (($pos = strpos($sFile, DS . 'plugin' . DS)) !== FALSE){ // TODO
                            // add
                            // recogniotion
                            // for
                            // *.tpl
                            // files
                            // for
                            // /admin
                            // and
                            // /themes
                            $endpos = strpos($sFile, 'controller');
                            if ($endpos === FALSE)
                                $endpos = strpos($sFile, 'model');
                            
                            if ($endpos === FALSE)
                                $endpos = strpos($sFile, 'form');
                            
                            $pos += strlen('/plugin/');
                            
                            echo 1;
                            $pluginData = substr($sFile, $pos, $endpos - $pos);
                        }
                        
                        if ($pluginData == null){
                            $this->log('Odd file name: %s', array(
                                $sFile
                            ));
                            
                            // lib or application location
                            $trans = GEKOSALE_PATH . DS . 'plugin' . DS . 'pl_PL.po';
                            
                            $content = '';
                            if (is_file($trans)){
                                $this->log('Using existing PO file: %s', array(
                                    $trans
                                ));
                                $existingTrans = $this->readPoFile($trans);
                                $content = file_get_contents($trans);
                            }
                            
                            if (! isset($existingTrans[$sLang])){
                                $content .= sprintf("msgid \"%s\"\nmsgstr \"\"\n\n", $sLang);
                            }
                            
                            // update REG file
                            if (isset($pureLangFile[$sLang]) && ! empty($pureLangFile[$sLang])){
                                $pureLangFile[$sLang] = '';
                            }
                            
                            file_put_contents($trans, $content);
                            continue;
                        }
                        
                        if (! isset($perPlugin[$pluginData]) || ! is_array($perPlugin[$pluginData]))
                            $perPlugin[$pluginData] = array();
                        
                        $perPlugin[$pluginData][] = $sLang;
                        $stringUsed = true;
                    }
                }
            }
        }
        
        foreach ($perPlugin as $pluginData => $translations){
            $pluginParts = explode(DS, $pluginData);
            $pluginName = $pluginParts[2];
            $pluginMode = $pluginParts[1];
            $pluginNs = $pluginParts[0];
            
            $this->log('Plugin: %s (ns: %s, mode: %s). New translations: %d', array(
                $pluginName,
                $pluginNs,
                $pluginMode,
                count($translations)
            ));
            
            $pluginDir = ROOTPATH . 'plugin' . DS . $pluginNs . DS . $pluginMode . DS . $pluginName;
            if (! is_dir($pluginDir)){
                $pluginDir = ROOTPATH . DS . 'plugin' . DS . 'Gekosale' . DS . 'Admin' . DS . 'translation';
                $this->log('Using replacement plugin dir: %s', array(
                    $pluginDir
                ));
            }
            
            $translationsDir = $pluginDir . DS . 'i18n';
            $translationsFile = $translationsDir . DS . $cultureToUpdate . '.po';
            
            if (! is_dir($translationsDir)){
                $this->log('Making directory %s', array(
                    $translationsDir
                ));
                mkdir($translationsDir);
            }
            
            $existingTrans = array();
            $oldContent = '';
            
            if (file_exists($translationsFile)){
                $this->log('Using existing PO file: %s', array(
                    $translationsFile
                ));
                $existingTrans = $this->readPoFile($translationsFile);
                $oldContent = file_get_contents($translationsFile);
            }
            
            $outputString = '';
            foreach ($translations as $newKey){
                
                if (! isset($existingTrans[$newKey])){
                    $outputString .= sprintf("msgid \"%s\"\nmsgstr \"\"\n\n", $newKey);
                }
                
                // update REG file
                if (isset($pureLangFile[$newKey]) && ! empty($pureLangFile[$newKey])){
                    $pureLangFile[$newKey] = '';
                }
            }
            
            $oldContent .= $outputString;
            $this->log('File %s written', array(
                $translationsFile
            ));
            file_put_contents($translationsFile, $oldContent);
        }
        
        // cleanup
        $this->log("Cleanup");
        $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(GEKOSALE_PATH));
        
        $aLang = array();
        $aFiles = array();
        
        $this->log("Searching in PHP files");
        
        $oDir = new \RegExIterator($dir, '~.+\.php\z~');
        
        foreach ($oDir as $oFile){
            $sPathName = $oFile->getPathName();
            if (preg_match_all('~\'((TXT|ERR)_[^\'\\\\"]+)~', file_get_contents($sPathName), $aData)){
                foreach ($aData[1] as $sLang){
                    $aLang[] = $sLang;
                }
            }
        }
        
        $this->log("Searching in admin TPL files");
        
        $oDir = new \RegExIterator($dir, '~.+\.tpl\z~');
        
        foreach ($oDir as $oFile){
            $sPathName = $oFile->getPathName();
            if (preg_match_all('~\{% trans %\}((TXT|ERR)_.+?)\{% endtrans %\}~', file_get_contents($sPathName), $aData)){
                foreach ($aData[1] as $sLang){
                    $aLang[] = $sLang;
                }
            }
        }
        
        // config/admin_menu.xml
        if (preg_match_all('~<name>((TXT|ERR)_.+?)</name>~', file_get_contents(GEKOSALE_PATH . DS . 'config' . DS . 'admin_menu.xml'), $aData)){
            foreach ($aData[1] as $sLang){
                $aLang[] = $sLang;
            }
        }
        
        $sql = "SELECT description FROM controller WHERE description LIKE 'TXT\_%' ORDER BY description ASC";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $aLang[] = $rs['description'];
        }
        
        $aLang = array_unique($aLang);
        
        $tmplang = $pureLangFile;
        
        $diff = array_diff($aXLang, $aLang);
        
        $this->log('%d of %d unused translations', array(
            count($diff),
            count($tmplang)
        ));
        echo implode("\n", $diff) . "\n";
        
        foreach ($diff as $lang){
            unset($tmplang[$lang]);
        }
        
        App::getRegistry()->cache->save('translations', $tmplang);
        
        $this->log($sTranslationsFile . ' rebuilt');
    }

    /**
	 * Read PO file to array
	 */
    protected function readPoFile ($filename)
    {
        $translations = array();
        $po = file($filename);
        $current = null;
        foreach ($po as $line){
            if (substr($line, 0, 5) == 'msgid'){
                $current = trim(substr(trim(substr($line, 5)), 1, - 1));
            }
            if (substr($line, 0, 6) == 'msgstr'){
                $translations[$current] = trim(substr(trim(substr($line, 6)), 1, - 1));
            }
        }
        
        return $translations;
    }

    public function xml ()
    {
        $viewId = $this->getParam('viewId', false, 3);
        $langId = $this->getParam('langId', false, 1);
        
        if (! is_file($sTranslationsFile = GEKOSALE_PATH . 'serialization' . DS . 'translations_' . $viewId . '_' . $langId . '.reg')){
            $this->log("File: %s doesn't exist!", array(
                $sTranslationsFile
            ));
            exit();
        }
        
        $langs = unserialize(file_get_contents($sTranslationsFile));
        
        ksort($langs);
        
        // Creating xml file
        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = TRUE;
        
        $rows = $xml->createElement('rows');
        
        foreach ($langs as $key => $val){
            
            $row = $xml->createElement('row');
            
            $name = $xml->createElement('field', $key);
            $name->setAttribute('name', 'name');
            $row->appendChild($name);
            
            $trans = $xml->createElement('field', $val);
            $trans->setAttribute('name', 'translation');
            $row->appendChild($trans);
            
            $rows->appendChild($row);
        }
        
        $xml->appendChild($rows);
        
        $xml->save(GEKOSALE_PATH . 'upload' . DS . 'pl_PL.xml');
        
        // en_EN
        $names = array();
        $trans = array();
        
        $xml = new \DOMDocument('1.0', 'urf-8');
        $xml->load(GEKOSALE_PATH . 'upload' . DS . 'en_EN.xml');
        
        $i = 0;
        foreach ($xml->getelementsByTagName('row') as $row){
            foreach ($row->getElementsByTagName('field') as $field){
                if ($field->getAttribute('name') === 'name'){
                    $names[$i] = $field->nodeValue;
                }
                else{
                    $trans[$i] = strtr($field->nodeValue, array(
                        "\r\n" => ' ',
                        "\n" => ' ',
                        "\r" => ' '
                    ));
                }
            }
            
            if (! isset($trans[$i])){
                $trans[$i] = '';
            }
            
            ++ $i;
        }
        
        $en = array();
        $tmp = array_combine($names, $trans);
        
        foreach ($langs as $key => $val){
            $val = '';
            if (isset($tmp[$key])){
                $val = $tmp[$key];
            }
            
            $en[$key] = $val;
        }
        
        ksort($en);
        
        // Creating xml file
        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = TRUE;
        
        $rows = $xml->createElement('rows');
        
        foreach ($en as $key => $val){
            
            //echo $key . "\n";
            $row = $xml->createElement('row');
            
            $name = $xml->createElement('field', $key);
            $name->setAttribute('name', 'name');
            $row->appendChild($name);
            
            $trans = $xml->createElement('field', $val);
            $trans->setAttribute('name', 'translation');
            $row->appendChild($trans);
            
            $rows->appendChild($row);
        }
        
        $xml->appendChild($rows);
        
        $xml->save(GEKOSALE_PATH . 'upload' . DS . 'en_EN.xml');
    }
}