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
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;

class newsModel extends Component\Model
{

    public function getNews ()
    {
        $sql = "SELECT 
					N.idnews, 
					NT.topic, 
					NT.summary,
					NT.content,
					N.adddate,
					N.featured,
					NT.seo
				FROM news N
				LEFT JOIN newsview NV ON NV.newsid = idnews
				LEFT JOIN newstranslation NT ON N.idnews = NT.newsid AND NT.languageid = :languageid
				WHERE
					publish = 1
				AND
					IF((N.startdate IS NULL OR N.startdate = '0000-00-00 00:00:00' OR N.startdate <= CURDATE()) AND (N.enddate IS NULL OR N.enddate = '0000-00-00 00:00:00' OR N.enddate >= CURDATE()), 1, 0)
				AND
					NV.viewid = :viewid ORDER BY N.`adddate` desc";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'topic' => $rs['topic'],
                'adddate' => $rs['adddate'],
                'summary' => $rs['summary'],
                'content' => $rs['content'],
                'link' => $this->generateUrl($rs['idnews'], $rs['seo']),
                'seo' => $rs['seo'],
                'idnews' => $rs['idnews'],
                'featured' => $rs['featured'],
                'mainphoto' => $this->getPhotosByNewsId($rs['idnews'])
            );
        }
        return $Data;
    }

    protected function generateUrl ($id, $seo)
    {
        return $this->registry->router->generate('frontend.news', true, Array(
            'param' => $id,
            'slug' => $seo
        ));
    }

    public function getNewsById ($id)
    {
        $sql = "SELECT 
					N.idnews, 
					NT.topic, 
					NT.summary,
					NT.content,
					NT.seo,
					NT.keyword_title,
					NT.keyword,
					NT.keyword_description,
					N.adddate,
					N.featured
				FROM news N
				LEFT JOIN newsview NV ON NV.newsid = idnews
				LEFT JOIN newstranslation NT ON N.idnews = NT.newsid AND NT.languageid = :languageid
				WHERE
					idnews=:id
				AND
					publish = 1
				AND
					IF((N.startdate IS NULL OR N.startdate = '0000-00-00 00:00:00' OR N.startdate <= CURDATE()) AND (N.enddate IS NULL OR N.enddate = '0000-00-00 00:00:00' OR N.enddate >= CURDATE()), 1, 0)
					
				AND
					NV.viewid = :viewid
				ORDER BY N.`adddate` desc";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'featured' => $rs['featured'],
                'topic' => $rs['topic'],
                'adddate' => $rs['adddate'],
                'summary' => $rs['summary'],
                'content' => $rs['content'],
                'seo' => $rs['seo'],
                'link' => $this->generateUrl($rs['idnews'], $rs['seo']),
                'keyword_title' => ($rs['keyword_title'] == NULL || $rs['keyword_title'] == '') ? $rs['topic'] : $rs['keyword_title'],
                'keyword' => $rs['keyword'],
                'keyword_description' => $rs['keyword_description'],
                'mainphoto' => $this->getPhotosByNewsId($id),
                'otherphoto' => $this->getOtherPhotosByNewsId($id)
            );
            
            return $Data;
        }
        else{
            App::redirectUrl($this->registry->router->generate('frontend.news', true));
        }
    }

    public function getPhotosByNewsId ($id)
    {
        $sql = "SELECT photoid
				FROM newsphoto
				WHERE newsid= :id AND mainphoto= 1";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $Data = Array();
        try{
            $stmt->execute();
            while ($rs = $stmt->fetch()){
                $Data['small'] = App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($rs['photoid']));
                $Data['normal'] = App::getModel('gallery')->getImagePath(App::getModel('gallery')->getNormalImageById($rs['photoid']));
                $Data['orginal'] = App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($rs['photoid']));
            }
        }
        catch (Exception $e){
            throw new FrontendException('Error while doing sql query.', 11, $e->getMessage());
        }
        return $Data;
    }

    public function getOtherPhotosByNewsId ($id)
    {
        $sql = "SELECT photoid
				FROM newsphoto
				WHERE newsid= :id 
				AND mainphoto = 0";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $Data = Array();
        try{
            $stmt->execute();
            while ($rs = $stmt->fetch()){
                $Data[] = Array(
                    'small' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($rs['photoid'])),
                    'normal' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getNormalImageById($rs['photoid'])),
                    'orginal' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($rs['photoid']))
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException('Error while doing sql query.', 11, $e->getMessage());
        }
        return $Data;
    }

    public function getMetadataForNews ()
    {
        if ($this->registry->core->getParam() == NULL){
            $sql = 'SELECT idcontentcategory FROM contentcategory WHERE redirect_route = :route';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('route', 'frontend.news');
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $Data = App::getModel('staticcontent')->getMetaData($rs['idcontentcategory']);
            }
            else{
                $Data = App::getModel('seo')->getMetadataForPage();
            }
        }
        else{
            $Data = $this->getNewsById((int) $this->registry->core->getParam());
        }
        return $Data;
    }
}