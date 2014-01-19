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
				$('#delete-selected').prop('disabled', true);
			}

		}
	});

	$.mediamanager('#mediaUploader', {
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
<style type="text/css">
.thumbnail:hover a.delete {
	display: block;
	opacity: 1;
}
.thumbnail a.delete {
	position: absolute;
	xtop: 2px;
	xright: 2px;
	background: white;
	letter-spacing: -99999px;
	width: 15px;
	height: 15px;
	box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.4);
	-moz-box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.4);
	-webkit-border-radius: 0px 2px 0px 0px;
	-moz-border-radius: 0px 2px 0px 0px;
	border-radius: 0px 2px 0px 0px;
	-webkit-border-radius: 0px 0px 0px 3px;
	-moz-border-radius: 0px 0px 0px 3px;
	border-radius: 0px 0px 0px 3px;
	cursor: pointer;
	display: none;
	opacity: 0;
}

.media-name {
	background:#eee; border-top: 1px dotted #ccc; xmargin:1em; padding:5px;
	overflow:hidden; white-space:nowrap; text-overflow:ellipsis; width: 169px;
	border-radius: 0 0 3px 3px;
}
</style>
@stop

{{-- Page content --}}
@section('content')

<div class="col-md-12">

	{{-- Page header --}}
	<div class="page-header">

		<span class="pull-right">

			<span class="btn btn-warning" data-toggle="modal" data-target="#mediaModal"><i class="fa fa-plus"></i> Upload</span>

		</span>

		<h1>{{{ trans('platform/media::general.title') }}}</h1>

	</div>

	<div class="row hide">

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
					<select class="form-control" name="column">
						<option value="all">{{{ trans('general.all') }}}</option>
						<option value="name">{{{ trans('platform/media::table.file_name') }}}</option>
						<option value="created_at">{{{ trans('platform/media::table.created_at') }}}</option>
					</select>
				</div>

				<div class="form-group">
					<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">
				</div>

				<button class="btn btn-default"><i class="fa fa-search"></i></button>

			</form>

		</div>

	</div>

	<br />

	<form action="{{ URL::toAdmin('media/delete') }}" method="post">

		{{-- CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="col-md-9">
		<div class="data-grid" data-source="{{ URL::toAdmin('media/grid') }}" data-grid="main"></div>
</div>

<div class="col-md-3">

	<div data-media-delete-box class="hide">

		<h4>Mass <small>Delete</small></h4>

		1 item(s) selected
		<br>

		<button data-media-delete-selected id="delete-selected" type="submit" class="btn btn-danger btn-xs">{{{ trans('button.delete_selected') }}}</button>

	</div>

	<!--
	<h4>Assign Tags <small>to selected items</small></h4>

	form


	<h4>Tags <small>Filter by Tag</small></h4>

	<hr>

	<ul class="nav nav-pills nav-stacked">
		<li><a href="#">Foo</a></li>
	</ul>
	-->

</div>
		<div class="clearfix"></div>

		{{-- Data Grid : Pagination --}}
		<div class="data-grid_pagination" data-grid="main"></div>

	</form>

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
