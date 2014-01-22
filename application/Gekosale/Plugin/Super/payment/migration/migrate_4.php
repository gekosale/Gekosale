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
namespace Gekosale\Super\Payment;

class Migrate_4 extends \Gekosale\Component\Migration
{

    public function up ()
    {
        $dirs = new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'themes')), '~.+\.tpl\z~');
        
        foreach ($dirs as $dir){
            $file = $dir->getPathName();
            if ($dir->getFilename() == 'paypal.tpl'){
                $data = file_get_contents($file);
                $orgData = $data;
                if (strpos($orgData, '<input type="hidden" name="custom" value="{{ content.session_id }}">')){
                    if (! strpos($orgData, '<input type="hidden" name="bn" value="Wellcommerce_Cart_Promo">')){
                        $sh = '<input type="hidden" name="custom" value="{{ content.session_id }}">';
                        $rp = '<input type="hidden" name="custom" value="{{ content.session_id }}">' . "\n\t" . '<input type="hidden" name="bn" value="Wellcommerce_Cart_Promo">';
                        $data = str_replace($sh, $rp, $orgData);
                        if ($data !== $orgData){
                            file_put_contents($file, $data);
                        }
                    }
                }
            }
        }
    }

    public function down ()
    {
    }
} 