@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans('platform/media::general.title') }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('underscore', 'js/underscore/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/cartalyst/data-grid.js', 'underscore') }}
{{ Asset::queue('dropzone.js', 'platform/media::js/dropzone/dropzone.js') }}
{{ Asset::queue('dropzone.css', 'platform/media::css/dropzone.css') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', 'dropzone') }}
{{ Asset::queue('media', 'platform/media::css/media.less') }}

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
		throttle: 12,
		callback: function() {

			$('.tip').tooltip();

			if ( ! $('input:checkbox').is(':checked'))
			{
				$('[data-media-delete-box]').addClass('hide');
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

<div class="col-md-12">

	<div class="row">

		{{-- Data Grid : Applied Filters --}}
		<div class="col-lg-7">

			<div class="data-grid_applied" data-grid="main"></div>

		</div>

		<div class="col-lg-5 text-right">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline" role="form">

				<div class="form-group">

					<div class="loading"></div>

				</div>

				<div class="form-group">
					<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">
				</div>

				<span class="btn btn-warning" data-toggle="modal" data-target="#mediaModal"><i class="fa fa-plus"></i> Upload</span>

			</form>

		</div>

	</div>

	<hr>


	<div class="row">

		{{-- CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="col-md-12">

			<div data-media-delete-box class="hide">

				<h4>Mass <small>Delete</small></h4>

				1 item(s) selected
				<br>

				<button data-media-delete-selected id="delete-selected" type="submit" class="btn btn-danger btn-xs">{{{ trans('button.delete_selected') }}}</button>

			</div>

		</div>

	</div>

	<div class="row">

		<div class="col-md-12">

			<div class="data-grid" data-source="{{ URL::toAdmin('media/grid') }}" data-grid="main"></div>

		</div>

	</div>

	<div class="clearfix"></div>

	{{-- Data Grid : Pagination --}}
	<div class="data-grid_pagination" data-grid="main"></div>

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
