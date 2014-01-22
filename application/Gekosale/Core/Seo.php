<?php

namespace Gekosale\Core;

class Seo
{

	private static $seocontrollers = Array();
	private static $generator = NULL;

	public static function getPathFromRoute ($route)
	{
		if (NULL === self::$generator){
			self::$generator = App::getRegistry()->router->getGenerator();
		}
		return self::$generator->generate($route, Array(), true);
	}

	public static function getSeo ($name)
	{
		if (empty(self::$seocontrollers)){
			self::load();
		}
		if (is_array(self::$seocontrollers) && ! empty(self::$seocontrollers)){
			$data = array_flip(self::$seocontrollers);
			if (! is_null($name) && ($name != '') && (isset($data[$name]))){
				return $data[$name];
			}
		}

		return $name;
	}

	public static function getController ($name)
	{
		if (empty(self::$seocontrollers)){
			self::load();
		}
		if (! is_null($name) && ($name != '') && (isset(self::$seocontrollers[$name]))){
			return self::$seocontrollers[$name];
		}
		else{
			return $name;
		}
	}

	public static function load ()
	{
		if ((self::$seocontrollers = App::getContainer()->get('cache')->load('seocontrollers')) === FALSE){
			$sql = 'SELECT
						C.name as name,
						IF(CS.name IS NOT NULL, CS.name, C.name) as alias
					FROM controller C
					LEFT JOIN controllerseo CS ON CS.controllerid = C.idcontroller
					WHERE CS.languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				self::$seocontrollers[$rs['alias']] = $rs['name'];
			}
			App::getContainer()->get('cache')->save('seocontrollers', self::$seocontrollers);
		}
	}

}