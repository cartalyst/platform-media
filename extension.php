<?php
/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Media extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Illuminate\Foundation\Application;

return [

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| Your extension name (it's only required for presentational purposes).
	|
	*/

	'name' => 'Media Management',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| Your extension unique identifier and should not be changed as
	| it will be recognized as a whole new extension.
	|
	| Ideally, this should match the folder structure within the extensions
	| folder, but this is completely optional.
	|
	*/

	'slug' => 'platform/media',

	/*
	|--------------------------------------------------------------------------
	| Author
	|--------------------------------------------------------------------------
	|
	| Because everybody deserves credit for their work, right?
	|
	*/

	'author' => 'Cartalyst LLC',

	/*
	|--------------------------------------------------------------------------
	| Description
	|--------------------------------------------------------------------------
	|
	| One or two sentences describing what the extension do for
	| users to view when they are installing the extension.
	|
	*/

	'description' => 'Manage your website media.',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	|
	*/

	'version' => '1.0.0',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List here all the extensions that this extension requires to work.
	|
	| This is used in conjunction with composer, so you should put the
	| same extension dependencies on your main composer.json require
	| key, so that they get resolved using composer, however you
	| can use without composer, at which point you'll have to
	| ensure that the required extensions are available.
	|
	*/

	'require' => [

		'platform/admin',

	],

	/*
	|--------------------------------------------------------------------------
	| Autoload Logic
	|--------------------------------------------------------------------------
	|
	| You can define here your extension autoloading logic, it may either
	| be 'composer', 'platform' or a 'Closure'.
	|
	| If composer is defined, your composer.json file specifies the autoloading
	| logic.
	|
	| If platform is defined, your extension receives convetion autoloading
	| based on the Platform standards.
	|
	| If a Closure is defined, it should take two parameters as defined
	| bellow:
	|
	|	object \Composer\Autoload\ClassLoader      $loader
	|	object \Illuminate\Foundation\Application  $app
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| Service Providers
	|--------------------------------------------------------------------------
	|
	| Define your extension service providers here. They will be dynamically
	| registered without having to include them in app/config/app.php.
	|
	*/

	'providers' => [

		'Platform\Media\MediaServiceProvider',

	],

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| any custom routing logic here.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{
		Route::group(['namespace' => 'Platform\Media\Controllers'], function()
		{
			Route::group(['prefix' => admin_uri().'/media', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'MediaController@index');
				Route::post('/', 'MediaController@executeAction');
				Route::get('grid', 'MediaController@grid');
				Route::post('upload', 'MediaController@upload');
				Route::get('{id}/edit', 'MediaController@edit');
				Route::post('{id}/edit', 'MediaController@update');
				Route::get('{id}/email', 'MediaMailerController@index');
				Route::post('{id}/email', 'MediaMailerController@process');
				Route::post('{id}/delete', 'MediaController@delete');
			});

			Route::group(['prefix' => 'media', 'namespace' => 'Frontend'], function()
			{
				Route::get('download/{id}', 'MediaController@download')->where('id', '.*?');
				Route::get('view/{id}', 'MediaController@view')->where('id', '.*?');
			});
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in the user
	| management area to build a graphical interface where permissions
	| can be selected to allow or deny user access.
	|
	| You can protect single or multiple controller methods at once.
	|
	| When writing permissions, if you put a 'key' => 'value' pair, the 'value'
	| will be the label for the permission which is going to be displayed
	| when editing the permissions.
	|
	| The permissions should follow the following structure:
	|
	|     vendor/extension::area.controller@method
	|     vendor/extension::area.controller@method1,method2, ...
	|
	| Examples:
	|
	|    Platform\Users\Controllers\Admin\UsersController@index
	|
	|      =>  platform/users::admin.usersController@index
	|
	|    Platform\Users\Controllers\Admin\UsersController@index
	|    Platform\Users\Controllers\Admin\UsersController@grid
	|
	|      =>  platform/users::admin.usersController@index,grid
	|
	*/

	'permissions' => function()
	{
		return [

			'Platform\Media\Controllers\Admin\MediaController@index,grid'  => Lang::get('platform/media::permissions.index'),
			'Platform\Media\Controllers\Admin\MediaController@upload'      => Lang::get('platform/media::permissions.upload'),
			'Platform\Media\Controllers\Admin\MediaController@edit,update' => Lang::get('platform/media::permissions.edit'),
			'Platform\Media\Controllers\Admin\MediaController@delete'      => Lang::get('platform/media::permissions.delete'),

		];
	},

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register any settings for your extension. You can also configure
	| the namespace and group that a setting belongs to.
	|
	*/

	'settings' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	|
	| You can provide a recursive array of menu children and their children.
	|
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for
	| extensions installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing the file "app/config/platform.php".
	|
	*/

	'menus' => [

		'admin' => [

			[
				'slug'  => 'admin-media',
				'name'  => 'Media',
				'class' => 'fa fa-picture-o',
				'uri'   => 'media',
				'regex' => '/admin\/media/i',
			],

		],

	],

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| all your custom widgets here. Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => function()
	{

	},

];
