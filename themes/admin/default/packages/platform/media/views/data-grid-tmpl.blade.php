<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<tr>
			<td><%= r.name %></td>
			<td><%= r.mime %></td>
			<td><%= r.created_at %></td>
		</tr>

	<% }); %>

</script>
