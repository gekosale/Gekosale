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
 * Migration1393685450
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393685450 extends Migration
{
    public function up()
    {
        /*
         * Create unit table
        */
        if (!$this->getDb()->schema()->hasTable('unit')) {
            $this->getDb()->schema()->create('unit', function ($table) {
                $table->increments('id');
                $table->timestamps();
            });
        }

        /*
         * Create unit_translation table
         */
        if (!$this->getDb()->schema()->hasTable('unit_translation')) {
            $this->getDb()->schema()->create('unit_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->integer('unit_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('unit_id')->references('id')->on('unit')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop unit_translation table
         */
        if ($this->getDb()->schema()->hasTable('unit_translation')) {
            $this->getDb()->schema()->drop('unit_translation');
        }

        /*
         * Drop unit table
        */
        if ($this->getDb()->schema()->hasTable('unit')) {
            $this->getDb()->schema()->drop('unit');
        }
    }
}