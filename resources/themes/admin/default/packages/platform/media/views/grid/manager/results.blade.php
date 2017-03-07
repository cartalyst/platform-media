<script type="text/template" data-grid="main" data-grid-template="results">

    <% var results = response.results; %>

    <% console.log('feesssssssss') %>
    <% console.log(results) %>

    <% if (_.isEmpty(results)) { %>

        <p class="media-no-results">{{{ trans('common.no_results') }}}</p>

    <% } else { %>

    	<% _.each(results, function(r) { %>

        <div class="media-item" data-grid-row <% if ($('#attached_media_' + r.id).length > 0) { %> data-selected-media<% } %>>
            <input id="media_<%= r.id %>" data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>" data-name="<%= r.name %>" data-thumbnail="<%= r.thumbnail_uri %>" data-mime="<%= r.mime %>" data-is_image="<%= r.is_image %>"<% if ($('#attached_media_' + r.id).length > 0) { %> checked disabled<% } %>>
            <label for="media_<%= r.id %>">

                <% if (r.is_image == 1) { %>
                <div class="media-img" style="background-image: url('<%= r.thumbnail_uri %>')"></div>
                <% } else if (r.mime == 'text/plain') { %>
                <div class="media-img" style="background-image:url('{{ Asset::getUrl('platform/media::img/txt.png') }}')"></div>
                <% } else if (r.mime == 'application/pdf') { %>
                <div class="media-img" style="background-image:url('{{ Asset::getUrl('platform/media::img/pdf.png') }}')"></div>
                <% } else { %>
                <div class="media-img" style="background-image:url('{{ Asset::getUrl('platform/media::img/other.png') }}')"></div>
                <% } %>

                <div class="media-item-info">
                    <span class="media-title"><a href="<%= r.edit_uri %>"><%= r.name %></a></span>
                    <span class="media-date"><%= moment(r.created_at).format('MMM DD, YYYY') %></span>
                </div>
            </label>
        </div>

        <% }); %>

    <% } %>

</script>
