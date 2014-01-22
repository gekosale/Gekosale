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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: pagescheme.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;
use PclZip;

class ImportModel extends Component\Model
{

    protected $importData = Array();

    protected $pageSchemeId = NULL;

    protected $boxMapping = NULL;

    public function check ($file)
    {
        $file = ROOTPATH . 'themes' . DS . $file;
        require_once (ROOTPATH . 'lib' . DS . 'zip' . DS . 'zip.php');
        $archive = new PclZip($file);
        return $archive->listContent();
    }

    public function importPagescheme ($file)
    {
        $file = ROOTPATH . 'themes' . DS . $file;
        require_once (ROOTPATH . 'lib' . DS . 'zip' . DS . 'zip.php');
        $archive = new PclZip($file);
        $this->targetFolder = 'temp' . date('YmdHis');
        $archive->extract(PCLZIP_OPT_PATH, ROOTPATH . 'themes' . DS . $this->targetFolder);
        
        $info = $this->getMainInfo();
        
        $seo = Core::clearUTF(trim($info['name']));
        $seo = preg_replace('/[^A-Za-z0-9\-\s\s+]/', '', $seo);
        $seo = Core::clearSeoUTF($seo);
        
        $this->templateFolder = str_replace('/', '', strtolower($seo));
        
        if (is_dir(ROOTPATH . 'themes' . DS . $this->templateFolder)){
            $this->templateFolder = $this->templateFolder . date('YmdHis');
            $info['name'] = $info['name'] . ' (Kopia ' . date('Y-m-d H:i:s') . ')';
        }
        rename(ROOTPATH . 'themes' . DS . $this->targetFolder, ROOTPATH . 'themes' . DS . $this->templateFolder);
        
        $this->getImportData();
        
        Db::getInstance()->beginTransaction();
        if (! empty($this->importData)){
            $this->pageSchemeId = App::getModel('pagescheme')->addPageScheme(Array(
                'name' => $info['name'],
                'templatefolder' => $this->templateFolder
            ));
            $this->savePageScheme($this->importData['pagescheme'], $this->pageSchemeId);
            $this->saveBoxes($this->importData['boxes'], $this->pageSchemeId);
            $this->saveSubpages($this->importData['layouts'], $this->pageSchemeId);
        }
        Db::getInstance()->commit();
        
        $this->clearCache(ROOTPATH . DS . 'serialization', false);
        $this->clearCache(ROOTPATH . DS . 'cache', false);
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

    public function getMainInfo ()
    {
        $this->xmlParser = new XmlParser();
        $this->xmlParser->parseFast(ROOTPATH . 'themes/' . $this->targetFolder . '/info/info.xml');
        $info = $this->xmlParser->getValue('template', true);
        return $info;
    }

    public function getImportData ()
    {
        if (is_file(ROOTPATH . 'themes' . DS . $this->templateFolder . DS . 'settings' . DS . 'export.json')){
            $this->importData = json_decode(file_get_contents(ROOTPATH . 'themes' . DS . $this->templateFolder . DS . 'settings' . DS . 'export.json'), true);
        }
    }

    public function savePageScheme ($Data, $id)
    {
        foreach ($Data as $selector => $attribute){
            
            foreach ($attribute as $attribute => $values){
                $sql = 'INSERT INTO pageschemecss (selector, attribute, pageschemeid)
						VALUES (:selector, :attribute, :pageschemeid)';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('selector', $selector);
                $stmt->bindValue('attribute', $attribute);
                $stmt->bindValue('pageschemeid', $id);
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
                $pageschemecssid = Db::getInstance()->lastInsertId();
                
                foreach ($values as $name => $value)
                    
                    if (is_array($value)){
                        foreach ($value as $vName => $vVal){
                            $sql = 'INSERT INTO pageschemecssvalue (pageschemeid, pageschemecssid, name, value, secondvalue)
									VALUES (:pageschemeid, :pageschemecssid, :name, :value, :secondvalue)';
                            $stmt = Db::getInstance()->prepare($sql);
                            $stmt->bindValue('pageschemeid', $id);
                            $stmt->bindValue('pageschemecssid', $pageschemecssid);
                            $stmt->bindValue('name', $name);
                            $stmt->bindValue('value', $vName);
                            $stmt->bindValue('secondvalue', $vVal);
                            try{
                                $stmt->execute();
                            }
                            catch (Exception $e){
                                throw new Exception($e->getMessage());
                            }
                        }
                    }
                    else{
                        $sql = 'INSERT INTO pageschemecssvalue (pageschemeid, pageschemecssid, name, value)
								VALUES (:pageschemeid, :pageschemecssid, :name, :value)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('pageschemeid', $id);
                        $stmt->bindValue('pageschemecssid', $pageschemecssid);
                        $stmt->bindValue('name', $name);
                        $stmt->bindValue('value', $value);
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new Exception($e->getMessage());
                        }
                    }
            }
        }
    }

