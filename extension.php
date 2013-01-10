<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is a name for your extension that is for presentational purposes
	| only.
	|
	*/

	'name' => 'Content',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is a unique slug to describe the extension. This is the only
	| identifier for this extension and should not be changed as it will be
	| recognized as a new extension. Ideally, this should match the folder
	| structure within the extnsions folder though this is not required.
	|
	*/

	'slug' => 'platform/content',

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
	| One or two sentences describing the extension for users to view when
	| they are installing the extension
	|
	*/

	'description' => 'An extension to manage content.',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string version that can be used with
	| version_compare(). This is how we compare versions of extensions.
	|
	*/

	'version' => '2.0.0',

	/*
	|--------------------------------------------------------------------------
	| Is Core
	|--------------------------------------------------------------------------
	|
	| Specifies that the extension is a core extension and is installed when
	| Platform is installed. Typically you wouldn't use this.
	|
	*/

	'is_core' => true,

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List all extensions this extension requires to install / run etc. This
	| is used in conjunction with composer (you should put the same extension
	| dependencies in composer.json so they're resolved using composer),
	| however it can be used without composer, at which point you'll have to
	| ensure the required extensions are available.
	|
	*/

	'require' => array(
		'platform/menus',
	),

	/*
	|--------------------------------------------------------------------------
	| Autoload Logic
	|--------------------------------------------------------------------------
	|
	| Autoloading Logic for the Extension. It may either be 'composer', where
	| your composer.json file specifies the autoloading logic, 'platform',
	| where your extension receives convention autoloading based on Platform
	| standards, or a closure which takes two parameters, first is an instance of
	| Composer\Autoload\ClassLoader and second is Platform\Extensions\Extension.
	| The autoload must set appropriate classes and namespaces available when the
	| extension is started.
	|
	*/

	'autoload' => 'platform',

	/*
	|--------------------------------------------------------------------------
	| URI
	|--------------------------------------------------------------------------
	|
	| Specify the URI the this extension will respond to. You can choose to
	| specify a single string, where the URI will be matched on the admin and
	| public sections of Platform. You can provide an array with keys 'admin'
	| and 'public' to specify a different URI for admin and public sections and
	| even provide an 'override' which is an array of Extensions this extension
	| overrides it's URI from.
	|
	*/

	'uri' => 'content',

	/*
	|--------------------------------------------------------------------------
	| Boot Callback
	|--------------------------------------------------------------------------
	|
	| Closure which takes a Platform\Extnesions\Extension object as a parameter
	| and is called when the extension is booted. This can do whatever custom
	| logic is needed upon booting.
	|
	*/

	'boot' => function(Platform\Extensions\Extension $extension)
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Routes closure to be called when the Extension is started. Register any
	| custom routing logic here. This closure takes a
	| Platform\Extensions\Extension object as a parameter just in case you
	| need it.
	|
	*/

	'routes' => function(Platform\Extensions\Extension $extension)
	{
		
	},

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| List of custom widgets associated with the extension. Like routes, the
	| value for the widget key may either be a closure or a class & method name
	| (joined with an @ symbol). Of course, Platform will guess the widget
	| class for you, this is just for custom widgets or if you do not wish to
	| make a new class for a very small widget.
	|
	*/

	'widgets' => array(),

	/*
	|--------------------------------------------------------------------------
	| Plugins
	|--------------------------------------------------------------------------
	|
	| List of custom plugins associated with the extension. Like routes, the
	| value for the plugin key may either be a closure or a class & method name
	| (joined with an @ symbol). Of course, Platform will guess the plugin
	| class for you, this is just for custom plugins or if you do not wish to
	| make a new class for a very small plugin.
	|
	*/

	'plugins' => array(),

);