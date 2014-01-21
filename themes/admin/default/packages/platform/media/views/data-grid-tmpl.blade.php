<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<div class="col-xs-6 col-md-3" style="margin-bottom: 20px;" data-media="<%= r.id %>">

			<span class="media">

				<div class="delete" data-media-delete="<%= r.id %>">&times;</div>

				<div class="thumb <%= (r.is_image == 1 ? 'image' : 'other') %> <%= r.extension %>" data-media-thumb="<%= r.id %>">
					<% if (r.is_image == 1) { %>
						<img src="{{ asset('media/<%= r.path %>') }}">
					<% } %>
				</div>

				<div class="name" data-media-name="<%= r.id %>">
					<input class="selectedId" type="checkbox" name="media" value="<%= r.id %>">

					<%= r.name %>
				</div>

				<div class="form hide" data-media-form="<%= r.id %>">

					Foo

				</div>

			</span>

		</div>

	<% }); %>

</script>
