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
 * Migration1393713732
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393713732 extends Migration
{
    public function up()
    {
        /*
         * Create contact table
        */
        if (!$this->getDb()->schema()->hasTable('contact')) {
            $this->getDb()->schema()->create('contact', function ($table) {
                $table->increments('id');
                $table->integer('is_enabled')->default(0);
                $table->timestamps();
            });
        }

        /*
         * Create contact_translation table
         */
        if (!$this->getDb()->schema()->hasTable('contact_translation')) {
            $this->getDb()->schema()->create('contact_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->string('email', 255);
                $table->string('phone', 255);
                $table->string('street', 255);
                $table->string('streetno', 255);
                $table->string('flatno', 12);
                $table->string('postcode', 12);
                $table->string('province', 255);
                $table->string('city', 255);
                $table->string('country', 12);
                $table->integer('contact_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('contact_id')->references('id')->on('contact')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
            });
        }
    }

    public function down()
    {
        /*
         * Drop contact_translation table
         */
        if ($this->getDb()->schema()->hasTable('contact_translation')) {
            $this->getDb()->schema()->drop('contact_translation');
        }

        /*
         * Drop contact table
        */
        if ($this->getDb()->schema()->hasTable('contact')) {
            $this->getDb()->schema()->drop('contact');
        }
    }
}