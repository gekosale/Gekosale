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
 * $Revision: 464 $
 * $Author: gekosale $
 * $Date: 2011-08-31 08:19:48 +0200 (Śr, 31 sie 2011) $
 * $Id: cssgenerator.php 464 2011-08-31 06:19:48Z gekosale $ 
 */
namespace Gekosale;

use sfEvent;

class CssgeneratorModel extends Component\Model
{
	private $sizes = array(
		array(
			' 0px',
			' 0em',
			' 0%',
			' 0ex',
			' 0cm',
			' 0mm',
			' 0in',
			' 0pt',
			' 0pc'
		),
		array(
			':0px',
			':0em',
			':0%',
			':0ex',
			':0cm',
			':0mm',
			':0in',
			':0pt',
			':0pc'
		)
	);
	private $shortcuts = array(
		
		', ' => ',',
		' , ' => ',',
		';}' => '}',
		'; }' => '}',
		' ; }' => '}',
		' :' => ':',
		': ' => ':',
		' {' => '{',
		'; ' => ';',
		
		// kolory
		':black' => ':#000',
		':darkgrey' => ':#666',
		':fuchsia' => ':#F0F',
		':lightgrey' => ':#CCC',
		':orange' => ':#F60',
		':white' => ':#FFF',
		':yellow' => ':#FF0',
		
		':silver' => ':#C0C0C0',
		':gray' => ':#808080',
		':maroon' => ':#800000',
		':red' => ':#FF0000',
		':purple' => ':#800080',
		':green' => ':#008000',
		':lime' => ':#00FF00',
		':olive' => ':#808000',
		':navy' => ':#000080',
		':blue' => ':#0000FF',
		':teal' => ':#008080',
		':aqua' => ':#00FFFF'
	);
	private $font_weight_to_num = array(
		'lighter' => 100,
		'normal' => 400,
		'bold' => 700,
		'bolder' => 900
	);
	protected $currentPageSchemeId;
	protected $pageschemeCssValues;
	protected $layoutboxCssValues;
	protected $layoutboxCss;

