<?php

namespace Gekosale\Admin\Product;

class Migrate_7 extends \Gekosale\Component\Migration
{

    public function up()
    {
        $this->execSql(
            'ALTER TABLE `producttranslation` CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT NULL',
            array()
        );
    }

    public function down()
    {
    }
}