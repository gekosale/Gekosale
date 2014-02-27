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
 * Migration1393512866
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393512866 extends Migration
{
    public function up()
    {
        if (!$this->getDb()->schema()->hasTable('language')) {
            $this->getDb()->schema()->create('language', function ($table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('name', 12)->unique();
                $table->string('translation', 255);
                $table->integer('currency_id')->unsigned();
                $table->foreign('currency_id')->references('id')->on('currency')
                    ->onDelete('SET NULL')
                    ->onUpdate('NO ACTION');
            });
        }
    }

    public function down()
    {
        if ($this->getDb()->schema()->hasTable('language')) {
            $this->getDb()->schema()->drop('language');
        }
    }
}