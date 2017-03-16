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
 * @version    6.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

var Extension;

;(function(window, document, $, undefined)
{
	'use strict';

	Extension = Extension || {
		Form: {},
	};

	// Initialize functions
	Extension.Form.init = function()
	{
		Extension.Form.selectize();
		Extension.Form.listeners();
	};

	// Add Listeners
	Extension.Form.listeners = function()
	{
		Platform.Cache.$body
			.on('change', '#private', Extension.Form.private)
		;
	};

	Extension.Form.private = function()
	{
		if ($(this).val() == 1)
		{
			$('[data-roles]').removeClass('hide');
		}
		else
		{
			$('[data-roles]').addClass('hide');
		}
	};

	// Initialize Bootstrap Popovers
	Extension.Form.selectize = function ()
	{
		$('#tags').selectize({
			create: true,
			sortField: 'text'
		});
	};

	// Job done, lets run.
	Extension.Form.init();

})(window, document, jQuery);
