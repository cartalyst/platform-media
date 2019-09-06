@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
 {{{ trans('platform/media::common.tabs.email') }}} {{{ trans('platform/media::common.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'styles') }}
{{ Asset::queue('redactor', 'redactor/css/redactor.css', 'styles') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('redactor', 'redactor/js/redactor.min.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
jQuery(document).ready(function($)
{
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

	$('#roles').selectize({
		persist: false,
		maxItems: null,
	});
});
</script>
@stop

{{-- Page content --}}
@section('page')
<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="media-form" action="{{ request()->fullUrl() }}" method="post" accept-char="UTF-8" autocomplete="off" enctype="multipart/form-data">

		{{-- Form: CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

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

						<ul class="nav navbar-nav navbar-cancel">
							<li>
								<a class="tip" href="{{ route('admin.media.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
									<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
								</a>
							</li>
						</ul>

						<span class="navbar-brand">{{{ trans('platform/media::common.tabs.email') }}}</span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.send_email') }}}">
									<i class="fa fa-save"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.send_email') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<div class="panel-body">

			<div role="tabpanel">

				{{-- Form: Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('platform/media::common.tabs.email') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<fieldset>


							<div class="col-lg-3">

								<div class="form-group">

									<label class="control-label" for="users">{{{ trans('platform/media::email.attachments') }}}</label>

									<ul class="list-group">

										<li class="list-group-item" style="max-height: 285px; overflow: auto">

											@foreach ($items as $item)
											<div class="media">

												@if ($item->thumbnail)
												<span class="pull-left">
													<img src="{{ URL::to($item->thumbnail) }}">
												</span>
												@endif

												<div class="media-body" syle="padding-left: 10px;">

													<span class="pull-right">
														<a href="{{{ URL::current() }}}?remove={{ $item->id }}">&times;</a>
													</span>

													<h5 class="media-heading">{{{ Illuminate\Support\Str::limit($item->name, 15) }}}</h5>

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

											<label class="control-label" for="roles">{{{ trans('platform/media::email.roles') }}}</label>

											<select name="roles[]" id="roles">
												<option></option>
												@foreach ($roles as $role)
												<option value="{{{ $role->id }}}">{{{ $role->name }}}</option>
												@endforeach
											</select>

										</div>

									</div>

								</div>
									<div class="form-group{{ Alert::onForm('subject', ' has-error') }}">

										<label for="value" class="control-label">Subject <i class="fa fa-info-circle" data-toggle="popover" data-content="..."></i></label>

										<input type="text" class="form-control" name="subject" id="subject" value="{{{ request()->old('subject', Config::get('platform/media::email.subject')) }}}">

										<span class="help-block">{{{ Alert::onForm('value') }}}</span>

									</div>

									<div class="form-group{{ Alert::onForm('body', ' has-error') }}">

										<label for="body" class="control-label">Body <i class="fa fa-info-circle" data-toggle="popover" data-content="..."></i></label>

										<textarea class="form-control redactor" name="body" id="body">{{{ request()->old('body') }}}</textarea>

										<span class="help-block">{{{ Alert::onForm('body') }}}</span>

									</div>

								</div>

							</div>

						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</form>

</section>
@stop
