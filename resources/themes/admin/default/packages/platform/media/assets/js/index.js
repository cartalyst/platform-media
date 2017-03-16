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
		Index: {},
		Config: {},
		lastChecked: null,
	};

	// Initialize functions
	Extension.Index.init = function()
	{
		Extension.Index
			.listeners()
			.datePicker()
			.dataGrid()
			.mediaManager()
		;
	};

	// Add Listeners
	Extension.Index.listeners = function()
	{
		Platform.Cache.$body
			.on('click', '[data-grid-row]', Extension.Index.checkRow)
			.on('click', '[data-grid-row] a', Extension.Index.titleClick)
			.on('click', '[data-grid-checkbox]', Extension.Index.checkboxes)
			.on('click', '.media-item', Extension.Index.shiftMultiSelect)
			.on('click', '#modal-confirm button.confirm', Extension.Index.bulkActions)
			.on('click', '[data-grid-calendar-preset]', Extension.Index.calendarPresets)
			.on('click', '[data-grid-bulk-action]:not([data-grid-bulk-action="delete"])', Extension.Index.bulkActions)
		;

		return this;
	};

	// Date range picker initialization
	Extension.Index.datePicker = function()
	{
		var startDate, endDate, config, filter;

		var filters = _.compact(
			String(window.location.hash.slice(1)).split('/')
		);

		config = {
			opens: 'left'
		};

		_.each(filters, function(route)
		{
			filter = route.split(':');

			if (filter[0] === 'created_at' && filter[1] !== undefined && filter[2] !== undefined)
			{
				startDate = moment(filter[1]);

				endDate = moment(filter[2]);
			}
		});

		if (startDate && endDate)
		{
			config = {
				startDate: startDate,
				endDate: endDate,
				opens: 'left',
			};
		}

		Platform.Cache.$body.on('click', '.range_inputs .applyBtn', function()
		{
			$('input[name="daterangepicker_start"]').trigger('change');
		});

		Extension.Index.datePicker = $('[data-grid-calendar]').daterangepicker(config, function(start, end, label)
		{
			$('input[name="daterangepicker_start"]').trigger('change');
		});

		$('.daterangepicker_start_input').attr('data-grid', 'main');

		$('.daterangepicker_end_input').attr('data-grid', 'main');

		$('input[name="daterangepicker_start"]')
			.attr('data-grid-type', 'range')
			.attr('data-grid-query', 'created_at:>:' + $('input[name="daterangepicker_start"]').val())
			.attr('data-grid-range', 'start')
			.attr('data-grid-filter', 'created_at')
			.attr('data-grid-label', 'Created At');

		$('input[name="daterangepicker_end"]')
			.attr('data-grid-type', 'range')
			.attr('data-grid-query', 'created_at:<:' + $('input[name="daterangepicker_end"]').val())
			.attr('data-grid-range', 'end')
			.attr('data-grid-filter', 'created_at')
			.attr('data-grid-label', 'Created At');

		return this;
	};

	// Data Grid initialization
	Extension.Index.dataGrid = function()
	{
		var config = {
			pagination: {
				scroll: '#data-grid',
			},
			loader: {
				element: '.loading'
			},
			callback: function()
			{
				$('[data-grid-checkbox-all]').prop('checked', false);

				$('[data-action]').prop('disabled', true);

				Extension.Index
					.bulkStatus()
					.exporterStatus(this)
				;
			}
		};

		Extension.Index.DataGridManager = new DataGridManager();

		Extension.Index.Grid = Extension.Index.DataGridManager.create('main', config);

		return this;
	};

	// Handle Data Grid checkboxes
	Extension.Index.checkboxes = function(event)
	{
		event.stopPropagation();

		var type = $(this).attr('data-grid-checkbox');

		if (type === 'all')
		{
			$('[data-grid-checkbox]').not(this).not('[data-grid-checkbox][disabled]').prop('checked', this.checked);

			$('[data-grid-row]').not('[data-grid-row][disabled]').not(this).toggleClass('active', this.checked);
		}

		// Multi Select
		if(!Extension.lastChecked) {
			Extension.lastChecked = this;
		}

		if(event.shiftKey) {
			var start = $('[data-grid-checkbox]').index(this);
			var end = $('[data-grid-checkbox]').index(Extension.lastChecked);

			$('[data-grid-checkbox]').slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', Extension.lastChecked.checked);

			if(Extension.lastChecked.checked){
				$('[data-grid-checkbox]').slice(Math.min(start,end), Math.max(start,end)+ 1).parents('[data-grid-row]').not('[data-grid-row][disabled]').addClass('active');
			} else {
				$('[data-grid-checkbox]').slice(Math.min(start,end), Math.max(start,end)+ 1).parents('[data-grid-row]').not('[data-grid-row][disabled]').removeClass('active');
			}
		} else {
			$(this).parents('[data-grid-row]').not('[data-grid-row][disabled]').toggleClass('active');
		}

		Extension.Index.bulkStatus();

		Extension.lastChecked = this;
	};

	// Handle Data Grid shift multi select
	Extension.Index.shiftMultiSelect = function(event)
	{
		event.stopPropagation();

		var checkbox = $(this).find('[data-grid-checkbox]')[0];

		if(!Extension.lastChecked) {
			Extension.lastChecked = checkbox;
		}

		if(event.shiftKey) {
			var start = $('[data-grid-checkbox]').index(checkbox);
			var end = $('[data-grid-checkbox]').index(Extension.lastChecked);

			$('[data-grid-checkbox]').slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', Extension.lastChecked.checked);
		}

		Extension.Index.bulkStatus();

		Extension.lastChecked = checkbox;

		// Remove Selection
		if (window.getSelection) {
		  if (window.getSelection().empty) {  // Chrome
			window.getSelection().empty();
		  } else if (window.getSelection().removeAllRanges) {  // Firefox
			window.getSelection().removeAllRanges();
		  }
		} else if (document.selection) {  // IE?
		  document.selection.empty();
		}
	};

	// Handle Data Grid row checking
	Extension.Index.checkRow = function()
	{
		if ($(this).find('[data-grid-checkbox]').prop('disabled')) return false;

		$(this).toggleClass('active');

		var checkbox = $(this).find('[data-grid-checkbox]');

		checkbox.prop('checked', ! checkbox.prop('checked'));

		Extension.Index.bulkStatus();
	};

	Extension.Index.bulkStatus = function()
	{
		var rows = $('[data-grid-checkbox]').not('[data-grid-checkbox="all"]').not('[data-grid-checkbox][disabled]').length;

		var checked = $('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]').not('[data-grid-checkbox][disabled]').length;

		$('[data-grid-bulk-action]').closest('li').toggleClass('disabled', ! checked);

		if (checked > 0)
		{
			$('[data-grid-bulk-action="delete"]').attr('data-modal', true);
		}
		else
		{
			$('[data-grid-bulk-action="delete"]').removeAttr('data-modal');
		}

		$('[data-grid-checkbox="all"]')
			.prop('disabled', rows < 1)
			.prop('checked', rows < 1 ? false : rows === checked)
		;

		return this;
	};

	Extension.Index.exporterStatus = function(grid)
	{
		$('[data-grid-exporter]').closest('li').toggleClass('disabled', grid.pagination.filtered == 0);

		return this;
	};

	// Handle Data Grid bulk actions
	Extension.Index.bulkActions = function(event)
	{
		event.preventDefault();

		var url = window.location.origin + window.location.pathname;

		var action = $(this).data('grid-bulk-action') ? $(this).data('grid-bulk-action') : 'delete';

		var rows = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]'), function(event)
		{
			return +event.value;
		});

		if (rows.length > 0)
		{
			if (action == 'email')
			{
				window.location = Extension.Config.emailRoute.replace('rows-ids', rows.join(','));
			}
			else
			{
				$.ajax({
					type: 'POST',
					url: url,
					data: {
						action : action,
						rows   : rows
					},
					success: function(response)
					{
						Extension.Index.Grid.refresh(true);
					}
				});
			}
		}
	};

	// Handle Data Grid calendar
	Extension.Index.calendarPresets = function(event)
	{
		event.preventDefault();

		var start, end;

		switch ($(this).data('grid-calendar-preset'))
		{
			case 'day':
				start = end = moment().subtract(1, 'day').startOf('day').format('YYYY-MM-DD');
			break;

			case 'week':
				start = moment().startOf('week').format('YYYY-MM-DD');
				end   = moment().endOf('week').format('YYYY-MM-DD');
			break;

			case 'month':
				start = moment().startOf('month').format('YYYY-MM-DD');
				end   = moment().endOf('month').format('YYYY-MM-DD');
			break;

			default:
		}

		$('input[name="daterangepicker_start"]').val(start);

		$('input[name="daterangepicker_end"]').val(end);

		$('.range_inputs .applyBtn').trigger('click');
	};

	// Ignore row selection on title click
	Extension.Index.titleClick = function(event)
	{
		event.stopPropagation();
	};

	Extension.Index.mediaManager = function()
	{
		Extension.Index.MediaManager = $.mediamanager({
			onFileQueued : function(file)
			{
				$('input.file-tags').not('.selectize-control').selectize({
					delimiter: ',',
					persist: false,
					maxItems: 3,
					create: function(input) {
						return {
							value: input,
							text: input
						}
					}
				});

				$('.upload__instructions').hide();
			},
			onComplete : function()
			{
				$('#media-modal').modal('hide');

				$('.upload__instructions').show();

				Extension.Index.Grid.refresh(true);
			},
			onFail : function(e)
			{
				// alert(e.responseText);
			},
			onRemove : function(manager, file)
			{
				if (manager.totalFiles == 0)
				{
					$('.upload__instructions').show();
				}
			}
		});

		return this;
	};

	Extension.Index.setEmailRoute = function(url)
	{
		Extension.Config.emailRoute = url;

		return this;
	};

	// Job done, lets run.
	Extension.Index.init();

})(window, document, jQuery);
