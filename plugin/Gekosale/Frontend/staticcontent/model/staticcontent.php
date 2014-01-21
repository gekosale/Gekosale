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
 * $Revision: 513 $
 * $Author: gekosale $
 * $Date: 2011-09-08 09:06:11 +0200 (Cz, 08 wrz 2011) $
 * $Id: staticcontent.php 513 2011-09-08 07:06:11Z gekosale $
 */
namespace Gekosale;

class StaticContentModel extends Component\Model
{

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
        $this->viewid = Helper::getViewId();
        $this->languageid = Helper::getLanguageId();
        $this->_rawData = Array();
        $this->activePath = Array();
    }

    public function renderPolicyJS ()
    {
        $render = '';
        $moduleSettings = $this->registry->core->getModuleSettingsForView();
        if (isset($moduleSettings['privacy']['privacypolicyenabled']) && $moduleSettings['privacy']['privacypolicyenabled'] == 1){
            if (isset($moduleSettings['privacy']['privacypolicyid'])){
                $content = $this->getStaticContent($moduleSettings['privacy']['privacypolicyid']);
                if (! empty($content)){
                    $link = $this->registry->router->generate('frontend.staticcontent', true, Array(
                        'param' => $moduleSettings['privacy']['privacypolicyid'],
                        'slug' => $content['seo']
                    ));
                    
                    $render .= '<link rel="stylesheet" href="http://static.divante.pl/cookies/divante.cookies.min.css" type="text/css"/>';
                    $render .= '<script type="text/javascript" src="' . DESIGNPATH . '_js_frontend/core/policy.js"></script>';
                    $render .= "
					<script type=\"text/javascript\">
						$('body').GPrivacyBar({
			    			sUrl: '{$link}',
			    			sText1: '" . addslashes($this->trans('TXT_COOKIES_1')) . "',
			    			sText2: '" . addslashes($this->trans('TXT_COOKIES_2')) . "',
			    			sClose: '" . addslashes($this->trans('TXT_COOKIES_CLOSE')) . "'
			   			});
					</script>";
                }
            }
        }
        return $render;
    }

    public function getConditionsId ()
    {
        $sql = "SELECT
					C.idcontentcategory as id
				FROM contentcategory C
				LEFT JOIN contentcategoryview CV ON CV.contentcategoryid = C.idcontentcategory
				WHERE C.redirect_route = :route AND CV.viewid = :viewid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('route', 'frontend.conditions');
        $stmt->bindValue('viewid', $this->viewid);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            return $rs['id'];
        }
        return 0;
    }

    public function getContentByRoute ($route)
    {
        $sql = "SELECT
					C.idcontentcategory as id,
					CT.name,
					CT.description
				FROM contentcategory C
				LEFT JOIN contentcategoryview CV ON CV.contentcategoryid = C.idcontentcategory
				LEFT JOIN contentcategorytranslation CT ON C.idcontentcategory = CT.contentcategoryid AND CT.languageid = :languageid
				WHERE C.redirect_route = :route AND CV.viewid = :viewid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('route', $route);
        $stmt->bindValue('languageid', $this->languageid);
        $stmt->bindValue('viewid', $this->viewid);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'id' => $rs['id'],
                'topic' => $rs['name'],
                'content' => $rs['description']
            );
        }
        return $Data;
    }

    public function getStaticContent ($id)
    {
        $sql = "SELECT
					C.idcontentcategory as id,
					CT.name,
					CT.description
				FROM contentcategory C
				LEFT JOIN contentcategoryview CV ON CV.contentcategoryid = C.idcontentcategory
				LEFT JOIN contentcategorytranslation CT ON C.idcontentcategory = CT.contentcategoryid AND CT.languageid = :languageid
				WHERE C.idcontentcategory = :id AND CV.viewid = :viewid
				ORDER BY C.hierarchy ASC";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', $this->languageid);
        $stmt->bindValue('viewid', $this->viewid);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'id' => $rs['id'],
                'topic' => $rs['name'],
                'content' => $rs['description'],
                'undercategorybox' => $this->getUnderCategoryBox($id),
                'seo' => strtolower(Core::clearSeoUTF($rs['name']))
            );
        }
        return $Data;
    }

    public function getRedirection ($id)
    {
        $sql = "SELECT
					C.idcontentcategory AS id,
					C.redirect,
					C.redirect_route,
					C.redirect_url
				FROM contentcategory C
				LEFT JOIN contentcategoryview CCV ON CCV.contentcategoryid = C.idcontentcategory
				LEFT JOIN contentcategorytranslation CCT ON CCT.contentcategoryid = C.idcontentcategory
				WHERE C.idcontentcategory = :id AND CCV.viewid=:viewid AND languageid=:languageid
				ORDER BY C.hierarchy ASC";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', $this->viewid);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        
        $rs = $stmt->fetch();
        if ($rs){
            $Data = Array(
                'type' => $rs['redirect'],
                'redirect' => $redirect = $this->generateUrl(array(
                    'id' => $rs['id'],
                    'redirect' => $rs['redirect'],
                    'redirect_route' => $rs['redirect_route'],
                    'redirect_url' => $rs['redirect_url'],
                    'seo' => ''
                ))
            );
        }
        return $Data;
    }

    public function getUnderCategoryBox ($id)
    {
        $sql = "SELECT
					C.idcontentcategory AS id,
					CCT.name,
					C.redirect,
					C.redirect_route,
					C.redirect_url
				FROM contentcategory C
				LEFT JOIN contentcategoryview CCV ON CCV.contentcategoryid = C.idcontentcategory
				LEFT JOIN contentcategorytranslation CCT ON CCT.contentcategoryid = C.idcontentcategory
				WHERE C.contentcategoryid =:id AND CCV.viewid=:viewid AND languageid=:languageid
				ORDER BY C.hierarchy ASC";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', $this->viewid);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'redirect' => $redirect = $this->generateUrl(array(
                    'id' => $rs['id'],
                    'name' => $rs['name'],
                    'redirect' => $rs['redirect'],
                    'redirect_route' => $rs['redirect_route'],
                    'redirect_url' => $rs['redirect_url'],
                    'seo' => strtolower(Core::clearSeoUTF($rs['name']))
                ))
            );
        }
        return $Data;
    }

    protected function _loadRawContentCategoriesFromDatabase ()
    {
        $sql = 'SELECT
					C.idcontentcategory AS id,
					CT.name AS name,
					CCV.viewid,
					C.contentcategoryid as parent,
					C.header,
					C.footer,
					C.redirect,
					C.redirect_route,
					C.redirect_url
				FROM contentcategory C
				LEFT JOIN contentcategoryview CCV ON CCV.contentcategoryid = C.idcontentcategory
				LEFT JOIN contentcategorytranslation CT ON CT.contentcategoryid = C.idcontentcategory AND CT.languageid = :languageid
				WHERE CCV.viewid = :viewid
				ORDER BY C.hierarchy ASC
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'viewid' => $rs['viewid'],
                'parent' => $rs['parent'],
                'header' => $rs['header'],
                'footer' => $rs['footer'],
                'redirect' => $rs['redirect'],
                'redirect_route' => $rs['redirect_route'],
                'redirect_url' => $rs['redirect_url'],
                'seo' => strtolower(Core::clearSeoUTF($rs['name']))
            );
        }
        $this->_rawData = $Data;
    }

    public function getActivePath ()
    {
        return $this->activePath;
    }

    public function getContentCategoriesTree ()
    {
        // if (($categories = App::getContainer()->get('cache')->load('contentcategory'))
        // === FALSE){
        $this->_loadRawContentCategoriesFromDatabase();
        $categories = $this->_parseCategorySubtree();
        // App::getContainer()->get('cache')->save('contentcategory', $categories);
        // }
        return $categories;
    }

    protected function _parseCategorySubtree ($level = 0, $parent = null)
    {
        $categories = Array();
        foreach ($this->_rawData as $category){
            if ($parent == null){
                if ($category['parent'] != ''){
                    continue;
                }
            }
            elseif ($category['parent'] != $parent){
                continue;
            }
            
            $url = $this->generateUrl($category);
            
            $active = $this->checkActiveCategory($category, $url);
            
            if ($active){
                $this->activePath[] = $category['parent'];
                $this->activePath[] = $category['id'];
            }
            $categories[$category['id']] = Array(
                'id' => $category['id'],
                'parent' => $category['parent'],
                'header' => $category['header'],
                'footer' => $category['footer'],
                'name' => $category['name'],
                'seo' => $category['seo'],
                'link' => $this->generateUrl($category),
                'active' => ($url == App::getUrl()) ? true : false,
                'children' => $this->_parseCategorySubtree($level + 1, $category['id'])
            );
        }
        
        return $categories;
    }

    public function getConditionsLink ()
    {
        $link = $this->registry->router->generate('frontend.conditions', true);
        return '<a href="' . $link . '" title="' . $this->trans('TXT_CONDITIONS') . '" target="_blank">' . $this->trans('TXT_CONDITIONS') . '</a>';
    }

    protected function generateUrl ($category)
    {
        switch ($category['redirect']) {
            case 0:
                $link = $this->registry->router->generate('frontend.staticcontent', true, Array(
                    'param' => $category['id'],
                    'slug' => $category['seo']
                ));
                break;
            case 1:
                $link = $this->registry->router->generate($category['redirect_route'], true);
                break;
            case 2:
                $link = $category['redirect_url'];
                break;
        }
        
        return $link;
    }

    protected function checkActiveCategory ($category, $url)
    {
        if ($category['redirect_route'] == 'frontend.news' && $this->registry->router->getCurrentController() == 'news'){
            return true;
        }
        else{
            return ($url == App::getUrl()) ? true : false;
        }
    }

    public function getBoxHeadingName ($contentcategoryid)
    {
        $sql = "SELECT
					CCT.name
				FROM contentcategorytranslation CCT
				LEFT JOIN contentcategoryview CCV ON CCV.contentcategoryid = CCT.contentcategoryid
 				WHERE languageid = :languageid AND viewid = :viewid AND CCT.contentcategoryid =:contentcategoryid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', $this->languageid);
        $stmt->bindValue('viewid', $this->viewid);
        $stmt->bindValue('contentcategoryid', $contentcategoryid);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'name' => $rs['name'],
                'seo' => strtolower(Core::clearSeoUTF($rs['name']))
            );
        }
        return $Data;
    }

    public function getMetaData ($contentcategoryid)
    {
        $Data = Array();
        $sql = 'SELECT
					CCT.name,
					CCT.keyword_title AS keyword_title,
					IF(CCT.keyword = \'\',VT.keyword, CCT.keyword) AS keyword,
					IF(CCT.keyword_description = \'\',VT.keyword_description,CCT.keyword_description) AS keyword_description
				FROM contentcategorytranslation CCT
				LEFT JOIN contentcategoryview CCV ON CCV.contentcategoryid = CCT.contentcategoryid
				LEFT JOIN viewtranslation VT ON VT.viewid = CCV.viewid
				WHERE CCT.contentcategoryid =:contentcategoryid AND CCT.languageid = :languageid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('contentcategoryid', $contentcategoryid);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $Data = Array(
                'keyword_title' => ($rs['keyword_title'] == NULL || $rs['keyword_title'] == '') ? $rs['name'] : $rs['keyword_title'],
                'keyword' => $rs['keyword'],
                'keyword_description' => $rs['keyword_description']
            );
        }
        return $Data;
    }
}