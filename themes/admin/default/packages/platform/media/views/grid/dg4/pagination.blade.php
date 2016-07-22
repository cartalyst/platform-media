<script type="text/template" data-grid="main" data-grid-template="pagination">

	<%

        // Declare the pagination variable
        var p = pagination;

    %>

    <div class="pull-left">

        <div class="pages">
            {{{ trans('common.showing') }}} <%= p.pageStart %> {{{ trans('common.to') }}} <%= p.pageLimit %> {{{ trans('common.of') }}} <span class="total"><%= p.filtered %></span>
        </div>

    </div>

    <div class="pull-right">

        <ul class="pagination pagination-sm">

            <% if (p.previousPage !== null) { %>

                <li><a href="#" data-grid="main" data-grid-page="1"><i class="fa fa-angle-double-left"></i></a></li>

                <li><a href="#" data-grid="main" data-grid-page="<%= p.previousPage %>"><i class="fa fa-chevron-left"></i></a></li>

            <% } else { %>

                <li class="disabled"><span><i class="fa fa-angle-double-left"></i></span></li>

                <li class="disabled"><span><i class="fa fa-chevron-left"></i></span></li>

            <% } %>

            <%

            var numPages = 11,
                split    = numPages - 1,
                middle   = Math.floor(split / 2);

            var i = p.page - middle > 0 ? p.page - middle : 1,
                j = p.pages;

            j = p.page + middle > p.pages ? j : p.page + middle;

            i = j - i < split ? j - split : i;

            if (i < 1)
            {
                i = 1;
                j = p.pages > split ? split + 1 : p.pages;
            }

            %>

            <% for(i; i <= j; i++) { %>

                <% if (p.page === i) { %>

                <li class="active"><span><%= i %></span></li>

                <% } else { %>

                <li><a href="#" data-grid="main" data-grid-page="<%= i %>"><%= i %></a></li>

                <% } %>

            <% } %>

            <% if (p.nextPage !== null) { %>

                <li><a href="#" data-grid="main" data-grid-page="<%= p.nextPage %>"><i class="fa fa-chevron-right"></i></a></li>

                <li><a href="#" data-grid="main" data-grid-page="<%= p.pages %>"><i class="fa fa-angle-double-right"></i></a></li>

            <% } else { %>

                <li class="disabled"><span><i class="fa fa-chevron-right"></i></span></li>

                <li class="disabled"><span><i class="fa fa-angle-double-right"></i></span></li>

            <% } %>

        </ul>

    </div>


</script>
