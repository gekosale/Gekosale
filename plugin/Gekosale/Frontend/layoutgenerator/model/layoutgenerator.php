<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: layoutgenerator.php 576 2011-10-22 08:23:55Z gekosale $
 */

namespace Gekosale;

class LayoutGeneratorModel extends Component\Model
{
	
	protected $_subpageName;
	protected $_columns;
	protected $_width;
	protected $_disabled;
	public $boxes = array();

	public function LoadLayout ($subpageName)
	{
		
		if (($LayoutBoxParams = App::getContainer()->get('cache')->load('layoutbox')) === FALSE){
			$LayoutBoxParams = $this->getLayoutBoxParams();
			App::getContainer()->get('cache')->save('layoutbox', $LayoutBoxParams);
		}
		
		$this->_subpageName = $subpageName;
		$this->_columns = Array();
		if (($this->_columns = App::getContainer()->get('cache')->load('columns' . $subpageName)) === FALSE){
			$query = '
						SELECT
							SLC.order AS `column`,
							SLC.width AS width,
							SLB.layoutboxid AS id,
							SLB.colspan AS colspan,
							SLB.collapsed AS collapsed,
							LB.controller
						FROM
							subpage S
							JOIN subpagelayout SL ON SL.subpageid = S.idsubpage
							JOIN subpagelayoutcolumn SLC ON SL.idsubpagelayout = SLC.subpagelayoutid
							LEFT JOIN subpagelayoutcolumnbox SLB ON SLC.idsubpagelayoutcolumn = SLB.subpagelayoutcolumnid
							LEFT JOIN layoutbox LB ON SLB.layoutboxid = LB.idlayoutbox
						WHERE
							S.name = :subpagename AND SL.pageschemeid = :pageschemeid
						GROUP BY
							SLB.idsubpagelayoutcolumnbox
						ORDER BY
							SLC.order,
							SLB.order
					';
			$stmt = Db::getInstance()->prepare($query);
			$stmt->bindValue('subpagename', $this->_subpageName);
			$stmt->bindValue('pageschemeid', $this->registry->loader->getParam('pageschemeid'));
			$stmt->execute();
			$previousColumn = 0;
			$currentColumn = - 1;
			while ($rs = $stmt->fetch()){
				$column = $rs['column'];
				if ($previousColumn != $column){
					$this->_columns[] = Array(
						'width' => $rs['width'],
						'boxes' => Array()
					);
					$previousColumn = $column;
					$currentColumn ++;
				}
				$this->_columns[$currentColumn]['boxes'][] = Array(
					'id' => $rs['id'],
					'controller' => $rs['controller'],
					'params' => isset($LayoutBoxParams[$rs['id']]) ? $LayoutBoxParams[$rs['id']] : Array(),
					'colspan' => $rs['colspan'],
					'collapsed' => $rs['collapsed']
				);
			}
			App::getContainer()->get('cache')->save('columns' . $subpageName, $this->_columns);
		}
	}

	public function GetTemplateData ($containerId, $action = 'index')
	{
		return Array(
			'content' => $this->GenerateContent($action),
			'js' => $this->GenerateScript($containerId)
		);
	}

	public function GenerateContent ($action)
	{
		$query = '
				SELECT
					PSV.value AS value
				FROM
					pageschemecssvalue PSV
					LEFT JOIN pageschemecss PSC ON PSV.pageschemecssid = PSC.idpageschemecss
					LEFT JOIN pagescheme PS ON PSC.pageschemeid = PS.idpagescheme
				WHERE
					PSC.selector = \'#main-container\'
				LIMIT 1
			';
		$stmt = Db::getInstance()->prepare($query);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$width = $rs['value'];
		$content = '';
		$widths = Array();
		$autoWidths = 0;
		$widthLeft = intval($width);
		$marginWidth = 20;
		$widthLeft += $marginWidth;
		foreach ($this->_columns as $i => $column){
			$widths[$i] = $column['width'];
			if ($column['width'] == 0){
				$autoWidths ++;
			}
			$widthLeft -= $column['width'] + $marginWidth;
		}
		foreach ($this->_columns as $i => $column){
			$margin = ($i > 0) ? $marginWidth : 0;
			if ($widths[$i] == 0){
				$widths[$i] = $widthLeft / $autoWidths;
			}
			$page = strtolower($this->_subpageName);
			$content .= "\n<div class=\"layout-column {$page}\" id=\"layout-column-{$i}\" style=\"width: {$widths[$i]}px; margin-left: {$margin}px;\">";
			foreach ($column['boxes'] as $box){
				if (! isset($box['id'])){
					continue;
				}
				$showBox = false;
				
				if (isset($box['params']['js']['iEnableBox'])){
					if ($box['params']['js']['iEnableBox'] == 0){
						$showBox = true;
					}
					
					if ($box['params']['js']['iEnableBox'] == 1 && App::getContainer()->get('session')->getActiveClientid() > 0){
						$showBox = true;
					}
					
					if ($box['params']['js']['iEnableBox'] == 2 && App::getContainer()->get('session')->getActiveClientid() == NULL){
						$showBox = true;
					}
					
					if ($box['params']['js']['iEnableBox'] == 3){
						$showBox = false;
					}
				}
				else{
					$showBox = true;
				}
				
				if ($showBox == true){
					$controller = $box['controller'];
					$namespaces = $this->registry->loader->getNamespaces();
					foreach ($namespaces as $namespace){
						if (is_file(ROOTPATH . 'plugin' . DS . $namespace . DS . 'Frontend' . DS . strtolower($controller . DS . 'controller' . DS . $controller . '.php'))){
							$controllerFile = ROOTPATH . 'plugin' . DS . $namespace . DS . 'Frontend' . DS . strtolower($controller . DS . 'controller' . DS . $controller . '.php');
							require_once ($controllerFile);
							$controllerFullName = $namespace . '\\' . $controller . 'Controller';
							$controllerObject = new $controllerFullName($this->registry, $box);
							
							if (! is_callable(Array(
								$controllerObject,
								$action
							))){
								$controllerObject->setDesignPath(strtolower($controller . DS . 'index' . DS));
							}else{
								$controllerObject->setDesignPath(strtolower($controller . DS . $action . DS));
							}
							
						}
					}
					
					if ($controllerObject->boxVisible()){
						$this->boxes[] = $box['id'];
						$content .= "\n" . $controllerObject->getBoxContents($action);
					}
				}
			}
			$content .= "\n</div>";
		}
		return $content;
	}

