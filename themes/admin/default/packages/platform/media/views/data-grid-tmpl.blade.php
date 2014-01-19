<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

	<div class="col-xs-6 col-md-3" style="margin-bottom: 20px;">

		<span class="thumbnail" style="padding: 0" data-media="<%= r.id %>">
			<span class="btn delete" data-media-delete="<%= r.id %>">&times;</span>

			<!--<img src="http://placehold.it/170x160&text=Foo">-->
			<img src="{{ asset('media/<%= r.path %>') }}" style="width: 169px; height: 169px;">

			<div class="media-name">
				<input class="selectedId" type="checkbox" name="media" value="<%= r.id %>">

				<%= r.name %>
			</div>
		</span>

	</div>

	<% }); %>

</script>
