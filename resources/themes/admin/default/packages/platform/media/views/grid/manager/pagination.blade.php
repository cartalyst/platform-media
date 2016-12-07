<script type="text/template" data-grid="main" data-grid-template="pagination">

	<%
        // Pagination
        var p = pagination;
    %>

	<div class="pages">
        {{{ trans('common.showing') }}} <%= p.pageStart %> {{{ trans('common.to') }}} <%= p.pageLimit %> {{{ trans('common.of') }}} <span class="total"><%= p.filtered %></span>
    </div>

	<div class="flex">

		<ul class="pagination pagination-sm">

			<% if (p.previousPage !== null) { %>

				<li><a href="#" data-grid-page="<%= p.previousPage %>"><i class="fa fa-chevron-left"></i></a></li>

			<% } else { %>

				<li class="disabled"><span><i class="fa fa-chevron-left"></i></span></li>

			<% } %>

			<% if (p.nextPage !== null) { %>

				<li><a href="#" data-grid-page="<%= p.nextPage %>"><i class="fa fa-chevron-right"></i></a></li>

			<% } else { %>

				<li class="disabled"><span><i class="fa fa-chevron-right"></i></span></li>

			<% } %>

		</ul>

	</div>

</script>
