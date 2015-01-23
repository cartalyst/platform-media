<div class="form-group{{ Alert::onForm('tags', ' has-error') }}">

	<label for="menu" class="control-label">
		<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/media::model.tag.tags_help') }}}"></i>
		{{{ trans('platform/media::model.tag.tags') }}}
	</label>

	<select class="form-control" name="tags[]" id="tags" data-parsley-trigger="change" multiple>
		@foreach ($availableTags as $tag)
		<option value="{{ $tag->slug }}"{{ in_array($tag->slug, $tags) ? ' selected' : null }}>{{ $tag->name }}</option>
		@endforeach
	</select>

	<span class="help-block">{{{ Alert::onForm('tags') }}}</span>

</div>
