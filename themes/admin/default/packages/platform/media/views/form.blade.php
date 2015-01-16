@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
: {{{ trans('action.update') }}}
@stop

{{-- Queue assets --}}

{{ Asset::queue('selectize', 'selectize/css/selectize.css', 'style') }}
{{ Asset::queue('media', 'platform/media::css/media.scss', 'style') }}

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
								<a href="{{ route('media.download', $media->path) }}" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.bulk.email') }}}">
									<i class="fa fa-envelope"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.bulk.email') }}}</span>
								</a>
							</li>

							<li>
								<a href="{{ route('media.download', $media->path) }}" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.bulk.private') }}}">
									<i class="fa fa-lock"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.bulk.private') }}}</span>
								</a>
							</li>

							<li>
								<a href="{{ route('media.download', $media->path) }}" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.bulk.public') }}}">
									<i class="fa fa-unlock"></i> <span class="visible-xs-inline">{{{ trans('platform/media::action.bulk.public') }}}</span>
								</a>
							</li>

							<li>
								<a href="{{ route('media.view', $media->path) }}" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::modal.share') }}}">
									<i class="fa fa-share-alt"></i> <span class="visible-xs-inline">{{{ trans('platform/media::modal.share') }}}</span>
								</a>
							</li>

							<li>
								<a href="{{ route('media.download', $media->path) }}" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::modal.download') }}}">
									<i class="fa fa-download"></i> <span class="visible-xs-inline">{{{ trans('platform/media::modal.download') }}}</span>
								</a>
							</li>

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

						<div class="row">

							<div class="col-md-6">

								<fieldset>

									<legend>File Details</legend>

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

								</fieldset>

							</div>

							<div class="col-md-6">

								<div class="panel panel-default panel-file-details">

									<div class="panel-body">

										@if ( ($media->mime == 'audio/ogg') || ($media->mime == 'video/mp4') || ($media->mime == 'video/ogg') )

										<i class="fa fa-file-movie-o fa-5x"></i>

										@elseif ( $media->is_image == 1)

										@thumbnail($media->id)

										@elseif ( $media->mime == 'application/zip')

										<i class="fa fa-file-zip-o fa-5x"></i>

										@elseif ( $media->mime == 'application/pdf')

										<i class="fa fa-file-pdf-o fa-5x"></i>

										@else

										<i class="fa fa-file-o fa-5x"></i>

										@endif

										<h3>{{ $media->mime }}</h3>

									</div>

									<!-- List group -->
									<ul class="list-group">

										<li class="list-group-item">{{ $media->path }}</li>

										<li class="list-group-item">{{ formatBytes($media->size) }}</li>

										<li class="list-group-item">
											@if ($media->private == 1)
											<i class="fa fa-lock"></i> Private
											@else
											<i class="fa fa-unlock"></i> Public
											@endif
										</li>

										<li class="list-group-item">{{ $media->path }}</li>

										@if ($media->is_image == 1)
										<li class="list-group-item">{{ $media->width }}x{{ $media->height }}</li>
										@endif

									</ul>

									<div class="panel-footer">
										{{-- File --}}
										<div class="btn btn-warning btn-block upload__select">
											<div class="upload__select-text">Update File</div>
											<input name="files" class="upload__select-input" type="file" name="file" id="file" />
										</div>
									</div>
								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

</section>
@stop
