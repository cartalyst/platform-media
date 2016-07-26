@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
 {{{ trans('platform/media::common.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/css/daterangepicker-bs3.css', 'style') }}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'style') }}
{{ Asset::queue('media', 'platform/media::css/media.scss', 'style') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.min.js', 'jquery') }}
{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('exoskeleton', 'cartalyst/js/exoskeleton.min.js', 'jquery') }}
{{ Asset::queue('lodash', 'cartalyst/js/lodash.min.js', 'jquery') }}

{{ Asset::queue('bootstrap-daterange', 'bootstrap/js/daterangepicker.js', 'jquery') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', ['jquery']) }}
{{ Asset::queue('index', 'platform/media::js/index.js', ['platform', 'mediamanager']) }}

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Inline scripts --}}
@section('scripts')
@parent

<script src="{{ Asset::getUrl('platform/media::js/FileAPI/FileAPI.min.js') }}"></script>
<script src="{{ Asset::getUrl('platform/media::js/FileAPI/FileAPI.exif.js') }}"></script>

<script type="text/javascript">
	Extension.Index
		.setEmailRoute('{{ route('admin.media.email', 'rows-ids') }}')
		.MediaManager.setUploadUrl('{{ route('admin.media.upload') }}')
	;
</script>

@stop

{{-- Page content --}}
@section('page')

{{-- Grid --}}
<section class="panel panel-default panel-grid" data-grid="main" data-grid-source="{{ route('admin.media.grid') }}">

	{{-- Loader --}}
    <div class="loading"></div>

	{{-- Grid: Header --}}
	<header class="panel-heading">

		<nav class="navbar navbar-default navbar-actions">

			<div class="container-fluid">

				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<span class="navbar-brand">{{{ trans('platform/media::common.title') }}}</span>

				</div>

				{{-- Grid: Actions --}}
				<div class="collapse navbar-collapse" id="actions">

					<ul class="nav navbar-nav navbar-left">

						<li class="disabled">
							<a data-grid-bulk-action="email" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.bulk.email') }}}">
								<i class="fa fa-envelope"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.bulk.email') }}}</span>
							</a>
						</li>

						<li class="disabled">
							<a data-grid-bulk-action="makePrivate" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.bulk.private') }}}">
								<i class="fa fa-lock"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.bulk.private') }}}</span>
							</a>
						</li>

						<li class="disabled">
							<a data-grid-bulk-action="makePublic" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.bulk.public') }}}">
								<i class="fa fa-unlock"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.bulk.public') }}}</span>
							</a>
						</li>

						<li class="danger disabled">
							<a data-grid-bulk-action="delete" data-toggle="tooltip" data-target="modal-confirm" data-original-title="{{{ trans('action.bulk.delete') }}}">
								<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.delete') }}}</span>
							</a>
						</li>

						<li class="dropdown disabled">
							<a href="#" data-grid-exporter class="dropdown-toggle tip" data-toggle="dropdown" role="button" aria-expanded="false" data-original-title="{{{ trans('action.export') }}}">
								<i class="fa fa-download"></i> <span class="visible-xs-inline">{{{ trans('action.export') }}}</span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a data-grid-download="pdf"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
								<li><a data-grid-download="csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
								<li><a data-grid-download="json"><i class="fa fa-file-code-o"></i> JSON</a></li>
							</ul>
						</li>

						<li class="primary">
							<a class="tip" href="#" data-toggle="modal" data-target="#media-modal" data-original-title="{{{ trans('action.create') }}}">
								<i class="fa fa-plus"></i> <span class="visible-xs-inline">{{{ trans('action.upload') }}}</span>
							</a>
						</li>

					</ul>

					{{-- Grid: Filters --}}
					<form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-grid-search role="form">

						@if ( ! empty($tags))
						<div class="btn-group">

							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-tags"></i> <span class="caret"></span>
							</button>

							<ul class="dropdown-menu" role="tags" data-grid-group data-grid-reset-group>
								@foreach ($tags as $tag)
								<li><a href="#" data-grid-filter="tag:{{{ $tag }}}" data-grid-query="tags..name:{{{ $tag }}}" data-grid-label="{{{ $tag }}}">{{{ $tag }}}</a></li>
								@endforeach
							</ul>

						</div>
						@endif

						<div class="input-group">

							<span class="input-group-btn">

								<button class="btn btn-default" type="button" disabled>
									{{{ trans('common.filters') }}}
								</button>

								<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>

								<ul class="dropdown-menu" role="menu" data-grid-group data-grid-reset-group>

									<li>
										<a data-grid-filter="private:0" data-grid-query="private:0" data-grid-label="{{{ trans('platform/media::action.filter.public') }}}">
											<i class="fa fa-unlock"></i> {{{ trans('platform/media::action.filter.public') }}}
										</a>
									</li>

									<li>
										<a data-grid-filter="private:1" data-grid-query="private:1" data-grid-label="{{{ trans('platform/media::action.filter.private') }}}">
											<i class="fa fa-lock"></i> {{{ trans('platform/media::action.filter.private') }}}
										</a>
									</li>

									<li class="divider"></li>

									<li>
										<a data-grid-calendar-preset="day">
											<i class="fa fa-calendar"></i> {{{ trans('date.day') }}}
										</a>
									</li>

									<li>
										<a data-grid-calendar-preset="week">
											<i class="fa fa-calendar"></i> {{{ trans('date.week') }}}
										</a>
									</li>

									<li>
										<a data-grid-calendar-preset="month">
											<i class="fa fa-calendar"></i> {{{ trans('date.month') }}}
										</a>
									</li>

								</ul>

								<button class="btn btn-default hidden-xs" type="button" data-grid-calendar>
									<i class="fa fa-calendar"></i>
								</button>

							</span>

							<input class="form-control" name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

							<span class="input-group-btn">

								<button class="btn btn-default" type="submit">
									<span class="fa fa-search"></span>
								</button>

								<button class="btn btn-default" data-reset>
									<i class="fa fa-refresh fa-sm"></i>
								</button>

							</span>

						</div>

					</form>

				</div>

			</div>

		</nav>

	</header>

	<div class="panel-body">

		{{-- Applied filters container --}}
    	<div data-grid-layout="filters"></div>

		{{-- Grid: Applied Filters --}}
		<div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

			<div id="data-grid_applied" class="btn-group"></div>

		</div>

	</div>

	{{-- Grid: Table --}}
	<div class="table-responsive">

		<table id="data-grid" class="table table-hover">
			<thead>
				<tr>
					<th><input data-grid-checkbox="all" type="checkbox"></th>
					<th class="sortable" data-grid-sort="mime"><i class="fa fa-file-o"></i></th>
					<th class="sortable" data-grid-sort="mime"><i class="fa fa-shield"></i></th>
					<th class="sortable" data-grid-sort="name">{{{ trans('model.name') }}}</th>
					<th>{{{ trans('platform/tags::model.tag.legend') }}}</th>
					<th class="sortable" data-grid-sort="size">{{{ trans('platform/media::model.general.size') }}}</th>
					<th class="sortable hidden-xs" data-grid-sort="created_at">{{{ trans('model.created_at') }}}</th>
					<th class="text-center">{{{ trans('common.actions') }}}</th>
				</tr>
			</thead>
			<tbody data-grid-layout="table"></tbody>
		</table>

	</div>

	<footer class="panel-footer clearfix" data-grid-layout="pagination"></footer>

	{{-- Grid: templates --}}
	@include('platform/media::grid/index/table')
	@include('platform/media::grid/index/grid')
	@include('platform/media::grid/index/pagination')
	@include('platform/media::grid/index/filters')

</section>

@include('platform/media::modal')

@help('platform/media::help')

@stop
