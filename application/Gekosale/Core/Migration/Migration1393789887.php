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
 * Migration1393789887
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393789887 extends Migration
{
    public function up()
    {
        /*
         * Create producer table
        */
        if (!$this->getDb()->schema()->hasTable('producer')) {
            $this->getDb()->schema()->create('producer', function ($table) {
                $table->increments('id');
                $table->timestamps();
            });
        }

        /*
         * Create producer_translation table
         */
        if (!$this->getDb()->schema()->hasTable('producer_translation')) {
            $this->getDb()->schema()->create('producer_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->string('slug', 255);
                $table->text('short_description');
                $table->text('description');
                $table->text('meta_keywords');
                $table->string('meta_title', 255);
                $table->text('meta_description');
                $table->integer('producer_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('producer_id')->references('id')->on('producer')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }

        if (!$this->getDb()->schema()->hasTable('producer_deliverer')) {
            $this->getDb()->schema()->create('producer_deliverer', function ($table) {
                $table->increments('id');
                $table->integer('producer_id')->unsigned();
                $table->integer('deliverer_id')->unsigned();
                $table->timestamps();
                $table->foreign('producer_id')->references('id')->on('producer')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('deliverer_id')->references('id')->on('deliverer')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('producer_id', 'deliverer_id'));
            });
        }

        if (!$this->getDb()->schema()->hasTable('producer_shop')) {
            $this->getDb()->schema()->create('producer_shop', function ($table) {
                $table->increments('id');
                $table->integer('producer_id')->unsigned();
                $table->integer('shop_id')->unsigned();
                $table->timestamps();
                $table->foreign('producer_id')->references('id')->on('producer')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('shop_id')->references('id')->on('shop')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('producer_id', 'shop_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop producer_deliverer table
         */
        if ($this->getDb()->schema()->hasTable('producer_shop')) {
            $this->getDb()->schema()->drop('producer_shop');
        }

        /*
         * Drop producer_deliverer table
         */
        if ($this->getDb()->schema()->hasTable('producer_deliverer')) {
            $this->getDb()->schema()->drop('producer_deliverer');
        }

        /*
         * Drop producer_translation table
         */
        if ($this->getDb()->schema()->hasTable('producer_translation')) {
            $this->getDb()->schema()->drop('producer_translation');
        }

        /*
         * Drop producer table
        */
        if ($this->getDb()->schema()->hasTable('producer')) {
            $this->getDb()->schema()->drop('producer');
        }
    }
}