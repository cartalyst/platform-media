<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<tr>
			<td><input class="selectedId" type="checkbox" name="media[]" value="<%= r.id %>"></td>
			<td><%= r.name %></td>
			<td><%= r.created_at %></td>
			<td>
				<a class="btn btn-primary tip" href="{{ URL::toAdmin('media/<%= r.id %>/edit') }}" title="{{{ trans('button.edit') }}}"><i class="fa fa-edit"></i></a>

				<a class="btn btn-danger tip" data-media-delete href="{{ URL::toAdmin('media/<%= r.id %>/delete') }}" title="{{{ trans('button.delete') }}}"><i class="fa fa-trash-o"></i></a>
			</td>
		</tr>

	<% }); %>

</script>
