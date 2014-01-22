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
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: layoutbox.php 583 2011-10-28 20:19:07Z gekosale $
 */
namespace Gekosale\Plugin;

use FormEngine;
use xajaxResponse;

class LayoutboxModel extends Component\Model
{
	protected $newLayoutBoxSchemeId;

	public function getValueForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getLayoutboxForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteLayoutbox ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteLayoutbox'
		), $this->getName(), Array(
			'layoutbox'
		));
	}

	public function deleteLayoutbox ($id)
	{
		$objResponse = new xajaxResponse();
		DbTracker::deleteRows('layoutbox', 'idlayoutbox', $id);
		$url = $this->registry->router->generate('admin', true, Array(
			'controller' => 'layoutbox'
		));
		$this->flushLayoutBoxCache();
		return $objResponse->script("window.location.href = '{$url}';");
	}

	public function getLayoutBoxContentTypeOptionsAllToSelect ()
	{
		$Data = $this->getLayoutBoxContentTypeOptions();
		asort($Data);
		return $Data;
	}

	public function getLayoutBoxContentTypeOptions ($Data = Array())
	{
		$boxData = Array(
			'TextBox' => $this->trans('TXT_TEXT_BOX'),
			'ProductPromotionsBox' => $this->trans('TXT_PRODUCT_PROMOTIONS_BOX'),
			'ProductNewsBox' => $this->trans('TXT_PRODUCT_NEWS_BOX'),
			'GraphicsBox' => $this->trans('TXT_GRAPHICS_BOX'),
			'CategoriesBox' => $this->trans('TXT_CATEGORIES_BOX'),
			'PollBox' => $this->trans('TXT_POLL_BOX'),
			'NewsBox' => $this->trans('TXT_NEWS'),
			'ProductsInCategoryBox' => $this->trans('TXT_PRODUCTS_IN_CATEGORY_BOX'),
			'ProductsCrossSellBox' => $this->trans('TXT_PRODUCTS_CROSS_SELL_BOX'),
			'ProductsSimilarBox' => $this->trans('TXT_PRODUCTS_SIMILAR_BOX'),
			'ProductsUpSellBox' => $this->trans('TXT_PRODUCTS_UP_SELL_BOX'),
			'ProductBox' => $this->trans('TXT_PRODUCT_BOX'),
			'ProductDescriptionBox' => $this->trans('TXT_PRODUCT_DESCRIPTION_BOX'),
			'LayeredNavigationBox' => $this->trans('TXT_LAYERED_NAVIGATION_BOX'),
			'ContactBox' => $this->trans('TXT_CONTACT_BOX'),
			'ProductBestsellersBox' => $this->trans('TXT_PRODUCT_BESTSELLERS_BOX'),
			'WishlistBox' => $this->trans('TXT_WISHLIST_BOX'),
			'MostSearchedBox' => $this->trans('TXT_MOST_SEARCHED_BOX'),
			'CartBox' => $this->trans('TXT_CART'),
			'RegistrationBox' => $this->trans('TXT_REGISTRATION_CART_BOX'),
			'PaymentBox' => $this->trans('TXT_PAYMENT_BOX'),
			'FinalizationBox' => $this->trans('TXT_FINALIZATION_BOX'),
			'ClientLoginBox' => $this->trans('TXT_CLIENT_LOGIN_BOX'),
			'ForgotPasswordBox' => $this->trans('TXT_FORGOT_PASSWORD_BOX'),
			'ClientSettingsBox' => $this->trans('TXT_CLIENT_SETTINGS_BOX'),
			'ClientOrderBox' => $this->trans('TXT_CLIENT_ORDER_BOX'),
			'ClientAddressBox' => $this->trans('TXT_CLIENT_ADDRESS_BOX'),
			'ProductSearchListBox' => $this->trans('TXT_PRODUCT_SEARCH_LIST_BOX'),
			'CartPreviewBox' => $this->trans('TXT_CART_PREVIEW_BOX'),
			'SearchBox' => $this->trans('TXT_SEARCH_BOX'),
			'RecommendFriendBox' => $this->trans('TXT_RECOMMEND_FRIEND_BOX'),
			'NewsletterBox' => $this->trans('TXT_NEWSLETTER_BOX'),
			'CmsBox' => $this->trans('TXT_CMS_BOX'),
			'ShowcaseBox' => $this->trans('TXT_SHOWCASE_BOX'),
			'ProductBuyAlsoBox' => $this->trans('TXT_BUY_ALSO_BOX'),
			'SitemapBox' => $this->trans('TXT_SITEMAP_BOX'),
			'SlideShowBox' => $this->trans('TXT_SLIDESHOW_BOX'),
			'ProducerBox' => $this->trans('TXT_PRODUCER_BOX'),
			'ProducerListBox' => $this->trans('TXT_PRODUCER_LIST_BOX'),
			'FacebookLikeBox' => $this->trans('TXT_FACEBOOK_LIKE_BOX'),
			'MainCategoriesBox' => $this->trans('TXT_MAIN_CATEGORIES_BOX'),
			'CustomProductListBox' => $this->trans('TXT_CUSTOM_PRODUCT_LIST_BOX'),
			'ClientAccountBox' => $this->trans('TXT_CLIENT_ACCOUNT_BOX'),
			'CheckoutBox' => $this->trans('TXT_CHECKOUT_BOX'),
			'ConditionsBox' => $this->trans('TXT_CONDITIONS'),
			'CmsMenuBox' => $this->trans('TXT_CMS_MENU_BOX'),
			'ProductCombinationBox' => $this->trans('TXT_PRODUCT_COMBINATION'),
		);

		$eventData = Event::filter($this, 'admin.layoutbox.getLayoutBoxContentTypeOptions', Array(
			'form' => &$form
		), $this->registry->core->getParam());

		foreach ($eventData as $Data){
			$boxData = Arr::merge($boxData, $Data);
		}

		return $boxData;
	}

	public function getLayoutBoxContentTypeSpecificValues ($idLayoutBox)
	{
		$query = '
				SELECT
					CS.variable AS variable,
					CS.value AS value,
					CS.languageid AS languageid
				FROM
					layoutboxcontentspecificvalue CS
				WHERE
					CS.layoutboxid = :layoutboxid
			';
		$stmt = Db::getInstance()->prepare($query);
		$stmt->bindValue('layoutboxid', $idLayoutBox);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			if ($languageid = $rs['languageid']){
				if (! isset($Data[$rs['variable']])){
					$Data[$rs['variable']] = Array();
				}
				$Data[$rs['variable']][$languageid] = $rs['value'];
			}
			else{
				$Data[$rs['variable']] = $rs['value'];
			}
		}
		return $Data;
	}

	public function addNewLayoutBox ($submittedData)
	{
		Db::getInstance()->beginTransaction();
		try{
			$idNewLayoutBox = $this->addLayoutBox($submittedData);
			if ($idNewLayoutBox != 0){
				$this->newLayoutBoxId = $idNewLayoutBox;
				App::getModel('fieldgenerator/fieldgenerator')->SaveCSSValues($idNewLayoutBox, $submittedData, Array(
					$this,
					'GetSelector'
				), Array(
					$this,
					'addNewLayoutBoxAttributeCss'
				), Array(
					$this,
					'addNewLayoutBoxAttributeCssValue'
				), Array(
					$this,
					'addNewLayoutBoxAttributeCss2ndValue'
				));
				$this->updateLayoutBoxContentTypeSpecificValues($idNewLayoutBox, FormEngine\FE::SubmittedData());
				if (isset($submittedData['bFixedPosition']) && $submittedData['bFixedPosition'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'bFixedPosition', $submittedData['bFixedPosition']);
				}
				if (isset($submittedData['bClosingProhibited']) && $submittedData['bClosingProhibited'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'bClosingProhibited', $submittedData['bClosingProhibited']);
				}
				if (isset($submittedData['bNoHeader']) && $submittedData['bNoHeader'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'bNoHeader', $submittedData['bNoHeader']);
				}
				if (isset($submittedData['bCollapsingProhibited']) && $submittedData['bCollapsingProhibited'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'bCollapsingProhibited', $submittedData['bCollapsingProhibited']);
				}
				if (isset($submittedData['bExpandingProhibited']) && $submittedData['bExpandingProhibited'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'bExpandingProhibited', $submittedData['bExpandingProhibited']);
				}
				if (isset($submittedData['iDefaultSpan']) && $submittedData['iDefaultSpan'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'iDefaultSpan', $submittedData['iDefaultSpan']);
				}
				if (isset($submittedData['iEnableBox']) && $submittedData['iEnableBox'] !== NULL){
					$this->addLayoutboxJSValue($idNewLayoutBox, 'iEnableBox', $submittedData['iEnableBox']);
				}
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}

		Db::getInstance()->commit();
		$this->flushLayoutBoxCache();
		return true;
	}

	public function addLayoutBox ($submittedData)
	{
		$sql = 'INSERT INTO layoutbox (name, pageschemeid, controller)
				VALUES (:name, :pageschemeid,:controller)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $submittedData['name']);
		$stmt->bindValue('controller', $submittedData['box_content']);
		$stmt->bindValue('pageschemeid', $submittedData['pageschemeid']);

		try{
			$stmt->execute();
			$layoutboxid = Db::getInstance()->lastInsertId();
			$sql = 'INSERT INTO layoutboxtranslation (layoutboxid, languageid, title)
					VALUES (:layoutboxid, :languageid, :title)';
			foreach ($submittedData['title'] as $languageid => $title){
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('layoutboxid', $layoutboxid);
				$stmt->bindValue('languageid', $languageid);
				$stmt->bindValue('title', $title);
				$stmt->execute();
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LAYOUTBOX_ADD'), 11, $e->getMessage());
		}
		return $layoutboxid;
	}

	protected function deleteLayoutBoxContentTypeSpecificValues ($idLayoutBox)
	{
		$sql = '
				DELETE
				FROM
					layoutboxcontentspecificvalue
				WHERE
					layoutboxid = :id
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $idLayoutBox);
		$stmt->execute();
	}

	protected function updateLayoutBoxContentTypeSpecificValues ($idLayoutBox, $submittedData)
	{
		$this->deleteLayoutBoxContentTypeSpecificValues($idLayoutBox);
		$variables = Array();
		switch ($submittedData['box']['box_content']) {
			case 'TextBox':
				$content = Array();
				foreach ($submittedData['ct_TextBox']['textbox_content_translation'] as $languageid => $value){
					$content[$languageid] = $value['textbox_content'];
				}
				$variables['content'] = $content;
				break;
			case 'GraphicsBox':
				$variables['image'] = 'design/_images_frontend/upload/' . $submittedData['ct_GraphicsBox']['image']['file'];
				$size = getimagesize(ROOTPATH . $variables['image']);
				$variables['height'] = $size[1] - 10;
				$variables['align'] = $submittedData['ct_GraphicsBox']['align'];
				$variables['url'] = $submittedData['ct_GraphicsBox']['url'];
				break;
			case 'FacebookLikeBox':
				$variables['url'] = $submittedData['ct_FacebookLikeBox']['url'];
				$variables['width'] = $submittedData['ct_FacebookLikeBox']['width'];
				$variables['height'] = $submittedData['ct_FacebookLikeBox']['height'];
				$variables['scheme'] = $submittedData['ct_FacebookLikeBox']['scheme'];
				$variables['faces'] = $submittedData['ct_FacebookLikeBox']['faces'];
				$variables['stream'] = $submittedData['ct_FacebookLikeBox']['stream'];
				$variables['header'] = $submittedData['ct_FacebookLikeBox']['header'];
				break;
			case 'ProductDescriptionBox':
				$variables['tabbed'] = (isset($submittedData['ct_ProductDescriptionBox']['tabbed']) && $submittedData['ct_ProductDescriptionBox']['tabbed']) ? '1' : '0';
				break;
			case 'ProductsInCategoryBox':
				$variables['productsCount'] = $submittedData['ct_ProductsInCategoryBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductsInCategoryBox']['view'];
				// $variables['orderBy'] =
				// $submittedData['ct_ProductsInCategoryBox']['orderBy'];
				// $variables['orderDir'] =
				// $submittedData['ct_ProductsInCategoryBox']['orderDir'];
				$variables['pagination'] = (isset($submittedData['ct_ProductsInCategoryBox']['pagination']) && $submittedData['ct_ProductsInCategoryBox']['pagination']) ? '1' : '0';
				break;
			case 'ProductSearchListBox':
				$variables['productsCount'] = $submittedData['ct_ProductSearchListBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductSearchListBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductSearchListBox']['orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductSearchListBox']['orderDir'];
				$variables['pagination'] = (isset($submittedData['ct_ProductSearchListBox']['pagination']) && $submittedData['ct_ProductSearchListBox']['pagination']) ? '1' : '0';
				break;
			case 'ProductPromotionsBox':
				$variables['productsCount'] = $submittedData['ct_ProductPromotionsBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductPromotionsBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductPromotionsBox']['ct_ProductPromotionsBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductPromotionsBox']['ct_ProductPromotionsBox_orderDir'];
				$variables['pagination'] = (isset($submittedData['ct_ProductPromotionsBox']['pagination']) && $submittedData['ct_ProductPromotionsBox']['pagination']) ? '1' : '0';
				break;
			case 'ProductNewsBox':
				$variables['productsCount'] = $submittedData['ct_ProductNewsBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductNewsBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductNewsBox']['ct_ProductNewsBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductNewsBox']['ct_ProductNewsBox_orderDir'];
				$variables['pagination'] = (isset($submittedData['ct_ProductNewsBox']['pagination']) && $submittedData['ct_ProductNewsBox']['pagination']) ? '1' : '0';
				break;
			case 'ProductCombinationBox':
				$variables['orderBy'] = $submittedData['ct_ProductCombinationBox']['ct_ProductCombinationBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductCombinationBox']['ct_ProductCombinationBox_orderDir'];
				break;
			case 'ProductsCrossSellBox':
				$variables['productsCount'] = $submittedData['ct_ProductsCrossSellBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductsCrossSellBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductsCrossSellBox']['ct_ProductsCrossSellBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductsCrossSellBox']['ct_ProductsCrossSellBox_orderDir'];
				break;
			case 'ProductsSimilarBox':
				$variables['productsCount'] = $submittedData['ct_ProductsSimilarBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductsSimilarBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductsSimilarBox']['ct_ProductsSimilarBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductsSimilarBox']['ct_ProductsSimilarBox_orderDir'];
				break;
			case 'ProductsUpSellBox':
				$variables['productsCount'] = $submittedData['ct_ProductsUpSellBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProductsUpSellBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductsUpSellBox']['ct_ProductsUpSellBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductsUpSellBox']['ct_ProductsUpSellBox_orderDir'];
				break;
			case 'CategoriesBox':
				App::getModel('category')->flushCache();
				$variables['showcount'] = (isset($submittedData['ct_CategoriesBox']['showcount']) && $submittedData['ct_CategoriesBox']['showcount']) ? '1' : '0';
				$variables['hideempty'] = (isset($submittedData['ct_CategoriesBox']['hideempty']) && $submittedData['ct_CategoriesBox']['hideempty']) ? '1' : '0';
				$variables['showall'] = isset($submittedData['ct_CategoriesBox']['showall']) ? $submittedData['ct_CategoriesBox']['showall'] : 1;
				$variables['categoryIds'] = (isset($submittedData['ct_CategoriesBox']['categoryIds']) && is_array($submittedData['ct_CategoriesBox']['categoryIds']) && count($submittedData['ct_CategoriesBox']['categoryIds']) > 0) ? implode(',', $submittedData['ct_CategoriesBox']['categoryIds']) : '';
				break;
			case 'MainCategoriesBox':
				$variables['showall'] = isset($submittedData['ct_MainCategoriesBox']['showall']) ? $submittedData['ct_MainCategoriesBox']['showall'] : 1;
				$variables['categoryIds'] = (isset($submittedData['ct_MainCategoriesBox']['categoryIds']) && is_array($submittedData['ct_MainCategoriesBox']['categoryIds']) && count($submittedData['ct_MainCategoriesBox']['categoryIds']) > 0) ? implode(',', $submittedData['ct_MainCategoriesBox']['categoryIds']) : '';
				break;
			case 'ShowcaseBox':
				$variables['productsCount'] = $submittedData['ct_ShowcaseBox']['productsCount'];
				$variables['orderBy'] = $submittedData['ct_ShowcaseBox']['ct_ShowcaseBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ShowcaseBox']['ct_ShowcaseBox_orderDir'];
				$variables['statusId'] = $submittedData['ct_ShowcaseBox']['statusId'];
				break;
			case 'ProductBestsellersBox':
				$variables['productsCount'] = $submittedData['ct_ProductBestsellersBox']['productsCount'];
				$variables['minProductsCount'] = $submittedData['ct_ProductBestsellersBox']['minProductsCount'];
				$variables['view'] = $submittedData['ct_ProductBestsellersBox']['view'];
				$variables['orderBy'] = $submittedData['ct_ProductBestsellersBox']['ct_ProductBestsellersBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_ProductBestsellersBox']['ct_ProductBestsellersBox_orderDir'];
				break;
			case 'CustomProductListBox':
				$variables['productsCount'] = $submittedData['ct_CustomProductListBox']['productsCount'];
				$variables['view'] = $submittedData['ct_CustomProductListBox']['view'];
				$variables['orderBy'] = $submittedData['ct_CustomProductListBox']['ct_CustomProductListBox_orderBy'];
				$variables['orderDir'] = $submittedData['ct_CustomProductListBox']['ct_CustomProductListBox_orderDir'];
				$variables['products'] = (isset($submittedData['ct_CustomProductListBox']['custom_products'])) ? implode(',', $submittedData['ct_CustomProductListBox']['custom_products']) : '';
				break;
			case 'SitemapBox':
				$variables['categoryTreeLevels'] = $submittedData['ct_SitemapBox']['categoryTreeLevels'];
				break;
			case 'SlideShowBox':
				for ($i = 1; $i <= 10; $i ++){
					if ($submittedData['ct_SlideShowBox']['image' . $i]['file'] != ''){
						$variables['image' . $i] = 'design/_images_frontend/upload/' . $submittedData['ct_SlideShowBox']['image' . $i]['file'];
						$size = getimagesize(ROOTPATH . $variables['image' . $i]);
						$variables['height' . $i] = $size[1];
						$variables['url' . $i] = $submittedData['ct_SlideShowBox']['url' . $i];
						$variables['caption' . $i] = $submittedData['ct_SlideShowBox']['caption' . $i];
					}
				}
				break;
			case 'ProducerBox':
				$variables['view'] = $submittedData['ct_ProducerBox']['view'];
				$variables['producers'] = (isset($submittedData['ct_ProducerBox']['producers'])) ? implode(',', $submittedData['ct_ProducerBox']['producers']) : '';
				break;
			case 'ProducerListBox':
				$variables['productsCount'] = $submittedData['ct_ProducerListBox']['productsCount'];
				$variables['view'] = $submittedData['ct_ProducerListBox']['view'];
				$variables['pagination'] = (isset($submittedData['ct_ProducerListBox']['pagination']) && $submittedData['ct_ProducerListBox']['pagination']) ? '1' : '0';
				break;
		}

		foreach ($variables as $variable => $value){
			if (is_array($value)){
				foreach ($value as $languageid => $translatedValue){
					$sql = '
							INSERT
							INTO
								layoutboxcontentspecificvalue (
									layoutboxid,
									variable,
									value,
									languageid
								)
							VALUES (
								:id,
								:variable,
								:value,
								:languageid
							)
						';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('id', $idLayoutBox);
					$stmt->bindValue('variable', $variable);
					$stmt->bindValue('value', $translatedValue);
					$stmt->bindValue('languageid', $languageid);
					$stmt->execute();
				}
			}
			else{
				$sql = '
						INSERT
						INTO
							layoutboxcontentspecificvalue (
								layoutboxid,
								variable,
								value
							)
						VALUES (
							:id,
							:variable,
							:value
						)
					';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('id', $idLayoutBox);
				$stmt->bindValue('variable', $variable);
				$stmt->bindValue('value', $value);
				$stmt->execute();
			}
		}
	}

	public function GetSelector ($selector)
	{
		return str_replace('__id__', (! $this->registry->core->getParam(1)) ? $this->newLayoutBoxId : $this->registry->core->getParam(1), $selector);
	}

	public function addNewLayoutBoxAttributeCss ($idNewLayoutBox, $attribute, $selector, $class = NULL)
	{
		$sql = 'INSERT INTO layoutboxcss (class, selector, attribute, layoutboxid)
					VALUES (:class, :selector, :attribute, :layoutboxid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('class', $class);
		$stmt->bindValue('selector', $selector);
		$stmt->bindValue('attribute', $attribute);
		$stmt->bindValue('layoutboxid', $idNewLayoutBox);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LAYOUTBOX_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewLayoutBoxAttributeCssValue ($idNewLayoutBox, $newLayoutAttrCssId, $name, $value)
	{
		$sql = 'INSERT INTO layoutboxcssvalue (layoutboxid, layoutboxcssid, name, value)
				VALUES (:layoutboxid, :layoutboxcssid, :name, :value)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('layoutboxid', $idNewLayoutBox);
		$stmt->bindValue('layoutboxcssid', $newLayoutAttrCssId);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('value', $value);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LAYOUTBOX_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewLayoutBoxAttributeCss2ndValue ($idNewLayoutBox, $newLayoutAttrCssId, $name, $value, $value2)
	{
		$sql = 'INSERT INTO layoutboxcssvalue (layoutboxid, layoutboxcssid, name, value, secondvalue)
				VALUES (:layoutboxid, :layoutboxcssid, :name, :value, :secondvalue)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('layoutboxid', $idNewLayoutBox);
		$stmt->bindValue('layoutboxcssid', $newLayoutAttrCssId);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('value', $value);
		$stmt->bindValue('secondvalue', $value2);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LAYOUTBOX_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addLayoutboxJSValue ($idNewLayoutBox, $variable, $value)
	{
		$sql = 'INSERT INTO layoutboxjsvalue (layoutboxid, variable, value)
				VALUES (:layoutboxid, :variable, :value)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('layoutboxid', $idNewLayoutBox);
		$stmt->bindValue('variable', $variable);
		$stmt->bindValue('value', $value);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LAYOUTBOX_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function deleteLayoutboxJSValue ($id)
	{
		DbTracker::deleteRows('layoutboxjsvalue', 'layoutboxid', $id);
	}

	public function getLayoutBoxToEdit ($IdLayoutBox)
	{
		$sql = 'SELECT
					LB.name,
					LB.controller
				FROM layoutbox LB
				WHERE LB.idlayoutbox= :idlayoutbox';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idlayoutbox', $IdLayoutBox);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$sql = '
					SELECT
						LBT.languageid AS languageid,
						LBT.title AS title
					FROM
						layoutboxtranslation LBT
					WHERE
						LBT.layoutboxid = :idlayoutbox
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('idlayoutbox', $IdLayoutBox);
			$rs2 = $stmt->execute();
			$title = Array();
			while ($rs2 = $stmt->fetch()){
				$title[$rs2['languageid']] = $rs2['title'];
			}
			$Data = Array(
				'name' => $rs['name'],
				'title' => $title,
				'controller' => $rs['controller']
			);
		}
		return $Data;
	}

	public function prepareFieldName ($class = NULL, $selector, $attribute)
	{
		$fieldName = '';
		if ($selector != NULL && $attribute != NULL){
			if ($class !== NULL){
				$prepareName = $class . ',' . $selector . '_' . $attribute;
			}
			else{
				$prepareName = $selector . '_' . $attribute;
			}
			$fieldName = $prepareName;
		}
		return $fieldName;
	}

	public function getLayoutBoxCSSToEdit ($IdLayoutBox)
	{
		$sql = "SELECT
					LBC.idlayoutboxcss,
					LBC.class,
					LBC.selector,
					LBC.attribute
				FROM layoutboxcss LBC
				WHERE LBC.layoutboxid = :layoutboxid";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('layoutboxid', $IdLayoutBox);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['selector']][$rs['attribute']] = $this->getLayoutBoxCssValueToEdit($rs['idlayoutboxcss']);
		}
		return $Data;
	}

	public function getLayoutBoxCssValueToEdit ($idLayoutBoxCss)
	{
		$sql = "SELECT
					LBCV.idlayoutboxcssvalue,
					LBCV.layoutboxcssid,
					LBCV.layoutboxid,
					LBCV.name,
					LBCV.value,
					LBCV.secondvalue
				FROM layoutboxcssvalue LBCV
				WHERE LBCV.layoutboxcssid= :layoutboxcssid";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('layoutboxcssid', $idLayoutBoxCss);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			if ($rs['secondvalue'] != NULL){
				$Data[$rs['name']][$rs['value']] = $rs['secondvalue'];
			}
			else{
				$Data[$rs['name']] = $rs['value'];
			}
		}
		return $Data;
	}

	public function getLayoutBoxJSValuesToEdit ($idLayoutBox)
	{
		$sql = "SELECT LBJV.idlayoutboxjsvalue, LBJV.variable, LBJV.value
					FROM layoutboxjsvalue LBJV
					WHERE  LBJV.layoutboxid= :idlayoutbox";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idlayoutbox', $idLayoutBox);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['variable']] = $rs['value'];
		}
		return $Data;
	}

	public function editLayoutBox ($submittedData, $idlayoutbox)
	{
		$this->updateLayoutBox($submittedData, $idlayoutbox);
		$this->updateLayoutBoxContentTypeSpecificValues($idlayoutbox, FormEngine\FE::SubmittedData());
		$cssValues = $this->deleteLayoutBoxCssValue($idlayoutbox);
		$css = $this->deleteLayoutBoxCss($idlayoutbox);
		App::getModel('fieldgenerator/fieldgenerator')->SaveCSSValues($idlayoutbox, $submittedData, Array(
			$this,
			'GetSelector'
		), Array(
			$this,
			'addNewLayoutBoxAttributeCss'
		), Array(
			$this,
			'addNewLayoutBoxAttributeCssValue'
		), Array(
			$this,
			'addNewLayoutBoxAttributeCss2ndValue'
		));
		$js = $this->deleteLayoutboxJSValue($idlayoutbox);
		if (isset($submittedData['bFixedPosition']) && $submittedData['bFixedPosition'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'bFixedPosition', $submittedData['bFixedPosition']);
		}
		if (isset($submittedData['bClosingProhibited']) && $submittedData['bClosingProhibited'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'bClosingProhibited', $submittedData['bClosingProhibited']);
		}
		if (isset($submittedData['bNoHeader']) && $submittedData['bNoHeader'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'bNoHeader', $submittedData['bNoHeader']);
		}
		if (isset($submittedData['bCollapsingProhibited']) && $submittedData['bCollapsingProhibited'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'bCollapsingProhibited', $submittedData['bCollapsingProhibited']);
		}
		if (isset($submittedData['bExpandingProhibited']) && $submittedData['bExpandingProhibited'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'bExpandingProhibited', $submittedData['bExpandingProhibited']);
		}
		if (isset($submittedData['iDefaultSpan']) && $submittedData['iDefaultSpan'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'iDefaultSpan', $submittedData['iDefaultSpan']);
		}
		if (isset($submittedData['iEnableBox']) && $submittedData['iEnableBox'] !== NULL){
			$this->addLayoutboxJSValue($idlayoutbox, 'iEnableBox', $submittedData['iEnableBox']);
		}
		$this->flushLayoutBoxCache();
	}

	public function updateLayoutBox ($submittedData, $id)
	{
		DbTracker::deleteRows('layoutboxtranslation', 'layoutboxid', $id);

		$sql = 'UPDATE layoutbox SET
					name= :name,
					controller = :controller
				WHERE
					idlayoutbox = :idlayoutbox';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idlayoutbox', $id);
		$stmt->bindValue('name', $submittedData['name']);
		$stmt->bindValue('controller', $submittedData['box_content']);

		try{
			$stmt->execute();
			foreach ($submittedData['title'] as $languageid => $title){
				$sql = '
						INSERT INTO
							layoutboxtranslation (layoutboxid, languageid, title)
						VALUES (:layoutboxid, :languageid, :title)
					';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('layoutboxid', $id);
				$stmt->bindValue('languageid', $languageid);
				$stmt->bindValue('title', $title);
				$stmt->execute();
			}
		}
		catch (Exception $e){
			return false;
		}
		return true;
	}

	public function deleteLayoutBoxCssValue ($id)
	{
		DbTracker::deleteRows('layoutboxcssvalue', 'layoutboxid', $id);
	}

	public function deleteLayoutBoxCss ($id)
	{
		DbTracker::deleteRows('layoutboxcss', 'layoutboxid', $id);
	}

	public function changeBorderSize ($idBorderSize)
	{
		return $idBorderSize;
	}

	public function changeBackground ($background)
	{
		if ($background != NULL){
			return $background;
		}
	}

	public function getSchemeValuesForAjax ($request)
	{
		$values = Array();
		if ($request['id'] == '0'){
			$rawValues = App::getModel('cssgenerator/cssgenerator')->getPageSchemeStyleSheetContent();
			foreach ($rawValues as $value){
				if (strpos($value['selector'], 'layout-box') === false){
					continue;
				}
				$value['selector'] = str_replace(',', ', #layout-box-__id__', '#layout-box-__id__ ' . $value['selector']);
				$value['selector'] = str_replace('#layout-box-__id__ .layout-box ', '#layout-box-__id__ ', $value['selector']);
				$value['selector'] = str_replace('.layout-box.layout-box ', '#layout-box-__id__ ', $value['selector']);
				$value['selector'] = preg_replace('/#layout-box-__id__ \.layout-box$/', '#layout-box-__id__.layout-box', $value['selector']);
				$values[$value['selector']][$value['attribute']] = $value['value'];
			}
		}
		elseif (! empty($request['id'])){
			$rawValues = App::getModel('cssgenerator/cssgenerator')->getLayoutBoxSchemeStyleSheetContent($request['id']);
			foreach ($rawValues as $value){
				if (strpos($value['selector'], 'layout-box') === false){
					continue;
				}
				$value['selector'] = preg_replace('/.layout-box-scheme-\d+/', '#layout-box-__id__', $value['selector']);
				$values[$value['selector']][$value['attribute']] = $value['value'];
			}
		}
		return Array(
			'values' => $values
		);
	}

	public function flushLayoutBoxCache ()
	{
		$sql = "SELECT name FROM subpage ORDER BY name ASC";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		while ($rs = $stmt->fetch()) {
			App::getContainer()->get('cache')->delete('columns'. $rs['name']);
		}
		App::getContainer()->get('cache')->delete('layoutbox');
	}

	public function getLayoutBoxTree ()
	{
		$sql = 'SELECT
					idpagescheme AS id,
					name
				FROM pagescheme
				WHERE IF(:viewid > 0, idpagescheme = :pageschemeid, 1)
				ORDER BY name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', (int) Helper::getViewId());
		$stmt->bindValue('pageschemeid', $this->registry->loader->getParam('pageschemeid'));
		$stmt->execute();
		$Data = Array();
		$i = 0;
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'name' => $rs['name'],
				'parent' => NULL,
				'weight' => $i ++
			);

			$sql2 = 'SELECT
						name,
						idlayoutbox
					FROM layoutbox L
					WHERE pageschemeid = :pageschemeid
					ORDER BY name ASC';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('pageschemeid', $rs['id']);
			$stmt2->execute();
			$j = 0;
			while ($rs2 = $stmt2->fetch()){
				$Data[$rs['id'] . ',' . $rs2['idlayoutbox']] = Array(
					'name' => $rs2['name'],
					'parent' => $rs['id'],
					'weight' => $j ++
				);
			}
		}

		return $Data;
	}

	public function getFirstLayoutBox ($id)
	{
		$sql = 'SELECT idlayoutbox FROM layoutbox WHERE pageschemeid = :pageschemeid ORDER BY name ASC LIMIT 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('pageschemeid', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		return $rs['idlayoutbox'];
	}
}