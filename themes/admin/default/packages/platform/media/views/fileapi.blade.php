@extends('layouts/default')

{{ Asset::queue('new.css', 'platform/media::css/new.css') }}

@section('scripts')
@parent

	<script>
		// var FileAPI = {
		// 	staticPath: '{{ Asset::getUrl('platform/media::js/FileAPI/') }}'
		// };
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
				<div><input type="text" name="name" value="<%=file.name%>"></div>
				<div><input type="text" name="tags" value="tag1, tag2, tag3"></div>
				<div class="js-info b-file__info">size: <%=(file.size/FileAPI.KB).toFixed(2)%> KB</div>
				<div class="js-progress b-file__bar" style="display: none">
					<div class="b-progress"><div class="js-bar b-progress__bar"></div></div>
				</div>
			</div>
			<i class="js-abort b-file__abort" title="abort">&times;</i>
		</div>
	</script>

	<script type="text/javascript">
		jQuery(document).ready(function($)
		{
			$.mediamanager('#mediaUploader');
		});
	</script>
@stop

@section('content')

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

	<form enctype="multipart/form-data" method="post" action="{{ url()->toAdmin('media/upload') }}">
		{{-- CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<input type="file" name="file">
		<button>Send</button>
	</form>

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

@stop
