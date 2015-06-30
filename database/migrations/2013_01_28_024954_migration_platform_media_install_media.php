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
 * @version    2.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformMediaInstallMedia extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media', function($table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('path')->nullable();
			$table->string('extension')->nullable();
			$table->string('mime')->nullable();
			$table->integer('size')->nullable();
			$table->boolean('is_image')->default(0);
			$table->text('thumbnail')->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->boolean('private')->default(0);
			$table->text('roles')->nullable();
			$table->timestamps();

			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('media');
	}

}
