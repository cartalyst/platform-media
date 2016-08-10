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
 * @version    3.3.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Illuminate\Contracts\Foundation\Application;
use Cartalyst\Permissions\Container as Permissions;

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

    'version' => '3.3.3',

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

        'platform/access',

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
    |	object \Composer\Autoload\ClassLoader  $loader
    |	object \Illuminate\Contracts\Foundation\Application  $app
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

        'Platform\Media\Providers\MediaServiceProvider',

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
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'routes' => function (ExtensionInterface $extension, Application $app) {
        if (! $app->routesAreCached()) {
            Route::group([
                'prefix'    => admin_uri().'/media',
                'namespace' => 'Platform\Media\Controllers\Admin',
            ], function () {
                Route::get('/', ['as' => 'admin.media.all', 'uses' => 'MediaController@index']);
                Route::post('/', ['as' => 'admin.media.all', 'uses' => 'MediaController@executeAction']);

                Route::get('grid', ['as' => 'admin.media.grid', 'uses' => 'MediaController@grid']);

                Route::get('files_list', ['as' => 'admin.media.files_list', 'uses' => 'MediaController@filesList']);
                Route::get('images_list', ['as' => 'admin.media.images_list', 'uses' => 'MediaController@imagesList']);

                Route::post('upload', ['as' => 'admin.media.upload', 'uses' => 'MediaController@upload']);
                Route::post('link_media', ['as' => 'admin.media.link_media', 'uses' => 'MediaController@linkMedia']);

                Route::get('email/{id}', ['as' => 'admin.media.email', 'uses' => 'MediaMailerController@index']);
                Route::post('email/{id}', ['as' => 'admin.media.email', 'uses' => 'MediaMailerController@process']);

                Route::get('{id}', ['as' => 'admin.media.edit', 'uses' => 'MediaController@edit']);
                Route::post('{id}', ['as' => 'admin.media.edit', 'uses' => 'MediaController@update']);
                Route::delete('{id}', ['as' => 'admin.media.delete', 'uses' => 'MediaController@delete']);
            });

            Route::group([
                'prefix'    => 'media',
                'namespace' => 'Platform\Media\Controllers\Frontend',
            ], function () {
                Route::get('view/{id}', ['as' => 'media.view', 'uses' => 'MediaController@view'])->where('id', '.*?');
                Route::get('download/{id}', ['as' => 'media.download', 'uses' => 'MediaController@download'])->where('id', '.*?');
            });
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Register here all the permissions that this extension has. These will
    | be shown in the user management area to build a graphical interface
    | where permissions can be selected to allow or deny user access.
    |
    | For detailed instructions on how to register the permissions, please
    | refer to the following url https://cartalyst.com/manual/permissions
    |
    */

    'permissions' => function (Permissions $permissions, Application $app) {
        $permissions->group('media', function ($g) {
            $g->name = 'Media';

            $g->permission('media.index', function ($p) {
                $p->label = trans('platform/media::permissions.index');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'index, grid, filesList, imagesList');
            });

            $g->permission('media.upload', function ($p) {
                $p->label = trans('platform/media::permissions.upload');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'upload, linkMedia');
            });

            $g->permission('media.edit', function ($p) {
                $p->label = trans('platform/media::permissions.edit');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'edit, update');
            });

            $g->permission('media.delete', function ($p) {
                $p->label = trans('platform/media::permissions.delete');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'delete');
            });
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Register here all the settings that this extension has.
    |
    | For detailed instructions on how to register the settings, please
    | refer to the following url https://cartalyst.com/manual/settings
    |
    */

    'settings' => function (Settings $settings, Application $app) {

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
                'regex' => '/:admin\/media/i',
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

    'widgets' => null,

];
