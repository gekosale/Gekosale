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
 * Migration1393684174
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393684174 extends Migration
{
    public function up()
    {
        /*
         * Create shop table
        */
        if (!$this->getDb()->schema()->hasTable('shop')) {
            $this->getDb()->schema()->create('shop', function ($table) {
                $table->increments('id');
                $table->string('url', 255);
                $table->integer('offline')->default(0);
                $table->integer('company_id')->unsigned();
                $table->timestamps();
                $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade')->onUpdate('no action');
            });
        }

        /*
         * Create shop_translation table
         */
        if (!$this->getDb()->schema()->hasTable('shop_translation')) {
            $this->getDb()->schema()->create('shop_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->text('meta_keywords');
                $table->string('meta_title', 255);
                $table->text('meta_description');
                $table->integer('shop_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('shop_id')->references('id')->on('shop')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop shop_translation table
         */
        if ($this->getDb()->schema()->hasTable('shop_translation')) {
            $this->getDb()->schema()->drop('shop_translation');
        }

        /*
         * Drop shop table
        */
        if ($this->getDb()->schema()->hasTable('shop')) {
            $this->getDb()->schema()->drop('shop');
        }
    }
}