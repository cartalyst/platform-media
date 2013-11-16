@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans('platform/media::general.title') }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('underscore', 'js/underscore/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/cartalyst/data-grid.js', 'underscore') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
$(function() {

	$.datagrid('main', '.data-grid', '.data-grid_pagination', '.data-grid_applied', {
		loader: '.loading',
		paginationType: 'single',
		defaultSort: {
			column: 'created_at',
			direction: 'desc'
		},
		callback: function() {

			$('.tip').tooltip();

		}
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

	{{-- Page header --}}
	<div class="page-header">

		<span class="pull-right">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline" role="form">

				<div class="form-group">

					<div class="loading"></div>

				</div>

				<div class="form-group">
					<select class="form-control" name="column">
						<option value="all">{{{ trans('general.all') }}}</option>
						<option value="name">{{{ trans('platform/media::table.name') }}}</option>
						<option value="created_at">{{{ trans('platform/media::table.created_at') }}}</option>
					</select>
				</div>

				<div class="form-group">
					<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">
				</div>

				<button class="btn btn-default"><i class="fa fa-search"></i></button>

			</form>

		</span>

		<h1>{{{ trans('platform/media::general.title') }}}</h1>

	</div>

	<div class="row">

		{{-- Data Grid : Applied Filters --}}
		<div class="col-lg-10">

			<div class="data-grid_applied" data-grid="main"></div>

		</div>

		<div class="col-lg-2 text-right">
			<a class="btn btn-warning" href="{{ URL::toAdmin('media/create') }}"><i class="fa fa-plus"></i> Upload</a>
		</div>

	</div>

	<br />

	<table data-source="{{ URL::toAdmin('media/grid') }}" data-grid="main" class="data-grid table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th data-sort="name" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/media::table.file_name') }}}</th>
				<th data-sort="mime" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/media::table.mime') }}}</th>
				<th data-sort="created_at" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/media::table.created_at') }}}</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

	{{-- Data Grid : Pagination --}}
	<div class="data-grid_pagination" data-grid="main"></div>

</div>

@include('platform/media::data-grid-tmpl')
@include('platform/media::data-grid_pagination-tmpl')
@include('platform/media::data-grid_applied-tmpl')
@include('platform/media::data-grid_no-results-tmpl')

@stop
