<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core\Migration;

use Gekosale\Core\Migration;

/**
 * Migration1393537998
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393537998 extends Migration
{

    public function up()
    {
        /*
         * Create vat table
        */
        if (!$this->getDb()->schema()->hasTable('vat')) {
            $this->getDb()->schema()->create('vat', function ($table) {
                $table->increments('id');
                $table->decimal('value')->unique();
                $table->timestamps();
            });
        }

        /*
         * Create vat_translation table
         */
        if (!$this->getDb()->schema()->hasTable('vat_translation')) {
            $this->getDb()->schema()->create('vat_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->integer('vat_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('vat_id')->references('id')->on('vat')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop vat_translation table
         */
        if ($this->getDb()->schema()->hasTable('vat_translation')) {
            $this->getDb()->schema()->drop('vat_translation');
        }

        /*
         * Drop vat table
        */
        if ($this->getDb()->schema()->hasTable('vat')) {
            $this->getDb()->schema()->drop('vat');
        }
    }
}