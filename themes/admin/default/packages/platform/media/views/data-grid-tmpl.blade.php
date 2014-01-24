<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<div class="col-xs-6 col-md-3" style="margin-bottom: 20px;" data-media="<%= r.id %>">

			<div class="media">

				<div class="delete" data-media-delete="<%= r.id %>">&times;</div>

				<div data-media-file="<%= r.id %>">

					<a href="{{ URL::to('media/<%= r.unique_id %>') }}" target="_blank">
						<div class="thumb <%= (r.is_image == 1 ? 'image' : 'other') %> <%= r.extension %>">
							<% if (r.is_image == 1) { %>
								<img src="{{ URL::to('media/<%= r.unique_id %>/176x176&crop=true') }}">
							<% } %>
						</div>
					</a>

					<div class="name" data-media-name="<%= r.id %>">
						<input class="selectedId" type="checkbox" name="media" value="<%= r.id %>">

						<%= r.name %>
					</div>

				</div>

				<form class="form hide"method="post" data-media-form="<%= r.id %>">

					<div class="content">

						<div class="form-group" data-media-groups="<%= r.id %>">

							<select name="private" class="form-control" data-media-private="<%= r.id %>">
								<option value="0"<%= (r.private == 0 ? ' selected="selected"' : '') %>>Public</option>
								<option value="1"<%= (r.private == 1 ? ' selected="selected"' : '') %>>Private</option>
							</select>

						</div>

						<div class="form-group<%= (r.private == 0 ? ' hide' : '') %>" data-media-groups="<%= r.id %>">

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
