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
        if (!$this->getDb()->schema()->hasTable('routes')) {
            $this->getDb()->schema()->create('routes', function ($table) {
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
    }

    public function down()
    {

    }
}