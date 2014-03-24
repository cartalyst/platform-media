<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-id="<%= r.id %>">
			<td class="_hide"><input type="checkbox" name="entries[]" value="<%= r.id %>"></td>
			<td>
				<% if (r.is_image == 1) { %>
					<img src="{{ URL::to(media_cache_path('<%= r.thumbnail %>')) }}" />
				<% } else{ %>
					<i class="fa fa-file fa-3x"></i>
				<% } %>
			</td>
			<td class="col-md-9">

				<span class="pull-right">

					<div class="btn-group dropup">

						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
							{{{ trans('general.actions') }}} <span class="caret"></span>
						</button>

						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ URL::to('media/<%= r.path %>') }}" target="_blank">Share</a></li>
							<li><a href="{{ URL::to('media/download/<%= r.path %>') }}">Download</a></li>
							<li><a href="{{ URL::toAdmin('media/<%= r.id %>/email') }}">Email</a></li>
						</ul>

					</div>

				</span>

				<a href="{{ URL::toAdmin('media/<%= r.id %>/edit') }}"><%= r.name %></a>

				<br />

				<small><%= bytesToSize(r.size) %></small>

				&nbsp;

				<% if (r.private == 1) { %>
					<i class="fa fa-lock"></i>
				<% } %>

			</td>
			<td><%= moment(r.created_at).format('MMM DD, YYYY') %></td>
		</tr>

	<% }); %>

</script>
