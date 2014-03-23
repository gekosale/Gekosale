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
 * Migration1394375351
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1394375351 extends Migration
{
    public function up()
    {
        /*
         * Create product table
        */
        if (!$this->getDb()->schema()->hasTable('product')) {
            $this->getDb()->schema()->create('product', function ($table) {
                $table->increments('id');
                $table->string('sku', 64);
                $table->string('ean', 64);
                $table->integer('hierarchy')->unsigned()->default(0);
                $table->integer('enabled')->unsigned()->default(1);
                $table->decimal('buy_price', 15, 4)->unsigned()->default(0);
                $table->integer('buy_currency_id')->unsigned()->nullable();
                $table->decimal('sell_price', 15, 4)->unsigned()->default(0);
                $table->integer('sell_currency_id')->unsigned()->nullable();
                $table->integer('producer_id')->unsigned()->nullable();
                $table->integer('tax_id')->unsigned()->nullable();
                $table->integer('unit_id')->unsigned()->nullable();
                $table->integer('stock')->unsigned()->default(0);
                $table->integer('track_stock')->unsigned()->default(1);
                $table->decimal('weight', 15, 4)->unsigned()->default(0);
                $table->decimal('width', 15, 4)->unsigned()->default(0);
                $table->decimal('height', 15, 4)->unsigned()->default(0);
                $table->decimal('depth', 15, 4)->unsigned()->default(0);
                $table->decimal('package_size', 15, 4)->unsigned()->default(1);
                $table->timestamps();
                $table->foreign('buy_currency_id')->references('id')->on('currency')->onDelete('SET NULL')->onUpdate('NO ACTION');
                $table->foreign('sell_currency_id')->references('id')->on('currency')->onDelete('SET NULL')->onUpdate('NO ACTION');
                $table->foreign('producer_id')->references('id')->on('producer')->onDelete('SET NULL')->onUpdate('NO ACTION');
                $table->foreign('tax_id')->references('id')->on('tax')->onDelete('SET NULL')->onUpdate('NO ACTION');
                $table->foreign('unit_id')->references('id')->on('unit')->onDelete('SET NULL')->onUpdate('NO ACTION');
            });
        }

        if (!$this->getDb()->schema()->hasTable('product_shop')) {
            $this->getDb()->schema()->create('product_shop', function ($table) {
                $table->increments('id');
                $table->integer('product_id')->unsigned();
                $table->integer('shop_id')->unsigned();
                $table->timestamps();
                $table->foreign('product_id')->references('id')->on('product')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('shop_id')->references('id')->on('shop')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->unique(Array('product_id', 'shop_id'));
            });
        }
        
        if (!$this->getDb()->schema()->hasTable('product_category')) {
            $this->getDb()->schema()->create('product_category', function ($table) {
                $table->increments('id');
                $table->integer('product_id')->unsigned();
                $table->integer('category_id')->unsigned();
                $table->timestamps();
                $table->foreign('product_id')->references('id')->on('product')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('category_id')->references('id')->on('category')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->unique(Array('product_id', 'category_id'));
            });
        }

        if (!$this->getDb()->schema()->hasTable('product_deliverer')) {
            $this->getDb()->schema()->create('product_deliverer', function ($table) {
                $table->increments('id');
                $table->integer('product_id')->unsigned();
                $table->integer('deliverer_id')->unsigned();
                $table->timestamps();
                $table->foreign('product_id')->references('id')->on('product')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('deliverer_id')->references('id')->on('deliverer')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->unique(Array('product_id', 'deliverer_id'));
            });
        }

        if (!$this->getDb()->schema()->hasTable('product_photo')) {
            $this->getDb()->schema()->create('product_photo', function ($table) {
                $table->increments('id');
                $table->integer('product_id')->unsigned();
                $table->integer('file_id')->unsigned();
                $table->timestamps();
                $table->foreign('product_id')->references('id')->on('product')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('file_id')->references('id')->on('file')->onDelete('CASCADE')->onUpdate('NO ACTION');
            });
        }

        /*
         * Create product_translation table
         */
        if (!$this->getDb()->schema()->hasTable('product_translation')) {
            $this->getDb()->schema()->create('product_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->string('slug', 255);
                $table->text('short_description');
                $table->text('description');
                $table->text('long_description');
                $table->text('meta_keywords');
                $table->string('meta_title', 255);
                $table->text('meta_description');
                $table->integer('product_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('product_id')->references('id')->on('product')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->unique(Array('slug', 'language_id'));
            });
        }

        $this->getDb()->schema()->table('product', function ($table) {
            $table->integer('photo_id')->nullable()->unsigned()->after('unit_id');;
            $table->foreign('photo_id')->references('id')->on('file')->onDelete('SET NULL')->onUpdate('NO ACTION');
        });
    }

    public function down()
    {

    }
}