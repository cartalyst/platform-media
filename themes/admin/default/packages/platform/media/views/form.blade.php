@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans('platform/media::general.update') }}} ::
@parent
@stop

{{-- Inline scripts --}}
@section('scripts')
@parent
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

		<h1>{{{ trans('platform/media::general.update') }}}</h1>

	</div>

	{{-- Media form --}}
	<form id="media-form" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off">

		{{-- CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		{{-- Name --}}
		<div class="form-group">
			<label class="control-label" for="name">{{{ trans('platform/media::form.name') }}}</label>

			<div class="controls">
				<input type="text" name="name" id="name" class="form-control" value="{{ $media->name }}">
			</div>
		</div>

		{{-- Groups --}}
		<div class="form-group">
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

		{{-- Form actions --}}
		<div class="row">

			<div class="col-md-12">

				{{-- Form actions --}}
				<div class="form-group">

					<button class="btn btn-success" type="submit">{{{ trans('button.update') }}}</button>

				</div>

			</div>

		</div>
	</form>

</div>

@stop