	public function createPageSchemeStyleSheetDocument ($theme)
	{
		$filename = ROOTPATH . 'themes' . DS . $theme . DS . 'assets' . DS . 'css' . DS . 'scheme.less';
		try{
			$pageScheme = $this->getPageSchemeStyleSheetContent();
			if (! empty($pageScheme)){
				$layoutBoxes = $this->getLayoutBoxesStyleSheetContent();
				$pageSchemeStyleSheet = $this->_preparePageSchemeCssContent($pageScheme, $theme);
				$file = @fopen($filename, "w+");
				$write = fwrite($file, '@import "mixins.less";' . "\n\n");
				$write = fwrite($file, $pageSchemeStyleSheet);
				foreach ($layoutBoxes as $layoutBox){
					$layoutBoxStyleSheet = $this->_preparePageSchemeCssContent($layoutBox, $theme);
					$write = fwrite($file, $layoutBoxStyleSheet);
				}
				fclose($file);
				clearstatcache();
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
			clearstatcache();
		}
	}

	public function cleanCode ($code)
	{
		$code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', null, $code);
		$code = str_replace(array(
			"\r\n",
			"\r",
			"\n",
			"\t",
			'  ',
			'    '
		), null, $code);
		return $code;
	}

	public function compressCode ($code)
	{
		$code = str_replace($this->sizes[0], ' 0', $code);
		$code = str_replace($this->sizes[1], ':0', $code);
		$code = str_ireplace(array_keys($this->shortcuts), array_values($this->shortcuts), $code);
		$search = array(
			1 => '/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i',
			2 => '/(font-weight|font):([a-z- ]*)(normal|bolder|bold|lighter)/ie'
		);
		
		$replace = array(
			1 => '$1#$2$3$4$5',
			2 => '"$1:$2" . $this->font_weight_to_num["$3"]'
		);
		
		$code = preg_replace($search, $replace, $code);
		return $code;
	}

	protected function clearMinifyFiles ()
	{
		$cachePath = ROOTPATH . 'cache';
		if ($dir = opendir($cachePath)){
			while (false !== ($file = readdir($dir))){
				if (substr($file, 0, 6) == 'minify'){
					unlink($cachePath . DS . $file);
				}
			}
		}
		closedir($dir);
	}

	protected function _preparePageSchemeCssContent ($schemeRules, $theme)
	{
		$gradientsPath = '../img/';
		$uploadsPath = '../img/';
		
		$css = Array();
		foreach ($schemeRules as $rule){
			$attributes = Array();
			if (! strlen($rule['attribute'])){
				continue;
			}
			$value = $rule['value'];
			switch ($rule['attribute']) {
				
				case 'background':
					switch ($value['type']) {
						
						case 1: // Single colour
							if (isset($value['start'])){
								$attributes[] = "background: {$this->_formatCssColour($value['start'])};";
							}
							else{
								$attributes[] = "background: transparent;";
							}
							break;
						
						case 2: // Gradient
							if (isset($value['start']) && isset($value['end'])){
								$attributes[] = ".gradient({$this->_formatCssColour($value['start'])},{$this->_formatCssColour($value['end'])});";
							}
							else{
								$attributes[] = "background: transparent;";
							}
							break;
						
						case 3: // Picture
							! isset($value['start']) && ($value['start'] = 'transparent');
							if (isset($value['file'])){
								$attributes[] = "background: {$this->_formatCssColour($value['start'])} url('{$uploadsPath}{$value['file']}') {$value['position']} {$value['repeat']};";
							}
							else{
								$attributes[] = "background: {$this->_formatCssColour($value['start'])};";
							}
							break;
					}
					break;
				
				case 'icon':
					$attributes[] = "background: transparent url('{$uploadsPath}{$value['file']}') center center no-repeat;";
					break;
				
				case 'font':
					isset($value['colour']) && $attributes[] = "color: {$this->_formatCssColour($value['colour'])};";
					isset($value['family']) && $attributes[] = "font-family: {$value['family']};";
					isset($value['size']) && $attributes[] = "font-size: {$value['size']}px;";
					isset($value['bold']) && $attributes[] = 'font-weight: ' . ($value['bold'] ? 'bold' : 'normal') . ';';
					isset($value['italic']) && $attributes[] = 'font-style: ' . ($value['italic'] ? 'italic' : 'normal') . ';';
					isset($value['underline']) && $attributes[] = 'text-decoration: ' . ($value['underline'] ? 'underline' : 'none') . ';';
					isset($value['uppercase']) && $attributes[] = 'text-transform: ' . ($value['uppercase'] ? 'uppercase' : 'none') . ';';
					break;
				
				case 'border':
					$sides = Array(
						'top',
						'right',
						'bottom',
						'left'
					);
					foreach ($sides as $side){
						isset($value[$side]['size']) && $attributes[] = "border-{$side}-style: " . (($value[$side]['size'] > 0) ? 'solid' : 'none') . ';';
						isset($value[$side]['colour']) && $attributes[] = "border-{$side}-color: {$this->_formatCssColour($value[$side]['colour'])};";
						isset($value[$side]['size']) && $attributes[] = "border-{$side}-width: {$value[$side]['size']}px;";
					}
					break;
				
				case 'border-radius':
					$radius = isset($value['value']) ? $value['value'] : '0px';
					$attributes[] = ".radius({$radius});";
					break;
				
				case 'line-height':
					$formattedValue = $value['value'];
					if (! preg_match('/(em|px|\%)$/', $formattedValue)){
						$formattedValue .= 'px';
					}
					$attributes[] = "line-height: {$formattedValue};";
					break;
				
				case 'width':
					$attributes[] = "width: {$value['value']}px;";
					break;
				
				case 'height':
					$attributes[] = "height: {$value['value']}px;";
					break;
				
				default:
					$attributes[] = "{$rule['attribute']}: {$value['value']};";
					break;
			}
			$cssRuleString = $this->_formatCssRule($rule['selector'], $attributes);
			if ($cssRuleString){
				$css[] = $cssRuleString;
			}
		}
		$cssString = implode("\n\n", $css);
		return $cssString;
	}

	protected function _formatCssColour ($colour)
	{
		if (strlen($colour) == 6){
			return '#' . $colour;
		}
		if (empty($colour)){
			return 'transparent';
		}
		return $colour;
	}

	protected function _formatCssRule ($selector, $attributes)
	{
		if (! is_array($attributes) || ! count($attributes)){
			return false;
		}
		$attributesString = '';
		foreach ($attributes as $attribute){
			$attributesString .= "\t{$attribute}\n";
		}
		$selector = str_replace(', ', ",\n", $selector);
		return "{$selector} {\n{$attributesString}}";
	}

	public function getPageSchemeStyleSheetContent ()
	{
		$Data = $this->getPageSchemeCss($this->registry->core->getParam());
		return $Data;
	}

	public function getLayoutBoxesStyleSheetContent ()
	{
		$boxes = $this->getLayoutBox();
		$Data = Array();
		foreach ($boxes as $box){
			$Data[] = $box['css'];
		}
		return $Data;
	}

	public function getLayoutBoxCssValues ($layoutBoxId)
	{
		$sql = 'SELECT 
					LB.idlayoutbox, 
					LB.name
				FROM layoutbox LB
				WHERE LB.idlayoutbox= :idlayoutbox';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idlayoutbox', $layoutBoxId);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'idlayoutbox' => $rs['idlayoutbox'],
				'name' => $rs['name'],
				'boxcss' => $this->getLayoutBoxCSS($IdLayoutBox)
			);
		}
		return $Data;
	}

	public function getLayoutBox ()
	{
		$sql = "SELECT LB.idlayoutbox
				FROM layoutbox LB";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$layoutboxid = $rs['idlayoutbox'];
			$Data[$layoutboxid] = Array(
				'css' => $this->getLayoutBoxCSS($layoutboxid)
			);
		}
		return $Data;
	}

	public function getLayoutBoxCSS ($idlayoutbox)
	{
		$this->collectLayoutBoxCss();
		if (! isset($this->layoutboxCss[$idlayoutbox])){
			return Array();
		}
		return $this->layoutboxCss[$idlayoutbox];
	}

	public function getLayoutBoxCssValue ($id)
	{
		$this->collectLayoutBoxCssValues();
		if (! isset($this->layoutboxCssValues[$id])){
			return Array();
		}
		return $this->layoutboxCssValues[$id];
	}

	protected function collectLayoutBoxCss ()
	{
		if (is_array($this->layoutboxCss)){
			return;
		}
		$sql = '
				SELECT
					LBC.idlayoutboxcss,
					LBC.layoutboxid,
					LBC.selector,
					LBC.attribute
				FROM
					layoutboxcss LBC
					LEFT JOIN layoutbox LB ON LB.idlayoutbox = LBC.layoutboxid
			';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['layoutboxid']][] = Array(
				'idlayoutboxschemecss' => $rs['idlayoutboxcss'],
				'selector' => $rs['selector'],
				'attribute' => $rs['attribute'],
				'value' => $this->getLayoutBoxCssValue($rs['idlayoutboxcss'])
			);
		}
		$this->layoutboxCss = $Data;
	}

	protected function collectLayoutBoxCssValues ()
	{
		if (is_array($this->layoutboxCssValues)){
			return;
		}
		$sql = '
				SELECT
					LBCV.layoutboxid,
					LBCV.layoutboxcssid,
					LBCV.name,
					LBCV.value,
					LBCV.secondvalue
				FROM
					layoutboxcssvalue LBCV
					LEFT JOIN layoutbox LB ON LB.idlayoutbox = LBCV.layoutboxid
			';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			if ($rs['secondvalue'] != NULL){
				$Data[$rs["layoutboxcssid"]][$rs['name']][$rs['value']] = $rs['secondvalue'];
			}
			else{
				$Data[$rs["layoutboxcssid"]][$rs['name']] = $rs['value'];
			}
		}
		$this->layoutboxCssValues = $Data;
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

	public function getPageSchemeCss ($idPageScheme)
	{
		$sql = "SELECT 
					PSC.idpageschemecss, 
					PSC.class, 
					PSC.selector, 
					PSC.attribute
				FROM pageschemecss PSC
				WHERE PSC.pageschemeid = :idpagescheme";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idpagescheme', $idPageScheme);
		$stmt->execute();
		$this->currentPageSchemeId = $idPageScheme;
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'idpageschemecss' => $rs['idpageschemecss'],
				'class' => $rs['class'],
				'selector' => $rs['selector'],
				'attribute' => $rs['attribute'],
				'value' => $this->getTemplateCssValue($rs['idpageschemecss'])
			);
		}
		$this->templateCssValues = null;
		return $Data;
	}

	protected function collectPageschemeCssValues ($id)
	{
		if (is_array($this->pageschemeCssValues)){
			return;
		}
		$sql = "SELECT
					PSCV.pageschemecssid,
					PSCV.name,
					PSCV.`value`,
					PSCV.`secondvalue`
				FROM pageschemecssvalue PSCV
				WHERE PSCV.pageschemeid = :id
			";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			if ($rs['secondvalue'] != NULL){
				$Data[$rs["pageschemecssid"]][$rs['name']][$rs['value']] = $rs['secondvalue'];
			}
			else{
				$Data[$rs['pageschemecssid']][$rs['name']] = $rs['value'];
			}
		}
		$this->pageschemeCssValues = $Data;
	}

	public function getTemplateCssValue ($pageschemecssid)
	{
		$this->collectPageschemeCssValues($this->currentPageSchemeId);
		if (! isset($this->pageschemeCssValues[$pageschemecssid])){
			return Array();
		}
		return $this->pageschemeCssValues[$pageschemecssid];
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
}