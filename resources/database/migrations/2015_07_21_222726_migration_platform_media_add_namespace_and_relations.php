<?php

/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Media extension
 * @version    8.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformMediaAddNamespaceAndRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function ($table) {
            $table->string('namespace')->before('created_at')->nullable();
        });

        Schema::create('media_relations', function ($table) {
            $table->increments('id');
            $table->string('object_type');
            $table->integer('object_id')->unsigned();
            $table->integer('media_id')->unsigned();
            $table->tinyInteger('sort')->nullable();

            $table->engine = 'InnoDB';

            $table->index([ 'object_type', 'object_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('media_relations');

        Schema::table('media', function ($table) {
            $table->dropColumn('namespace');
        });
    }
}
