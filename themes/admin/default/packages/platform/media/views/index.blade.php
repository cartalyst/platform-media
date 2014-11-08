@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans('platform/media::general.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'styles') }}

{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'underscore') }}
{{ Asset::queue('moment', 'moment/js/moment.js') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', ['jquery']) }}
{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('new.css', 'platform/media::css/new.css') }}

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

				var url = '{{ url()->toAdmin('media') }}';

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
				uploadUrl : '{{ url()->toAdmin('media/upload') }}',
				onFileQueued : function(file)
				{
					// $('.tags').selectize({
					// 	maxItems: 4,
					// 	create: true
					// });
				},
				onSuccess : function()
				{
					dg.refresh();
				}
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
	<script src="{{ Asset::getUrl('platform/media::js/FileAPI/FileAPI.min.js') }}"></script>
	<script src="{{ Asset::getUrl('platform/media::js/FileAPI/FileAPI.exif.js') }}"></script>
	<script src="{{ Asset::getUrl('platform/media::js/MediaManagerNew.js') }}"></script>
@stop

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

<table data-source="{{ url()->toAdmin('media/grid') }}" data-grid="main" class="data-grid table _table-striped table-bordered _table-hover">
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

			<div data-media-queue-list style="min-height: 400px; max-height: 400px; overflow: auto;">

				<!--
				@for ($i = 1; $i < 20; $i++)
				<div data-media-file="<%=FileAPI.uid(file)%>" class="media-file media-file_<%=file.type.split('/')[0]%>">

					<div class="media-file__left">
						<img src="//cdn1.iconfinder.com/data/icons/humano2/32x32/apps/synfig_icon.png" width="60" height="60" />
					</div>

					<div class="media-file__right">

						<div>
							<input type="text" name="name" value="<%=file.name%>">
							<input type="text" name="tags" value="" class="tags">
						</div>

						<div class="media-file__info">size: <%=(file.size/FileAPI.KB).toFixed(2)%> KB</div>

						<div data-media-progress style="display: none">
							<div class="media-progress"><div data-media-progress-bar class="media-progress__bar"></div></div>
						</div>

					</div>

					<i data-media-remove="<%=FileAPI.uid(file)%>" class="media-file__remove">&times;</i>

				</div>
				@endfor
				-->

			</div>

			<div class="modal-footer" style="margin-top: 0;">

				<span class="pull-left text-left">
					<div><span data-media-total-files>0</span> files in queue</div>
					<div><span data-media-total-size>0</span> kb</div>
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

		<div data-media-file-image="60" class="media-file__left">
			<img src="<%= icon[file.type.split('/')[0]]||icon.def %>" width="60" height="60" />
		</div>

		<div class="media-file__right">

			<div>
				<input type="text" name="name" value="<%=file.name%>">
				<input type="text" name="tags" value="" class="tags">
			</div>

			<div class="media-file__info">size: <%= (file.size/FileAPI.KB).toFixed(2) %> KB</div>

			<div data-media-progress style="display: none">
				<div class="media-progress"><div data-media-progress-bar class="media-progress__bar"></div></div>
			</div>

		</div>

		<i data-media-remove="<%= FileAPI.uid(file) %>" class="media-file__remove">&times;</i>

	</div>
</script>

@stop
