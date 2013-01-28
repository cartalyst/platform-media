<?php

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
			$table->string('name');
			$table->string('file_path');
			$table->string('file_name');
			$table->string('file_extension');
			$table->string('file_mime_type');
			$table->integer('file_size')->unsigned();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->timestamps();
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
