<script type="text/template" data-grid="main" data-grid-template="filters">

	<%
		// Get the applied filters, but we'll make sure to not
        // show filters when doing any kind of live search.
        var filters = _.reject(grid.appliedFilters, function(f) { return f.type === 'live'; });

        // To validate, below, if the applied filter is a date with the format: YYYY-mm-dd
        var dateRegex = /[0-9]{4}-[0-9]{2}-[0-9]{2}/g;

        //
        var operators = {
            '='    : 'is equal to',
            '<'    : 'is less than',
            '>'    : 'is greater than',
            '!='   : 'is not equal to',
            'like' : 'contains',
        };
	%>

	<% if (_.isObject(filters)) { %>

		<% _.each(filters, function(f) { %>

			<button class="btn btn-default btn-sm">

				<span><i class="fa fa-times"></i></span>

				<% if (f.from !== undefined && f.to !== undefined) { %>

					<% if (/[0-9]{4}-[0-9]{2}-[0-9]{2}/g.test(f.from) && /[0-9]{4}-[0-9]{2}-[0-9]{2}/g.test(f.to)) { %>

						<%= f.label %> <em><%= moment(f.from).format('MMM DD, YYYY') %> - <%= moment(f.to).format('MMM DD, YYYY') %></em>

					<% } else { %>

						<%= f.label %> <em><%= f.from %> - <%= f.to %></em>

					<% } %>

				<% } else if (f.col_mask !== undefined && f.val_mask !== undefined) { %>

					<%= f.col_mask %> <em><%= f.val_mask %></em>

				<% } else { %>

					<% if (f.column === 'all') { %>

						<%= f.value %>

					<% } else { %>

						<%= f.value %> {{{ trans('common.in') }}} <em><%= f.column %></em>

					<% } %>

				<% } %>

			</button>

		<% }); %>

	<% } %>

</script>
