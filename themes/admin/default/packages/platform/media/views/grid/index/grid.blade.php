<script type="text/template" data-grid="main" data-grid-template="grid">

	<% var results = response.results; %>

    <% if (_.isEmpty(results)) { %>

        <tr>
			<td class="no-results" colspan="8">{{{ trans('common.no_results') }}}</td>
		</tr>

    <% } else { %>

		<% _.each(results, function(r) { %>
			<div data-grid-row>
				<label>
					<input data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>">
					<img src="">
				</label>
				<a href="<%= r.edit_uri %>"><%= r.name %></a>

				<% if (r.private == 1) { %>
					<i class="fa fa-lock"></i>
				<% } else { %>
					<i class="fa fa-unlock"></i>
				<% } %>

				<div class="hidden-xs">
					<small>
					<% _.each(r.tags, function(tag) { %>
						<span class="label label-default"><%= tag.name %></span>
					<% }); %>
					</small>
				</div>

				<div class="text-center">

					<a class="btn btn-default btn-sm" href="<%= r.view_uri %>" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.share') }}}">
						<i class="fa fa-share-alt"></i>
					</a>

					<a class="btn btn-default btn-sm" href="<%= r.download_uri %>" target="_blank" data-toggle="tooltip" data-original-title="{{{ trans('platform/media::action.download') }}}">
						<i class="fa fa-download"></i>
					</a>

				</div>
			</div>


		<% }); %>

	<% } %>


</script>