	public function getLayoutBoxParams ()
	{
		$LayoutBoxParams = Array();
		
		$query = '
					SELECT
						LB.idlayoutbox AS id,
						LB.controller AS controller,
						IF(LBT.title IS NOT NULL,LBT.title, LB.controller) AS heading
					FROM
						layoutbox LB
						LEFT JOIN layoutboxtranslation LBT ON LBT.layoutboxid = LB.idlayoutbox AND LBT.languageid = :languageid
					GROUP BY LB.idlayoutbox
				';
		$stmt = Db::getInstance()->prepare($query);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$boxId = $rs['id'];
			
			$queryJS = '
					SELECT
						JS.variable AS variable,
						JS.value AS value
					FROM
						layoutboxjsvalue AS JS
					WHERE
						JS.layoutboxid = :layoutboxid
				';
			$stmtJS = Db::getInstance()->prepare($queryJS);
			$stmtJS->bindValue('layoutboxid', $boxId);
			$stmtJS->execute();
			$jsVariables = Array();
			while ($rsJS = $stmtJS->fetch()){
				$jsVariables[$rsJS['variable']] = $rsJS['value'];
			}
			
			$queryCSS = '
					SELECT
						CS.variable AS variable,
						CS.value AS value
					FROM
						layoutboxcontentspecificvalue AS CS
					WHERE
						CS.layoutboxid = :layoutboxid
						AND ((CS.languageid = :languageid) OR (CS.languageid IS NULL))
				';
			$stmtCSS = Db::getInstance()->prepare($queryCSS);
			$stmtCSS->bindValue('layoutboxid', $boxId);
			$stmtCSS->bindValue('languageid', Helper::getLanguageId());
			$stmtCSS->execute();
			$boxAttributes = Array();
			while ($rsCSS = $stmtCSS->fetch()){
				$boxAttributes[$rsCSS['variable']] = $rsCSS['value'];
			}
			
			$LayoutBoxParams[$boxId] = Array(
				'controller' => $rs['controller'],
				'heading' => $rs['heading'],
				'js' => $jsVariables,
				'css' => $boxAttributes
			);
		
		}
		return $LayoutBoxParams;
	
	}

	protected function _GenerateLayoutHash ()
	{
		return md5(json_encode($this->_columns) . ' ' . json_encode($this->boxes));
	}

	public function GenerateScript ($containerId)
	{
		if (($LayoutBoxParams = App::getContainer()->get('cache')->load('layoutbox')) === FALSE){
			$LayoutBoxParams = $this->getLayoutBoxParams();
			App::getContainer()->get('cache')->save('layoutbox', $LayoutBoxParams);
		}
		$columns = Array();
		foreach ($this->_columns as $column){
			$boxes = Array();
			foreach ($column['boxes'] as $box){
				if (! isset($box['id'])){
					continue;
				}
				if (in_array($box['id'], $this->boxes)){
					$showBox = false;
					
					if (isset($LayoutBoxParams[$box['id']]['js']['iEnableBox'])){
						if ($LayoutBoxParams[$box['id']]['js']['iEnableBox'] == 0){
							$showBox = true;
						}
						
						if ($LayoutBoxParams[$box['id']]['js']['iEnableBox'] == 1 && App::getContainer()->get('session')->getActiveClientid() > 0){
							$showBox = true;
						}
						
						if ($LayoutBoxParams[$box['id']]['js']['iEnableBox'] == 2 && App::getContainer()->get('session')->getActiveClientid() == NULL){
							$showBox = true;
						}
						
						if ($LayoutBoxParams[$box['id']]['js']['iEnableBox'] == 3){
							$showBox = false;
						}
					}
					else{
						$showBox = true;
					}
					
					if ($showBox == true){
						$boxes[] = '
											{
												sName: \'' . $box['id'] . '\',
												bCollapsed: ' . ($box['collapsed'] ? 'true' : 'false') . ',
												iSpan: ' . $box['colspan'] . '
											}';
					}
				
				}
			}
			$columns[] = '
									new GLayoutColumn({
										iWidth: ' . $column['width'] . ',
										asBoxes: [' . implode(',', $boxes) . '
										]
									})';
		}
		$script = '
				<script type="text/javascript">
					/* <![CDATA[ */
						GCore.OnLoad(function() {
							$(\'#' . $containerId . '\').GLayoutBoxes({
								aoColumns: [' . implode(',', $columns) . '
								],
								sLayoutHash: \'' . $this->_GenerateLayoutHash() . '\'
							});
						});
					/* ]]> */
				</script>
			';
		return $script;
	}

}