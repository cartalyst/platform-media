<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

		<tr data-id="<%= r.id %>">
			<td class="_hide"><input type="checkbox" name="entries[]" value="<%= r.id %>"></td>
			<td>
				<% if (r.is_image == 1) { %>
					<img src="{{ URL::to(media_cache_path('<%= r.thumbnail %>')) }}" />
				<% } else{ %>
					<i class="fa fa-file fa-3x"></i>
				<% } %>
			</td>
			<td class="col-md-9">

				<span class="pull-right text-right">

					<div class="btn-group dropup text-left">

						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
							{{{ trans('general.actions') }}} <span class="caret"></span>
						</button>

						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ URL::to('media/view/<%= r.path %>') }}" target="_blank">Share</a></li>
							<li><a href="{{ URL::to('media/download/<%= r.path %>') }}">Download</a></li>
							<li><a href="{{ URL::toAdmin('media/<%= r.id %>/email') }}">Email</a></li>
						</ul>

					</div>

					<br />

					<small>
					<% _.each(r.tags, function(tag) { %>
						<span class="label label-info"><%= tag %></span>
					<% }); %>
					</small>

				</span>

				<label class="label label-info"><%= r.id %></label> <a href="{{ URL::toAdmin('media/<%= r.id %>/edit') }}"><%= r.name %></a>

				<br />

				<% if (r.private == 1) { %>
					<i class="fa fa-lock"></i>
				<% } else { %>
					<i class="fa fa-unlock"></i>
				<% } %>

				&nbsp;

				<small><%= bytesToSize(r.size) %></small>

			</td>
			<td><%= moment(r.created_at).format('MMM DD, YYYY') %></td>
		</tr>

	<% }); %>

</script>
