<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<tr>
			<td><%= r.name %></td>
			<td><%= r.mime %></td>
			<td><%= r.created_at %></td>
			<td>
				<span class="btn btn-danger tip" data-media-delete="<%= r.id %>" title="{{{ trans('button.delete') }}}"><i class="fa fa-trash-o"></i></span>
			</td>
		</tr>

	<% }); %>

</script>
