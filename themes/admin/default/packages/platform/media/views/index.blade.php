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
	Extension.Index
		.setEmailRoute('{{ route('admin.media.email', 'rows-ids') }}')
		.MediaManager.setUploadUrl('{{ route('admin.media.upload') }}')
	;
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
								<li><a data-download="pdf"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
								<li><a data-download="csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
								<li><a data-download="json"><i class="fa fa-file-code-o"></i> JSON</a></li>
							</ul>
						</li>

						<li class="primary">
							<a class="tip" href="#" data-toggle="modal" data-target="#media-modal" data-original-title="{{{ trans('action.create') }}}">
								<i class="fa fa-plus"></i> <span class="visible-xs-inline">{{{ trans('action.upload') }}}</span>
							</a>
						</li>

					</ul>

					{{-- Grid: Filters --}}
					<form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-search data-grid="main" role="form">

						@if ( ! empty($tags))
						<div class="btn-group">

							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-tags"></i> <span class="caret"></span>
							</button>

							<ul class="dropdown-menu" role="tags">
								@foreach ($tags as $tag)
								<li><a href="#" data-grid="main" data-filter="tags..name:{{{ $tag }}}" data-label="tags..name::{{{ $tag }}}">{{{ $tag }}}</a></li>
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

								<ul class="dropdown-menu" role="menu">

									<li>
										<a data-grid="main" data-filter="private:0" data-label="private::{{{ trans('platform/media::action.filter.public') }}}" data-reset>
											<i class="fa fa-unlock"></i> {{{ trans('platform/media::action.filter.public') }}}
										</a>
									</li>

									<li>
										<a data-grid="main" data-filter="private:1" data-label="private::{{{ trans('platform/media::action.filter.private') }}}" data-reset>
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

								<button class="btn btn-default hidden-xs" type="button" data-grid-calendar data-range-filter="created_at">
									<i class="fa fa-calendar"></i>
								</button>

							</span>

							<input class="form-control" name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

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
					<th class="sortable" data-sort="mime"><i class="fa fa-file-o"></i></th>
					<th class="sortable" data-sort="mime"><i class="fa fa-shield"></i></th>
					<th class="sortable" data-sort="name">{{{ trans('model.name') }}}</th>
					<th>{{{ trans('platform/tags::model.tag.legend') }}}</th>
					<th class="sortable" data-sort="size">{{{ trans('platform/media::model.general.size') }}}</th>
					<th class="sortable hidden-xs" data-sort="created_at">{{{ trans('model.created_at') }}}</th>
					<th class="text-center">{{{ trans('common.actions') }}}</th>
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

<div class="modal modal-media fade" id="media-modal" tabindex="-1" role="dialog" aria-labelledby="media-modal" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-body upload">

				<div class="upload__instructions">

					<i class="fa fa-upload fa-5x"></i>
					<h4>Select Files</h4>
					<p class="lead">Acceptable File Types.</p>
					<p class="small">
						<i>
							{{ $mimes }}
						</i>
					</p>

				</div>

				<div class="upload__files" data-media-queue-list ></div>

				<div class="btn btn-default btn-block upload__select">
					<div>Select</div>
					<input name="files" class="upload__select-input" type="file" multiple />
				</div>

			</div>


			<div class="modal-footer">

				<span class="pull-left text-left">
					<div><span data-media-total-files>0</span> files in queue</div>
					<div><span data-media-total-size>0</span> KB</div>
				</span>

				<span class="pull-right text-right">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{{ trans('action.cancel') }}}</button>

					<button type="button" class="btn btn-primary" data-media-upload><i class="fa fa-upload"></i> Start Upload</button>
				</span>
			</div>

		</div>

	</div>

</div>

<script type="text/template" data-media-file-template>
	<div data-media-file="<%= FileAPI.uid(file) %>" class="file file_<%= file.type.split('/')[0] %>">

		<form class="form-inline">

			<div class="form-group">

				<div class="btn-group">
					<button class="btn btn-default file-type" disabled><i class="fa <%= icon[file.type.split('/')[0]]||icon.def %>"></i></button>
					<button class="btn btn-default file-size" disabled><small><%= (file.size/FileAPI.KB).toFixed(2) %> kb</small></button>
				</div>

			</div>

			<div class="form-group">
				<label class="sr-only" for="label">Filename</label>
				<input type="text" class="form-control file-name" name="<%= FileAPI.uid(file) %>_name" value="<%= file.name %>" placeholder="Filename" >
			</div>

			<div class="form-group">
				<label class="sr-only" for="tags">Tags</label>
				<input type="text" class="form-control file-tags" name="<%= FileAPI.uid(file) %>_tags[]" value="" placeholder="Tags">
			</div>

			<div class="form-group">

				<button class="btn btn-default file-remove" data-media-remove="<%= FileAPI.uid(file) %>"><i class="fa fa-trash-o"></i></button>

				<button class="btn btn-default file-status" disabled>

					<span class="file-ready">
						<i class="fa fa-clock-o"></i>
					</span>

					<span class="file-progress">
						<i class="fa fa-refresh fa-spin"></i>
					</span>

					<span class="file-success">
						<i class="fa fa-thumbs-o-up text-success"></i>
					</span>

					<span class="file-error" data-toggle="tooltip" data-title>
						<i class="fa fa-exclamation text-danger"></i>
					</span>

				</button>

			</div>

		</form>

		<div class="file-error-help text-danger"></div>

	</div>
</script>

@if (config('platform.app.help'))
@include('platform/media::help')
@endif

@stop
