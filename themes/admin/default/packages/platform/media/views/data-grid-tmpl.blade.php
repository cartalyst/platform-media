<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<div class="col-lg-3 col-md-4 col-xs-6">

			<div class="media" data-media="<%= r.id %>">

				<div class="media__type">
					<div class="media__type--<%= r.extension %>">
						<% if (r.is_image == 1) { %>
							<img src="{{ URL::to(media_cache_path('<%= r.thumbnail %>')) }}" />
						<% } %>
					</div>
				</div>

				<div class="media__mask">

					<h2><%= r.name %></h2>

					<p>@media('<%= r.id %>')</p>

					<ul class="media__actions">
						<li class="action">
							<a href="{{ URL::to('media/<%= r.path %>') }}" target="_blank">
								<i class="fa fa-link tip" data-placement="bottom" title="View"></i>
							</a>
						</li>
						<li class="action">
							<a href="{{ URL::to('media/download/<%= r.path %>') }}">
								<i class="fa fa-download tip" data-placement="bottom" title="Download"></i>
							</a>
						</li>
						<li class="action action--checkbox">
							<label data-media-marked="<%= r.id %>">
								<input class="selectedId" name="marked" id="marked_<%= r.id %>" type="checkbox" value="<%= r.id %>">

								<i class="fa fa-check-square-o tip" data-placement="bottom" title="Select"></i>
							</label>
						</li>
					</ul>

				</div>

				<div class="media__selected<%= r.private == 1 ? ' media__selected--is_private' : null %>"></div>

			</div>

		</div>

	<% }); %>

</script>



