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
 * $Revision: 114 $
 * $Author: gekosale $
 * $Date: 2011-05-07 18:41:26 +0200 (So, 07 maj 2011) $
 * $Id: store.php 114 2011-05-07 16:41:26Z gekosale $
 */
namespace Gekosale;

use sfEvent;
use Symfony\Component\Filesystem\Filesystem;

class GlobalsettingsModel extends Component\Model
{

    protected $robotsFile = 'robots.txt';

    public function __construct($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
        $this->filesystem = new Filesystem();
    }

    public function updateGallerySettings($Data)
    {
        $sql
              = 'UPDATE gallerysettings SET
					width = :width,
					height = :height,
					keepproportion = :keepproportion
				WHERE method = :method';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('width', $Data['smallest_width']);
        $stmt->bindValue('height', $Data['smallest_height']);
        if (isset($Data['smallest_keepproportion']) && $Data['smallest_keepproportion'] == 1) {
            $stmt->bindValue('keepproportion', 1);
        } else {
            $stmt->bindValue('keepproportion', 0);
        }
        $stmt->bindValue('method', 'getSmallestImageById');
        try {
            $stmt->execute();
        } catch (Exception $e) {
            throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
        }

        $sql
              = 'UPDATE gallerysettings SET
					width = :width,
					height = :height,
					keepproportion = :keepproportion
				WHERE method = :method';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('width', $Data['small_width']);
        $stmt->bindValue('height', $Data['small_height']);
        if (isset($Data['small_keepproportion']) && $Data['small_keepproportion'] == 1) {
            $stmt->bindValue('keepproportion', 1);
        } else {
            $stmt->bindValue('keepproportion', 0);
        }
        $stmt->bindValue('method', 'getSmallImageById');
        try {
            $stmt->execute();
        } catch (Exception $e) {
            throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
        }

        $sql
              = 'UPDATE gallerysettings SET
					width = :width,
					height = :height,
					keepproportion = :keepproportion
				WHERE method = :method';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('width', $Data['medium_width']);
        $stmt->bindValue('height', $Data['medium_height']);
        if (isset($Data['medium_keepproportion']) && $Data['medium_keepproportion'] == 1) {
            $stmt->bindValue('keepproportion', 1);
        } else {
            $stmt->bindValue('keepproportion', 0);
        }
        $stmt->bindValue('method', 'getMediumImageById');
        try {
            $stmt->execute();
        } catch (Exception $e) {
            throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
        }

        $sql
              = 'UPDATE gallerysettings SET
					width = :width,
					height = :height,
					keepproportion = :keepproportion
				WHERE method = :method';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('width', $Data['normal_width']);
        $stmt->bindValue('height', $Data['normal_height']);
        if (isset($Data['normal_keepproportion']) && $Data['normal_keepproportion'] == 1) {
            $stmt->bindValue('keepproportion', 1);
        } else {
            $stmt->bindValue('keepproportion', 0);
        }
        $stmt->bindValue('method', 'getNormalImageById');
        try {
            $stmt->execute();
        } catch (Exception $e) {
            throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
        }

        $sql
              = 'UPDATE gallerysettings SET
					width = :width,
					height = :height,
					keepproportion = :keepproportion
				WHERE method = :method';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('width', $Data['large_width']);
        $stmt->bindValue('height', $Data['large_height']);
        if (isset($Data['large_keepproportion']) && $Data['large_keepproportion'] == 1) {
            $stmt->bindValue('keepproportion', 1);
        } else {
            $stmt->bindValue('keepproportion', 0);
        }
        $stmt->bindValue('method', 'getLargeImageById');
        try {
            $stmt->execute();
        } catch (Exception $e) {
            throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
        }
    }

    public function getGallerySettings()
    {
        $sql
              = 'SELECT
					width,
					height,
					keepproportion,
					method
				FROM gallerysettings
				WHERE width IS NOT NULL';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            $method = $rs['method'];
            switch ($method) {
                case 'getSmallestImageById':
                    $Data['smallest_width']          = $rs['width'];
                    $Data['smallest_height']         = $rs['height'];
                    $Data['smallest_keepproportion'] = $rs['keepproportion'];
                    break;
                case 'getSmallImageById':
                    $Data['small_width']          = $rs['width'];
                    $Data['small_height']         = $rs['height'];
                    $Data['small_keepproportion'] = $rs['keepproportion'];
                    break;
                case 'getMediumImageById':
                    $Data['medium_width']          = $rs['width'];
                    $Data['medium_height']         = $rs['height'];
                    $Data['medium_keepproportion'] = $rs['keepproportion'];
                    break;
                case 'getNormalImageById':
                    $Data['normal_width']          = $rs['width'];
                    $Data['normal_height']         = $rs['height'];
                    $Data['normal_keepproportion'] = $rs['keepproportion'];
                    break;
                case 'getLargeImageById':
                    $Data['large_width']          = $rs['width'];
                    $Data['large_height']         = $rs['height'];
                    $Data['large_keepproportion'] = $rs['keepproportion'];
                    break;
            }
        }

        return $Data;
    }

    public function configWriter($Data)
    {
        $Config                     = App::getConfig();
        $ssl                        = (isset($Data['ssl']) && $Data['ssl'] == 1) ? 1 : 0;
        $db                         = $Config['database'];
        $Config['admin_panel_link'] = addslashes($Data['admin_panel_link']);
        $Config['ssl']              = $ssl;
        $filename                   = ROOTPATH . 'config' . DS . 'settings.php';
        $out                        = @fopen($filename, "w");
        @fwrite($out, "<?php \r\n");
        @fwrite($out, '/**
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
 */' . "\r\n"
        );

        fwrite($out, '
		return Array(' . "
			'database'=> Array(
				'driver'=> '{$db['driver']}',
				'host'=> '{$db['host']}',
				'user'=> '{$db['user']}',
				'password'=> '{$db['password']}',
				'dbname'=> '{$db['dbname']}',
			),
			'admin_panel_link'=> '{$Config['admin_panel_link']}',
			'client_data_encription_string'=> '{$Config['client_data_encription_string']}',
			'ssl'=> {$Config['ssl']},
		);"
        );
        @fclose($out);
    }

    public function updateGlobalSettings($Data, $type)
    {
        foreach ($Data as $param => $value) {
            $sql
                  = 'INSERT INTO globalsettings SET
						param = :param,
						type = :type,
						value = :value
					ON DUPLICATE KEY UPDATE
						value = :value
					';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('param', $param);
            $stmt->bindValue('type', $type);
            $stmt->bindValue('value', $value);
            try {
                $stmt->execute();
            } catch (Exception $e) {
                throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
            }
        }
    }

    public function getSettings()
    {
        $Data = Array();
        $sql  = 'SELECT * FROM globalsettings WHERE param IS NOT NULL';
        $stmt = Db::getInstance()->prepare($sql);
        try {
            $stmt->execute();
            while ($rs = $stmt->fetch()) {
                $Data[$rs['type']][$rs['param']] = $rs['value'];
            }
        } catch (Exception $e) {
            throw new CoreException($this->trans('ERR_GALLERYSETTINGS_EDIT'), 18, $e->getMessage());
        }
        if (!isset($Data['gallerysettings_data']['colour'])) {
            $Data['gallerysettings_data']['colour'] = 'ffffff';
        }

        return $Data;
    }

    public function getRobotsFile()
    {
        return file_get_contents(ROOTPATH . $this->robotsFile);
    }

    public function setRobotsFile($content)
    {
        if (strlen($content)) {
            $this->filesystem->dumpFile(ROOTPATH . $this->robotsFile, $content);
        }
    }
}