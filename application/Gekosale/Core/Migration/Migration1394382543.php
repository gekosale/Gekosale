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
 * Migration1394382543
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1394382543 extends Migration
{
    public function up()
    {
        if (!$this->getDb()->schema()->hasTable('shipping_method')) {
            $this->getDb()->schema()->create('shipping_method', function ($table) {
                $table->increments('id');
                $table->integer('hierarchy')->unsigned()->default(0);
                $table->integer('enabled')->default(1);
                $table->integer('type')->default(1);
                $table->integer('file_id')->unsigned()->nullable();
                $table->timestamps();
                $table->foreign('file_id')->references('id')->on('file')->onDelete('SET NULL')->onUpdate('NO ACTION');
            });
        }

        if (!$this->getDb()->schema()->hasTable('shipping_method_translation')) {
            $this->getDb()->schema()->create('shipping_method_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->integer('shipping_method_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('shipping_method_id')->references('id')->on('shipping_method')->onDelete('cascade')->onUpdate('NO ACTION');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->unique(Array('name', 'language_id'));
            });
        }

        if (!$this->getDb()->schema()->hasTable('shipping_method_shop')) {
            $this->getDb()->schema()->create('shipping_method_shop', function ($table) {
                $table->increments('id');
                $table->integer('shipping_method_id')->unsigned();
                $table->integer('shop_id')->unsigned();
                $table->timestamps();
                $table->foreign('shipping_method_id')->references('id')->on('shipping_method')->onDelete('cascade')->onUpdate('NO ACTION');
                $table->foreign('shop_id')->references('id')->on('shop')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->unique(Array('shipping_method_id', 'shop_id'));
            });
        }

        if (!$this->getDb()->schema()->hasTable('shipping_method_cost')) {
            $this->getDb()->schema()->create('shipping_method_cost', function ($table) {
                $table->increments('id');
                $table->integer('shipping_method_id')->unsigned();
                $table->decimal('from',15,4)->default(0);
                $table->decimal('to', 15,4)->default(0);
                $table->decimal('cost', 15,4)->default(0);
                $table->integer('tax_id')->unsigned()->default(null)->nullable();
                $table->timestamps();
                $table->foreign('shipping_method_id')->references('id')->on('shipping_method')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('tax_id')->references('id')->on('tax')->onDelete('CASCADE')->onUpdate('NO ACTION');
            });
        }
    }

    public function down()
    {

    }
}