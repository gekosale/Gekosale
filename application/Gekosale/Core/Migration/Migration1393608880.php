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
 * Migration1393608880
 *
 * This class has been auto-generated
 * by the Gekosale Console migrate:add command
 */
class Migration1393608880 extends Migration
{
    public function up()
    {
        /*
         * Create category table
         */
        if (!$this->getDb()->schema()->hasTable('category')) {
            $this->getDb()->schema()->create('category', function ($table) {
                $table->increments('id');
                $table->integer('hierarchy')->unsigned()->default(0);
                $table->integer('enabled')->default(1);
                $table->integer('parent_id')->unsigned()->nullable();
                $table->integer('file_id')->unsigned()->nullable();
                $table->timestamps();
                $table->foreign('parent_id')->references('id')->on('category')->onDelete('CASCADE')->onUpdate('NO ACTION');
                $table->foreign('file_id')->references('id')->on('file')->onDelete('SET NULL')->onUpdate('NO ACTION');
            });
        }

        /*
         * Create category_translation table
         */
        if (!$this->getDb()->schema()->hasTable('category_translation')) {
            $this->getDb()->schema()->create('category_translation', function ($table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->string('slug', 255);
                $table->text('short_description');
                $table->longText('description');
                $table->text('meta_keywords');
                $table->string('meta_title', 255);
                $table->text('meta_description');
                $table->integer('category_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->timestamps();
                $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade')->onUpdate('no action');
                $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade')->onUpdate('no action');
                $table->unique(Array('slug', 'language_id'));
            });
        }
    }

    public function down()
    {
        /*
         * Drop table category
         */
        if ($this->getDb()->schema()->hasTable('category')) {
            $this->getDb()->schema()->drop('category');
        }

        /*
         * Drop table category_translation
         */
        if ($this->getDb()->schema()->hasTable('category_translation')) {
            $this->getDb()->schema()->drop('category_translation');
        }
    }
}