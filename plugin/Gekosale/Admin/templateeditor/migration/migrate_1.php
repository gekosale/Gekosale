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
*/

namespace Gekosale\Admin\TemplatEeditor;

class Migrate_1 extends \Gekosale\Component\Migration
{
	public function up ()
	{
		$dirs = new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'themes')), '~.+\.tpl\z~');

		foreach ($dirs as $dir) {
			$file = $dir->getPathName();

			$data = file_get_contents($file);
			$orgData = $data;

			// przelewy24
			if (strpos($data, '"{{ URL }}przelewy24report"') !== FALSE) {
				$data = str_replace('"{{ URL }}przelewy24report"', '"{{ path(\'frontend.payment\', {"action": \'report\', "param": \'przelewy24\'}) }}"', $data);
			}

			// {{ URL }}newsletter/index/{{ newsletterlink }}
			//
			// {{ path('frontend.newsletter', {"param": newsletterlink}) }}
			if (preg_match_all('~\{\{\s*(?i:URL)\s*\}\}newsletter/index/\{\{\s*.+?\s*\}\}~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~\{\{\s*(?i:URL)\s*\}\}newsletter/index/\{\{\s*(.+?)\s*\}\}~', '{{ path(\'frontend.newsletter\', {"param": \\1}) }}', $data);
			}


			// {{ URL }}{{ path('frontend.registration') }}
			//
			//          {{ path('frontend.registration') }}
			if (preg_match_all('~\{\{\s*(?i:URL)\s*\}\}\{\{\s*path\(\'frontend\..+?\'\)\s*\}\}~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~\{\{\s*(?i:URL)\s*\}\}(\{\{\s*path\(\'frontend\..+?\'\)\s*\}\})~', '\1', $data);
			}

			// {{ path('frontend.productcart') }}/{{ item.seo }}
			//
			// {{ path('frontend.productcart', {"param": item.seo}) }}
			if (preg_match_all('~"\{\{\s*path\(\'frontend\..+?\'\)\s*\}\}/\{\{\s*.+?\s*\}\}"~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~"\{\{\s*path\(\'frontend\.(.+?)\'\)\s*\}\}/\{\{\s*(.+?)\s*\}\}"~', '"{{ path(\'frontend.\\1\', {"param": \\2}) }}"', $data);
			}

			// {{ URL }}mainside
			//
			// {{ path('frontend.mainside') }}
			if (preg_match_all('~\{\{\s*(?i:URL)\s*\}\}mainside~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~\{\{\s*(?i:URL)\s*\}\}mainside~', '{{ path(\'frontend.mainside\') }}', $data);
			}

			// {{ URL }}{{ 'mainside'|seo }}
			//
			// {{ path('frontend.mainside') }}
			if (preg_match_all('~\{\{\s*(?i:URL)\s*\}\}\{\{\s*\'[^\']+\'\|seo\s*\}\}~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~\{\{\s*(?i:URL)\s*\}\}\{\{\s*\'([^\']+)\'\|seo\s*\}\}~', '{{ path(\'frontend.\1\') }}', $data);
			}

			// {{ URL }}
			//
			// {{ path('frontend.home') }}
			if (preg_match_all('~\{\{\s*(?i:URL)\s*\}\}~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~\{\{\s*(?i:URL)\s*\}\}~', '{{ path(\'frontend.home\') }}', $data);
			}

			// usuwanie koncowego slasha
			// <a href="{{ path('frontend.mainside') }}/"
			//
			// <a href="{{ path('frontend.mainside') }}"
			if (preg_match_all('~"\{\{ path\(\'frontend.mainside\'\) \}\}/"~', $data, $match)) {
				//echo implode("\n", $match[0]) . "\n";
				$data = preg_replace('~"\{\{ path\(\'frontend.mainside\'\) \}\}/"~', '"{{ path(\'frontend.mainside\') }}"', $data);
			}

			// {{ path('frontend.staticcontent', {"param": subpage.id }}/{{ subpage.seo}) }}
			//
			// {{ path('frontend.staticcontent', {"param": subpage.id, "slug": subpage.seo})  }}
			if (strpos($data, '"{{ path(\'frontend.staticcontent\', {"param": subpage.id }}/{{ subpage.seo}) }}"') !== FALSE) {
				$data = str_replace('"{{ path(\'frontend.staticcontent\', {"param": subpage.id }}/{{ subpage.seo}) }}"', '"{{ path(\'frontend.staticcontent\', {"param": subpage.id, "slug": subpage.seo}) }}"', $data);
			}

			if ($data !== $orgData) {
				file_put_contents($file, $data);
				//echo "Writing " . $file . "\n";
			}
		}
	}

	public function down ()
	{
	}
}