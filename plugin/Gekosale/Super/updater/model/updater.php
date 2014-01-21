<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: updater.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale;

use sfEvent;

class UpdaterModel extends Component\Model
{
    protected $InstallerFile = NULL;
    protected $packageName = 'Gekosale';
    protected $packageInfo = 'http://www.gekosale.pl/packages';
    protected $packageServer = 'update.gekosale.pl';

    public function parseXML ($file)
    {
        if ($this->InstallerFile !== NULL){
            return $this->InstallerFile;
        }
        try{
            $this->xmlParser = new xmlParser();
            $this->setInstallerFile($this->xmlParser->parseFast($file));
            return $this->getInstallerFile();
        }
        catch (Exception $e){
            throw $e;
        }
    }

    public function flushXML ()
    {
        $this->InstallerFile = NULL;
    }

    public function getInstallerFile ()
    {
        return $this->InstallerFile;
    }

    public function setInstallerFile ($content)
    {
        $this->InstallerFile = $content;
    }

    public function flushInstallerFile ()
    {
        $this->InstallerFile = NULL;
    }

    public function install ($file, $revision, &$_fh, $current)
    {
        $Data = $this->parseXML($file);
        foreach ($Data->install as $queries){
            if ($revision == $current){
                if ($queries->attributes()->version == $current){
                    foreach ($queries->query as $query){
                        try{
                            $this->registry->db->executeUpdate((string) $query);
                        }
                        catch (Exception $e){
                            fwrite($_fh, $e->getMessage() . "\n");
                        }
                    }
                }
            }
            else{
                if ($queries->attributes()->version > $current){
                    foreach ($queries->query as $query){
                        try{
                            $this->registry->db->executeUpdate((string) $query);
                        }
                        catch (Exception $e){
                            fwrite($_fh, $e->getMessage() . "\n");
                        }
                    }
                }
            }
        }
        $this->flushXML();
    }

    public function installFromArray ($files, $revision, $current)
    {
        $_fh = fopen(ROOTPATH . 'logs/pear.log', 'a');
        $this->registry->db->executeQuery('SET foreign_key_checks = 0');
        foreach ($files as $file){
            fwrite($_fh, $file . "\n");
            $this->install($file, $revision, $_fh, $current);
        }
        $this->registry->db->executeQuery('SET foreign_key_checks = 1');
        fclose($_fh);
    }

    public function uninstallFromArray ($files, $revision)
    {
        foreach ($files as $file){
            $this->uninstall($file, $revision);
        }
    }

    public function uninstall ($file, $revision)
    {
        $Data = $this->parseXML($file);
        $_queries = Array();
        foreach ($Data->uninstall as $uninstall){
            if (current($uninstall->attributes()->version) == $revision){
                foreach ($uninstall->query as $query){
                    $_queries[] = (string) $query;
                }
            }
        }
        foreach ($_queries as $query){
            $this->registry->db->executeUpdate($query);
        }
    }

