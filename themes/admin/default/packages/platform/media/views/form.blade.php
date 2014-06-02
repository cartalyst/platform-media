@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: {{{ trans('platform/media::general.update') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'styles') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		$('#tags').selectize({
			maxItems: 4,
			create: true
		});

		$('#private').on('change', function()
		{
			if ($(this).val() == 1)
			{
				$('[data-groups]').removeClass('hide');
			}
			else
			{
				$('[data-groups]').addClass('hide');
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

{{-- Page header --}}
<div class="page-header">

	<h1>{{{ trans('platform/media::general.update') }}}</h1>

</div>

{{-- Media form --}}
<form id="media-form" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off" enctype="multipart/form-data">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<div class="row">

		{{-- Name --}}
		<div class="col-lg-7">

			<div class="form-group">

				<label class="control-label" for="name">{{{ trans('platform/media::form.name') }}}</label>

				<div class="controls">
					<input type="text" name="name" id="name" class="form-control" value="{{ $media->name }}">
				</div>

			</div>

		</div>

		{{-- Tags --}}
		<div class="col-lg-5">

			<div class="form-group">

				<label class="control-label" for="tags">{{{ trans('platform/media::form.tags') }}}</label>

				<div class="controls">
					<select id="tags" name="tags[]" multiple="multiple" tabindex="-1">
					@foreach ($tags as $tag)
						<option value="{{{ $tag }}}"{{ in_array($tag, $media->tags) ? ' selected="selected"' : null }}>{{{ $tag }}}</option>
					@endforeach
					</select>
				</div>

			</div>

		</div>
	</div>

	<div class="row">

		{{-- Private --}}
		<div class="col-lg-4">

			<div class="form-group">

				<label class="control-label" for="private">{{{ trans('platform/media::form.private') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/media::form.private_help') }}}"></i>

				<div class="controls">
					<select name="private" id="private" class="form-control">
						<option value="0"{{ Input::old('private', $media->private) == 0 ? ' selected="selected"' : null }}>Public</option>
						<option value="1"{{ Input::old('private', $media->private) == 1 ? ' selected="selected"' : null }}>Private</option>
					</select>
				</div>

			</div>

		</div>

		<div class="col-lg-4">

			<div class="form-group">

				<label class="control-label" for="name">{{{ trans('platform/media::form.file') }}}</label>

				<div class="controls">

					<span class="btn btn-warning btn-file">
						Browse <input type="file" name="file" id="file">
					</span>

				</div>

			</div>

		</div>

	</div>

	{{-- Groups --}}
	<div class="row">

		<div class="col-lg-4">

			<div class="form-group{{ Input::old('private', $media->private) == 0 ? ' hide' : null }}" data-groups>
				<label class="control-label" for="groups">{{{ trans('platform/media::form.groups') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/media::form.groups_help') }}}"></i>

				<div class="controls">
					<select name="groups[]" id="groups" class="form-control" multiple="true">
					@foreach ($groups as $group)
						<option value="{{{ $group->id }}}"{{ in_array($group->id, $media->groups) ? ' selected="selected"' : null }}>{{{ $group->name }}}</option>
					@endforeach
					</select>
				</div>
			</div>

		</div>

	</div>

	{{-- Form actions --}}
	<div class="row">

		<div class="col-lg-12 text-right">

			{{-- Form actions --}}
			<div class="form-group">

				<button class="btn btn-success" type="submit">{{{ trans('button.save') }}}</button>

				<a class="btn btn-default" href="{{{ URL::toAdmin('media') }}}">{{{ trans('button.cancel') }}}</a>

			</div>

		</div>

	</div>

</form>

@stop
