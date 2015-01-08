<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-grid-row>
			<td><input data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>"></td>
			<td>
				<% if (r.is_image == 1) { %>
					<img src="{{ URL::to('/') }}<%= r.thumbnail %>" />
				<% } else{ %>
					<i class="fa fa-file fa-3x"></i>
				<% } %>
			</td>
			<td>
				<span class="pull-right text-right">

					<div class="btn-group dropup text-left">

						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
							{{{ trans('action.actions') }}} <span class="caret"></span>
						</button>

						<ul class="dropdown-menu" role="menu">
							<li><a href="<%= r.view_uri %>" target="_blank">Share</a></li>
							<li><a href="<%= r.download_uri %>">Download</a></li>
							<li><a href="<%= r.email_uri %>">Email</a></li>
						</ul>

					</div>

					<br />

					<small>
					<% _.each(r.tags, function(tag) { %>
						<span class="label label-info"><%= tag.name %></span>
					<% }); %>
					</small>

				</span>

				<label class="label label-info"><%= r.id %></label> <a href="<%= r.edit_uri %>"><%= r.name %></a>

				<br />

				<% if (r.private == 1) { %>
					<i class="fa fa-lock"></i>
				<% } else { %>
					<i class="fa fa-unlock"></i>
				<% } %>

				&nbsp;

				<small><%= (r.size/FileAPI.KB).toFixed(2) %> KB</small>
			</td>
			<td class="hidden-xs"><%= moment(r.created_at).format('MMM DD, YYYY') %></td>
		</tr>

	<% }); %>

</script>