    public function getLastUpdateHistoryByPackage ($packageName)
    {
        $sql = 'SELECT
					idupdatehistory,
					packagename,
					version
				FROM updatehistory
				WHERE packageName = :packageName
				ORDER BY idupdatehistory DESC LIMIT 1';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('packageName', $packageName);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['version'];
        }
    }

    public function addPackageHistory ($Package)
    {
        $sql = 'INSERT INTO updatehistory (packagename, version)
				VALUES (:packagename, :version)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('packagename', $Package['packagename']);
        $stmt->bindValue('version', $Package['version']);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw $e;
        }
    }

    public function doSuccessQueque ($request)
    {
        if ($request['bFinished']){
            $this->clearSerialization(ROOTPATH . DS . 'serialization', false);
            $this->clearCache(ROOTPATH . DS . 'cache', false);
            App::getContainer()->get('session')->setActiveUpdateData(NULL);
            return Array(
                'bCompleted' => true
            );
        }
    }

    public function doLoadQueque ()
    {
        return Array(
            'iTotal' => 4,
            'iCompleted' => 0
        );
    }

    public function doProcessQueque ($request)
    {
        $Data = App::getContainer()->get('session')->getActiveUpdateData();

        $startFrom = intval($request['iStartFrom']);

        if ($startFrom == 0){

            $path = ROOTPATH . 'upload' . DS . $Data['file'];
            if (! is_file($path)){
                @set_time_limit(120);
                $curl = curl_init($Data['url']);
                $fp = fopen($path, 'wb');
                $options = array(
                    CURLOPT_FILE => $fp,
                    CURLOPT_HEADER => 0
                );
                curl_setopt_array($curl, $options);
                curl_exec($curl);
                curl_close($curl);
                fclose($fp);
                chmod($path, 0755);
            }
            return Array(
                'iStartFrom' => 1
            );
        }
        elseif ($startFrom == 1){

            require_once (ROOTPATH . 'lib' . DS . 'zip' . DS . 'zip.php');
            $path = ROOTPATH . 'upload' . DS . $Data['file'];
            $archive = new PclZip($path);
            $list = $archive->extract(PCLZIP_OPT_PATH, ROOTPATH, PCLZIP_OPT_REPLACE_NEWER);
            $this->executeUpdateXml($Data);
            return Array(
                'iStartFrom' => 2
            );
        }
        elseif ($startFrom == 2){

            $file = 'pl_PL.xml';
            App::getModel('language')->importTranslationFromFile($file, 1);

            return Array(
                'iStartFrom' => 3
            );
        }
        elseif ($startFrom == 3){
            $Package['packagename'] = $this->packageName;
            $Package['version'] = $Data['version'];

            $this->addPackageHistory($Package);
            App::getModel('cssgenerator')->createPageSchemeStyleSheetDocument();
            return Array(
                'iStartFrom' => 4,
                'bFinished' => true
            );
        }
    }

    public function executeUpdateXml ($packageData)
    {
        $revision = $packageData['version'];
        $current = $packageData['current'];

        $updateXmlFile = ROOTPATH . 'sql' . DS . 'mysql_update' . DS . 'update.xml';
        if (is_file($updateXmlFile)){
            $Data = $this->parseXML($updateXmlFile);
            foreach ($Data->install as $queries){
                if ($revision == $current){
                    if ($queries->attributes()->version == $current){
                        foreach ($queries->query as $query){
                            try{
                                $this->registry->db->executeUpdate((string) $query);
                            }
                            catch (Exception $e){
                            }
                        }
                    }
                }
                else{
                    if ($queries->attributes()->version > $current){
                        foreach ($queries->query as $query){
                            try{
                                $this->registry->db->executeUpdate((string) $query);
                            }
                            catch (Exception $e){
                            }
                        }
                    }
                }
            }
            $this->flushXML();
        }
    }

    public function deletePackageHistory ($Package)
    {
        $sql = 'DELETE FROM updatehistory WHERE
				packagename = :packagename';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('packagename', $Package['packagename']);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw $e;
        }
    }

    public function clearSerialization ($dir, $DeleteMe)
    {
        if (! $dh = @opendir($dir))
            return;
        while (false !== ($obj = readdir($dh))){
            if ($obj == '.' || $obj == '..')
                continue;
            if (! @unlink($dir . '/' . $obj))
                $this->clearSerialization($dir . '/' . $obj, true);
        }

        closedir($dh);
        if ($DeleteMe){
            @rmdir($dir);
        }
    }

    public function clearCache ($dir, $DeleteMe)
    {
        if (! $dh = @opendir($dir))
            return;
        while (false !== ($obj = readdir($dh))){
            if ($obj == '.' || $obj == '..')
                continue;
            if (! @unlink($dir . '/' . $obj))
                $this->clearCache($dir . '/' . $obj, true);
        }

        closedir($dh);
        if ($DeleteMe){
            @rmdir($dir);
        }
    }
}