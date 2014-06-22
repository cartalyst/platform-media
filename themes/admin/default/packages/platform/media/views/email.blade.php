@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@parent
	: Email Media
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'styles') }}
{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('redactor', 'imperavi/css/redactor.css', 'styles') }}
{{ Asset::queue('redactor', 'imperavi/js/redactor.min.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
jQuery(document).ready(function($)
{
	// Instantiate the editor
	$('.redactor').redactor({
		toolbarFixed: true,
		minHeight: 200,
	});

	$('#users').selectize({
		persist: false,
		maxItems: null,
		valueField: 'email',
		labelField: 'name',
		searchField: ['name', 'email'],
		options: [
			@foreach ($users as $user)
			{email: '{{ $user->email }}', name: '{{ $user->first_name }} {{ $user->last_name }}'},
			@endforeach
		],
		render: {
			item: function(item, escape)
			{
				return '<div>' +
					(item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
					(item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
				'</div>';
			},
			option: function(item, escape)
			{
				var label = item.name || item.email;
				var caption = item.name ? item.email : null;
				return '<div>' +
					'<span class="label">' + escape(label) + '</span>' +
					(caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
				'</div>';
			}
		},
	});

	$('#groups').selectize({
		persist: false,
		maxItems: null,
	});
});
</script>
@stop

{{-- Page content --}}
@section('content')

{{-- Media form --}}
<form id="media-form" action="{{ Request::fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off" enctype="multipart/form-data">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<div class="row">

		<div class="col-lg-3">

			<div class="form-group">

				<label class="control-label" for="users">{{{ trans('platform/media::email.attachments') }}}</label>

				<ul class="list-group">

					<li class="list-group-item" style="max-height: 285px; overflow: auto">

						@foreach ($items as $item)
						<div class="media">

							<span class="pull-left">
								<img src="{{ URL::to(media_cache_path($item->thumbnail)) }}">
							</span>

							<div class="media-body" syle="padding-left: 10px;">

								<span class="pull-right">
									<a href="{{{ URL::current() }}}?remove={{ $item->id }}">&times;</a>
								</span>

								<h5 class="media-heading">{{{ Str::limit($item->name, 15) }}}</h5>

								{{{ formatBytes($item->size) }}}

							</div>

						</div>
						@endforeach

					</li>

				<li class="list-group-item">
					Attachments: {{{ count($items) }}}
					<br />
					Total: {{{ formatBytes($total) }}}
				</li>

				</ul>

			</div>

		</div>

		<div class="col-lg-9">

			<div class="row">

			<div class="row">

				<div class="col-lg-6">

					<div class="form-group">

						<label class="control-label" for="users">{{{ trans('platform/media::email.users') }}}</label>

						<select name="users[]" id="users"></select>

					</div>

				</div>

				<div class="col-lg-6">

					<div class="form-group">

						<label class="control-label" for="groups">{{{ trans('platform/media::email.groups') }}}</label>

						<select name="groups[]" id="groups">
							<option></option>
						@foreach ($groups as $group)
							<option value="{{{ $group->id }}}">{{{ $group->name }}}</option>
						@endforeach
						</select>

					</div>

				</div>

			</div>
				<div class="form-group{{ $errors->first('subject', ' has-error') }}">

					<label for="value" class="control-label">Subject <i class="fa fa-info-circle" data-toggle="popover" data-content="..."></i></label>

					<input type="text" class="form-control" name="subject" id="subject" value="{{{ Input::old('subject', Config::get('platform/media::email.subject')) }}}">

					<span class="help-block">{{{ $errors->first('value', ':message') }}}</span>

				</div>

				<div class="form-group{{ $errors->first('body', ' has-error') }}">

					<label for="body" class="control-label">Body <i class="fa fa-info-circle" data-toggle="popover" data-content="..."></i></label>

					<textarea class="form-control redactor" name="body" id="body">{{{ Input::old('body') }}}</textarea>

					<span class="help-block">{{{ $errors->first('body', ':message') }}}</span>

				</div>

				{{-- Form actions --}}
				<div class="row">

					<div class="col-lg-8">

						{{-- Form actions --}}
						<div class="form-group">

							<button class="btn btn-success" type="submit">{{{ trans('platform/media::button.send_email') }}}</button>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

@stop
