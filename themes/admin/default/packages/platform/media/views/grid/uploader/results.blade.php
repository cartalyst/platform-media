<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

        <div class="media-item" data-grid-row <% if ($('#attached_media_' + r.id).length > 0) { %> data-selected-media<% } %>>
            <input id="media_<%= r.id %>" data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>" data-name="<%= r.name %>" data-thumbnail="<%= r.thumbnail_uri %>"<% if ($('#attached_media_' + r.id).length > 0) { %> checked disabled<% } %>>
            <label for="media_<%= r.id %>">
                <div class="media-img" style="background-image: url('<%= r.thumbnail_uri %>')"></div>
                <div class="media-item-info">
                    <span class="media-title"><a href="<%= r.edit_uri %>"><%= r.name %></a></span>
                    <span class="media-date"><%= moment(r.created_at).format('MMM DD, YYYY') %></span>
                </div>
            </label>
        </div>

	<% }); %>

</script>
