<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: product.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale\Plugin;

class ProducersModel extends Component\Model
{

    public function getProducer ($id)
    {
        $sql = 'SELECT
					P.idproducer AS id,
					PT.name,
					PT.description,
					PT.seo,
					P.photoid,
					COUNT(PROD.idproduct) AS totalproducts
				FROM producer P
				INNER JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :language
				LEFT JOIN product PROD ON PROD.producerid = P.idproducer
				WHERE P.idproducer = :id
				GROUP BY P.idproducer';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('language', Helper::getLanguageId());
        $stmt->bindValue('id', $id);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data = Array(
                'id' => $rs['id'],
                'translation' => $this->getProducerTranslationById($rs['id']),
                'photos' => App::getModel('webapi')->getPhotos($rs['photoid'])
            );
        }
        return $Data;
    }

    public function getProducerTranslationById ($id)
    {
        $sql = "SELECT
					producerid,
					name,
					seo,
					description,
					languageid,
					keyword_title,
					keyword,
					keyword_description
				FROM producertranslation
				WHERE producerid = :id ";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $langid = $rs['languageid'];
            $Data[$langid] = Array(
                'producerid' => $rs['producerid'],
                'name' => $rs['name'],
                'link' => $this->registry->router->generate('frontend.producerlist', true, Array(
                    'param' => $rs['seo']
                )),
                'seo' => $rs['seo'],
                'description' => $rs['description'],
                'keyword_title' => $rs['keyword_title'],
                'keyword' => $rs['keyword'],
                'keyword_description' => $rs['keyword_description']
            );
        }
        return $Data;
    }

    public function getProducers ()
    {
        $sql = 'SELECT
					P.idproducer AS id,
					PT.name,
					PT.description,
					PT.seo,
					P.photoid
				FROM producer P
				INNER JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :languageid
				GROUP BY P.idproducer';
        $Data = Array();
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['id'],
                'translation' => $this->getProducerTranslationById($rs['id']),
                'photos' => App::getModel('webapi')->getPhotos($rs['photoid'])
            );
        }
        return $Data;
    }

    public function addProducer ($Data)
    {
        $sql = 'INSERT INTO producer (adddate) VALUES (NOW())';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        
        $producerid = Db::getInstance()->lastInsertId();
        
        foreach ($Data['translation'] as $key => $val){
            $sql = 'INSERT INTO producertranslation (producerid, name, seo, languageid)
					VALUES (:producerid, :name, :seo,:languageid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('producerid', $producerid);
            $stmt->bindValue('name', $val['name']);
            $stmt->bindValue('languageid', $key);
            $stmt->bindValue('seo', $val['seo']);
            $stmt->execute();
        }
        
        $sql = 'INSERT INTO producerview (producerid, viewid)
					VALUES (:producerid, :viewid)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('producerid', $producerid);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        
        return Array(
            'success' => true,
            'id' => $producerid
        );
    }

    public function updateProducer ($Data)
    {
        DbTracker::deleteRows('producertranslation', 'producerid', $Data['id']);
        
        foreach ($Data['translation'] as $key => $val){
            $sql = 'INSERT INTO producertranslation (producerid, name, seo, languageid)
					VALUES (:producerid, :name, :seo,:languageid)';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('producerid', $Data['id']);
            $stmt->bindValue('name', $val['name']);
            $stmt->bindValue('languageid', $key);
            $stmt->bindValue('seo', $val['seo']);
            $stmt->execute();
        }
        
        return Array(
            'success' => true
        );
    }

    public function deleteProducer ($id)
    {
        DbTracker::deleteRows('producer', 'idproducer', $id);
        
        return Array(
            'success' => true
        );
    }
} 