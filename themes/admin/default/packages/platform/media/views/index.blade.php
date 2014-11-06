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
				uploadUrl : '{{ url()->toAdmin('media/upload') }}'
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
	<script id="b-file-ejs" type="text/ejs">
		<div id="file-<%=FileAPI.uid(file)%>" class="js-file b-file b-file_<%=file.type.split('/')[0]%>">
			<div class="js-left b-file__left">
				<img src="<%=icon[file.type.split('/')[0]]||icon.def%>" width="32" height="32" style="margin: 2px 0 0 3px"/>
			</div>
			<div class="b-file__right">
				<div class="hide"><input type="text" name="name" value="<%=file.name%>"></div>
				<div class="hide"><input type="text" name="tags" value="tag1, tag2, tag3"></div>
				<div class="js-info b-file__info">size: <%=(file.size/FileAPI.KB).toFixed(2)%> KB</div>
				<div class="js-progress b-file__bar" style="display: none">
					<div class="b-progress"><div class="js-bar b-progress__bar"></div></div>
				</div>
			</div>
			<i class="js-abort b-file__abort" title="abort">&times;</i>
		</div>
	</script>
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

			<!--<form enctype="multipart/form-data" method="post" action="{{ url()->toAdmin('media/upload') }}">
				{{-- CSRF Token --}}
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<input type="file" name="file">
				<button>Send</button>
			</form>-->

			<div id="buttons-panel">
				<div class="b-button js-fileapi-wrapper">
					<div class="b-button__text">Select file(s)</div>
					<input name="files" class="b-button__input" type="file" multiple />
				</div>
				<div class="b-button js-fileapi-wrapper">
					<div class="b-button__text"><a href="#" class="test">Send</a></div>
				</div>
			</div>


			<div id="preview" style="margin-top: 30px"></div>


			<div class="hide modal-footer" style="margin-top: 0;">

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





	<script>
		// Simple JavaScript Templating
		// John Resig - http://ejohn.org/ - MIT Licensed
		(function (){
			var cache = {};

			this.tmpl = function tmpl(str, data){
				// Figure out if we're getting a template, or if we need to
				// load the template - and be sure to cache the result.
				var fn = !/\W/.test(str) ?
						cache[str] = cache[str] ||
								tmpl(document.getElementById(str).innerHTML) :

					// Generate a reusable function that will serve as a template
					// generator (and which will be cached).
						new Function("obj",
								"var p=[],print=function(){p.push.apply(p,arguments);};" +

									// Introduce the data as local variables using with(){}
										"with(obj){p.push('" +

									// Convert the template into pure JavaScript
										str
												.replace(/[\r\t\n]/g, " ")
												.split("<%").join("\t")
												.replace(/((^|%>)[^\t]*)'/g, "$1\r")
												.replace(/\t=(.*?)%>/g, "',$1,'")
												.split("\t").join("');")
												.split("%>").join("p.push('")
												.split("\r").join("\\'")
										+ "');}return p.join('');");

				// Provide some basic currying to the user
				return data ? fn(data) : fn;
			};
		})();
	</script>

@stop
