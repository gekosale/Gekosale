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
 * Migration1393625996
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393625996 extends Migration
{
    public function up()
    {
        /*
         * Create availability table
        */
        if (!$this->getDb()->schema()->hasTable('availability')) {
            $this->getDb()->schema()->create('availability', function ($table) {
                $table->increments('id');
                $table->timestamps();
            });
        }

        /*
         * Create availability_translation table
         */
        if (!$this->getDb()->schema()->hasTable('availability_translation')) {
            $this->getDb()->schema()->create('availability_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->text('description');
                $table->integer('availability_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('availability_id')->references('id')->on('availability')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop availability_translation table
         */
        if ($this->getDb()->schema()->hasTable('availability_translation')) {
            $this->getDb()->schema()->drop('availability_translation');
        }

        /*
         * Drop availability table
        */
        if ($this->getDb()->schema()->hasTable('availability')) {
            $this->getDb()->schema()->drop('availability');
        }
    }
}