    public function saveBoxes ($Data, $id)
    {
        $this->boxMapping = Array();
        foreach ($Data as $oldBoxId => $boxData){
            $sql = 'INSERT INTO layoutbox (name, pageschemeid, controller)
					VALUES (:name, :pageschemeid,:controller)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('name', $boxData['name']);
            $stmt->bindValue('controller', $boxData['controller']);
            $stmt->bindValue('pageschemeid', $id);
            try{
                $stmt->execute();
            }
            catch (Exception $e){
                throw new Exception($e->getMessage());
            }
            $layoutboxid = Db::getInstance()->lastInsertId();
            
            $this->boxMapping[$oldBoxId] = $layoutboxid;
            
            $sql = 'INSERT INTO layoutboxtranslation (layoutboxid, languageid, title)
					VALUES (:layoutboxid, :languageid, :title)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('layoutboxid', $layoutboxid);
            $stmt->bindValue('languageid', Helper::getLanguageId());
            $stmt->bindValue('title', ($boxData['title'] == NULL) ? $boxData['name'] : $boxData['title']);
            try{
                $stmt->execute();
            }
            catch (Exception $e){
                throw new Exception($e->getMessage());
            }
            
            foreach ($boxData['params'] as $variable => $values){
                $sql = 'INSERT INTO	layoutboxcontentspecificvalue (layoutboxid, variable, value, languageid)
						VALUES (:id,:variable,:value, :languageid)';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('id', $layoutboxid);
                $stmt->bindValue('variable', $variable);
                echo $values['languageid'] . '<br />';
                if ((int) $values['languageid'] > 0){
                    $stmt->bindValue('languageid', (int) $values['languageid']);
                }
                else{
                    $stmt->bindValue('languageid', NULL);
                }
                $stmt->bindValue('value', $values['value']);
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
            }
            
            foreach ($boxData['behaviour'] as $variable => $value){
                $sql = 'INSERT INTO layoutboxjsvalue (layoutboxid, variable, value)
						VALUES (:layoutboxid, :variable, :value)';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('layoutboxid', $layoutboxid);
                $stmt->bindValue('variable', $variable);
                $stmt->bindValue('value', $value);
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
            }
            
            foreach ($boxData['css'] as $key => $values){
                
                $selector = str_replace('-' . $oldBoxId, '-' . $layoutboxid, $values['selector']);
                
                $sql = 'INSERT INTO layoutboxcss (selector, attribute, layoutboxid)
						VALUES (:selector, :attribute, :layoutboxid)';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('selector', $selector);
                $stmt->bindValue('attribute', $values['attribute']);
                $stmt->bindValue('layoutboxid', $layoutboxid);
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
                $layoutboxcssid = Db::getInstance()->lastInsertId();
                
                foreach ($values['value'] as $name => $value){
                    
                    if (is_array($value)){
                        foreach ($value as $vName => $vVal){
                            $sql = 'INSERT INTO layoutboxcssvalue (layoutboxid, layoutboxcssid, name, value, secondvalue)
									VALUES (:layoutboxid, :layoutboxcssid, :name, :value, :secondvalue)';
                            $stmt = Db::getInstance()->prepare($sql);
                            $stmt->bindValue('layoutboxid', $layoutboxid);
                            $stmt->bindValue('layoutboxcssid', $layoutboxcssid);
                            $stmt->bindValue('name', $name);
                            $stmt->bindValue('value', $vName);
                            $stmt->bindValue('secondvalue', $vVal);
                            try{
                                $stmt->execute();
                            }
                            catch (Exception $e){
                                throw new Exception($e->getMessage());
                            }
                        }
                    }
                    else{
                        $sql = 'INSERT INTO layoutboxcssvalue (layoutboxid, layoutboxcssid, name, value)
								VALUES (:layoutboxid, :layoutboxcssid, :name, :value)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('layoutboxid', $layoutboxid);
                        $stmt->bindValue('layoutboxcssid', $layoutboxcssid);
                        $stmt->bindValue('name', $name);
                        $stmt->bindValue('value', $value);
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new Exception($e->getMessage());
                        }
                    }
                }
            }
        }
    }

    protected function checkSubpageId ($name)
    {
        $sql = 'SELECT idsubpage FROM subpage WHERE name = :name';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('name', $name);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                return $rs['idsubpage'];
            }
            else{
                return NULL;
            }
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function saveSubpages ($Data, $id)
    {
        foreach ($Data as $subpageId => $subpageData){
            $subpageId = $this->checkSubpageId($subpageData['name']);
            if (NULL == $subpageId){
                continue;
            }
            $sql = 'INSERT INTO subpagelayout (subpageid, pageschemeid)
					VALUES (:subpageid, :pageschemeid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('subpageid', $subpageId);
            $stmt->bindValue('pageschemeid', $id);
            try{
                $stmt->execute();
            }
            catch (Exception $e){
                throw new Exception($e->getMessage());
            }
            $subpageLayoutId = Db::getInstance()->lastInsertId();
            
            foreach ($subpageData['layout']['columns'] as $column){
                $sql = 'INSERT INTO subpagelayoutcolumn (subpagelayoutid, `order`, width)
						VALUES (:subpagelayoutid, :order, :width)';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('subpagelayoutid', $subpageLayoutId);
                $stmt->bindValue('order', $column['order']);
                $stmt->bindValue('width', $column['width']);
                try{
                    $stmt->execute();
                }
                catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
                $subpagelayoutcolumnid = Db::getInstance()->lastInsertId();
                
                foreach ($column['subpagelayoutcolumnbox'] as $box){
                    $sql = 'INSERT INTO subpagelayoutcolumnbox (subpagelayoutcolumnid, layoutboxid, `order`, colspan, collapsed)
							VALUES (:subpagelayoutcolumnid, :layoutboxid, :order, :colspan, :collapsed)';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('subpagelayoutcolumnid', $subpagelayoutcolumnid);
                    $stmt->bindValue('layoutboxid', $this->boxMapping[$box['layoutboxid']]);
                    $stmt->bindValue('order', $box['order']);
                    $stmt->bindValue('colspan', $box['colspan']);
                    $stmt->bindValue('collapsed', $box['collapsed']);
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
    }
}