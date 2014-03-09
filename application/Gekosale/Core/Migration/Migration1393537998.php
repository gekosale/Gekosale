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
         * Create tax table
        */
        if (!$this->getDb()->schema()->hasTable('tax')) {
            $this->getDb()->schema()->create('tax', function ($table) {
                $table->increments('id');
                $table->decimal('value')->unique();
                $table->timestamps();
            });
        }

        /*
         * Create tax_translation table
         */
        if (!$this->getDb()->schema()->hasTable('tax_translation')) {
            $this->getDb()->schema()->create('tax_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->integer('tax_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('tax_id')->references('id')->on('tax')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop tax_translation table
         */
        if ($this->getDb()->schema()->hasTable('tax_translation')) {
            $this->getDb()->schema()->drop('tax_translation');
        }

        /*
         * Drop tax table
        */
        if ($this->getDb()->schema()->hasTable('tax')) {
            $this->getDb()->schema()->drop('tax');
        }
    }
}