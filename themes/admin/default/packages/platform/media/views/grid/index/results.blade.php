<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-grid-row>
			<td><input data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>"></td>
			<td>
				<% if (r.is_image == 1) { %>
					<%= _.thumbnail(r.thumbnail_uri) %>
				<% } else{ %>
					<i class="fa fa-file fa-3x"></i>
				<% } %>
			</td>
			<td>
				<span class="pull-right text-right">

					<a href="<%= r.view_uri %>" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::modal.share') }}}"><i class="fa fa-share-alt"></i></a>

					<a href="<%= r.download_uri %>" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::modal.download') }}}"><i class="fa fa-download"></i></a>

					<a href="<%= r.email_uri %>" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::modal.email') }}}"><i class="fa fa-envelope"></i></a>

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
