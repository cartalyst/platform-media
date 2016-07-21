<script type="text/template" data-grid="main" data-grid-template="grid">

	<% var results = response.results; %>

    <% if (_.isEmpty(results)) { %>

        <tr>
			<td class="no-results" colspan="8">{{{ trans('common.no_results') }}}</td>
		</tr>

    <% } else { %>

		<% _.each(results, function(r) { %>

			<tr data-grid-row>
				<td><input data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>"></td>

				<td>

					<% if ( (r.mime == 'audio/ogg') || (r.mime == 'video/mp4') || (r.mime == 'video/ogg') ) { %>
						<i class="fa fa-file-movie-o"></i>
					<% } else if ( r.is_image == 1) { %>
						<i class="fa fa-file-image-o"></i>
					<% } else if (r.mime == 'application/zip') { %>
						<i class="fa fa-file-zip-o"></i>
					<% } else if (r.mime == 'application/pdf') { %>
						<i class="fa fa-file-pdf-o"></i>
					<% } else { %>
						<i class="fa fa-file-o"></i>
					<% } %>

				</td>

				<td>

					<% if (r.private == 1) { %>
						<i class="fa fa-lock"></i>
					<% } else { %>
						<i class="fa fa-unlock"></i>
					<% } %>

				</td>

				<td>

					<a href="<%= r.edit_uri %>"><%= r.name %></a>

				</td>

				<td class="hidden-xs">
					<small>
					<% _.each(r.tags, function(tag) { %>
						<span class="label label-default"><%= tag.name %></span>
					<% }); %>
					</small>
				</td>

				<td class="hidden-xs"><small><%= (r.size/FileAPI.KB).toFixed(2) %> KB</small></td>

				<td class="hidden-xs"><%= moment(r.created_at).format('MMM DD, YYYY') %></td>

				<td class="text-center">

					<a class="btn btn-default btn-sm" href="<%= r.view_uri %>" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.share') }}}">
						<i class="fa fa-share-alt"></i>
					</a>

					<a class="btn btn-default btn-sm" href="<%= r.download_uri %>" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.download') }}}">
						<i class="fa fa-download"></i>
					</a>

				</td>
			</tr>

		<% }); %>

	<% } %>


</script>
