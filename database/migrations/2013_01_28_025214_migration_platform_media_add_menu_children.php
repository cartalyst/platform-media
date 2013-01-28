<?php

use Illuminate\Database\Migrations\Migration;
use Platform\Menus\Menu;

class MigrationPlatformMediaAddMenuChildren extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$admin = Menu::adminMenu();

		$media = new Menu(array(
			'slug'      => 'admin-media',
			'extension' => 'platform/media',
			'name'      => 'Media',
			'driver'    => 'static',
			'class'     => 'icon-picture',
			'uri'       => 'media'
		));

		$media->makeLastChildOf($admin);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if ($menu = Menu::find('admin-media'))
		{
			$menu->delete();
		}
	}

}
