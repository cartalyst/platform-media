@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
: {{{ trans('action.update') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'styles') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}
{{ Asset::queue('form', 'platform/media::js/form.js', 'platform') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page --}}
@section('page')

{{-- Page header --}}
<section class="panel panel-default panel-tabs">

	{{-- Media form --}}
	<form id="media-form" action="{{ request()->fullUrl() }}" role="form" method="post" accept-char="UTF-8" autocomplete="off" enctype="multipart/form-data" data-parsley-validate>

		{{-- CSRF Token --}}
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

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.media.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i>  <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans('action.update') }}} <small>{{{ $media->name }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							<li>
								<a href="{{ route('admin.media.delete', $media->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
									<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
								</a>
							</li>

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i> <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
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
					<li class="active" role="presentation"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{{ trans('common.tabs.general') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Form: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general">

						<fieldset>

							{{-- Name --}}
							<div class="form-group">

								<label class="control-label" for="name">{{{ trans('platform/media::model.name') }}}</label>

								<div class="controls">
									<input type="text" name="name" id="name" class="form-control" value="{{ $media->name }}">
								</div>

							</div>

							{{-- Tags --}}
							<div class="form-group">

								<label class="control-label" for="tags">{{{ trans('platform/media::model.tags') }}}</label>

								<div class="controls">
									<select id="tags" name="tags[]" multiple="multiple" tabindex="-1">
										@foreach ($tags as $tag)
										<option value="{{{ $tag }}}"{{ in_array($tag, $media->tags->lists('name')) ? ' selected="selected"' : null }}>{{{ $tag }}}</option>
										@endforeach
									</select>
								</div>

							</div>

							{{-- Private --}}
							<div class="form-group{{ Alert::form('private', ' has-error') }}">

								<label class="control-label" for="private">{{{ trans('model.status') }}}</label>

								<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/media::model.private_help') }}}"></i>

								<select class="form-control" name="private" id="private" required data-parsley-trigger="change">
									<option value="0"{{ request()->old('private', $media->private) == 0 ? ' selected="selected"' : null }}>{{{ trans('platform/media::model.public') }}}</option>
									<option value="1"{{ request()->old('private', $media->private) == 1 ? ' selected="selected"' : null }}>{{{ trans('platform/media::model.private') }}}</option>
								</select>

							</div>

							{{-- Roles --}}
							<div class="form-group{{ request()->old('private', $media->private) == 0 ? ' hide' : null }}" data-roles>
								<label class="control-label" for="roles">{{{ trans('platform/media::model.roles') }}}</label>

								<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/media::model.roles_help') }}}"></i>

								<div class="controls">
									<select name="roles[]" id="roles" class="form-control" multiple="true">
										@foreach ($roles as $role)
										<option value="{{{ $role->id }}}"{{ in_array($role->id, $media->roles) ? ' selected="selected"' : null }}>{{{ $role->name }}}</option>
										@endforeach
									</select>
								</div>
							</div>

							{{-- File --}}
							<div class="form-group">

								<label class="control-label" for="name">{{{ trans('platform/media::model.file') }}}</label>

								<div class="controls">

									<span class="btn btn-warning btn-file">
										Browse <input type="file" name="file" id="file">
									</span>

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
