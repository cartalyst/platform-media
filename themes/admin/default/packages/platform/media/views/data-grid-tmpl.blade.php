<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<div class="col-xs-6 col-md-3" style="margin-bottom: 20px;" data-media="<%= r.id %>">

			<div class="media">

				<div class="delete" data-media-delete="<%= r.id %>">&times;</div>

				<div data-media-file="<%= r.id %>">

					<div class="thumb <%= (r.is_image == 1 ? 'image' : 'other') %> <%= r.extension %>">
						<% if (r.is_image == 1) { %>
							<img src="{{ asset('media/<%= r.path %>') }}">
						<% } %>
					</div>

					<div class="name" data-media-name="<%= r.id %>">
						<input class="selectedId" type="checkbox" name="media" value="<%= r.id %>">

						<%= r.name %>
					</div>

				</div>


				<form class="form hide"method="post" data-media-form="<%= r.id %>">

					<div class="content">

						{{-- Groups --}}
						<div class="form-group">
							<label class="control-label" for="groups">{{{ trans('platform/media::form.groups') }}}</label>

							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/media::form.groups_help') }}}"></i>

							<div class="controls">
								<select name="groups[]" id="groups_<%= r.id %>" class="form-control" multiple="true">
								@foreach ($groups as $group)
									<option value="{{{ $group->id }}}"<%= (_.contains(r.groups, {{ $group->id }}) ? ' selected="selected"' : '') %>>{{{ $group->name }}}</option>
								@endforeach
								</select>
							</div>
						</div>


					</div>

					<input type="text" class="name" name="name" id="name_<%= r.id %>" value="<%= r.name %>" />

				</form>

			</div>

		</div>

	<% }); %>

</script>
