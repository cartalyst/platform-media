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
 * @version    7.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Illuminate\Contracts\Foundation\Application;
use Cartalyst\Permissions\Container as Permissions;
use Illuminate\Contracts\Routing\Registrar as Router;

return [

    /*
    |--------------------------------------------------------------------------
    | Slug
    |--------------------------------------------------------------------------
    |
    | This is the extension unique identifier and should not be
    | changed as it will be recognized as a new extension.
    |
    | Note:
    |
    |   Ideally this should match the folder structure within the
    |   extensions folder, however this is completely optional.
    |
    */

    'slug' => 'platform/media',

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | This is the extension name, used mainly for presentational purposes.
    |
    */

    'name' => 'Media Management',

    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    |
    | A brief sentence describing what the extension does.
    |
    */

    'description' => 'Manage your website media.',

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | This is the extension version and it should be set as a string
    | so it can be used with the version_compare() function.
    |
    */

    'version' => '7.0.0',

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
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Define here all the extensions that this extension depends on to work.
    |
    | Note:
    |
    |   This is used in conjunction with Composer, so you should put the
    |   exact same dependencies on the extension composer.json require
    |   array, so that they get resolved automatically by Composer.
    |
    |   However you can use without Composer, at which point you will
    |   have to ensure that the required extensions are available!
    |
    */

    'requires' => [

        'platform/access',

    ],

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Define here your extension service providers. They will be dynamically
    | registered without having to include them in config/app.php file.
    |
    */

    'providers' => [

        Platform\Media\Providers\MediaServiceProvider::class,

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
    |   object \Illuminate\Contracts\Routing\Registrar  $router
    |	object \Cartalyst\Extensions\ExtensionInterface  $extension
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'routes' => function (Router $router, ExtensionInterface $extension, Application $app) {
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
                Route::post('upload_redactor', ['as' => 'admin.media.upload_redactor', 'uses' => 'MediaController@uploadRedactor']);
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
    | The closure parameters are:
    |
    |   object \Cartalyst\Permissions\Container  $permissions
    |	object \Illuminate\Contracts\Foundation\Application  $app
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

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'upload, uploadRedactor, linkMedia');
            });

            $g->permission('media.edit', function ($p) {
                $p->label = trans('platform/media::permissions.edit');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'edit, update');
            });

            $g->permission('media.delete', function ($p) {
                $p->label = trans('platform/media::permissions.delete');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'delete');
            });

            $g->permission('media.bulk_actions', function ($p) {
                $p->label = trans('platform/media::permissions.bulk_actions');

                $p->controller('Platform\Media\Controllers\Admin\MediaController', 'executeAction');
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
    | The closure parameters are:
    |
    |   object \Cartalyst\Settings\Repository  $settings
    |	object \Illuminate\Contracts\Foundation\Application  $app
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
    | found by editing the file "config/platform.php".
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

];
