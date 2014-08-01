@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans('platform/media::general.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('dropzone.css', 'platform/media::css/dropzone.css') }}
{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'styles') }}

{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'underscore') }}
{{ Asset::queue('moment', 'moment/js/moment.js') }}
{{ Asset::queue('dropzone.js', 'platform/media::js/dropzone/dropzone.js') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', ['jquery', 'dropzone']) }}
{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
	jQuery(document).ready(function($)
	{
		var dg = $.datagrid('main', '.data-grid', '.data-grid_pagination', '.data-grid_applied', {
			loader: '.loading',
			scroll: '.data-grid',
			callback: function()
			{
				$('#checkAll').prop('checked', false);

				$('#actions').prop('disabled', true);
			}
		});

		$(document).on('click', '#checkAll', function()
		{
			$('input:checkbox').not(this).prop('checked', this.checked);

			var status = $('input[name="entries[]"]:checked').length > 0;

			$('#actions').prop('disabled', ! status);
		});

		$(document).on('click', 'input[name="entries[]"]', function()
		{
			var status = $('input[name="entries[]"]:checked').length > 0;

			$('#actions').prop('disabled', ! status);
		});

		$(document).on('click', '[data-action]', function(e)
		{
			e.preventDefault();

			var action = $(this).data('action');

			var url = '{{ URL::toAdmin('media') }}';

			var entries = $.map($('input[name="entries[]"]:checked'), function(e, i)
			{
				return +e.value;
			});

			if (action == 'email')
			{
				window.location = url + '/' + entries.join(',') + '/email';
			}
			else
			{
				$.ajax({
					type: 'POST',
					url: url,
					data: {
						action : action,
						entries: entries
					},
					success: function(response)
					{
						dg.refresh();
					}
				});
			}
		});

		$.mediamanager('#mediaUploader', {
			updateUrl : '{{ URL::toAdmin('media/:id/edit') }}',
			deleteUrl : '{{ URL::toAdmin('media/:id/delete') }}',
			onSuccess : function(response)
			{
				dg.refresh();
			}
		});

		$('#tags').selectize({
			maxItems: 4,
			create: true
		});
	});

	function bytesToSize(bytes)
	{
		if (bytes === 0) return '0 Bytes';

		var k = 1000;

		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

		var i = parseInt(Math.floor(Math.log(bytes) / Math.log(k)), 10);

		return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
	}
</script>
@stop

{{-- Inline styles --}}
@section('styles')
@parent
<style type="text/css">
tr { cursor: default; }
.highlight { background: lightblue; }
</style>
@stop

{{-- Page content --}}
@section('content')

{{-- Page header --}}
<div class="page-header">

	<h1>{{{ trans('platform/media::general.title') }}}</h1>

</div>

<div class="row">

	<div class="col-lg-6">

		{{-- Data Grid : Applied Filters --}}
		<div class="data-grid_applied" data-grid="main"></div>

	</div>

	<div class="col-lg-6 text-right">

		<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline" role="form">

			<div class="form-group">

				<div class="loading"></div>

			</div>

			@if ( ! empty($tags))
			<div class="btn-group text-left">

				<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
					Tags <span class="caret"></span>
				</button>

				<ul class="dropdown-menu" role="menu">
					@foreach ($tags as $tag)
					<li><a href="#" data-grid="main" data-filter="tags:{{{ $tag }}}">{{{ $tag }}}</a></li>
					@endforeach
				</ul>

			</div>
			@endif

			<div class="btn-group text-left">

				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					{{{ trans('general.filters') }}} <span class="caret"></span>
				</button>

				<ul class="dropdown-menu" role="menu">
					<li><a href="#" data-grid="main" data-reset>{{{ trans('general.show_all') }}}</a></li>
					<li><a href="#" data-grid="main" data-filter="private:0" data-label="private::Show Public" data-reset>Show Public</a></li>
					<li><a href="#" data-grid="main" data-filter="private:1" data-label="private::Show Private" data-reset>Show Private</a></li>
				</ul>

			</div>

			<div class="form-group has-feedback">

				<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">

				<span class="glyphicon fa fa-search form-control-feedback"></span>

			</div>

			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#mediaModal"><i class="fa fa-plus"></i> {{{ trans('button.upload') }}}</a>

		</form>

	</div>

</div>

<br />

<table data-source="{{ URL::toAdmin('media/grid') }}" data-grid="main" class="data-grid table _table-striped table-bordered _table-hover">
	<thead>
		<tr>
			<th class="_hide"><input type="checkbox" name="checkAll" id="checkAll"></th>
			<th data-sort="name" class="sortable" colspan="2">Name</th>
			<th data-sort="created_at" class="col-md-3 sortable">Uploaded At</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

{{-- Data Grid : Pagination --}}
<div class="data-grid_pagination" data-grid="main"></div>

@include('platform/media::grid/results')
@include('platform/media::grid/pagination')
@include('platform/media::grid/filters')
@include('platform/media::grid/no_results')

<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content" style="width: 660px;">

			<div id="dropzone" style="height: 360px;overflow-y:scroll;">
				<form action="{{ URL::toAdmin('media/upload') }}" class="media-dropzone dz-clickable" id="mediaUploader">

					{{-- CSRF Token --}}
					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<select placeholder="{{{ trans('platform/media::form.tags_help') }}}" id="tags" name="tags[]" multiple="multiple" tabindex="-1">
						@foreach ($tags as $tag)
						<option value="{{{ $tag }}}">{{{ $tag }}}</option>
						@endforeach
					</select>

					<div class="dz-default dz-message"></div>

				</form>
			</div>

			<div class="modal-footer" style="margin-top: 0;">

				<span class="pull-left text-left">
					<div data-media-total-files></div>
					<div data-media-total-size></div>
				</span>

				<button type="button" class="btn btn-success" data-media-upload><i class="fa fa-upload"></i> Start Upload</button>

				<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>

			</div>

		</div>

	</div>

</div>

@stop
