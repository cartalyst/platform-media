@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
: {{{ trans('platform/media::common.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/css/daterangepicker-bs3.css', 'style') }}
{{ Asset::queue('new.css', 'platform/media::css/new.css') }}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'styles') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
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
		Extension.Index.setEmailRoute('{{ route('admin.media.email', 'rows-ids') }}');

		Extension.Index.MediaManager.setUploadUrl('{{ url()->toAdmin('media/upload') }}');

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

{{-- Page --}}
@section('page')

{{-- Grid --}}
<section class="panel panel-default panel-grid">

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
							<a data-grid-bulk-action="email" data-toggle="tooltip" data-original-title="* Email Selected">
								<i class="fa fa-mail-forward"></i> <span class="visible-xs-inline">* Email Selected</span>
							</a>
						</li>

						<li class="disabled">
							<a data-grid-bulk-action="private" data-toggle="tooltip" data-original-title="* Make Private">
								<i class="fa fa-eye-slash"></i> <span class="visible-xs-inline">* Make Private</span>
							</a>
						</li>

						<li class="disabled">
							<a data-grid-bulk-action="public" data-toggle="tooltip" data-original-title="* Make Public">
								<i class="fa fa-eye"></i> <span class="visible-xs-inline">* Make Public</span>
							</a>
						</li>

						<li class="danger disabled">
							<a data-grid-bulk-action="delete" data-toggle="tooltip" data-target="modal-confirm" data-original-title="{{{ trans('action.bulk.delete') }}}">
								<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.delete') }}}</span>
							</a>
						</li>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle tip" data-toggle="dropdown" role="button" aria-expanded="false" data-original-title="{{{ trans('action.export') }}}">
								<i class="fa fa-download"></i> <span class="visible-xs-inline">{{{ trans('action.export') }}}</span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a data-download="json"><i class="fa fa-file-code-o"></i> JSON</a></li>
								<li><a data-download="csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
								<li><a data-download="pdf"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
							</ul>
						</li>

						<li class="primary">
							<a href="#" data-toggle="modal" data-target="#mediaModal">
								<i class="fa fa-plus"></i>  <span class="visible-xs-inline">{{{ trans('action.upload') }}}</span>
							</a>
						</li>

					</ul>

					{{-- Grid: Filters --}}
					<form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-search data-grid="main" role="form">

						<div class="input-group">

							<span class="input-group-btn">

								<button class="btn btn-default" type="button" disabled>
									{{{ trans('common.filters') }}}
								</button>

								<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>

								<ul class="dropdown-menu" role="menu">

									<li>
										<a data-grid="main" data-filter="enabled:1" data-label="enabled::{{{ trans('common.all_enabled') }}}" data-reset>
											<i class="fa fa-eye"></i> {{{ trans('common.show_enabled') }}}
										</a>
									</li>

									<li>
										<a data-toggle="tooltip" data-placement="top" data-original-title="" data-grid="main" data-filter="enabled:0" data-label="enabled::{{{ trans('common.all_disabled') }}}" data-reset>
											<i class="fa fa-eye-slash"></i> {{{ trans('common.show_disabled') }}}
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

								<button class="btn btn-default hidden-xs" type="button" data-grid-calendar data-range-filter="created_at">
									<i class="fa fa-calendar"></i>
								</button>

							</span>

							<input class="form-control " name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

							<span class="input-group-btn">

								<button class="btn btn-default" type="submit">
									<span class="fa fa-search"></span>
								</button>

								<button class="btn btn-default" data-grid="main" data-reset>
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

		{{-- Grid: Applied Filters --}}
		<div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

			<div id="data-grid_applied" class="btn-group" data-grid="main"></div>

		</div>

	</div>

	{{-- Grid: Table --}}
	<div class="table-responsive">

		<table id="data-grid" class="table table-hover" data-source="{{ route('admin.media.grid') }}" data-grid="main">
			<thead>
				<tr>
					<th><input data-grid-checkbox="all" type="checkbox"></th>
					<th class="sortable" data-sort="name" colspan="2">{{{ trans('model.name') }}}</th>
					<th class="sortable hidden-xs" data-sort="created_at">{{{ trans('model.created_at') }}}</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

	</div>

	<footer class="panel-footer clearfix">

		{{-- Grid: Pagination --}}
		<div id="data-grid_pagination" data-grid="main"></div>

	</footer>

	{{-- Grid: templates --}}
	@include('platform/media::grid/index/results')
	@include('platform/media::grid/index/pagination')
	@include('platform/media::grid/index/filters')
	@include('platform/media::grid/index/no_results')

</section>

<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content" style="width: 660px;">

			<div data-media-queue-list style="min-height: 400px; max-height: 400px; overflow: auto;"></div>

			<div class="modal-footer" style="margin-top: 0;">

				<span class="pull-left text-left">
					<div><span data-media-total-files>0</span> files in queue</div>
					<div><span data-media-total-size>0</span> KB</div>
				</span>

				<div class="media-button">
					<div class="media-button__text">Select file(s)</div>
					<input name="files" class="media-button__input" type="file" multiple />
				</div>

				<button type="button" class="btn btn-success" data-media-upload><i class="fa fa-upload"></i> Start Upload</button>

				<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>

			</div>

		</div>

	</div>

</div>

<script type="text/template" data-media-file-template>
	<div data-media-file="<%= FileAPI.uid(file) %>" class="media-file media-file_<%= file.type.split('/')[0] %>">

		<form class="form-inline">

			<div data-media-file-image="60" class="media-file__left">
				<img src="<%= icon[file.type.split('/')[0]]||icon.def %>" width="60" height="60" />
			</div>

			<div class="media-file__right">

				<div class="form-group">
					<input type="text" name="<%= FileAPI.uid(file) %>_name" value="<%= file.name %>" placeholder="File name." class="form-control">
				</div>

				<div class="form-group">
					<input type="text" name="<%= FileAPI.uid(file) %>_tags[]" value="" placeholder="File tags." class="form-control tags">
				</div>

				<div class="media-file__info">size: <%= (file.size/FileAPI.KB).toFixed(2) %> KB</div>

				<div data-media-progress style="display: none" class="media-progress">
					<div data-media-progress-bar class="media-progress__bar"></div>
				</div>

			</div>

			<i data-media-remove="<%= FileAPI.uid(file) %>" class="media-file__remove">&times;</i>

		</form>

	</div>
</script>

@if (config('platform.app.help'))
	@include('platform/media::help')
@endif

@stop
