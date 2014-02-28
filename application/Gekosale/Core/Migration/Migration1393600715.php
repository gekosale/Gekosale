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
 * Migration1393600715
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393600715 extends Migration
{
    public function up()
    {
        /*
         * Create deliverer table
        */
        if (!$this->getDb()->schema()->hasTable('deliverer')) {
            $this->getDb()->schema()->create('deliverer', function ($table) {
                $table->increments('id');
                $table->timestamps();
            });
        }

        /*
         * Create deliverer_translation table
         */
        if (!$this->getDb()->schema()->hasTable('deliverer_translation')) {
            $this->getDb()->schema()->create('deliverer_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 64);
                $table->integer('deliverer_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('deliverer_id')->references('id')->on('deliverer')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('name', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop deliverer_translation table
         */
        if ($this->getDb()->schema()->hasTable('deliverer_translation')) {
            $this->getDb()->schema()->drop('deliverer_translation');
        }

        /*
         * Drop deliverer table
        */
        if ($this->getDb()->schema()->hasTable('deliverer')) {
            $this->getDb()->schema()->drop('deliverer');
        }
    }
}