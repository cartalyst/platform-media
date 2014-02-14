@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans('platform/media::general.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('dropzone.css', 'platform/media::css/dropzone.css') }}
{{ Asset::queue('media', 'platform/media::css/media.less') }}

{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'underscore') }}
{{ Asset::queue('dropzone.js', 'platform/media::js/dropzone/dropzone.js') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', 'dropzone') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
$(function() {

	var datagrid = $.datagrid('main', '.data-grid', '.data-grid_pagination', '.data-grid_applied', {
		loader: '.loading',
		paginationType: 'single',
		defaultSort: {
			column: 'created_at',
			direction: 'desc'
		},
		dividend: 1,
		threshold: 1,
		throttle: 24,
		callback: function() {

			$('.tip').tooltip({animation: false});

			if ( ! $('input:checkbox').is(':checked'))
			{
				$('[data-media-sidebar], [data-media-groups]').addClass('hide');
			}

		}
	});

	$.mediamanager('#mediaUploader', {
		updateUrl : '{{ URL::toAdmin('media/:id/edit') }}',
		deleteUrl : '{{ URL::toAdmin('media/:id/delete') }}',
		onSuccess : function() {

			datagrid._refresh();

		}
	});

	$('.data-grid_pagination').on('click', 'a', function() {

		$(document.body).animate({ scrollTop: $('.data-grid').offset().top }, 200);

	});

});
</script>
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('content')

{{-- Page header --}}
<div class="page-header">

	<div class="pull-right">

		<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline" role="form">

			<div class="form-group">

				<div class="loading"></div>

			</div>

			<div class="form-group" style="display:none;">
				<select class="form-control" name="column">
					<option value="all">{{{ trans('general.all') }}}</option>
					<option value="name">{{{ trans('platform/media::table.file_name') }}}</option>
					<option value="mime">{{{ trans('platform/media::table.mime') }}}</option>
				</select>
			</div>

			<div class="form-group">
				<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">
			</div>

			<button class="btn btn-default"><i class="fa fa-search"></i></button>

			<button class="btn btn-info" data-toggle="modal" data-target="#mediaModal"><i class="fa fa-plus"></i> Upload</button>

		</form>

	</div>

	<h1>{{{ trans('platform/media::general.title') }}}</h1>

</div>

<div class="row">

	<div class="col-xs-12 col-sm-9">
		<div class="data-grid" data-source="{{ URL::toAdmin('media/grid') }}" data-grid="main"></div>
	</div>

	<div class="col-xs-6 col-sm-3">

		{{-- Data Grid : Applied Filters --}}
		<div class="data-grid_applied" data-grid="main"></div>

		@if (count($tags) > 0)
		<span data-media-tags>

			<h4># Tags</h4>

			@foreach ($tags as $tag)
			<span class="label label-info" data-filter="tags:{{{ $tag }}}" data-grid="main">{{{ $tag }}}</span>
			@endforeach

		</span>
		@endif

		<span data-media-sidebar class="hide">

			@if (count($tags) > 0)<hr />@endif

			<h4><span data-media-total-selected></span> selected</h4>

			<div class="form-group">

				<select name="private" id="private" class="form-control">
					<option value="0">Public</option>
					<option value="1">Private</option>
				</select>

			</div>

			<div class="form-group hide" data-media-groups>

				<div class="controls">
					<select name="groups[]" id="groups" class="form-control" multiple="true">
					@foreach ($groups as $group)
						<option value="{{{ $group->id }}}">{{{ $group->name }}}</option>
					@endforeach
					</select>
				</div>

			</div>

			<div class="form-actions">

				<button data-media-update-selected id="update-selected" type="submit" class="btn btn-info btn-xs">Save changes</button>

				<button data-media-delete-selected id="delete-selected" type="submit" class="btn btn-danger btn-xs">{{{ trans('button.delete_selected') }}}</button>

			</div>

		</span>

	</div>

	<div class="clearfix"></div>

	{{-- Data Grid : Pagination --}}
	<div class="col-lg-12">

		<div class="data-grid_pagination" data-grid="main"></div>

	</div>

</div>

@include('platform/media::data-grid-tmpl')
@include('platform/media::data-grid_pagination-tmpl')
@include('platform/media::data-grid_applied-tmpl')
@include('platform/media::data-grid_no-results-tmpl')

<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content" style="width: 660px;">

			<div id="dropzone" style="height: 360px;overflow-y:scroll;">
				<form action="{{ URL::toAdmin('media/upload') }}" class="media-dropzone dz-clickable" id="mediaUploader">

					{{-- CSRF Token --}}
					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<div class="dz-default dz-message"></div>

				</form>
			</div>

			<div class="modal-footer" style="margin-top: 0;">
				<span class="pull-left text-left">
					<div data-media-total-files></div>
					<div data-media-total-size></div>
				</span>
				<button type="button" class="btn btn-success" data-media-upload><i class="fa fa-upload"></i> Start Upload</button>
			</div>

		</div>

	</div>

</div>

@stop
